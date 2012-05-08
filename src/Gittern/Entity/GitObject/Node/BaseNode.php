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

  public function equals(BaseNode $node)
  {
    if ($this->getIntegerMode() === $node->getIntegerMode() && $this->getName() === $node->getName())
    {
      $our_related = $this->getRelatedObject();
      $theirs_related = $node->getRelatedObject();

      // When the objects are new, or both proxies or non-proxies
      if ($our_related == $theirs_related)
      {
        return true;
      }
      else
      {
        // When at least one of the objects are proxies
        if ($our_related->getSha() != "" && $theirs_related->getSha() != "")
        {
          // SHA string comparison has to be with ===
          return $our_related->getSha() === $theirs_related->getSha();
        }
      }
    }

    return false;
  }

  abstract public function getRelatedObject();
}