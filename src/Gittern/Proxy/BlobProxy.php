<?php

namespace Gittern\Proxy;

use Gittern\GitObject\Blob;
use Gittern\Repository;

/**
* @author Magnus Nordlander
**/
class BlobProxy extends Blob
{
  protected $sha;
  protected $blob;
  protected $repo;

  public function __construct(Repository $repo, $sha)
  {
    $this->sha = $sha;
    $this->repo = $repo;
  }

  /**
   * @author Magnus Nordlander
   **/
  public function __load()
  {
    if (!$this->blob)
    {
      $this->blob = $this->repo->getObject($this->sha);
    }
  }

  /**
   * @author Magnus Nordlander
   **/
  public function getSha()
  {
    return $this->sha;
  }

  /**
   * @author Magnus Nordlander
   **/
  public function setSha($sha)
  {
    $this->__load();
    return $this->blob->setSha($sha);
  }

  /**
   * @author Magnus Nordlander
   **/
  public function setContents($contents)
  {
    $this->__load();
    return $this->blob->setContents($contents);
  }

  /**
   * @author Magnus Nordlander
   **/
  public function getContents()
  {
    $this->__load();
    return $this->blob->getContents();
  }
}