<?php

namespace Gittern\Entity;

use Mockery as M;

/**
* @covers Gittern\Entity\IndexEntry
* @author Magnus Nordlander
*/
class IndexEntryTest extends \PHPUnit_Framework_TestCase
{
  public function setUp()
  {
    $this->entry = new IndexEntry();
  }

  public function tearDown()
  {
    M::close();
  }

  public function testCanCreateEntryFromBlobNode()
  {
    $blob_mock = M::mock('Gittern\Entity\GitObject\Blob');
    $node_mock = M::mock('Gittern\Entity\GitObject\Node\BlobNode', array('getBlob' => $blob_mock, 'getIntegerMode' => 0100644));
    $entry = IndexEntry::createFromBlobNode($node_mock);
    $this->assertEquals($blob_mock, $entry->getBlob());
    $this->assertEquals(0100644, $entry->getMode());
  }

  public function testCanCreateBlobNodeFromEntry()
  {
    $blob_mock = M::mock('Gittern\Entity\GitObject\Blob');
    $this->entry->setBlob($blob_mock);
    $this->entry->setMode(0100644);
    $node = $this->entry->createBlobNode();

    $this->assertEquals($blob_mock, $node->getBlob());
    $this->assertEquals(0100644, $node->getIntegerMode());
  }

  public function testCanGetAndSetCtime()
  {
    $this->entry->setCtime(123456);
    $this->assertEquals(123456, $this->entry->getCtime());
  }

  public function testCanGetAndSetMtime()
  {
    $this->entry->setMtime(123456);
    $this->assertEquals(123456, $this->entry->getMtime());
  }

  public function testCanGetAndSetDev()
  {
    $this->entry->setDev(102);
    $this->assertEquals(102, $this->entry->getDev());
  }

  public function testCanGetAndSetInode()
  {
    $this->entry->setInode(1204593);
    $this->assertEquals(1204593, $this->entry->getInode());
  }

  public function testCanGetAndSetMode()
  {
    $this->entry->setMode(0100644);
    $this->assertEquals(0100644, $this->entry->getMode());
  }

  public function testCanGetAndSetUid()
  {
    $this->entry->setUid(1001);
    $this->assertEquals(1001, $this->entry->getUid());
  }

  public function testCanGetAndSetGid()
  {
    $this->entry->setGid(1001);
    $this->assertEquals(1001, $this->entry->getGid());
  }

  public function testCanGetAndSetFileSize()
  {
    $this->entry->setFileSize(10042);
    $this->assertEquals(10042, $this->entry->getFileSize());
  }

  public function testCanGetAndSetBlob()
  {
    $blob = M::mock('Gittern\Entity\GitObject\Blob');
    $this->entry->setBlob($blob);
    $this->assertEquals($blob, $this->entry->getBlob());
  }

  public function testCanGetAndSetStage()
  {
    $this->entry->setStage(1);
    $this->assertEquals(1, $this->entry->getStage());
  }

  public function testCanGetAndSetName()
  {
    $this->entry->setName('foobar');
    $this->assertEquals('foobar', $this->entry->getName());
  }
}