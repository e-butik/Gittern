<?php

namespace Gittern\Entity\GitObject\Node;

/**
* @author Magnus Nordlander
**/
abstract class BaseNode
{
  protected $mode;

  protected $name;

  public function setIntegerMode($mode)
  {
    $this->mode = $mode;
  }

  public function getIntegerMode()
  {
    return $this->mode;
  }

  public function getOctalModeString()
  {
    return str_pad(decoct($this->mode), 6, "0", STR_PAD_LEFT);
  }

  public function setName($name)
  {
    $this->name = $name;
  }

  public function getName()
  {
    return $this->name;
  }

  abstract public function getRelatedObject();
}