<?php

namespace Gittern\Proxy;

use Mockery as M;

require_once('DecoratorTest.php');

/**
 * @covers Gittern\Proxy\CommitProxy
 */
class CommitProxyTest extends DecoratorTest
{
  public function setUp()
  {
    $this->repo_mock = M::mock('Gittern\Repository');
    $this->sha = 'deadbeef';
    $this->commit_proxy = new CommitProxy($this->repo_mock, $this->sha);
  }

  public function testIsGaplessDecorator()
  {
    $this->assertClassIsGaplessDecorator('Gittern\Entity\GitObject\Commit', 'Gittern\Proxy\CommitProxy');
  }

  public function testCanGetShaWithoutLoading()
  {
    $this->assertEquals('deadbeef', $this->commit_proxy->getSha());
  }

  public function getProxiedMethods()
  {
    return array(
      array('setSha'),
      array('setMessage'),
      array('getMessage'),
      array('setTree'),
      array('getTree'),
      array('addParent'),
      array('getParents'),
      array('setAuthor'),
      array('getAuthor'),
      array('setAuthorTime'),
      array('getAuthorTime'),
      array('setCommitter'),
      array('getCommitter'),
      array('setCommitTime'),
      array('getCommitTime'),
    );
  }

  /**
   * @dataProvider getProxiedMethods
   */
  public function testMethodIsProperlyDecorated($method_name)
  {
    $commit_mock = M::mock('Gittern\Entity\GitObject\Commit');
    $this->repo_mock->shouldReceive('getObject')->with($this->sha)->andReturn($commit_mock);

    $return_value = uniqid();

    $params = $this->setupExpectationsOnMockAndGetParams($commit_mock, $method_name, $return_value);

    $this->assertEquals($return_value, call_user_func_array(array($this->commit_proxy, $method_name), $params));
  }
}
