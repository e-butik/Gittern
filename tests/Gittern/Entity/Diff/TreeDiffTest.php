<?php

namespace Gittern\Entity\Diff;

use Mockery as M;

/**
* @covers Gittern\Entity\Diff\TreeDiff
* @author Magnus Nordlander
*/
class TreeDiffTest extends \PHPUnit_Framework_TestCase
{
  public function setUp()
  {
    $this->base_tree_mock = M::mock('Gittern\Entity\GitObject\Tree');
    $this->comp_tree_mock = M::mock('Gittern\Entity\GitObject\Tree');

    $this->diff = new TreeDiff($this->base_tree_mock, $this->comp_tree_mock);
  }

  public function tearDown()
  {
    M::close();
  }

  public function testCanGetAddedNodes()
  {
    $node1 = M::mock(array('getName' => 'foobar'));
    $node2 = M::mock(array('getName' => 'foobaz'));

    $this->base_tree_mock->shouldReceive('hasNodeNamed')->with('foobar')->andReturn(true);
    $this->base_tree_mock->shouldReceive('hasNodeNamed')->with('foobaz')->andReturn(false);
    $this->comp_tree_mock->shouldReceive('getNodes')->andReturn(array($node1, $node2));

    $this->assertEquals(array($node2), $this->diff->getAddedNodes());
  }

  public function testCanGetRemovedNodes()
  {
    $node1 = M::mock(array('getName' => 'foobar'));
    $node2 = M::mock(array('getName' => 'foobaz'));

    $this->base_tree_mock->shouldReceive('getNodes')->andReturn(array($node1, $node2));
    $this->comp_tree_mock->shouldReceive('hasNodeNamed')->with('foobar')->andReturn(true);
    $this->comp_tree_mock->shouldReceive('hasNodeNamed')->with('foobaz')->andReturn(false);

    $this->assertEquals(array($node2), $this->diff->getRemovedNodes());
  }

  public function testCanGetModifiedNodes()
  {
    $node1 = M::mock(array('getName' => 'foobar'));
    $node2a = M::mock(array('getName' => 'foobaz'));
    $node2b = M::mock(array('getName' => 'foobaz'));

    $node1->shouldReceive('equals')->with($node1)->andReturn(true);
    $node2b->shouldReceive('equals')->with($node2a)->andReturn(false);

    $this->comp_tree_mock->shouldReceive('getNodes')->andReturn(array($node1, $node2a));
    $this->base_tree_mock->shouldReceive('hasNodeNamed')->with('foobar')->andReturn(true);
    $this->base_tree_mock->shouldReceive('hasNodeNamed')->with('foobaz')->andReturn(true);

    $this->base_tree_mock->shouldReceive('getNodeNamed')->with('foobar')->andReturn($node1);
    $this->base_tree_mock->shouldReceive('getNodeNamed')->with('foobaz')->andReturn($node2b);

    $this->assertEquals(array($node2a), $this->diff->getModifiedNodes());
  }

  public function testCanGetAddedLeafNodesRecursively()
  {
    /*
    Node trees

    Base:
      foobaz/
        bar

    Comp:
      foobar
      foobaz/
        bar
        foo
      quux/
        bork
    */

    $comp_subtree_mock = M::mock('Gittern\Entity\GitObject\Tree');
    $comp_tree_node = M::mock('Gittern\Entity\GitObject\Node\TreeNode', array('getName' => 'foobaz', 'getTree' => $comp_subtree_mock));

    $base_subtree_mock = M::mock('Gittern\Entity\GitObject\Tree');
    $base_tree_node = M::mock('Gittern\Entity\GitObject\Node\TreeNode', array('getName' => 'foobaz', 'getTree' => $base_subtree_mock));

    $base_new_subtree_mock = M::mock('Gittern\Entity\GitObject\Tree');
    $base_new_tree_node = M::mock('Gittern\Entity\GitObject\Node\TreeNode', array('getName' => 'quux', 'getTree' => $base_new_subtree_mock));

    $node1 = M::mock(array('getName' => 'foobar'));
    $node2 = M::mock(array('getName' => 'foo'));
    $node3 = M::mock(array('getName' => 'bar'));
    $node4 = M::mock(array('getName' => 'bork'));

    $this->base_tree_mock->shouldReceive('hasNodeNamed')->with('foobar')->andReturn(false)->once()->atLeast();
    $this->base_tree_mock->shouldReceive('hasNodeNamed')->with('foobaz')->andReturn(true)->once()->atLeast();
    $this->base_tree_mock->shouldReceive('hasNodeNamed')->with('quux')->andReturn(false)->once()->atLeast();
    $this->base_tree_mock->shouldReceive('getNodeNamed')->with('foobaz')->andReturn($comp_tree_node);
    $this->comp_tree_mock->shouldReceive('getNodes')->andReturn(array($node1, $base_tree_node, $base_new_tree_node));

    $comp_subtree_mock->shouldReceive('hasNodeNamed')->with('foo')->andReturn(false)->once()->atLeast();
    $comp_subtree_mock->shouldReceive('hasNodeNamed')->with('bar')->andReturn(true)->once()->atLeast();
    $base_subtree_mock->shouldReceive('getNodes')->andReturn(array($node2, $node3));

    $base_new_subtree_mock->shouldReceive('getNodes')->andReturn(array($node4));

    $this->assertEquals(array('foobar' => $node1, 'foobaz/foo' => $node2, 'quux/bork' => $node4), $this->diff->getAddedLeafNodesRecursive());
  }

