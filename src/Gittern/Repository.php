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

  protected $transport;

  /**
   * @author Magnus Nordlander
   **/
  public function setHydrator($type, Hydrator\ObjectHydrating $hydrator)
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
      $this->index = $this->index_hydrator->hydrate($this->transport->getIndexData());
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
  }

  /**
   * @author Magnus Nordlander
   **/
  public function getObject($treeish)
  {
    $sha = $this->transport->resolveTreeish($treeish);

    return $this->hydrateGitObject($sha, $this->transport->resolveObject($sha));
  }

  /**
   * @author Magnus Nordlander
   **/
  public function desiccateGitObject($object)
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
  public function hydrateGitObject($sha, $compressed_data)
  {
    $uncompressed_data = gzuncompress($compressed_data);

    sscanf($uncompressed_data, "%s %d\0", $type, $length);

    $offset = strlen($type)+strlen($length)+2; //Space and NUL

    if (strlen($uncompressed_data) !== $offset+$length)
    {
      throw new \RuntimeException("Length specified in git object header does not match actual length");
    }

    $data = substr($uncompressed_data, $offset, $length);

    $hydrator = $this->getHydratorForType($type);

    if (!$hydrator)
    {
      throw new \RuntimeException("No hydrator for type $type set");
    }

    return $hydrator->hydrate($sha, $data);
  }
}