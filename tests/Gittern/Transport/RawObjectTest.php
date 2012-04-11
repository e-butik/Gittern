<?php

namespace Gittern\Transport;

/**
* @covers Gittern\Transport\RawObject
* @author Magnus Nordlander
*/
class RawObjectTest extends \PHPUnit_Framework_TestCase
{
  public function testConstructWithNumericCommit()
  {
    $commit = new RawObject(RawObject::NUMERIC_TYPE_COMMIT, '');
    $this->assertEquals('commit', $commit->getType());
  }

  public function testConstructWithNumericTree()
  {
    $commit = new RawObject(RawObject::NUMERIC_TYPE_TREE, '');
    $this->assertEquals('tree', $commit->getType());
  }

  public function testConstructWithNumericBlob()
  {
    $commit = new RawObject(RawObject::NUMERIC_TYPE_BLOB, '');
    $this->assertEquals('blob', $commit->getType());
  }

  public function testConstructWithNumericTag()
  {
    $commit = new RawObject(RawObject::NUMERIC_TYPE_TAG, '');
    $this->assertEquals('tag', $commit->getType());
  }

  /**
   * @expectedException RuntimeException
   * @expectedExceptionMessage Numeric type 0x2a unknown
   */
  public function testCannotConstructWithOtherNumericType()
  {
    $commit = new RawObject(42, '');
  }

  public function testConstructWithStringType()
  {
    $commit = new RawObject('foo', '');
    $this->assertEquals('foo', $commit->getType());
  }

  public function testConstructCanSetData()
  {
    $commit = new RawObject('blob', 'foobar');
    $this->assertEquals('foobar', $commit->getData());
  }

  public function testCanCalculateSha()
  {
    $raw_object = new RawObject('blob', 'foobar');
    $this->assertEquals(sha1("blob 6\0foobar"), $raw_object->getSha());
  }

  public function testCanCalculateLength()
  {
    $raw_object = new RawObject('blob', 'foobar');
    $this->assertEquals(6, $raw_object->getLength());
  }
}