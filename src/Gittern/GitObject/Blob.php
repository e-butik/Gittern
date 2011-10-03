<?php

namespace Gittern\GitObject;

/**
* @author Magnus Nordlander
**/
class Blob
{
  protected $sha;

  protected $contents;

  /**
   * @author Magnus Nordlander
   **/
  public function setSha($sha)
  {
    $this->sha = $sha;
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
  public function setContents($contents)
  {
    $this->contents = $contents;
  }

  /**
   * @author Magnus Nordlander
   **/
  public function getContents()
  {
    return $this->contents;
  }
}