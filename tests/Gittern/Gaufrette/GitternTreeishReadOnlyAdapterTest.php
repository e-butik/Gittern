<?php

namespace Gittern\Gaufrette;

use Mockery as M;

/**
* @covers Gittern\Gaufrette\GitternTreeishReadOnlyAdapter
* @author Magnus Nordlander
*/
class GitternTreeishReadOnlyAdapterTest extends \PHPUnit_Framework_TestCase
{
  public function setUp()
  {
    $this->repo_mock = M::mock('Gittern\Repository');
    $this->tree_mock = M::mock('Gittern\Entity\GitObject\Tree');
    $this->repo_mock->shouldReceive('getObject')->with('foo')->andReturn($this->tree_mock)->atLeast()->once();

    $this->adapter = new GitternTreeishReadOnlyAdapter($this->repo_mock, 'foo');
  }

  public function tearDown()
  {
    M::close();
  }

  public function testCanConstructWithTreeRef()
  {
    // Tested in setUp
  }

  public function testCanConstructWithCommitRef()
  {
    $repo_mock = M::mock('Gittern\Repository');
    $commit_mock = M::mock('Gittern\Entity\GitObject\Commit', array('getTree' => $this->tree_mock));
    $repo_mock->shouldReceive('getObject')->with('foo')->andReturn($commit_mock)->atLeast()->once();

    new GitternTreeishReadOnlyAdapter($repo_mock, 'foo');
  }

  /**
   * @expectedException RuntimeException
   * @expectedExceptionMessage Could not resolve treeish to a tree.
   */
  public function testCantConstructWithOtherRef()
  {
    $repo_mock = M::mock('Gittern\Repository');
    $mock = M::mock();
    $repo_mock->shouldReceive('getObject')->with('foo')->andReturn($mock)->atLeast()->once();

    new GitternTreeishReadOnlyAdapter($repo_mock, 'foo');
  }

  public function testCanGetKeys()
  {
    $iter = new \RecursiveArrayIterator(array('foo' => array('foo/bar' => 1, 'foo/baz' => 2), 'quux' => 3));

    $rp = new \ReflectionProperty('Gittern\Gaufrette\GitternTreeishReadOnlyAdapter', 'tree');
    $rp->setAccessible(true);
    $rp->setValue($this->adapter, $iter);

    $this->assertEquals(array('foo/bar', 'foo/baz', 'quux'), $this->adapter->keys());
  }

  public function testCanCheckIfKeyExists()
  {
    $iter = new \RecursiveArrayIterator(array('foo' => array('foo/bar' => 1, 'foo/baz' => 2), 'quux' => 3));

    $rp = new \ReflectionProperty('Gittern\Gaufrette\GitternTreeishReadOnlyAdapter', 'tree');
    $rp->setAccessible(true);
    $rp->setValue($this->adapter, $iter);

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

    $foo_mock->shouldReceive('getContents')->andReturn('Foobar');

    $this->assertEquals(md5('Foobar'), $this->adapter->checksum('foo/bar'));
  }

  public function testMtimeReturnsNow()
  {
    $expected_time = time();
    $actual_time = $this->adapter->mtime('foo');
    $this->assertGreaterThanOrEqual($expected_time, $actual_time);
    $this->assertLessThan($expected_time+5, $actual_time);
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
}