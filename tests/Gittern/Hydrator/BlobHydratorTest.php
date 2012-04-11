<?php

namespace Gittern\Hydrator;

use Mockery as M;

/**
 * @covers Gittern\Hydrator\BlobHydrator
 * @author Magnus Nordlander
 */
class BlobHydratorTest extends \PHPUnit_Framework_TestCase
{
  public function testBlobHydratorHappyPath()
  {
    $sha = "deadbeefcafebabefacebadc0ffeebadf00dcafe";
    $data = "This blob is pretty boss, wouldn't you say?";

    $raw_object = M::mock('Gittern\Transport\RawObject', array('getSha' => $sha, 'getData' => $data));

    $hydrator = new BlobHydrator(M::mock('Gittern\Repository'));

    $blob = $hydrator->hydrate($raw_object);

    $this->assertEquals($sha, $blob->getSha());
    $this->assertEquals($data, $blob->getContents());
  }
}
