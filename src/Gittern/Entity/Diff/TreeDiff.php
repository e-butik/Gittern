<?php

namespace Gittern\Entity\Diff;

use Gittern\Entity\GitObject\Tree;

use Gittern\Entity\GitObject\Node\TreeNode;

/**
* 
*/
class TreeDiff
{
  protected $base, $comparison;

  public function __construct(Tree $base, Tree $comparison)
  {
    $this->base = $base;
    $this->comparison = $comparison;
  }

  public function getAddedNodes()
  {
    $added = array();
    foreach ($this->comparison->getNodes() as $node)
    {
      if (!$this->base->hasNodeNamed($node->getName()))
      {
        $added[] = $node;
      }
    }
    return $added;
  }

  public function getRemovedNodes()
  {
    $removed = array();
    foreach ($this->base->getNodes() as $node)
    {
      if (!$this->comparison->hasNodeNamed($node->getName()))
      {
        $removed[] = $node;
      }
    }
    return $removed;
  }

  public function getModifiedNodes()
  {
    $modified = array();
    foreach ($this->comparison->getNodes() as $node)
    {
      if ($this->base->hasNodeNamed($node->getName()) && !$this->base->getNodeNamed($node->getName())->equals($node))
      {
        $modified[] = $node;
      }
    }
    return $modified;
  }

  public function getAddedLeafNodesRecursive()
  {
    $leaf_nodes = array();
    foreach ($this->comparison->getNodes() as $node)
    {
      if ($node instanceof TreeNode)
      {
        if ($this->base->hasNodeNamed($node->getName()))
        {
          $base_tree = $this->base->getNodeNamed($node->getName())->getTree();
        }
        else
        {
          $base_tree = new Tree;
        }

        $diff = new TreeDiff($base_tree, $node->getTree());
        foreach ($diff->getAddedLeafNodesRecursive() as $leaf_node)
        {
          $leaf_nodes[$node->getName().'/'.$leaf_node->getName()] = $leaf_node;
        }
      }
      else if (!$this->base->hasNodeNamed($node->getName()))
      {
        $leaf_nodes[$node->getName()] = $node;
      }
    }

    return $leaf_nodes;
  }

  public function getRemovedLeafNodesRecursive()
  {
    $leaf_nodes = array();
    foreach ($this->base->getNodes() as $node)
    {
      if ($node instanceof TreeNode)
      {
        if ($this->comparison->hasNodeNamed($node->getName()))
        {
          $comp_tree = $this->comparison->getNodeNamed($node->getName())->getTree();
        }
        else
        {
          $comp_tree = new Tree;
        }

        $diff = new TreeDiff($node->getTree(), $comp_tree);
        foreach ($diff->getRemovedLeafNodesRecursive() as $leaf_node)
        {
          $leaf_nodes[$node->getName().'/'.$leaf_node->getName()] = $leaf_node;
        }
      }
      else if (!$this->comparison->hasNodeNamed($node->getName()))
      {
        $leaf_nodes[$node->getName()] = $node;
      }
    }

    return $leaf_nodes;
  }

  public function getModifiedLeafNodesRecursive()
  {
    $leaf_nodes = array();
    foreach ($this->getModifiedNodes() as $node)
    {
      if ($node instanceof TreeNode)
      {
        $diff = new TreeDiff($this->base->getNodeNamed($node->getName())->getTree(), $node->getTree());
        foreach ($diff->getModifiedLeafNodesRecursive() as $leaf_node)
        {
          $leaf_nodes[$node->getName().'/'.$leaf_node->getName()] = $leaf_node;
        }
      }
      else
      {
        $leaf_nodes[$node->getName()] = $node;
      }
    }

    return $leaf_nodes;
  }
}