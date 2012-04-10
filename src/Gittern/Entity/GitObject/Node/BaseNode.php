<?php

namespace Gittern\Entity\GitObject\Node;

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
    return str_pad(decoct($this->mode), 6, "0", STR_PAD_LEFT);
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