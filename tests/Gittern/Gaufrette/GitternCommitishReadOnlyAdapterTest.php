<?php

namespace Gittern\Gaufrette;

use Mockery as M;

/**
* @covers Gittern\Gaufrette\GitternCommitishReadOnlyAdapter
* @author Magnus Nordlander
*/
class GitternCommitishReadOnlyAdapterTest extends \PHPUnit_Framework_TestCase
{
  public function setUp()
  {
    $this->repo_mock = M::mock('Gittern\Repository');
    $this->tree_mock = M::mock('Gittern\Entity\GitObject\Tree');
    $this->commit_mock = M::mock('Gittern\Entity\GitObject\Commit', array('getTree' => $this->tree_mock));
    $this->repo_mock->shouldReceive('getObject')->with('foo')->andReturn($this->commit_mock)->atLeast()->once();

    $this->adapter = new GitternCommitishReadOnlyAdapter($this->repo_mock, 'foo');
  }

  public function tearDown()
  {
    M::close();
  }

  public function testCanConstructWithCommitRef()
  {
    // Tested in setUp
  }

  /**
   * @expectedException RuntimeException
   * @expectedExceptionMessage Could not resolve commitish to a commit.
   */
  public function testCantConstructWithOtherRef()
  {
    $repo_mock = M::mock('Gittern\Repository');
    $mock = M::mock();
    $repo_mock->shouldReceive('getObject')->with('foo')->andReturn($mock)->atLeast()->once();

    new GitternCommitishReadOnlyAdapter($repo_mock, 'foo');
  }

  public function testCanGetKeys()
  {
    $iter = new \RecursiveArrayIterator(array('foo' => array('foo/bar' => 1, 'foo/baz' => 2), 'quux' => 3));

    $rp = new \ReflectionProperty('Gittern\Gaufrette\GitternCommitishReadOnlyAdapter', 'tree');
    $rp->setAccessible(true);
    $rp->setValue($this->adapter, $iter);

    $this->assertEquals(array('foo/bar', 'foo/baz', 'quux'), $this->adapter->keys());
  }

  public function testCanCheckIfKeyExists()
  {
    $foo_tree_mock = M::mock('Gittern\Entity\GitObject\Tree');
    $foo_node_mock = M::mock(array('getRelatedObject' => $foo_tree_mock));

    $root_tree_mock = M::mock('Gittern\Entity\GitObject\Tree');
    $root_tree_mock->shouldReceive('getNodeNamed')->with('foo')->andReturn($foo_node_mock);

    $foo_tree_mock->shouldReceive('getNodeNamed')->with('bar')->andReturn(M::mock(array('getRelatedObject' => M::mock('Gittern\Entity\GitObject\Blob'))));
    $root_tree_mock->shouldReceive('getNodeNamed')->with('quux')->andReturn(M::mock(array('getRelatedObject' => M::mock('Gittern\Entity\GitObject\Blob'))));

    $rp = new \ReflectionProperty('Gittern\Gaufrette\GitternCommitishReadOnlyAdapter', 'tree');
    $rp->setAccessible(true);
    $rp->setValue($this->adapter, $root_tree_mock);

    $this->assertTrue($this->adapter->exists('foo/bar'));
    $this->assertTrue($this->adapter->exists('quux'));
    $this->assertFalse($this->adapter->exists('foo'));
  }

  public function testCanReadFile()
  {
    $foo_mock = M::mock('Gittern\Entity\GitObject\Tree');
    $this->tree_mock->shouldReceive('getNodeNamed')->with('foo')->andReturn($this->tree_mock);
    $this->tree_mock->shouldReceive('getRelatedObject')->andReturn($foo_mock);

    $blob_mock = M::mock('Gittern\Entity\GitObject\Blob');
    $foo_mock->shouldReceive('getNodeNamed')->with('bar')->andReturn($foo_mock);
    $foo_mock->shouldReceive('getRelatedObject')->andReturn($blob_mock);

    $blob_mock->shouldReceive('getContents')->andReturn('Foobar');

    $this->assertEquals('Foobar', $this->adapter->read('foo/bar'));
  }

