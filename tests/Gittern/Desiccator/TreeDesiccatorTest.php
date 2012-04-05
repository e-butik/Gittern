<?php

namespace Gittern\Desiccator;

use Mockery as M;

/**
 * @covers Gittern\Desiccator\TreeDesiccator
 */
class TreeDesiccatorTest extends \PHPUnit_Framework_TestCase
{
  public function testCanDesiccateTreeWithAllRelationsPersisted()
  {
    $desiccator = new TreeDesiccator();

    $tree = M::mock('Gittern\GitObject\Tree');
    $node1_mock = M::mock('Gittern\GitObject\Node\TreeNode', array('getOctalModeString' => '040000', 'getName' => 'abacus'));
    $node2_mock = M::mock('Gittern\GitObject\Node\BlobNode', array('getOctalModeString' => '100644', 'getName' => 'babacus'));

    $node1_mock->shouldReceive('getRelatedObject')->andReturn($node1_mock);
    $node1_mock->shouldReceive('getSha')->andReturn('935122a4458399ef488c872b42c6e9985f1d1e3b');

    $node2_mock->shouldReceive('getRelatedObject')->andReturn($node2_mock);
    $node2_mock->shouldReceive('getSha')->andReturn('24fb5bad9c8f3b2694412ea309f207091f2309cf');

    $tree->shouldReceive('getNodes')->andReturn(array($node1_mock, $node2_mock));

    $expected = sprintf("040000 abacus\0%s100644 babacus\0%s", pack("H*", "935122a4458399ef488c872b42c6e9985f1d1e3b"), pack("H*", "24fb5bad9c8f3b2694412ea309f207091f2309cf"));

    $this->assertEquals($expected, $desiccator->desiccate($tree));
  }

  /**
   * @expectedException RuntimeException
   * @expectedExceptionMessage Object referred to by node named abacus is not persisted yet.
   */
  public function testCantDesiccateTreeWithoutAllRelationsPersisted()
  {
    $desiccator = new TreeDesiccator();

    $tree = M::mock('Gittern\GitObject\Tree');
    $node_mock = M::mock('Gittern\GitObject\Node\TreeNode', array('getOctalModeString' => '040000', 'getName' => 'abacus'));

    $node_mock->shouldReceive('getRelatedObject')->andReturn($node_mock);
    $node_mock->shouldReceive('getSha')->andReturn(null);

    $tree->shouldReceive('getNodes')->andReturn(array($node_mock));

    $desiccator->desiccate($tree);
  }
}
