<?php

namespace Gittern\GitObject\Node;

use Mockery as M;

/**
 * @covers Gittern\GitObject\Node\BlobNode
 */
class BlobNodeTest extends \PHPUnit_Framework_TestCase
{
  public function setUp()
  {
    $this->node_mock = new BlobNode;
  }

  public function testCanGetAndSetBlob()
  {
    $blob = M::mock('Gittern\GitObject\Blob');
    $this->node_mock->setBlob($blob);
    $this->assertEquals($blob, $this->node_mock->getBlob());
  }

  public function testGetRelatedObject()
  {
    $blob = M::mock('Gittern\GitObject\Blob');
    $this->node_mock->setBlob($blob);
    $this->assertEquals($blob, $this->node_mock->getRelatedObject());
  }
}
