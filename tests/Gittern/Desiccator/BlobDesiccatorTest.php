<?php

namespace Gittern\Desiccator;

use Mockery as M;

/**
 * @covers Gittern\Desiccator\BlobDesiccator
 */
class BlobDesiccatorTest extends \PHPUnit_Framework_TestCase
{
  public function testCanDesiccateBlob()
  {
    $desiccator = new BlobDesiccator();

    $blob = M::mock('Gittern\GitObject\Blob', array('getContents' => 'foobar'));
    $this->assertEquals('foobar', $desiccator->desiccate($blob));
  }
}
