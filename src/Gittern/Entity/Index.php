<?php

namespace Gittern\Entity;

use Gittern\Entity\GitObject\Tree;
use Gittern\Entity\GitObject\Node\TreeNode;
use Gittern\Entity\GitObject\Node\BlobNode;

/**
* @author Magnus Nordlander
**/
class Index
{
  const SIGNATURE = 'DIRC';

  const VERSION = 2;

  protected $entries = array();

  protected $extensions = array();

  /**
   * @author Magnus Nordlander
   **/
  public function addEntry(IndexEntry $entry)
  {
    $this->entries[$entry->getName()] = $entry;
  }

  /**
   * @author Magnus Nordlander
   **/
  public function getEntries()
  {
    return array_values($this->entries);
  }

  public function countEntries()
  {
    return count($this->entries);
  }

  /**
   * @author Magnus Nordlander
   **/
  public function getEntryNamed($name)
  {
    if (!isset($this->entries[$name]))
    {
      throw new \OutOfBoundsException('No entry named '.$name);
    }
    return $this->entries[$name];
  }

  /**
   * @author Magnus Nordlander
   **/
  public function removeEntryNamed($name)
  {
    if (isset($this->entries[$name]))
    {
      unset($this->entries[$name]);
      return;
    }
    throw new \OutOfBoundsException('No entry named '.$name);
  }

  /**
   * @author Magnus Nordlander
   **/
  public function getEntryNames()
  {
    return array_keys($this->entries);
  }

  public function createTree()
  {
    $tree = new Tree;
    foreach ($this->entries as $name => $entry) 
    {
      $explosion = explode("/", $name);
      $blob_name = array_pop($explosion);

      $current_tree = $tree;
      foreach ($explosion as $subtree_name) 
      {
        if (!$current_tree->hasNodeNamed($subtree_name))
        {
          $subtree = new Tree;
          $subtree_node = new TreeNode;
          $subtree_node->setTree($subtree);
          $subtree_node->setName($subtree_name);
          $current_tree->addNode($subtree_node);
        }

        $node = $current_tree->getNodeNamed($subtree_name);
        if (!($node instanceof TreeNode))
        {
          throw new RuntimeException("Blob path $name specifies another blob as parent tree, which is impossible");
        }

        $current_tree = $node->getTree();
      }

      $blob_node = $entry->createBlobNode();
      $blob_node->setName($blob_name);
      $current_tree->addNode($blob_node);
    }

    return $tree;
  }

  public function populateFromTree(Tree $tree, $prefix = '')
  {
    foreach ($tree as $node)
    {
      if ($node instanceOf TreeNode)
      {
        $this->populateFromTree($node->getTree(), $prefix.$node->getName().'/');
      }
      elseif ($node instanceof BlobNode)
      {
        $entry = IndexEntry::createFromBlobNode($node);
        $entry->setName($prefix.$node->getName());
        $this->addEntry($entry);
      }
    }
  }
}