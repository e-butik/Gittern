<?php

namespace Gittern\Proxy;

use Mockery as M;

require_once('DecoratorTest.php');

/**
 * @covers Gittern\Proxy\BlobProxy
 */
class BlobProxyTest extends DecoratorTest
{
  public function setUp()
  {
    $this->repo_mock = M::mock('Gittern\Repository');
    $this->sha = 'deadbeef';
    $this->blob_proxy = new BlobProxy($this->repo_mock, $this->sha);
  }

  public function testIsGaplessDecorator()
  {
    $this->assertClassIsGaplessDecorator('Gittern\GitObject\Blob', 'Gittern\Proxy\BlobProxy');
  }

  public function testCanGetShaWithoutLoading()
  {
    $this->assertEquals('deadbeef', $this->blob_proxy->getSha());
  }

  public function getProxiedMethods()
  {
    return array(
      array('setSha'),
      array('setContents'),
      array('getContents'),
    );
  }

  /**
   * @dataProvider getProxiedMethods
   */
  public function testMethodIsProperlyDecorated($method_name)
  {
    $blob_mock = M::mock('Gittern\GitObject\Blob');
    $this->repo_mock->shouldReceive('getObject')->with($this->sha)->andReturn($blob_mock);

    $return_value = uniqid();

    $params = $this->setupExpectationsOnMockAndGetParams($blob_mock, $method_name, $return_value);

    $this->assertEquals($return_value, call_user_func_array(array($this->blob_proxy, $method_name), $params));
  }
}
