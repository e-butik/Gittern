<?php

namespace Gittern\Hydrator;

use Gittern\Entity\GitObject;

use Mockery as M;

/**
 * @covers Gittern\Hydrator\CommitHydrator
 * @author Magnus Nordlander
 */
class CommitHydratorTest extends \PHPUnit_Framework_TestCase
{
  public function testCommitHydratorHappyPath()
  {
    $sha = "c3c8d149d63338c1215e0b06c839768cdf1933d5";
    $data = <<<EOF
tree b4ac469697c1e0f5fbb5702befc59fd7a90970a0
parent b3bf978c063bc6491919a97d1f6d6c9a6c074a2d
parent deadbeefcafec0ffee19a97d1f6d6c9a6c074a2d
parent cafebabe063bc6491919a97d1f6d6c9a6c074a2d
author Magnus Nordlander <magnus@nordlander.se> 1316430078 +0200
committer Magnus Nordlander <magnus@nordlander.se> 1316430078 +0200

Updated to version 2.1.2
EOF;

    $raw_object = M::mock('Gittern\Transport\RawObject', array('getSha' => $sha, 'getData' => $data));

    $hydrator = new CommitHydrator(M::mock('Gittern\Repository'));

    $commit = $hydrator->hydrate($raw_object);

    $this->assertEquals($sha, $commit->getSha());
    $this->assertEquals("b4ac469697c1e0f5fbb5702befc59fd7a90970a0", $commit->getTree()->getSha());
    $parents = $commit->getParents();
    $this->assertEquals("b3bf978c063bc6491919a97d1f6d6c9a6c074a2d", $parents[0]->getSha());

    $user = new GitObject\User('Magnus Nordlander', "magnus@nordlander.se");
    $this->assertEquals($user, $commit->getAuthor());
    $this->assertEquals($user, $commit->getCommitter());

    $timestamp = \DateTime::createFromFormat('U O', '1316430078 +0200');
    $this->assertEquals($timestamp, $commit->getAuthorTime());
    $this->assertEquals($timestamp, $commit->getCommitTime());
  }
}
