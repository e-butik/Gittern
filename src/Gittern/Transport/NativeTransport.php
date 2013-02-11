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

    // Maybe it's a tag?
    if ($tag = $this->resolveTag($treeish))
    {
      return $tag;
    }

    // Maybe it's a branch?
    if ($branch_sha = $this->resolveHead($treeish))
    {
      return $branch_sha;
    }

    // Maybe it's a remote branch?
    if ($branch_sha = $this->resolveRemote($treeish))
    {
      return $branch_sha;
    }

    return false;
  }

  public function resolveHead($head_name)
  {
    return $this->resolveRef('heads/'.$head_name);
  }

  public function resolveTag($tag_name)
  {
    return $this->resolveRef("tags/{$tag_name}");
  }

  public function resolveRemote($remote_name)
  {
    return $this->resolveRef('remotes/'.$remote_name);
  }

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

  public function removeBranch($branch)
  {
    $this->removeFileRelative('refs/heads/'.$branch);
  }

  public function fetchRawObject($sha)
  {
    // Unpacked case
    if ($this->isLoose($sha))
    {
      $first = substr($sha, 0, 2);
      $last = substr($sha, 2);

      $loose_object_path = 'objects/'.$first.'/'.$last;

      $compressed_data = $this->readFileRelative($loose_object_path);
      $uncompressed_data = @gzuncompress($compressed_data);

      if ($uncompressed_data === false)
      {
        throw new \RuntimeException(sprintf('Couldn\'t decompress Git object in %s.', $this->resolveRelativePath($loose_object_path)));
      }

      if (strlen($uncompressed_data) == 0)
      {
        throw new \RuntimeException("Attempting to hydrate empty object");
      }

      sscanf($uncompressed_data, "%s %d\0", $type, $length);

      $offset = strlen($type)+strlen($length)+2; //Space and NUL

      $raw_object = new RawObject($type, substr($uncompressed_data, $offset));

      if ($raw_object->getLength() !== $length)
      {
        throw new \RuntimeException(sprintf("Length derived from git object header (%d) does not match actual length (%d)", $offset+$length, strlen($uncompressed_data)));
      }

      if ($raw_object->getSha() != $sha)
      {
        throw new \RuntimeException(sprintf("Unexpected RawObject sha, expected %s, was %s", $sha, $raw_object->getSha()));
      }

      return $raw_object;
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

  public function isLoose($sha)
  {
    $first = substr($sha, 0, 2);
    $last = substr($sha, 2);

    $loose_object_path = 'objects/'.$first.'/'.$last;

    return $this->isFileRelative($loose_object_path);
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
    if (is_dir($this->git_dir.'/objects/pack/'))
    {
      foreach (scandir($this->git_dir.'/objects/pack/') as $filename)
      {
        $matches = array();
        if (preg_match('/^(pack-[0-9a-fA-F]+).pack$/', $filename, $matches) > 0)
        {
          $return[] = $matches[1];
        }
      }
    }
    return $return;
  }

  public function hasIndexData()
  {
    return $this->isFileRelative('index');
  }

  public function getIndexData()
  {
    return $this->readFileRelative('index');
  }

  public function putIndexData($data)
  {
    $this->writeFileRelative('index', $data);
  }

  public function putRawObject(RawObject $raw_object)
  {
    $sha = $raw_object->getSha();
    $first = substr($sha, 0, 2);
    $last = substr($sha, 2);

    $data = gzcompress($raw_object->getType().' '.$raw_object->getLength()."\0".$raw_object->getData(), 4);

    if (!$this->isFileRelative('objects/'.$first.'/'.$last))
    {
      $this->writeFileRelative('objects/'.$first.'/'.$last, $data);
    }
  }

  protected function isFileRelative($relative_path)
  {
    return is_file($this->resolveRelativePath($relative_path));
  }

  protected function readFileRelative($relative_path)
  {
    return file_get_contents($this->resolveRelativePath($relative_path));
  }

  protected function writeFileRelative($relative_path, $data)
  {
    $path = $this->resolveRelativePath($relative_path);

    $dir = pathinfo($path, PATHINFO_DIRNAME);

    if (!is_dir($dir))
    {
      mkdir($dir, 0777, true);
    }

    file_put_contents($path, $data);
  }

  protected function removeFileRelative($relative_path)
  {
    unlink($this->resolveRelativePath($relative_path));
  }

  protected function resolveRelativePath($relative_path)
  {
    return $this->git_dir.'/'.$relative_path;
  }
}
