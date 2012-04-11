<?php

namespace Gittern\Entity\GitObject;

/**
* @author Magnus Nordlander
**/
class Blob
{
  protected $sha;

  protected $contents;

  public function setSha($sha)
  {
    $this->sha = $sha;
  }

  public function getSha()
  {
    return $this->sha;
  }

  public function setContents($contents)
  {
    $this->contents = $contents;
  }

  public function getContents()
  {
    return $this->contents;
  }
}