<?php

namespace Gittern\GitObject;

/**
* @covers Gittern\GitObject\User
*/
class UserTest extends \PHPUnit_Framework_TestCase
{
  public function setUp()
  {
    $this->user = new User("Foo Bar", "foo.bar@example.com");
  }

  public function testCanBeConstructed()
  {
  }

  public function testCanGetName()
  {
    $this->assertEquals("Foo Bar", $this->user->getName());
  }

  public function testCanGetEmail()
  {
    $this->assertEquals("foo.bar@example.com", $this->user->getEmail());
  }
}