<?php

namespace Gittern\Desiccator;

use Gittern\Entity\GitObject\Blob;
use Gittern\Transport\RawObject;

/**
* @author Magnus Nordlander
**/
class BlobDesiccator
{
  /**
   * @author Magnus Nordlander
   **/
  public function desiccate(Blob $blob)
  {
    $contents = $blob->getContents();

    return new RawObject('blob', $blob->getContents());
  }
}