<?php

namespace Gittern\Hydrator;

use Gittern\Entity\GitObject\Blob;
use Gittern\Transport\RawObject;

/**
* @author Magnus Nordlander
**/
class BlobHydrator implements HydratorInterface
{
  /**
   * @author Magnus Nordlander
   **/
  public function hydrate(RawObject $raw_object)
  {
    $blob = new Blob;

    $blob->setSha($raw_object->getSha());
    $blob->setContents($raw_object->getData());

    return $blob;
  }
}