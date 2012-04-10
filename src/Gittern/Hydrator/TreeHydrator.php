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

  /**
   * @author Magnus Nordlander
   **/
  public function __construct(Repository $repo)
  {
    $this->repo = $repo;
  }

  /**
   * @author Magnus Nordlander
   **/
  public function hydrate(RawObject $raw_object)
  {
    $tree = new Tree;
    $tree->setSha($raw_object->getSha());

    $reader = new StringReader($raw_object->getData());

    while ($reader->available())
    {
      $mode = intval($reader->readString8(6), 8);
      assert($reader->readString8(1) == ' ');
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