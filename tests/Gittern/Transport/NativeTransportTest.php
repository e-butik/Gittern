<?php

namespace Gittern\Transport;

use org\bovigo\vfs\vfsStream as VfsStream;
use org\bovigo\vfs\vfsStreamWrapper as VfsStreamWrapper;

use Mockery as M;

/**
 * @covers Gittern\Transport\NativeTransport
 * @author Magnus Nordlander
 */
class NativeTransportTest extends \PHPUnit_Framework_TestCase
{
  public function setUp()
  {
    VfsStream::setup('Testrepo');

    if (!class_exists('ZipArchive'))
    {
      $this->markTestSkipped('The ZipArchive class is not available.');
    }

    $zip = new \ZipArchive;
    if ($zip->open(__DIR__.'/../Fixtures/Testrepo.git.zip') === true) 
    {
      $zip->extractTo(VfsStream::url('Testrepo'));
      $zip->close();
    } else {
      $this->markTestSkipped('Couldn\'t extract repo zip');
    }

    $this->repo = VfsStream::url('Testrepo').'/Testrepo.git';

    $this->transport = new NativeTransport($this->repo);
  }

  public function testCanResolvePackedHeadRef()
  {
    $this->assertEquals("7fafbb3abfeb673be3726e657bbdbb50b606fce3", $this->transport->resolveHead('packed-ref'));
  }

  public function testCanResolveLooseHeadRef()
  {
    $this->assertEquals("935122a4458399ef488c872b42c6e9985f1d1e3b", $this->transport->resolveHead('loose-ref'));
  }

  public function testCanResolveShaAsTreeish()
  {
    $this->assertEquals("935122a4458399ef488c872b42c6e9985f1d1e3b", $this->transport->resolveTreeish('935122a4458399ef488c872b42c6e9985f1d1e3b'));
  }

  public function testCanResolveRefAsTreeish()
  {
    $this->assertEquals("0c634a2539363d4404761cd990ccac26c694f000", $this->transport->resolveTreeish('master'));
  }

  public function testCantResolveUnknownAsTreeish()
  {
    $this->assertFalse($this->transport->resolveTreeish('meister'));
  }

  public function testCanSetNewBranch()
  {
    $this->transport->setBranch('new-branch', '7fafbb3abfeb673be3726e657bbdbb50b606fce3');

    $this->assertTrue(file_exists($this->repo.'/refs/heads/new-branch'));
    $this->assertEquals('7fafbb3abfeb673be3726e657bbdbb50b606fce3', file_get_contents($this->repo.'/refs/heads/new-branch'));
  }

  public function testCanOverwriteOldBranch()
  {
    $this->transport->setBranch('master', '7fafbb3abfeb673be3726e657bbdbb50b606fce3');

    $this->assertEquals('7fafbb3abfeb673be3726e657bbdbb50b606fce3', file_get_contents($this->repo.'/refs/heads/master'));
  }

  public function testCanRemoveBranch()
  {
    $this->transport->removeBranch('master');

    $this->assertFalse(is_file($this->repo.'/refs/heads/master'));
  }

  public function testCanCheckWhetherIndexDataExists()
  {
    $this->assertTrue($this->transport->hasIndexData());
  }

  public function testCanReadIndexData()
  {
    $this->assertEquals(file_get_contents($this->repo.'/index'), $this->transport->getIndexData());
  }

  public function testCanPutIndexData()
  {
    $this->transport->putIndexData('foo');

    $this->assertEquals('foo', file_get_contents($this->repo.'/index'));
  }

  public function testCanPutRawObject()
  {
    $raw_object = M::mock('Gittern\Transport\RawObject', array('getSha' => 'deadbeefcafebabefacebadc0ffeebadf00dcafe', 'getData' => 'foobar', 'getType' => 'blob', 'getLength' => 6));
    $this->transport->putRawObject($raw_object);

    $this->assertEquals(gzcompress("blob 6\0foobar", 4), file_get_contents($this->repo.'/objects/de/adbeefcafebabefacebadc0ffeebadf00dcafe'));
  }

  public function testCanReadLooseObject()
  {
    $raw_object = $this->transport->fetchRawObject('a1a97f672a3421b53928cb9e6952e228eb8a4e04');

    $this->assertEquals('blob', $raw_object->getType());
    $this->assertEquals("New file, new exciting contents!", $raw_object->getData());
  }

  public function testCanReadPackedObject()
  {
    $raw_object = $this->transport->fetchRawObject('24fb5bad9c8f3b2694412ea309f207091f2309cf');
    $this->assertEquals('blob', $raw_object->getType());
  }

  /**
   * @expectedException        RuntimeException
   * @expectedExceptionMessage Attempting to hydrate empty object
   */
  public function testCantReadEmptyLooseObject()
  {
    $this->writeData('objects/de/adbeefcafebabefacebadc0ffeebadf00dcafe', gzcompress(''));

    $this->transport->fetchRawObject('deadbeefcafebabefacebadc0ffeebadf00dcafe');
  }

  /**
   * @expectedException        RuntimeException
   * @expectedExceptionMessage Length derived from git object header (109) does not match actual length (10)
   */
  public function testCantLooseObjectWithInvalidLength()
  {
    $this->writeData('objects/de/adbeefcafebabefacebadc0ffeebadf00dcafe', gzcompress("blob 100\0a"));

    $this->transport->fetchRawObject('deadbeefcafebabefacebadc0ffeebadf00dcafe');
  }

  public function testCantResolveNonExistantObject()
  {
    $raw_object = $this->transport->fetchRawObject('deadbeefcafebabefacebadc0ffeebadf00dcafe');

    $this->assertNull($raw_object);
  }

  protected function writeData($relative_path, $data)
  {
    $path = $this->repo.'/'.$relative_path;

    $dir = pathinfo($path, PATHINFO_DIRNAME);

    if (!is_dir($dir))
    {
      mkdir($dir, 0777, true);
    }

    file_put_contents($path, $data);
  }

  /**
   * Test to see so a tag can be resolved
   *
   * @return void
   */
  public function testCanResoveTag()
  {
    $this->assertEquals(
      '0c634a2539363d4404761cd990ccac26c694f000',
      $this->transport->resolveTag('v0.0.0')
    );
  }

  /**
   * Test to ensure no such tag resolves to false.
   *
   * @return void
   */
  public function testNoSuchTag()
  {
    $this->assertFalse($this->transport->resolveTag('no_such_tag'));
  }
}