  public function testCanGetRemovedLeafNodesRecursively()
  {
    /*
    Node trees

    Base:
      foobar
      foobaz/
        bar
        foo
      quux/
        bork

    Comp:
      foobaz/
        bar
    */


    $comp_subtree_mock = M::mock('Gittern\Entity\GitObject\Tree');
    $comp_tree_node = M::mock('Gittern\Entity\GitObject\Node\TreeNode', array('getName' => 'foobaz', 'getTree' => $comp_subtree_mock));

    $base_subtree_mock = M::mock('Gittern\Entity\GitObject\Tree');
    $base_tree_node = M::mock('Gittern\Entity\GitObject\Node\TreeNode', array('getName' => 'foobaz', 'getTree' => $base_subtree_mock));

    $base_new_subtree_mock = M::mock('Gittern\Entity\GitObject\Tree');
    $base_new_tree_node = M::mock('Gittern\Entity\GitObject\Node\TreeNode', array('getName' => 'quux', 'getTree' => $base_new_subtree_mock));

    $node1 = M::mock(array('getName' => 'foobar'));
    $node2 = M::mock(array('getName' => 'foo'));
    $node3 = M::mock(array('getName' => 'bar'));
    $node4 = M::mock(array('getName' => 'bork'));

    $this->comp_tree_mock->shouldReceive('hasNodeNamed')->with('foobar')->andReturn(false)->once()->atLeast();
    $this->comp_tree_mock->shouldReceive('hasNodeNamed')->with('foobaz')->andReturn(true)->once()->atLeast();
    $this->comp_tree_mock->shouldReceive('hasNodeNamed')->with('quux')->andReturn(false)->once()->atLeast();
    $this->comp_tree_mock->shouldReceive('getNodeNamed')->with('foobaz')->andReturn($comp_tree_node);
    $this->base_tree_mock->shouldReceive('getNodes')->andReturn(array($node1, $base_tree_node, $base_new_tree_node));

    $comp_subtree_mock->shouldReceive('hasNodeNamed')->with('foo')->andReturn(false)->once()->atLeast();
    $comp_subtree_mock->shouldReceive('hasNodeNamed')->with('bar')->andReturn(true)->once()->atLeast();
    $base_subtree_mock->shouldReceive('getNodes')->andReturn(array($node2, $node3));

    $base_new_subtree_mock->shouldReceive('getNodes')->andReturn(array($node4));

    $this->assertEquals(array('foobar' => $node1, 'foobaz/foo' => $node2, 'quux/bork' => $node4), $this->diff->getRemovedLeafNodesRecursive());
  }

  public function testCanGetModifiedNodesRecursively()
  {
    $comp_subtree_mock = M::mock('Gittern\Entity\GitObject\Tree');
    $comp_tree_node = M::mock('Gittern\Entity\GitObject\Node\TreeNode', array('getName' => 'foobaz', 'getTree' => $comp_subtree_mock));

    $base_subtree_mock = M::mock('Gittern\Entity\GitObject\Tree');
    $base_tree_node = M::mock('Gittern\Entity\GitObject\Node\TreeNode', array('getName' => 'foobaz', 'getTree' => $base_subtree_mock));

    $node1_a = M::mock(array('getName' => 'foobar'));
    $node1_b = M::mock(array('getName' => 'foobar'));
    $node2_a = M::mock(array('getName' => 'foo'));
    $node2_b = M::mock(array('getName' => 'foo'));
    $node3 = M::mock(array('getName' => 'bork'));

    $node1_a->shouldReceive('equals')->with($node1_b)->andReturn(false)->once()->atLeast();
    $node2_a->shouldReceive('equals')->with($node2_b)->andReturn(false)->once()->atLeast();
    $base_tree_node->shouldReceive('equals')->with($comp_tree_node)->andReturn(false)->once()->atLeast();
    $node3->shouldReceive('equals')->with($node3)->andReturn(true)->once()->atLeast();

    $this->base_tree_mock->shouldReceive('hasNodeNamed')->with('bork')->andReturn(true)->once()->atLeast();
    $this->base_tree_mock->shouldReceive('getNodeNamed')->with('bork')->andReturn($node3)->once()->atLeast();
    $this->base_tree_mock->shouldReceive('hasNodeNamed')->with('foobar')->andReturn(true)->once()->atLeast();
    $this->base_tree_mock->shouldReceive('getNodeNamed')->with('foobar')->andReturn($node1_a)->once()->atLeast();
    $this->base_tree_mock->shouldReceive('hasNodeNamed')->with('foobaz')->andReturn(true)->once()->atLeast();
    $this->base_tree_mock->shouldReceive('getNodeNamed')->with('foobaz')->andReturn($base_tree_node);
    $this->comp_tree_mock->shouldReceive('getNodes')->andReturn(array($node3, $node1_b, $comp_tree_node));

    $base_subtree_mock->shouldReceive('hasNodeNamed')->with('foo')->andReturn(true)->once()->atLeast();
    $base_subtree_mock->shouldReceive('getNodeNamed')->with('foo')->andReturn($node2_a)->once()->atLeast();
    $comp_subtree_mock->shouldReceive('getNodes')->andReturn(array($node2_b));

    $this->assertEquals(array('foobar' => $node1_b, 'foobaz/foo' => $node2_b), $this->diff->getModifiedLeafNodesRecursive());
  }
}