  /**
   * @expectedException RuntimeException
   * @expectedExceptionMessage Could not read the 'foo/bar' file.
   */
  public function testCantReadNonExistingFile()
  {
    $this->tree_mock->shouldReceive('getNodeNamed')->with('foo')->andReturn(null);

    $this->assertEquals('Foobar', $this->adapter->read('foo/bar'));
  }

  public function testCanChecksumFile()
  {
    $foo_mock = M::mock('Gittern\Entity\GitObject\Blob');
    $this->tree_mock->shouldReceive('getNodeNamed')->with('foo')->andReturn($this->tree_mock);
    $this->tree_mock->shouldReceive('getRelatedObject')->andReturn($foo_mock);

    $foo_mock->shouldReceive('getSha')->andReturn('f00bar');

    $this->assertEquals('f00bar', $this->adapter->checksum('foo/bar'));
  }

  public function testMtimeReturnsCommitTime()
  {
    $expected_time = new \DateTime();
    $this->commit_mock->shouldReceive('getCommitTime')->andReturn($expected_time);
    $this->assertEquals($expected_time->format('U'), $this->adapter->mtime('foo'));
  }

  /**
   * @expectedException RuntimeException
   * @expectedExceptionMessage This adapter is read-only
   */
  public function testCantWrite()
  {
    $this->adapter->write('foo', 'bar');
  }

  /**
   * @expectedException RuntimeException
   * @expectedExceptionMessage This adapter is read-only
   */
  public function testCantDelete()
  {
    $this->adapter->delete('foo');
  }

  /**
   * @expectedException RuntimeException
   * @expectedExceptionMessage This adapter is read-only
   */
  public function testCantRename()
  {
    $this->adapter->rename('foo', 'bar');
  }

  public function testDoesntSupportMetadata()
  {
    $this->assertFalse($this->adapter->supportsMetadata());
  }

  public function testCanListEmptyDirectory()
  {
    $test_dir = '';
    $dirname = 'child_dir';
    $filename = 'test_file.xml';
    $foo_tree_mock = M::mock('Gittern\Entity\GitObject\Node\TreeNode');
    $foo_tree_mock->shouldReceive('getName')->atLeast()->once()->andReturn($dirname);
    $foo_blob_mock = M::mock('Gittern\Entity\GitObject\Node\BlobNode');
    $foo_blob_mock->shouldReceive('getName')->atLeast()->once()->andReturn($filename);
    $this->tree_mock->shouldReceive('getNodeNamed')->with('')->andReturn($this->tree_mock);
    $this->tree_mock->shouldReceive('getRelatedObject')->andReturn($this->tree_mock);
    $this->tree_mock->shouldReceive('getNodes')->atLeast()->once()->andReturn(array($foo_tree_mock, $foo_blob_mock));

    $list = $this->adapter->listDirectory($test_dir);
    $this->assertEquals(array($filename), $list['keys']);
    $this->assertEquals(array($dirname), $list['dirs']);
  }

  public function testCanListGivenDirectory()
  {
      $test_dir = 'translations';
      $filename = 'test_file.xml';

      $tree_node_mock = M::mock('Gittern\Entity\GitObject\Node\TreeNode');
      $tree_mock = M::mock('Gittern\Entity\GitObject\Tree');
      $blob_mock = M::mock('Gittern\Entity\GitObject\Node\BlobNode');
      $blob_mock->shouldReceive('getName')->atLeast()->once()->andReturn($filename);
      $this->tree_mock->shouldReceive('getNodeNamed')->with($test_dir)->andReturn($tree_node_mock);
      $tree_node_mock->shouldReceive('getRelatedObject')->andReturn($tree_mock);
      $tree_mock->shouldReceive('getNodes')->atLeast()->once()->andReturn(array($blob_mock));

      $list = $this->adapter->listDirectory($test_dir);
      $this->assertEquals(array($filename), $list['key']);
      $this->assertEmpty($list['dirs']);
  }

}
