<?php

namespace Gittern\Proxy;

use Gittern\Entity\GitObject\Tree;
use Gittern\Entity\GitObject\Node\BaseNode;
use Gittern\Repository;

/**
* @author Magnus Nordlander
**/
class TreeProxy extends Tree
{
  protected $sha;
  protected $tree = false;
  protected $repo;

  public function __construct(Repository $repo, $sha)
  {
    $this->sha = $sha;
    $this->repo = $repo;
  }

  /**
   * @author Magnus Nordlander
   **/
  public function __load()
  {
    if (!$this->tree)
    {
      $this->tree = $this->repo->getObject($this->sha);
    }
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
  public function setSha($sha)
  {
    $this->__load();
    return $this->tree->setSha($sha);
  }

    /**
   * @author Magnus Nordlander
   **/
  public function addNode(BaseNode $node)
  {
    $this->__load();
    return $this->tree->addNode($node);
  }

  /**
   * @author Magnus Nordlander
   **/
  public function getNodes()
  {
    $this->__load();
    return $this->tree->getNodes();
  }

  /**
   * @author Magnus Nordlander
   **/
  public function getNodeNamed($name)
  {
    $this->__load();
    return $this->tree->getNodeNamed($name);
  }

  /**
   * @author Magnus Nordlander
   **/
  public function hasNodeNamed($name)
  {
    $this->__load();
    return $this->tree->hasNodeNamed($name);
  }

  /**
   * @author Magnus Nordlander
   **/
  public function getIterator()
  {
    $this->__load();
    return $this->tree->getIterator();
  }
}