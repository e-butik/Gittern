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

  public function __load()
  {
    if (!$this->commit)
    {
      $this->commit = $this->repo->getObject($this->sha);
    }
  }

  public function getSha()
  {
    return $this->sha;
  }

  public function setSha($sha)
  {
    $this->__load();
    return $this->commit->setSha($sha);
  }

  public function setMessage($message)
  {
    $this->__load();
    return $this->commit->setMessage($message);
  }

  public function getMessage()
  {
    $this->__load();
    return $this->commit->getMessage();
  }

  public function setTree(Tree $tree)
  {
    $this->__load();
    return $this->commit->setTree($tree);
  }

  public function getTree()
  {
    $this->__load();
    return $this->commit->getTree();
  }

  public function addParent(Commit $parent)
  {
    $this->__load();
    return $this->commit->addParent($parent);
  }

  public function getParents()
  {
    $this->__load();
    return $this->commit->getParents();
  }

  public function setAuthor(User $author)
  {
    $this->__load();
    return $this->commit->setAuthor($author);
  }

  public function getAuthor()
  {
    $this->__load();
    return $this->commit->getAuthor();
  }

  public function setAuthorTime(\DateTime $author_time)
  {
    $this->__load();
    return $this->commit->setAuthorTime($author_time);
  }

  public function getAuthorTime()
  {
    $this->__load();
    return $this->commit->getAuthorTime();
  }

  public function setCommitter(User $committer)
  {
    $this->__load();
    return $this->commit->setCommitter($committer);
  }

  public function getCommitter()
  {
    $this->__load();
    return $this->commit->getCommitter();
  }

  public function setCommitTime(\DateTime $commit_time)
  {
    $this->__load();
    return $this->commit->setCommitTime($commit_time);
  }

  public function getCommitTime()
  {
    $this->__load();
    return $this->commit->getCommitTime();
  }
}