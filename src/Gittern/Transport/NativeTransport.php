<?php

namespace Gittern\Transport;

use Iodophor\Io\FileReader;

/**
* @author Magnus Nordlander
**/
class NativeTransport implements TransportInterface
{
  protected $git_dir;

  public function __construct($git_dir)
  {
    $this->git_dir = $git_dir;
  }

  public function resolveTreeish($treeish)
  {
    // Maybe it's a full sha?
    if (preg_match("/^[0-9a-fA-F]{40}$/", $treeish))
    {
      return $treeish;
    }

    // Maybe it's a branch?
    if ($branch_sha = $this->resolveHead($treeish))
    {
      return $branch_sha;
    }

    return false;
  }

  /**
   * @author Magnus Nordlander
   **/
  public function resolveHead($head_name)
  {
    return $this->resolveRef('heads/'.$head_name);
  }

  /**
   * @author Magnus Nordlander
   **/
  protected function resolveRef($ref)
  {
    if ($this->isFileRelative('refs/'.$ref))
    {
      return trim($this->readFileRelative('refs/'.$ref), "\r\n");
    }
    else
    {
      return $this->resolvePackedRef($ref);
    }
  }

  /**
   * @author Magnus Nordlander
   **/
  protected function resolvePackedRef($ref)
  {
    if ($this->isFileRelative('packed-refs'))
    {
      $packed_refs = $this->readFileRelative('packed-refs');

      if (preg_match('/([0-9a-f]{40}) '.preg_quote('refs/'.$ref, '/').'/', $packed_refs, $matches))
      {
        return $matches[1];
      }
    }

    return false;
  }

  public function setBranch($branch, $sha)
  {
    $this->writeFileRelative('refs/heads/'.$branch, $sha);
  }

  /**
   * @author Magnus Nordlander
   **/
  public function resolveRawObject($sha)
  {
    $first = substr($sha, 0, 2);
    $last = substr($sha, 2);

    // Unpacked case
    if ($this->isFileRelative('objects/'.$first.'/'.$last))
    {
      $uncompressed_data = gzuncompress($this->readFileRelative('objects/'.$first.'/'.$last));

      if (strlen($uncompressed_data) == 0)
      {
        throw new \RuntimeException("Attempting to hydrate empty object");
      }

      sscanf($uncompressed_data, "%s %d\0", $type, $length);

      $offset = strlen($type)+strlen($length)+2; //Space and NUL

      if (strlen($uncompressed_data) !== $offset+$length)
      {
        throw new \RuntimeException(sprintf("Length derived from git object header (%d) does not match actual length (%d)", $offset+$length, strlen($uncompressed_data)));
      }

      $data = substr($uncompressed_data, $offset, $length);

      return new RawObject($type, $length, $data);
    }
    else
    {
      // Maybe it's packed?
      foreach ($this->getPackfiles() as $packfile) 
      {
        if ($packfile->hasSha($sha))
        {
          return $packfile->getRawObjectForSha($sha);
        }
      }
    }

    return null;
  }

  protected function getPackfiles()
  {
    $packfiles = array();
    foreach ($this->getPackfileNames() as $packfile_name) 
    {
      $packfile = new Packfile(new FileReader($this->git_dir.'/objects/pack/'.$packfile_name.'.pack'), $this);
      $packfile->setIndex(new PackfileIndex(new FileReader($this->git_dir.'/objects/pack/'.$packfile_name.'.idx')));

      $packfiles[] = $packfile;
    }

    return $packfiles;
  }

  protected function getPackfileNames()
  {
    $return = array();
    foreach (scandir($this->git_dir.'/objects/pack/') as $filename)
    {
      $matches = array();
      if (preg_match('/^(pack-[0-9a-fA-F]+).pack$/', $filename, $matches) > 0)
      {
        $return[] = $matches[1];
      }
    }
    return $return;
  }

  public function hasIndexData()
  {
    return $this->isFileRelative('index');
  }

  /**
   * @author Magnus Nordlander
   **/
  public function getIndexData()
  {
    return $this->readFileRelative('index');
  }

  /**
   * @author Magnus Nordlander
   **/
  public function putIndexData($data)
  {
    $this->writeFileRelative('index', $data);
  }

  /**
   * @author Magnus Nordlander
   **/
  public function putObject($sha, $data)
  {
    $first = substr($sha, 0, 2);
    $last = substr($sha, 2);

    $this->writeFileRelative('objects/'.$first.'/'.$last, $data);
  }

  protected function isFileRelative($relative_path)
  {
    return is_file($this->git_dir.'/'.$relative_path);
  }

  protected function readFileRelative($relative_path)
  {
    return file_get_contents($this->git_dir.'/'.$relative_path);
  }

  protected function writeFileRelative($relative_path, $data)
  {
    $path = $this->git_dir.'/'.$relative_path;

    $dir = pathinfo($path, PATHINFO_DIRNAME);

    if (!is_dir($dir))
    {
      mkdir($dir, 0777, true);
    }

    file_put_contents($path, $data);
  }
}