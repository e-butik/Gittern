<?php

namespace Gittern;

use Gittern\Entity\Index;
use Gittern\Transport\RawObject;
use Gittern\Transport\TransportInterface;

use Gittern\Exception\EntityNotFoundException;

/**
* @author Magnus Nordlander
**/
class Repository
{
  protected $hydrators = array();
  protected $desiccators = array();

  protected $index_hydrator = null;
  protected $index_desiccator = null;

  protected $index = null;

  protected $unflushed_objects = array();
  protected $branch_moves = array();
  protected $branch_removes = array();

  protected $transport;

  public function setHydrator($type, Hydrator\HydratorInterface $hydrator)
  {
    $this->hydrators[$type] = $hydrator;
  }

  public function setDesiccator($type, $desiccator)
  {
    $this->desiccators[$type] = $desiccator;
  }

  public function setIndexHydrator($index_hydrator)
  {
    $this->index_hydrator = $index_hydrator;
  }

  public function setIndexDesiccator($index_desiccator)
  {
    $this->index_desiccator = $index_desiccator;
  }

  public function getHydratorForType($type)
  {
    return $this->hydrators[$type];
  }

  public function getDesiccatorForType($type)
  {
    return $this->desiccators[$type];
  }

  public function getTypeForObject($object)
  {
    if ($object instanceof Entity\GitObject\Blob)
    {
      return "blob";
    }
    if ($object instanceof Entity\GitObject\Tree)
    {
      return "tree";
    }
    if ($object instanceof Entity\GitObject\Commit)
    {
      return "commit";
    }

    return null;
  }

  public function setTransport(TransportInterface $transport)
  {
    $this->transport = $transport;
  }

  public function getIndex()
  {
    if (!$this->index)
    {
      if ($this->transport->hasIndexData())
      {
        $this->index = $this->index_hydrator->hydrate($this->transport->getIndexData());
      }
      else
      {
        $this->index = new Index;
      }
    }
    return $this->index;
  }

  public function flushIndex()
  {
    if ($this->index)
    {
      $this->transport->putIndexData($this->index_desiccator->desiccate($this->getIndex()));
    }
  }

  public function flush()
  {
    foreach ($this->unflushed_objects as $sha => $raw_object)
    {
      $this->transport->putRawObject($raw_object);
    }

    $this->flushIndex();

    foreach ($this->branch_moves as $branch => $commit)
    {
      $this->transport->setBranch($branch, $commit->getSha());
    }

    foreach ($this->branch_removes as $branch)
    {
      $this->transport->removeBranch($branch);
    }
  }

  public function setBranch($branch, Entity\GitObject\Commit $commit)
  {
    // Because branch removes are executed after sets, if this branch
    // is queued for removal, remove it from that queue.
    if (($index = array_search($branch, $this->branch_removes)) !== false)
    {
      unset($this->branch_removes[$index]);
    }

    $this->branch_moves[$branch] = $commit;
  }

  public function removeBranch($branch)
  {
    $this->branch_removes[] = $branch;
  }

  public function renameBranch($from, $to)
  {
    $commit = new Proxy\CommitProxy($this, $this->transport->resolveTreeish($from));

    $this->setBranch($to, $commit);
    $this->removeBranch($from);
  }

  public function hasTag($tag)
  {
    return (bool)$this->transport->resolveTag($tag);
  }

  public function hasObject($treeish)
  {
    return (bool)$this->transport->resolveTreeish($treeish);
  }

  public function getObject($treeish)
  {
    $sha = $this->transport->resolveTreeish($treeish);

    if (!$sha)
    {
      throw new EntityNotFoundException(sprintf("Couldn't find an object with identifier %s", $treeish));
    }

    return $this->getObjectBySha($sha);
  }

  public function getObjectBySha($sha)
  {
    $raw_object = $this->transport->fetchRawObject($sha);

    if (!$raw_object)
    {
      throw new EntityNotFoundException(sprintf("Couldn't fetch object with identifier %s", $sha));
    }

    $hydrator = $this->getHydratorForType($raw_object->getType());

    if (!$hydrator)
    {
      throw new \RuntimeException("No hydrator for type $type set");
    }

    return $hydrator->hydrate($raw_object);
  }

  protected function doDesiccation($object)
  {
    $type = $this->getTypeForObject($object);

    $desiccator = $this->getDesiccatorForType($type);

    $raw_object = $desiccator->desiccate($object);

    $object->setSha($raw_object->getSha());

    $this->unflushed_objects[$raw_object->getSha()] = $raw_object;
  }

  public function desiccateGitObject($object)
  {
    if (!$object->getSha())
    {
      if ($object instanceof Entity\GitObject\Blob)
      {
        $this->doDesiccation($object);
      }
      else if ($object instanceof Entity\GitObject\Tree)
      {
        foreach ($object->getNodes() as $node)
        {
          $this->desiccateGitObject($node->getRelatedObject());
        }
        $this->doDesiccation($object);
      }
      else if ($object instanceof Entity\GitObject\Commit)
      {
        $this->desiccateGitObject($object->getTree());
        foreach ($object->getParents() as $parent)
        {
          $this->desiccateGitObject($parent);
        }
        $this->doDesiccation($object);
      }
    }
  }
}
