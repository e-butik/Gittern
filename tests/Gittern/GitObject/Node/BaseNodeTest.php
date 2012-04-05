<?php

namespace Gittern\GitObject\Node;

use Mockery as M;

/**
 * @covers Gittern\GitObject\Node\BaseNode
 */
class BaseNodeTest extends \PHPUnit_Framework_TestCase
{
  public function setUp()
  {
    $this->node_mock = M::mock('Gittern\GitObject\Node\BaseNode[getRelatedObject]');
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
}
