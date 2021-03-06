<?php

namespace Gittern\Entity\GitObject;

use Mockery as M;

/**
* @covers Gittern\Entity\GitObject\Tree
* @author Magnus Nordlander
*/
class TreeTest extends \PHPUnit_Framework_TestCase
{
  public function setUp()
  {
    $this->tree = new Tree();
    $this->node_mock = M::mock('Gittern\Entity\GitObject\Node\BaseNode', array('getName' => 'foobar'));
    $this->tree->addNode($this->node_mock);
  }

  public function testCanBeConstructed()
  {
    // Done in setup
  }

  public function testCanSetAndGetSha()
  {
    $this->tree->setSha('deadbeef');
    $this->assertEquals('deadbeef', $this->tree->getSha());
  }

  public function testCanAddNode()
  {
    // Done in setup
  }

  public function testCanGetAllNodes()
  {
    $this->assertEquals(array($this->node_mock), $this->tree->getNodes());
  }

  public function testCanCheckIfHasNodeByName()
  {
    $this->assertTrue($this->tree->hasNodeNamed('foobar'));
    $this->assertFalse($this->tree->hasNodeNamed('foobaz'));
  }

  public function testCanGetNodeByName()
  {
    $this->assertEquals($this->node_mock, $this->tree->getNodeNamed('foobar'));
  }

  public function testCanCreateRecursiveIterator()
  {
    $iterator = $this->tree->getIterator();
    $this->assertInstanceOf('RecursiveIterator', $iterator);
    $this->assertInstanceOf('ArrayIterator', $iterator);
    $this->assertEquals($this->node_mock, $iterator->current());
  }

  public function testSortsNodes()
  {
    $new_node = M::mock('Gittern\Entity\GitObject\Node\BaseNode', array('getName' => 'aardvark'));
    $this->tree->addNode($new_node);
    $this->assertEquals(array($new_node, $this->node_mock), $this->tree->getNodes());
    $first_node = M::mock('Gittern\Entity\GitObject\Node\BaseNode', array('getName' => 'Zebra'));
    $this->tree->addNode($first_node);
    $this->assertEquals(array($first_node, $new_node, $this->node_mock), $this->tree->getNodes());

  }

  public function testSortsNodesWithStrcmp()
  {
    $new_node = M::mock('Gittern\Entity\GitObject\Node\BaseNode', array('getName' => '1337'));
    $this->tree->addNode($new_node);
    $this->assertEquals(array($new_node, $this->node_mock), $this->tree->getNodes());
  }
}