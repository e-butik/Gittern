<?php

namespace Gittern\GitObject\Node;

/**
* @author Magnus Nordlander
**/
abstract class BaseNode
{
  protected $mode;

  protected $name;

  /**
   * @author Magnus Nordlander
   **/
  public function setIntegerMode($mode)
  {
    $this->mode = $mode;
  }

  /**
   * @author Magnus Nordlander
   **/
  public function getIntegerMode()
  {
    return $this->mode;
  }

  /**
   * @author Magnus Nordlander
   **/
  public function getOctalModeString()
  {
    return decoct($this->mode);
  }

  /**
   * @author Magnus Nordlander
   **/
  public function setName($name)
  {
    $this->name = $name;
  }

  /**
   * @author Magnus Nordlander
   **/
  public function getName()
  {
    return $this->name;
  }

  /**
   * @author Magnus Nordlander
   **/
  abstract public function getRelatedObject();
}