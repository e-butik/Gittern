<?php

namespace Gittern\Transport;

use Iodophor\Io\FileReader;

/**
* @covers Gittern\Transport\PackfileIndex
* @author Magnus Nordlander
*/
class PackfileIndexTest extends \PHPUnit_Framework_TestCase
{
  public function setUp()
  {
    $reader = new FileReader(__DIR__.'/../Fixtures/Packfiles/packfile.idx');
    $this->index = new PackfileIndex($reader);
  }

  public function testCanGetSize()
  {
    $this->assertEquals(7, $this->index->getSize());
  }

  public function testCanGetShas()
  {
    $shas = array(
      '24fb5bad9c8f3b2694412ea309f207091f2309cf',
      '2c1298dc7a92eb18a1e72658f61f181e1afdeb56',
      '4e2f28f8aefbc4d4e16a42f70502cab11e1ee946',
      '7fafbb3abfeb673be3726e657bbdbb50b606fce3',
      '935122a4458399ef488c872b42c6e9985f1d1e3b',
      'add3401663de36e96d701e7dc6ba6c65d19cde10',
      'e23af412a1428d91b51443c5629c7f2840c6062d',
    );

    $this->assertEquals($shas, $this->index->getShas());
  }

  public function testCanGetCrcForSha()
  {
    $this->assertEquals('d0f59504', $this->index->getCrcForSha('add3401663de36e96d701e7dc6ba6c65d19cde10'));
  }

  /**
   * @expectedException Gittern\Exception\NativeTransport\IndexEntryNotFoundException
   * @expectedExceptionMessage SHA deadbeefcafebabefacebadc0ffeebadf00dcafe is not in packfile index
   */
  public function testCantGetCrcForNonExistantSha()
  {
    $this->index->getCrcForSha('deadbeefcafebabefacebadc0ffeebadf00dcafe');
  }

  public function testCanGetPackfileOffsetForSha()
  {
    $this->assertEquals(293, $this->index->getPackfileOffsetForSha('add3401663de36e96d701e7dc6ba6c65d19cde10'));
  }

  /**
   * @expectedException Gittern\Exception\NativeTransport\IndexEntryNotFoundException
   * @expectedExceptionMessage SHA deadbeefcafebabefacebadc0ffeebadf00dcafe is not in packfile index
   */
  public function testCantGetPackfileOffsetForNonExistantSha()
  {
    $this->index->getPackfileOffsetForSha('deadbeefcafebabefacebadc0ffeebadf00dcafe');
  }

  public function testCanCheckIfIndexHasSha()
  {
    $this->assertTrue($this->index->hasSha('935122a4458399ef488c872b42c6e9985f1d1e3b'));
    $this->assertFalse($this->index->hasSha('deadbeefcafebabefacebadc0ffeebadf00dcafe'));
  }
}