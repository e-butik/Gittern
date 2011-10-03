<?php

namespace Gittern\Hydrator;

use Gittern\GitObject\Tree;
use Gittern\GitObject\Node;

use Gittern\Proxy\BlobProxy;
use Gittern\Proxy\TreeProxy;

use Gittern\Repository;

/**
* @author Magnus Nordlander
**/
class TreeHydrator implements ObjectHydrating
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
  public function hydrate($sha, $data)
  {
    $tree = new Tree;
    $tree->setSha($sha);

    while (strlen($data))
    {
      sscanf($data, "%s %s%n", $mode, $name, $pos);
      $sha = bin2hex(substr($data, $pos+1, 20));

      $mode = intval($mode, 8);
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

      $data = substr($data, $pos+21);
    }

    return $tree;
  }
}