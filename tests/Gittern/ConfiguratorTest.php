<?php

namespace Gittern;

use Mockery as M;

/**
* @covers Gittern\Configurator
*/
class ConfiguratorTest extends \PHPUnit_Framework_TestCase
{
  public function tearDown()
  {
    M::close();
  }

  public function testCanDefaultConfigure()
  {
    $repo_mock = M::mock('Gittern\Repository');
    $repo_mock->shouldReceive('setHydrator')->with('blob', M::type('Gittern\Hydrator\BlobHydrator'))->once();
    $repo_mock->shouldReceive('setHydrator')->with('commit', M::type('Gittern\Hydrator\CommitHydrator'))->once();
    $repo_mock->shouldReceive('setHydrator')->with('tree', M::type('Gittern\Hydrator\TreeHydrator'))->once();
    $repo_mock->shouldReceive('setDesiccator')->with('blob', M::type('Gittern\Desiccator\BlobDesiccator'))->once();
    $repo_mock->shouldReceive('setDesiccator')->with('commit', M::type('Gittern\Desiccator\CommitDesiccator'))->once();
    $repo_mock->shouldReceive('setDesiccator')->with('tree', M::type('Gittern\Desiccator\TreeDesiccator'))->once();
    $repo_mock->shouldReceive('setIndexHydrator')->with(M::type('Gittern\Hydrator\IndexHydrator'))->once();
    $repo_mock->shouldReceive('setIndexDesiccator')->with(M::type('Gittern\Desiccator\IndexDesiccator'))->once();

    $configurator = new Configurator;
    $configurator->defaultConfigure($repo_mock);
  }
}