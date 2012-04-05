<?php

namespace Gittern\GitObject;

/**
* @covers Gittern\GitObject\Blob
*/
class BlobTest extends \PHPUnit_Framework_TestCase
{
  public function setUp()
  {
    $this->blob = new Blob();
  }

  public function testCanBeConstructed()
  {
  }

  public function testCanSetAndGetSha()
  {
    $this->blob->setSha('deadbeef');
    $this->assertEquals('deadbeef', $this->blob->getSha());
  }

  public function testCanSetAndGetContents()
  {
    $this->blob->setContents('foobar');
    $this->assertEquals('foobar', $this->blob->getContents());
  }
}