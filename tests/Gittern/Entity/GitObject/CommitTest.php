<?php

namespace Gittern\Entity\GitObject;

use Mockery as M;

/**
* @covers Gittern\Entity\GitObject\Commit
* @author Magnus Nordlander
*/
class CommitTest extends \PHPUnit_Framework_TestCase
{
  public function setUp()
  {
    $this->commit = new Commit();
  }

  public function testCanBeConstructed()
  {
  }

  public function testCanSetAndGetSha()
  {
    $this->commit->setSha('deadbeef');
    $this->assertEquals('deadbeef', $this->commit->getSha());
  }

  public function testCanSetAndGetMessage()
  {
    $this->commit->setMessage('Foobar');
    $this->assertEquals('Foobar', $this->commit->getMessage());
  }

  public function testCanSetAndGetTree()
  {
    $tree_mock = M::mock('Gittern\Entity\GitObject\Tree');
    $this->commit->setTree($tree_mock);
    $this->assertEquals($tree_mock, $this->commit->getTree());
  }

  public function testCanAddAndGetParents()
  {
    $commit_mock = M::mock('Gittern\Entity\GitObject\Commit');
    $this->commit->addParent($commit_mock);
    $this->assertEquals(array($commit_mock), $this->commit->getParents());
  }

  public function testCanGetAndSetAuthor()
  {
    $user_mock = M::mock('Gittern\Entity\GitObject\User');
    $this->commit->setAuthor($user_mock);
    $this->assertEquals($user_mock, $this->commit->getAuthor());
  }

  public function testCanGetAndSetCommitter()
  {
    $user_mock = M::mock('Gittern\Entity\GitObject\User');
    $this->commit->setCommitter($user_mock);
    $this->assertEquals($user_mock, $this->commit->getCommitter());
  }

  public function testCanGetAndSetAuthorTime()
  {
    $time = new \DateTime();
    $this->commit->setAuthorTime($time);
    $this->assertEquals($time, $this->commit->getAuthorTime());
  }

  public function testCanGetAndSetCommitTime()
  {
    $time = new \DateTime();
    $this->commit->setCommitTime($time);
    $this->assertEquals($time, $this->commit->getCommitTime());
  }
}