<?php

namespace Gittern\Desiccator;

use Gittern\Entity\GitObject\Tree;
use Gittern\Transport\RawObject;

use Iodophor\Io\StringWriter;

/**
* @author Magnus Nordlander
**/
class TreeDesiccator
{
  /**
   * @author Magnus Nordlander
   **/
  public function desiccate(Tree $tree)
  {
    $writer = new StringWriter();

    foreach ($tree->getNodes() as $node) 
    {
      $writer->writeString8($node->getOctalModeString());
      $writer->writeString8(' ');
      $writer->writeString8($node->getName());
      $writer->writeString8("\0");
      $sha = $node->getRelatedObject()->getSha();
      if (strlen($sha) != 40)
      {
        throw new \RuntimeException("Object referred to by node named ".$node->getName()." is not persisted yet.");
      }
      $writer->writeHHex($sha);
    }

    $data = $writer->toString();

    return new RawObject('tree', $data);
  }
}