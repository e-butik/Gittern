<?php

namespace Gittern\Desiccator;

use Gittern\GitObject\Blob;

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
    return $blob->getContents();
  }
}