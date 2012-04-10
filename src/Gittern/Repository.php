<?php

namespace Gittern;

use Gittern\Entity\Index;

use Gittern\Transport\RawObject;

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

  protected $transport;

  /**
   * @author Magnus Nordlander
   **/
  public function setHydrator($type, Hydrator\HydratorInterface $hydrator)
  {
    $this->hydrators[$type] = $hydrator;
  }

  /**
   * @author Magnus Nordlander
   **/
  public function setDesiccator($type, $desiccator)
  {
    $this->desiccators[$type] = $desiccator;
  }

  /**
   * @author Magnus Nordlander
   **/
  public function setIndexHydrator($index_hydrator)
  {
    $this->index_hydrator = $index_hydrator;
  }

  /**
   * @author Magnus Nordlander
   **/
  public function setIndexDesiccator($index_desiccator)
  {
    $this->index_desiccator = $index_desiccator;
  }

  /**
   * @author Magnus Nordlander
   **/
  public function getHydratorForType($type)
  {
    return $this->hydrators[$type];
  }

  /**
   * @author Magnus Nordlander
   **/
  public function getDesiccatorForType($type)
  {
    return $this->desiccators[$type];
  }

  /**
   * @author Magnus Nordlander
   **/
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

  /**
   * @author Magnus Nordlander
   **/
  public function setTransport($transport)
  {
    $this->transport = $transport;
  }

  /**
   * @author Magnus Nordlander
   **/
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

  /**
   * @author Magnus Nordlander
   **/
  public function flushIndex()
  {
    $this->transport->putIndexData($this->index_desiccator->desiccate($this->getIndex()));
  }

  /**
   * @author Magnus Nordlander
   **/
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
  }

  public function setBranch($branch, Entity\GitObject\Commit $commit)
  {
    $this->branch_moves[$branch] = $commit;
  }

  /**
   * @author Magnus Nordlander
   **/
  public function getObject($treeish)
  {
    $sha = $this->transport->resolveTreeish($treeish);

    $raw_object = $this->transport->resolveRawObject($sha);

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

  /**
   * @author Magnus Nordlander
   **/
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