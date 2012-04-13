<?php

namespace Gittern\Entity;

use Mockery as M;

/**
* @covers Gittern\Entity\Index
* @author Magnus Nordlander
*/
class IndexTest extends \PHPUnit_Framework_TestCase
{
  public function setUp()
  {
    $this->index = new Index();
  }

  public function tearDown()
  {
    M::close();
  }

  public function testCanClearIndex()
  {
    $entry = M::mock('Gittern\Entity\IndexEntry', array('getName' => 'foo'));
    $this->index->addEntry($entry);
    $this->assertEquals(array('foo'), $this->index->getEntryNames());
    $this->index->clear();
    $this->assertEquals(array(), $this->index->getEntryNames());
  }
}