<?php

namespace Gittern\GitObject\Node;

use Gittern\GitObject\Blob;

/**
* @author Magnus Nordlander
**/
class BlobNode extends BaseNode
{
  protected $blob;

  /**
   * @author Magnus Nordlander
   **/
  public function setBlob(Blob $blob)
  {
    $this->blob = $blob;
  }

  /**
   * @author Magnus Nordlander
   **/
  public function getBlob()
  {
    return $this->blob;
  }

  public function getRelatedObject()
  {
    return $this->getBlob();
  }
}