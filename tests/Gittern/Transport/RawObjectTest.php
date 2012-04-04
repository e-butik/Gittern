<?php

namespace Gittern\Transport;

/**
* @covers Gittern\Transport\RawObject
*/
class RawObjectTest extends \PHPUnit_Framework_TestCase
{
  public function testConstructWithNumericCommit()
  {
    $commit = new RawObject(RawObject::NUMERIC_TYPE_COMMIT, 0, '');
    $this->assertEquals('commit', $commit->getType());
  }

  public function testConstructWithNumericTree()
  {
    $commit = new RawObject(RawObject::NUMERIC_TYPE_TREE, 0, '');
    $this->assertEquals('tree', $commit->getType());
  }

  public function testConstructWithNumericBlob()
  {
    $commit = new RawObject(RawObject::NUMERIC_TYPE_BLOB, 0, '');
    $this->assertEquals('blob', $commit->getType());
  }

  public function testConstructWithNumericTag()
  {
    $commit = new RawObject(RawObject::NUMERIC_TYPE_TAG, 0, '');
    $this->assertEquals('tag', $commit->getType());
  }

  /**
   * @expectedException RuntimeException
   * @expectedExceptionMessage Numeric type 0x2a unknown
   */
  public function testCannotConstructWithOtherNumericType()
  {
    $commit = new RawObject(42, 0, '');
  }

  public function testConstructWithStringType()
  {
    $commit = new RawObject('foo', 0, '');
    $this->assertEquals('foo', $commit->getType());
  }

  public function testConstructCanSetData()
  {
    $commit = new RawObject('blob', 6, 'foobar');
    $this->assertEquals('foobar', $commit->getData());
  }
}