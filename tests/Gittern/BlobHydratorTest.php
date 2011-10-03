<?php

namespace Gittern;

class BlobHydratorTest extends \PHPUnit_Framework_TestCase
{
  public function testBlobHydratorHappyPath()
  {
    $sha = "0477507aa58951806581a0df29bcf3b2491b67be";
    $data = file_get_contents(__DIR__.'/blobdata.bin');

    $hydrator = new Hydrator\BlobHydrator();
    $blob = $hydrator->hydrate($sha, $data);

    $this->assertEquals($sha, $blob->getSha());
    $this->assertEquals($data, $blob->getContents());
  }
}
