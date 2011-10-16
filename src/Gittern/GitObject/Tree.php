<?php

namespace Gittern\GitObject;

use Gittern\Iterator\RecursiveTreeIterator;

/**
* @author Magnus Nordlander
**/
class Tree implements \IteratorAggregate
{
  protected $sha;

  /**
   * @var array<Node\BaseNode>
   */
  protected $nodes = array();

  /**
   * @author Magnus Nordlander
   **/
  public function setSha($sha)
  {
    $this->sha = $sha;
  }

  /**
   * @author Magnus Nordlander
   **/
  public function getSha()
  {
    return $this->sha;
  }

  /**
   * @author Magnus Nordlander
   **/
  public function addNode(Node\BaseNode $node)
  {
    $this->nodes[$node->getName()] = $node;
  }

  /**
   * @author Magnus Nordlander
   **/
  public function getNodes()
  {
    return array_values($this->nodes);
  }

  /**
   * @author Magnus Nordlander
   **/
  public function getIterator()
  {
    return new RecursiveTreeIterator($this->nodes);
  }

  /**
   * @author Magnus Nordlander
   **/
  public function getNodeNamed($name)
  {
    return $this->nodes[$name];
  }

  public function hasNodeNamed($name)
  {
    return isset($this->nodes[$name]);
  }
}