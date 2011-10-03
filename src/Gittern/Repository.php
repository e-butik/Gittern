<?php

namespace Gittern;

/**
* @author Magnus Nordlander
**/
class Repository
{
  protected $hydrators = array();
  protected $index_hydrator = null;
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
  public function setIndexHydrator($index_hydrator)
  {
    $this->index_hydrator = $index_hydrator;
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
  public function setTransport($transport)
  {
    $this->transport = $transport;
  }

  /**
   * @author Magnus Nordlander
   **/
  public function getIndex()
  {
    return $this->index_hydrator->hydrate($this->transport->getIndexData());
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