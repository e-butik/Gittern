<?php

namespace Gittern\Entity\GitObject\Node;

use Mockery as M;

/**
 * @covers Gittern\Entity\GitObject\Node\BaseNode
 * @author Magnus Nordlander
 */
class BaseNodeTest extends \PHPUnit_Framework_TestCase
{
  public function setUp()
  {
    $this->node_mock = M::mock('Gittern\Entity\GitObject\Node\BaseNode[getRelatedObject]');
  }

  public function testCanGetAndSetMode()
  {
    $this->node_mock->setIntegerMode(0100644);
    $this->assertEquals(0100644, $this->node_mock->getIntegerMode());
    $this->assertEquals("100644", $this->node_mock->getOctalModeString());
  }

  public function testCanPadModeToSixChars()
  {
    $this->node_mock->setIntegerMode(040000);
    $this->assertEquals("040000", $this->node_mock->getOctalModeString());
  }

  public function testCanGetAndSetName()
  {
    $this->node_mock->setName('foobar');
    $this->assertEquals("foobar", $this->node_mock->getName());
  }

  public function testFailsEqualityTestForDifferentNameOrMode()
  {
    $comp_node = M::mock('Gittern\Entity\GitObject\Node\BaseNode[getRelatedObject]');

    $this->node_mock->setName('foobar');
    $this->node_mock->setIntegerMode(0100644);

    $comp_node->setName('foobaz');
    $comp_node->setIntegerMode(0100644);

    $this->assertFalse($this->node_mock->equals($comp_node));

    $this->node_mock->setIntegerMode(040000);

    $comp_node->setName('foobar');

    $this->assertFalse($this->node_mock->equals($comp_node));
  }

  public function testPassesEqualityTestWithSameAttrsAndRelatedObjects()
  {
    $comp_node = M::mock('Gittern\Entity\GitObject\Node\BaseNode[getRelatedObject]');
    $obj = M::mock();

    $this->node_mock->setName('foobar');
    $this->node_mock->setIntegerMode(0100644);
    $this->node_mock->shouldReceive('getRelatedObject')->andReturn($obj);

    $comp_node->setName('foobar');
    $comp_node->setIntegerMode(0100644);
    $comp_node->shouldReceive('getRelatedObject')->andReturn($obj);

    $this->assertTrue($this->node_mock->equals($comp_node));
  }

  public function testPassesEqualityTestWithSameAttrsAndRelatedObjectsWithSameSha()
  {
    $comp_node = M::mock('Gittern\Entity\GitObject\Node\BaseNode[getRelatedObject]');
    $obj1 = M::mock('Gittern\Entity\GitObject\Blob', array('getSha' => 'deadbeefcafe'));
    $obj2 = M::mock('Gittern\Proxy\BlobProxy', array('getSha' => 'deadbeefcafe'));

    $this->node_mock->setName('foobar');
    $this->node_mock->setIntegerMode(0100644);
    $this->node_mock->shouldReceive('getRelatedObject')->andReturn($obj1);

    $comp_node->setName('foobar');
    $comp_node->setIntegerMode(0100644);
    $comp_node->shouldReceive('getRelatedObject')->andReturn($obj2);

    $this->assertTrue($this->node_mock->equals($comp_node));
  }

  public function testFailsEqualityTestWithSameAttrsAndRelatedObjectsWithDifferentSha()
  {
    $comp_node = M::mock('Gittern\Entity\GitObject\Node\BaseNode[getRelatedObject]');
    $obj1 = M::mock('Gittern\Entity\GitObject\Blob', array('getSha' => 'deadbeefcafe'));
    $obj2 = M::mock('Gittern\Proxy\BlobProxy', array('getSha' => 'deadbeefcafebabe'));

    $this->node_mock->setName('foobar');
    $this->node_mock->setIntegerMode(0100644);
    $this->node_mock->shouldReceive('getRelatedObject')->andReturn($obj1);

    $comp_node->setName('foobar');
    $comp_node->setIntegerMode(0100644);
    $comp_node->shouldReceive('getRelatedObject')->andReturn($obj2);

    $this->assertFalse($this->node_mock->equals($comp_node));
  }

  public function testFailsEqualityTestWithSameAttrsAndRelatedObjectsWithoutSha()
  {
    $comp_node = M::mock('Gittern\Entity\GitObject\Node\BaseNode[getRelatedObject]');
    $obj1 = M::mock('Gittern\Entity\GitObject\Blob', array('getSha' => ''));
    $obj2 = M::mock('Gittern\Proxy\BlobProxy', array('getSha' => ''));

    $this->node_mock->setName('foobar');
    $this->node_mock->setIntegerMode(0100644);
    $this->node_mock->shouldReceive('getRelatedObject')->andReturn($obj1);

    $comp_node->setName('foobar');
    $comp_node->setIntegerMode(0100644);
    $comp_node->shouldReceive('getRelatedObject')->andReturn($obj2);

    $this->assertFalse($this->node_mock->equals($comp_node));
  }
}
