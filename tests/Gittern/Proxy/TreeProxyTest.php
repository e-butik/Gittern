<?php

namespace Gittern\Proxy;

use Mockery as M;

require_once('DecoratorTest.php');

/**
 * @covers Gittern\Proxy\TreeProxy
 * @author Magnus Nordlander
 */
class TreeProxyTest extends DecoratorTest
{
  public function setUp()
  {
    $this->repo_mock = M::mock('Gittern\Repository');
    $this->sha = 'deadbeef';
    $this->tree_proxy = new TreeProxy($this->repo_mock, $this->sha);
  }

  public function testIsGaplessDecorator()
  {
    $this->assertClassIsGaplessDecorator('Gittern\Entity\GitObject\Tree', 'Gittern\Proxy\TreeProxy');
  }

  public function testCanGetShaWithoutLoading()
  {
    $this->assertEquals('deadbeef', $this->tree_proxy->getSha());
  }

  public function getProxiedMethods()
  {
    return array(
      array('setSha'),
      array('addNode'),
      array('getNodes'),
      array('getNodeNamed'),
      array('hasNodeNamed'),
      array('getIterator'),
    );
  }

  /**
   * @dataProvider getProxiedMethods
   */
  public function testMethodIsProperlyDecorated($method_name)
  {
    $tree_mock = M::mock('Gittern\Entity\GitObject\Tree');
    $this->repo_mock->shouldReceive('getObject')->with($this->sha)->andReturn($tree_mock);

    $return_value = uniqid();

    $params = $this->setupExpectationsOnMockAndGetParams($tree_mock, $method_name, $return_value);

    $this->assertEquals($return_value, call_user_func_array(array($this->tree_proxy, $method_name), $params));
  }
}
