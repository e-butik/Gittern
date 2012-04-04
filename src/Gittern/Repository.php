<?php

namespace Gittern;

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
    if ($object instanceof GitObject\Blob)
    {
      return "blob";
    }
    if ($object instanceof GitObject\Tree)
    {
      return "tree";
    }
    if ($object instanceof GitObject\Commit)
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
    foreach ($this->unflushed_objects as $sha => $data) 
    {
      $this->transport->putObject($sha, $data);
    }
    
    $this->flushIndex();

    foreach ($this->branch_moves as $branch => $commit)
    {
      $this->transport->setBranch($branch, $commit->getSha());
    }
  }

  public function setBranch($branch, GitObject\Commit $commit)
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

    return $hydrator->hydrate($sha, $raw_object->getData());
  }

  protected function doDesiccation($object)
  {
    $type = $this->getTypeForObject($object);

    $desiccator = $this->getDesiccatorForType($type);

    $desiccated_data = $desiccator->desiccate($object);

    $data = $type.' '.strlen($desiccated_data)."\0".$desiccated_data;

    $sha = sha1($data);
    $object->setSha($sha);

    $this->unflushed_objects[$sha] = gzcompress($data, 4);
  }

  /**
   * @author Magnus Nordlander
   **/
  public function desiccateGitObject($object)
  {
    if (!$object->getSha())
    {
      if ($object instanceof GitObject\Blob)
      {
        $this->doDesiccation($object);
      }
      else if ($object instanceof GitObject\Tree)
      {
        foreach ($object->getNodes() as $node)
        {
          $this->desiccateGitObject($node->getRelatedObject());
        }
        $this->doDesiccation($object);
      }
      else if ($object instanceof GitObject\Commit)
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