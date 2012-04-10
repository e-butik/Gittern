<?php

namespace Gittern\Entity\GitObject\Node;

use Mockery as M;

/**
 * @covers Gittern\Entity\GitObject\Node\BlobNode
 */
class BlobNodeTest extends \PHPUnit_Framework_TestCase
{
  public function setUp()
  {
    $this->node_mock = new BlobNode;
  }

  public function testCanGetAndSetBlob()
  {
    $blob = M::mock('Gittern\Entity\GitObject\Blob');
    $this->node_mock->setBlob($blob);
    $this->assertEquals($blob, $this->node_mock->getBlob());
  }

  public function testGetRelatedObject()
  {
    $blob = M::mock('Gittern\Entity\GitObject\Blob');
    $this->node_mock->setBlob($blob);
    $this->assertEquals($blob, $this->node_mock->getRelatedObject());
  }
}
