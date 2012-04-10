<?php

namespace Gittern\Proxy;

use Gittern\Entity\GitObject\Commit;
use Gittern\Entity\GitObject\Tree;
use Gittern\Entity\GitObject\User;
use Gittern\Repository;

/**
* @author Magnus Nordlander
**/
class CommitProxy extends Commit
{
  protected $sha;
  protected $commit;
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
    if (!$this->commit)
    {
      $this->commit = $this->repo->getObject($this->sha);
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
    return $this->commit->setSha($sha);
  }

  /**
   * @author Magnus Nordlander
   **/
  public function setMessage($message)
  {
    $this->__load();
    return $this->commit->setMessage($message);
  }

  /**
   * @author Magnus Nordlander
   **/
  public function getMessage()
  {
    $this->__load();
    return $this->commit->getMessage();
  }

  /**
   * @author Magnus Nordlander
   **/
  public function setTree(Tree $tree)
  {
    $this->__load();
    return $this->commit->setTree($tree);
  }

  /**
   * @author Magnus Nordlander
   **/
  public function getTree()
  {
    $this->__load();
    return $this->commit->getTree();
  }

  /**
   * @author Magnus Nordlander
   **/
  public function addParent(Commit $parent)
  {
    $this->__load();
    return $this->commit->addParent($parent);
  }

  /**
   * @author Magnus Nordlander
   **/
  public function getParents()
  {
    $this->__load();
    return $this->commit->getParents();
  }

  /**
   * @author Magnus Nordlander
   **/
  public function setAuthor(User $author)
  {
    $this->__load();
    return $this->commit->setAuthor($author);
  }

  /**
   * @author Magnus Nordlander
   **/
  public function getAuthor()
  {
    $this->__load();
    return $this->commit->getAuthor();
  }

  /**
   * @author Magnus Nordlander
   **/
  public function setAuthorTime(\DateTime $author_time)
  {
    $this->__load();
    return $this->commit->setAuthorTime($author_time);
  }

  /**
   * @author Magnus Nordlander
   **/
  public function getAuthorTime()
  {
    $this->__load();
    return $this->commit->getAuthorTime();
  }

  /**
   * @author Magnus Nordlander
   **/
  public function setCommitter(User $committer)
  {
    $this->__load();
    return $this->commit->setCommitter($committer);
  }

  /**
   * @author Magnus Nordlander
   **/
  public function getCommitter()
  {
    $this->__load();
    return $this->commit->getCommitter();
  }

  /**
   * @author Magnus Nordlander
   **/
  public function setCommitTime(\DateTime $commit_time)
  {
    $this->__load();
    return $this->commit->setCommitTime($commit_time);
  }

  /**
   * @author Magnus Nordlander
   **/
  public function getCommitTime()
  {
    $this->__load();
    return $this->commit->getCommitTime();
  }
}