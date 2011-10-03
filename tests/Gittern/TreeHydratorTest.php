<?php

namespace Gittern;

class TreeHydratorTest extends \PHPUnit_Framework_TestCase
{
  public function testTreeHydratorHappyPath()
  {
    $sha = "b4ac469697c1e0f5fbb5702befc59fd7a90970a0";
    $data = file_get_contents(__DIR__.'/treedata.bin');
    
    $hydrator = new Hydrator\TreeHydrator($this->getMock('Gittern\Repository', array(), array(), '', false));
    $tree = $hydrator->hydrate($sha, $data);

    $this->assertEquals($sha, $tree->getSha());

    $nodes = $tree->getNodes();
    
    $this->assertInstanceOf('Gittern\GitObject\Node\BlobNode', $nodes[0]);
    $this->assertEquals("a4d5057250e7389c77a3b2d49307197a38fbf8d7", $nodes[0]->getBlob()->getSha());
    $this->assertEquals(33188, $nodes[0]->getIntegerMode());
    $this->assertEquals('Klarna.php', $nodes[0]->getName());

    $this->assertInstanceOf('Gittern\GitObject\Node\TreeNode', $nodes[7]);
    $this->assertEquals("f284e49d6b8b7275162d947894f2f6370e7c8e82", $nodes[7]->getTree()->getSha());
  }
}
