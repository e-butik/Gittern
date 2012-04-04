<?php

namespace Gittern\Hydrator;

use Mockery as M;

/**
 * @covers Gittern\Hydrator\BlobHydrator
 */
class BlobHydratorTest extends \PHPUnit_Framework_TestCase
{
  public function testBlobHydratorHappyPath()
  {
    $sha = "deadbeefcafebabefacebadc0ffeebadf00dcafe";
    $data = "This blob is pretty boss, wouldn't you say?";

    $hydrator = new BlobHydrator(M::mock('Gittern\Repository'));

    $blob = $hydrator->hydrate($sha, $data);

    $this->assertEquals($sha, $blob->getSha());
    $this->assertEquals($data, $blob->getContents());
  }
}
