<?php

namespace Gittern\Iterator;

use Gittern\GitObject\Node\TreeNode;

/**
* @author Magnus Nordlander
**/
class RecursiveTreeIterator extends \ArrayIterator implements \RecursiveIterator
{
  protected $key_base = false;

  /**
   * @author Magnus Nordlander
   **/
  public function setKeyBase($key_base)
  {
    $this->key_base = $key_base;
  }

  /**
   * @author Magnus Nordlander
   **/
  public function hasChildren()
  {
    if ($this->current() instanceof TreeNode)
    {
      return true;
    }
    return false;
  }

  /**
   * @author Magnus Nordlander
   **/
  public function getChildren()
  {
    if ($this->hasChildren())
    {
      $iter = $this->current()->getTree()->getIterator();
      if ($iter instanceof RecursiveTreeIterator)
      {
        $iter->setKeyBase($this->key());
      }
      
      return $iter;
    }
    return null;
  }

  /**
   * @author Magnus Nordlander
   **/  
  public function key()
  {
    return ($this->key_base ? $this->key_base.'/'.parent::key() : parent::key());
  }
}