<?php

namespace Gittern\Entity\GitObject\Node;

use Gittern\Entity\GitObject\Tree;

/**
* @author Magnus Nordlander
**/
class TreeNode extends BaseNode
{
  protected $tree;

  public function __construct()
  {
    $this->mode = 040000;
  }

  public function setTree(Tree $tree)
  {
    $this->tree = $tree;
  }

  public function getTree()
  {
    return $this->tree;
  }

  public function getRelatedObject()
  {
    return $this->getTree();
  }
}