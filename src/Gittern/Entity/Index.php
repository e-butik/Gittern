<?php

namespace Gittern\Entity;

use Gittern\Entity\GitObject\Tree;
use Gittern\Entity\GitObject\Node\TreeNode;
use Gittern\Entity\GitObject\Node\BlobNode;

use Gittern\Exception\EntityNotFoundException;
use Gittern\Exception\InvalidTypeException;

/**
* @author Magnus Nordlander
**/
class Index
{
  const SIGNATURE = 'DIRC';

  const VERSION = 2;

  protected $entries = array();

  protected $extensions = array();

  public function addEntry(IndexEntry $entry)
  {
    $this->entries[$entry->getName()] = $entry;
  }

  public function getEntries()
  {
    return array_values($this->entries);
  }

  public function countEntries()
  {
    return count($this->entries);
  }

  public function getEntryNamed($name)
  {
    if (!isset($this->entries[$name]))
    {
      throw new EntityNotFoundException('No entry named '.$name);
    }
    return $this->entries[$name];
  }

  public function removeEntryNamed($name)
  {
    if (isset($this->entries[$name]))
    {
      unset($this->entries[$name]);
      return;
    }
    throw new EntityNotFoundException('No entry named '.$name);
  }

  public function clear()
  {
    $this->entries = array();
  }

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
          throw new InvalidTypeException("Blob path $name specifies another blob as parent tree, which is impossible");
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