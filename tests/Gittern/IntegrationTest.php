<?php

namespace Gittern;

use org\bovigo\vfs\vfsStream as VfsStream;
use org\bovigo\vfs\vfsStreamWrapper as VfsStreamWrapper;

use Gittern\Transport\NativeTransport;

/**
* @author Magnus Nordlander
* @group functional
**/
class IntegrationTest extends \PHPUnit_Framework_TestCase
{
  public function setUp()
  {
    VfsStream::setup('Testrepo');

    if (!class_exists('ZipArchive'))
    {
      $this->markTestSkipped('The ZipArchive class is not available.');
    }

    $zip = new \ZipArchive;
    if ($zip->open(__DIR__.'/Fixtures/Testrepo.git.zip') === true) 
    {
      $zip->extractTo(VfsStream::url('Testrepo'));
      $zip->close();
    } else {
      $this->markTestSkipped('Couldn\'t extract repo zip');
    }

    $repo_url = VfsStream::url('Testrepo').'/Testrepo.git';

    $transport = new NativeTransport($repo_url);

    $this->repo = new Repository();
    $this->repo->setTransport($transport);

    $configurator = new Configurator;
    $configurator->defaultConfigure($this->repo);

    $this->master_adapter = new GitternTreeishReadOnlyAdapter($this->repo, "master");

    $this->index_adapter = new GitternIndexAdapter($this->repo);
  }

  public function testCanGetKeysOfMaster()
  {
    $this->assertEquals(array('Tech specs.pdf', 'classic.txt', 'newfile.txt'), $this->master_adapter->keys());
  }

  public function testCanReadFileInMaster()
  {
    $this->assertEquals('New file, new exciting contents!', $this->master_adapter->read('newfile.txt'));
  }

  public function testCanGetKeysOfIndex()
  {
    $this->assertEquals(array('Tech specs.pdf', 'classic.txt', 'newfile.txt'), $this->index_adapter->keys());
  }

  public function testCanReadFileInIndex()
  {
    $this->assertEquals('New file, new exciting contents!', $this->index_adapter->read('newfile.txt'));
  }

  public function testCanWriteToIndex()
  {
    $this->index_adapter->write('anotherfile.txt', 'Another day, another file');
    $this->assertEquals('Another day, another file', $this->index_adapter->read('anotherfile.txt'));
  }

  public function testCanCommitFromIndex()
  {
    $master = $this->repo->getObject('master');

    $this->index_adapter->write('anotherfile.txt', 'Another day, another file');
    $tree = $this->repo->getIndex()->createTree();
    $commit = new Entity\GitObject\Commit();
    $commit->setTree($tree);
    $commit->addParent($master);
    $commit->setMessage("Added another file");
    $commit->setAuthor(new Entity\GitObject\User("Tessie Testson", "tessie.testson@example.com"));
    $commit->setCommitter(new Entity\GitObject\User("Tessie Testson", "tessie.testson@example.com"));
    $commit->setAuthorTime(new \DateTime());
    $commit->setCommitTime(new \DateTime());

    $this->repo->desiccateGitObject($commit);
    $this->repo->setBranch('master', $commit);

    $this->repo->flush();

    $new_master_adapter = new GitternTreeishReadOnlyAdapter($this->repo, "master");

    $this->assertEquals(array('Tech specs.pdf', 'anotherfile.txt', 'classic.txt', 'newfile.txt'), $new_master_adapter->keys());
    $this->assertEquals('Another day, another file', $new_master_adapter->read('anotherfile.txt'));
  }
}