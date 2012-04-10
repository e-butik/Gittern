<?php

namespace Gittern;

use Mockery as M;

/**
 * @covers Gittern\Repository
 */
class RepositoryTest extends \PHPUnit_Framework_TestCase
{
  public function setUp()
  {
    $this->repo = new Repository();
  }

  public function tearDown()
  {
    M::close();
  }

  public function testCanSetAndGetHydrator()
  {
    $hydrator = M::mock('Gittern\Hydrator\HydratorInterface');
    $this->repo->setHydrator('foo', $hydrator);

    $this->assertEquals($hydrator, $this->repo->getHydratorForType('foo'));
  }

  public function testCanSetAndGetDesiccator()
  {
    $desiccator = M::mock();
    $this->repo->setDesiccator('foo', $desiccator);

    $this->assertEquals($desiccator, $this->repo->getDesiccatorForType('foo'));
  }

  public function testCanGetTypeForBlob()
  {
    $this->assertEquals('blob', $this->repo->getTypeForObject(M::mock('Gittern\Entity\GitObject\Blob')));
  }

  public function testCanGetTypeForTree()
  {
    $this->assertEquals('tree', $this->repo->getTypeForObject(M::mock('Gittern\Entity\GitObject\Tree')));
  }

  public function testCanGetTypeForCommit()
  {
    $this->assertEquals('commit', $this->repo->getTypeForObject(M::mock('Gittern\Entity\GitObject\Commit')));
  }

  public function testCantGetTypeForOther()
  {
    $this->assertEquals(null, $this->repo->getTypeForObject(M::mock()));
  }

  public function testCanSetTransport()
  {
    $this->repo->setTransport(M::mock('Gittern\Transport\TransportInterface'));
  }

  public function testCanSetIndexHydrator()
  {
    $this->repo->setIndexHydrator(M::mock());
  }

  public function testCanSetIndexDesiccator()
  {
    $this->repo->setIndexDesiccator(M::mock());
  }

  public function testWillReturnCachedIndexIfExists()
  {
    $index = M::mock();

    $rp = new \ReflectionProperty('Gittern\Repository', 'index');
    $rp->setAccessible(true);
    $rp->setValue($this->repo, $index);

    $this->assertEquals($index, $this->repo->getIndex());
  }

  public function testWillGetIndexFromTransportIfDataExists()
  {
    $index = M::mock();
    $transport = M::mock('Gittern\Transport\TransportInterface', array('hasIndexData' => true, 'getIndexData' => 'foo'));
    $hydrator = M::mock();
    $hydrator->shouldReceive('hydrate')->with('foo')->andReturn($index);

    $this->repo->setIndexHydrator($hydrator);
    $this->repo->setTransport($transport);

    $this->assertEquals($index, $this->repo->getIndex());
  }

  public function testWillCreateNewIndexIfNoneExists()
  {
    $transport = M::mock('Gittern\Transport\TransportInterface', array('hasIndexData' => false));

    $this->repo->setTransport($transport);

    $this->assertInstanceOf('Gittern\Entity\Index', $this->repo->getIndex());
  }

  public function testCanFlushIndex()
  {
    $index = M::mock();

    $rp = new \ReflectionProperty('Gittern\Repository', 'index');
    $rp->setAccessible(true);
    $rp->setValue($this->repo, $index);

    $desiccator = M::mock();
    $desiccator->shouldReceive('desiccate')->with($index)->andReturn('foo');

    $transport = M::mock('Gittern\Transport\TransportInterface');
    $transport->shouldReceive('putIndexData')->with('foo')->once();

    $this->repo->setIndexDesiccator($desiccator);
    $this->repo->setTransport($transport);

    $this->repo->flushIndex();
  }
}
