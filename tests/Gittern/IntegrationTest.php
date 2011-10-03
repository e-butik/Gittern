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
    $adapter = new Local('/Users/magnus/Developer/KlarnaPHP/.git');
    $filesystem = new Filesystem($adapter);

    $transport = new Transport\GaufretteTransport($filesystem);

    $repo = new Repository;
    $repo->setHydrator('commit', new Hydrator\CommitHydrator($repo));
    $repo->setHydrator('tree', new Hydrator\TreeHydrator($repo));
    $repo->setHydrator('blob', new Hydrator\BlobHydrator($repo));
    $repo->setDesiccator('blob', new Desiccator\BlobDesiccator());
    $repo->setIndexHydrator(new Hydrator\IndexHydrator($repo));
    $repo->setIndexDesiccator(new Desiccator\IndexDesiccator());
    $repo->setTransport($transport);

/*    $blob = new GitObject\Blob();

    $blob->setContents("Foobar..");

    $repo->desiccateGitObject($blob);

    var_dump($blob);

    $repo->flush();*/

    $git_adapter = new GitternIndexAdapter($repo);
    $git_fs = new Filesystem($git_adapter);

    $git_fs->write('foobar.txt', 'Testar...');

//    $git_fs->rename('klarnaaddr.php', 'klarnafoo.php');

/*    $git_adapter = new GitternReadOnlyAdapter($repo, 'master');
    //$git_adapter = new Local('/Users/magnus/Developer/KlarnaPHP/');
    $git_fs = new Filesystem($git_adapter);

    var_dump($git_fs->get('klarnaaddr.php')->getContent());*/
  }
}