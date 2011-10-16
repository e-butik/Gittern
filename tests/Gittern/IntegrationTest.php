<?php

namespace Gittern;

use Gaufrette\Filesystem;
use Gaufrette\Adapter\Local;

/**
* @author Magnus Nordlander
**/
class IntegrationTest extends \PHPUnit_Framework_TestCase
{
  /**
   * @author Magnus Nordlander
   **/
  public function testFetchTree()
  {
    $git_fs = new Filesystem(new Local(__DIR__.'/../Testrepo.git'));

    $transport = new Transport\GaufretteTransport($git_fs);

    $repo = new Repository;
    $repo->setHydrator('commit', new Hydrator\CommitHydrator($repo));
    $repo->setHydrator('tree', new Hydrator\TreeHydrator($repo));
    $repo->setHydrator('blob', new Hydrator\BlobHydrator($repo));
    $repo->setDesiccator('blob', new Desiccator\BlobDesiccator());
    $repo->setDesiccator('tree', new Desiccator\TreeDesiccator());
    $repo->setDesiccator('commit', new Desiccator\CommitDesiccator());
    $repo->setIndexHydrator(new Hydrator\IndexHydrator($repo));
    $repo->setIndexDesiccator(new Desiccator\IndexDesiccator());
    $repo->setTransport($transport);

    $git_index_adapter = new GitternIndexAdapter($repo, false);
    $git_index_fs = new Filesystem($git_index_adapter);

    $test_data_fs = new Filesystem(new Local(__DIR__.'/../Unversioned test data/'));

    foreach ($test_data_fs->keys() as $key) 
    {
      $git_index_fs->write($key, $test_data_fs->read($key));
    }

    $tree = $repo->getIndex()->createTree();

    $commit = new GitObject\Commit();
    $commit->setTree($tree);
    $commit->setAuthor(new GitObject\User("Magnus Nordlander", "magnus@nordlander.se"));
    $commit->setAuthorTime(new \DateTime());
    $commit->setCommitter(new GitObject\User("Magnus Nordlander", "magnus@nordlander.se"));
    $commit->setCommitTime(new \DateTime());
    $commit->setMessage("Initial commit");

    $repo->desiccateGitObject($commit);
    $repo->moveBranch('master', $commit);

    $repo->flush();
  }
}