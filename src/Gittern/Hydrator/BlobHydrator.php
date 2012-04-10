<?php

namespace Gittern\Hydrator;

use Gittern\Entity\GitObject\Blob;

/**
* @author Magnus Nordlander
**/
class BlobHydrator implements HydratorInterface
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