<?php

namespace Gittern\Iterator;

use Mockery as M;

/**
 * @covers Gittern\Iterator\RecursiveTreeIterator
 * @author Magnus Nordlander
 */
class RecursiveTreeIteratorTest extends \PHPUnit_Framework_TestCase
{
  public function tearDown()
  {
    M::close();
  }

  public function testCanBeCreated()
  {
    $iterator = new RecursiveTreeIterator();
    $this->assertInstanceOf('ArrayIterator', $iterator);
    $this->assertInstanceOf('RecursiveIterator', $iterator);
  }

  public function testCanSetKeyBase()
  {
    $iterator = new RecursiveTreeIterator(array('foo' => 'bar'));
    $this->assertEquals('foo', $iterator->key());
    $iterator->setKeyBase('baz');
    $this->assertEquals('baz/foo', $iterator->key());
  }

  public function testCanDetermineIfCurrentHasChildren()
  {
    $iterator = new RecursiveTreeIterator(array('foo' => M::mock('Gittern\Entity\GitObject\Node\TreeNode'), 'bar' => 'baz'));
    $this->assertTrue($iterator->hasChildren());
    $iterator->next();
    $this->assertFalse($iterator->hasChildren());
  }

  public function testCanGetCurrentChildrenWhenTheyExist()
  {
    $iterator_mock = M::mock('Gittern\Iterator\RecursiveTreeIterator');
    $treenode_mock = M::mock('Gittern\Entity\GitObject\Node\TreeNode');

    $treenode_mock->shouldReceive('getTree')->andReturn($treenode_mock);
    $treenode_mock->shouldReceive('getIterator')->andReturn($iterator_mock);

    $iterator_mock->shouldReceive('setKeyBase')->with('foo');

    $iterator = new RecursiveTreeIterator(array('foo' => $treenode_mock));

    $this->assertEquals($iterator_mock, $iterator->getChildren());
  }

  public function testCantGetCurrentChildrenWhenTheyDontExist()
  {
    $iterator = new RecursiveTreeIterator(array('foo' => 'bar'));

    $this->assertNull($iterator->getChildren());
  }
}
