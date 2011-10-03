<?php

namespace Gittern\Transport;

use Gaufrette\Filesystem;

/**
* @author Magnus Nordlander
**/
class GaufretteTransport implements Transportable
{
  protected $filesystem;

  public function __construct(Filesystem $fs)
  {
    $this->filesystem = $fs;
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
  public function resolveRef($ref)
  {
    if ($this->filesystem->has('refs/'.$ref))
    {
      return trim($this->filesystem->read('refs/'.$ref), "\r\n");
    }
    else
    {
      return $this->resolvePackedRef($ref);
    }
  }

  /**
   * @author Magnus Nordlander
   **/
  public function resolvePackedRef($ref)
  {
    if ($this->filesystem->has('packed-refs'))
    {
      $packed_refs = $this->filesystem->read('packed-refs');

      if (preg_match('/^([0-9a-f]{40}) '.preg_quote('refs/'.$ref).'/$', $matches))
      {
        return $matches[1];
      }
    }

    return false;
  }

  /**
   * @author Magnus Nordlander
   **/
  public function resolveObject($sha)
  {
    $first = substr($sha, 0, 2);
    $last = substr($sha, 2);

    // Unpacked case
    if ($this->filesystem->has('objects/'.$first.'/'.$last))
    {
      return $this->filesystem->read('objects/'.$first.'/'.$last);
    }
    else
    {
      // Maybe it's packed?
    }
  }

  /**
   * @author Magnus Nordlander
   **/
  public function getIndexData()
  {
    return $this->filesystem->read('index');
  }
}