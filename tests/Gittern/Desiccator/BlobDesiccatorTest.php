<?php

namespace Gittern\Desiccator;

use Mockery as M;

/**
 * @covers Gittern\Desiccator\BlobDesiccator
 * @author Magnus Nordlander
 */
class BlobDesiccatorTest extends \PHPUnit_Framework_TestCase
{
  public function testCanDesiccateBlob()
  {
    $desiccator = new BlobDesiccator();

    $blob = M::mock('Gittern\Entity\GitObject\Blob', array('getContents' => 'foobar'));

    $raw_object = $desiccator->desiccate($blob);
    $this->assertEquals('foobar', $raw_object->getData());
    $this->assertEquals('blob', $raw_object->getType());
  }
}
