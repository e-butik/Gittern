<?php

namespace Gittern\Entity\GitObject;

/**
* @author Magnus Nordlander
**/
class User
{
  protected $name;

  protected $email;

  public function __construct($name, $email)
  {
    $this->name = $name;
    $this->email = $email;
  }

  public function getName()
  {
    return $this->name;
  }

  public function getEmail()
  {
    return $this->email;
  }
}