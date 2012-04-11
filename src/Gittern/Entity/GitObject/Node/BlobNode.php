<?php

namespace Gittern\Entity\GitObject\Node;

use Gittern\Entity\GitObject\Blob;

/**
* @author Magnus Nordlander
**/
class BlobNode extends BaseNode
{
  protected $blob;

  public function setBlob(Blob $blob)
  {
    $this->blob = $blob;
  }

  public function getBlob()
  {
    return $this->blob;
  }

  public function getRelatedObject()
  {
    return $this->getBlob();
  }
}