<?php

namespace Gittern\Entity\GitObject;

/**
* @author Magnus Nordlander
**/
class Commit
{
  protected $sha;

  /** @var Tree */
  protected $tree;

  /** @var array<Commit> */
  protected $parents = array();

  /** @var User */
  protected $author;

  /** @var User */
  protected $committer;

  protected $message;

  /** @var DateTime */
  protected $author_time;

  /** @var DateTime */
  protected $commit_time;

  public function setSha($sha)
  {
    $this->sha = $sha;
  }

  public function getSha()
  {
    return $this->sha;
  }

  public function setMessage($message)
  {
    $this->message = $message;
  }

  public function getMessage()
  {
    return $this->message;
  }

  public function setTree(Tree $tree)
  {
    $this->tree = $tree;
  }

  public function getTree()
  {
    return $this->tree;
  }

  public function addParent(Commit $parent)
  {
    $this->parents[] = $parent;
  }

  public function getParents()
  {
    return $this->parents;
  }

  public function setAuthor(User $author)
  {
    $this->author = $author;
  }

  public function getAuthor()
  {
    return $this->author;
  }

  public function setAuthorTime(\DateTime $author_time)
  {
    $this->author_time = $author_time;
  }

  public function getAuthorTime()
  {
    return $this->author_time;
  }

  public function setCommitter(User $committer)
  {
    $this->committer = $committer;
  }

  public function getCommitter()
  {
    return $this->committer;
  }

  public function setCommitTime(\DateTime $commit_time)
  {
    $this->commit_time = $commit_time;
  }

  public function getCommitTime()
  {
    return $this->commit_time;
  }
}