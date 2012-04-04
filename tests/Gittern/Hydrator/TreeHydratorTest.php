<?php

namespace Gittern\Hydrator;

use Mockery as M;

/**
 * @covers Gittern\Hydrator\TreeHydrator
 */
class TreeHydratorTest extends \PHPUnit_Framework_TestCase
{
  public function testTreeHydratorHappyPath()
  {
    $sha = "deadbeefcafebabefacebadc0ffeebadf00dcafe";
    $blob_line = sprintf("%s %s\0%s", "100644", 'testblob.md', pack("H*", "deadbeefcafebabefacebadc0ffeebadf00dface"));
    $tree_line = sprintf("%s %s\0%s", "040000", 'testtree', pack("H*", "deadbeefcafebabefacebadc0ffeebadf00dbeef"));

    $hydrator = new TreeHydrator(M::mock('Gittern\Repository'));

    $tree = $hydrator->hydrate($sha, $blob_line.$tree_line);

    $this->assertEquals($sha, $tree->getSha());
    $nodes = $tree->getNodes();
    $this->assertEquals('testblob.md', $nodes[0]->getName());
    $this->assertEquals('100644', $nodes[0]->getOctalModeString());
    $this->assertInstanceOf('Gittern\GitObject\Node\BlobNode', $nodes[0]);
    $this->assertEquals("deadbeefcafebabefacebadc0ffeebadf00dface", $nodes[0]->getRelatedObject()->getSha());

    $this->assertEquals('testtree', $nodes[1]->getName());
    $this->assertEquals('040000', $nodes[1]->getOctalModeString());
    $this->assertInstanceOf('Gittern\GitObject\Node\TreeNode', $nodes[1]);
    $this->assertEquals("deadbeefcafebabefacebadc0ffeebadf00dbeef", $nodes[1]->getRelatedObject()->getSha());
  }
}
