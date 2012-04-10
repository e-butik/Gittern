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
  public function setMessage($message)
  {
    $this->message = $message;
  }

  /**
   * @author Magnus Nordlander
   **/
  public function getMessage()
  {
    return $this->message;
  }

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

  /**
   * @author Magnus Nordlander
   **/
  public function addParent(Commit $parent)
  {
    $this->parents[] = $parent;
  }

  /**
   * @author Magnus Nordlander
   **/
  public function getParents()
  {
    return $this->parents;
  }

  /**
   * @author Magnus Nordlander
   **/
  public function setAuthor(User $author)
  {
    $this->author = $author;
  }

  /**
   * @author Magnus Nordlander
   **/
  public function getAuthor()
  {
    return $this->author;
  }

  /**
   * @author Magnus Nordlander
   **/
  public function setAuthorTime(\DateTime $author_time)
  {
    $this->author_time = $author_time;
  }

  /**
   * @author Magnus Nordlander
   **/
  public function getAuthorTime()
  {
    return $this->author_time;
  }

  /**
   * @author Magnus Nordlander
   **/
  public function setCommitter(User $committer)
  {
    $this->committer = $committer;
  }

  /**
   * @author Magnus Nordlander
   **/
  public function getCommitter()
  {
    return $this->committer;
  }

  /**
   * @author Magnus Nordlander
   **/
  public function setCommitTime(\DateTime $commit_time)
  {
    $this->commit_time = $commit_time;
  }

  /**
   * @author Magnus Nordlander
   **/
  public function getCommitTime()
  {
    return $this->commit_time;
  }
}