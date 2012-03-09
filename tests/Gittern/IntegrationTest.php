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
    $transport = new Transport\NativeTransport(__DIR__.'/../../../Testrepo.git');

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

//    var_dump($repo->getObject('2c1298dc7a92eb18a1e72658f61f181e1afdeb56'));

/*    $adapter = new GitternTreeishReadOnlyAdapter($repo, 'loose-ref');
    $git_fs = new Filesystem($adapter);

    var_dump($git_fs->read('classic.txt'));*/
    
//    var_dump($git_fs->read('classic.txt'));

/*    $git_index_adapter = new GitternIndexAdapter($repo, false);
    $git_index_fs = new Filesystem($git_index_adapter);

    $git_index_fs->write('newfile.txt', "New file, new exciting contents!", true);

    $tree = $repo->getIndex()->createTree();

    $commit = new GitObject\Commit();
    $commit->setTree($tree);
    $commit->setAuthor(new GitObject\User("Magnus Nordlander", "magnus@nordlander.se"));
    $commit->setAuthorTime(new \DateTime());
    $commit->setCommitter(new GitObject\User("Magnus Nordlander", "magnus@nordlander.se"));
    $commit->setCommitTime(new \DateTime());
    $commit->setMessage("Added new and exciting file");

    $repo->desiccateGitObject($commit);
    $repo->setBranch('master', $commit);

    $repo->flush();*/
  }
}