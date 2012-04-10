<?php

namespace Gittern\Entity\GitObject;

/**
* @author Magnus Nordlander
**/
class User
{
  protected $name;

  protected $email;

  /**
   * @author Magnus Nordlander
   **/
  public function __construct($name, $email)
  {
    $this->name = $name;
    $this->email = $email;
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
  public function getEmail()
  {
    return $this->email;
  }
}