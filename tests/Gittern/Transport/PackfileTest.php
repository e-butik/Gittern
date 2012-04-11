<?php

namespace Gittern\Transport;

use Iodophor\Io\FileReader;

use Mockery as M;

/**
 * @covers Gittern\Transport\Packfile
 * @author Magnus Nordlander
 */
class PackfileTest extends \PHPUnit_Framework_TestCase
{
  public function setUp()
  {
    $index_reader = new FileReader(__DIR__.'/../Fixtures/Packfiles/packfile.idx');
    $this->index = new PackfileIndex($index_reader);

    $pack_reader = new FileReader(__DIR__.'/../Fixtures/Packfiles/packfile.pack');
    $this->packfile = new Packfile($pack_reader);

    $this->packfile->setIndex($this->index);
  }

  public function testCanGetIndex()
  {
    $this->assertEquals($this->index, $this->packfile->getIndex());
  }

  public function testCanSetIndex()
  {
    $this->packfile->setIndex(null);
    $this->assertNull($this->packfile->getIndex());
  }

  public function testCanCheckIfPackfileHasShaWithIndex()
  {
    $mock_index = M::mock('Gittern\\Transport\\PackfileIndex');
    $mock_index->shouldReceive('hasSha')->with('deadbeefcafebabefacebadc0ffeebadf00dcafe')->andReturn(true);
    $this->packfile->setIndex($mock_index);

    $this->assertTrue($this->packfile->hasSha('deadbeefcafebabefacebadc0ffeebadf00dcafe'));
  }

  /**
   * @expectedException LogicException
   * @expectedExceptionMessage hasSha without index is not implemented yet.
   */
  public function testCantCheckIfPackfileHasShaWithoutIndex()
  {
    $this->packfile->setIndex(null);

    $this->packfile->hasSha('deadbeefcafebabefacebadc0ffeebadf00dcafe');
  }

  public function testCanGetRegularObject()
  {
    // Offset 339525 is a blob, an ascii picture of a wurst, with an extra text about how
    // nice it is.
    // It corresponds to sha 24fb5bad9c8f3b2694412ea309f207091f2309cf
    // It's known to have md5 af4dc5e71d65c6770b8cf72e26a3a093
    $raw_object = $this->packfile->getRawObjectAtOffset(339525);

    $this->assertEquals('af4dc5e71d65c6770b8cf72e26a3a093', md5($raw_object->getData()));
    $this->assertEquals('blob', $raw_object->getType());
  }

    public function testCanGetDeltaOffsetObject()
  {
    // Offset 340054 is an offset delta patched blob, an ascii picture of a wurst.
    // It corresponds to sha 2c1298dc7a92eb18a1e72658f61f181e1afdeb56
    // It's known to have md5 30e1df56c73b2fba193d8c822a882271
    $raw_object = $this->packfile->getRawObjectAtOffset(340054);

    $this->assertEquals('30e1df56c73b2fba193d8c822a882271', md5($raw_object->getData()));
    $this->assertEquals('blob', $raw_object->getType());
  }

  /**
   * @covers Gittern\Transport\Packfile::getRawObjectForSha
   */
  public function testCanGetOffsetFromIndex()
  {
    // Same object as in testCanGetRegularObject
    $raw_object = $this->packfile->getRawObjectForSha('24fb5bad9c8f3b2694412ea309f207091f2309cf');

    $this->assertEquals('af4dc5e71d65c6770b8cf72e26a3a093', md5($raw_object->getData()));
    $this->assertEquals('blob', $raw_object->getType());
  }

  /**
   * @covers Gittern\Transport\Packfile::getRawObjectForSha
   * @expectedException LogicException
   * @expectedExceptionMessage getRawObjectForSha without index is not implemented yet.
   */
  public function testCantGetObjectForShaWithoutIndex()
  {
    $this->packfile->setIndex(null);
    $this->packfile->getRawObjectForSha('24fb5bad9c8f3b2694412ea309f207091f2309cf');
  }
}