<?php

namespace Gittern\GitObject\Node;

use Mockery as M;

/**
 * @covers Gittern\GitObject\Node\TreeNode
 */
class TreeNodeTest extends \PHPUnit_Framework_TestCase
{
  public function setUp()
  {
    $this->node_mock = new TreeNode;
  }

  public function testCanGetAndSetTree()
  {
    $tree = M::mock('Gittern\GitObject\Tree');
    $this->node_mock->setTree($tree);
    $this->assertEquals($tree, $this->node_mock->getTree());
  }

  public function testGetRelatedObject()
  {
    $tree = M::mock('Gittern\GitObject\Tree');
    $this->node_mock->setTree($tree);
    $this->assertEquals($tree, $this->node_mock->getRelatedObject());
  }

  public function testDefaultModeIsSet()
  {
    $this->assertEquals(040000, $this->node_mock->getIntegerMode());
  }
}
