<?php

namespace Gittern;

class RepositoryTest extends \PHPUnit_Framework_TestCase
{
  public function testSetHydrator()
  {
    $repo = new Repository();
    $hydrator = new Hydrator\BlobHydrator;
    $repo->setHydrator('blob', $hydrator);

    $this->assertEquals($hydrator, $repo->getHydratorForType('blob'));
  }
}
