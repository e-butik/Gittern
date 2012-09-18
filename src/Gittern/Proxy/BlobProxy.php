<?php

namespace Gittern\Proxy;

use Gittern\Entity\GitObject\Blob;
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

  public function __load()
  {
    if (!$this->blob)
    {
      $this->blob = $this->repo->getObjectBySha($this->sha);
    }
  }

  public function getSha()
  {
    return $this->sha;
  }

  public function setSha($sha)
  {
    $this->__load();
    return $this->blob->setSha($sha);
  }

  public function setContents($contents)
  {
    $this->__load();
    return $this->blob->setContents($contents);
  }

  public function getContents()
  {
    $this->__load();
    return $this->blob->getContents();
  }
}