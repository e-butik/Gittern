<?php

namespace Gittern\Hydrator;

use Gittern\GitObject\Blob;

/**
* @author Magnus Nordlander
**/
class BlobHydrator implements ObjectHydrating
{
  /**
   * @author Magnus Nordlander
   **/
  public function hydrate($sha, $data)
  {
    $blob = new Blob;

    $blob->setSha($sha);
    $blob->setContents($data);

    return $blob;
  }
}