<?php

namespace Gittern\Hydrator;

use Gittern\Entity\GitObject\Tree;
use Gittern\Entity\GitObject\Node;

use Gittern\Proxy\BlobProxy;
use Gittern\Proxy\TreeProxy;

use Gittern\Repository;

use Gittern\Transport\RawObject;

use Iodophor\Io\StringReader;

/**
* @author Magnus Nordlander
**/
class TreeHydrator implements HydratorInterface
{
  protected $repo;

  public function __construct(Repository $repo)
  {
    $this->repo = $repo;
  }

  public function hydrate(RawObject $raw_object)
  {
    $tree = new Tree;
    $tree->setSha($raw_object->getSha());

    $reader = new StringReader($raw_object->getData());

    while ($reader->available())
    {
      $mode = intval($this->readModeString($reader), 8);
      $name = $this->readName($reader);
      $sha = $reader->readHHex(20);
      $is_tree = (bool)($mode & 040000);

      if ($is_tree)
      {
        $node = new Node\TreeNode;
        $node->setTree(new TreeProxy($this->repo, $sha));
      }
      else
      {
        $node = new Node\BlobNode;
        $node->setBlob(new BlobProxy($this->repo, $sha));
      }

      $node->setIntegerMode($mode);
      $node->setName($name);

      $tree->addNode($node);
    }

    return $tree;
  }

  protected function readModeString(StringReader $reader)
  {
    $mode_string = '';
    do {
      $char = $reader->read(1);
      if ($char != " ")
      {
        assert(is_numeric($char));
        $mode_string .= $char;
      }

    } while($char != " ");

    return $mode_string;
  }

  protected function readName(StringReader $reader)
  {
    $name = '';
    do {
      $char = $reader->read(1);
      if ($char != "\0")
      {
        $name .= $char;
      }

    } while($char != "\0");

    return $name;
  }
}