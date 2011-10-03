<?php

namespace Gittern\GitObject\Node;

use Gittern\GitObject\Tree;

/**
* @author Magnus Nordlander
**/
class TreeNode extends BaseNode
{
  protected $tree;

  /**
   * @author Magnus Nordlander
   **/
  public function setTree(Tree $tree)
  {
    $this->tree = $tree;
  }

  /**
   * @author Magnus Nordlander
   **/
  public function getTree()
  {
    return $this->tree;
  }

  public function getRelatedObject()
  {
    return $this->getBlob();
  }
}