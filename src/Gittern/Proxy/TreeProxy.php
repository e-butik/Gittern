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

  public function __load()
  {
    if (!$this->tree)
    {
      $this->tree = $this->repo->getObjectBySha($this->sha);
    }
  }

  public function getSha()
  {
    return $this->sha;
  }

  public function setSha($sha)
  {
    $this->__load();
    return $this->tree->setSha($sha);
  }

  public function addNode(BaseNode $node)
  {
    $this->__load();
    return $this->tree->addNode($node);
  }

  public function getNodes()
  {
    $this->__load();
    return $this->tree->getNodes();
  }

  public function getNodeNamed($name)
  {
    $this->__load();
    return $this->tree->getNodeNamed($name);
  }

  public function hasNodeNamed($name)
  {
    $this->__load();
    return $this->tree->hasNodeNamed($name);
  }

  public function getIterator()
  {
    $this->__load();
    return $this->tree->getIterator();
  }
}