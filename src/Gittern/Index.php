<?php

namespace Gittern;

/**
* @author Magnus Nordlander
**/
class Index
{
  const SIGNATURE = 'DIRC';

  const VERSION = 2;

  protected $entries = array();

  protected $extensions = array();

  /**
   * @author Magnus Nordlander
   **/
  public function addEntry(IndexEntry $entry)
  {
    $this->entries[$entry->getName()] = $entry;
  }

  /**
   * @author Magnus Nordlander
   **/
  public function getEntries()
  {
    return array_values($this->entries);
  }

  /**
   * @author Magnus Nordlander
   **/
  public function getEntryNamed($name)
  {
    return $this->entries[$name];
  }

  /**
   * @author Magnus Nordlander
   **/
  public function removeEntryNamed($name)
  {
    if (isset($this->entries[$name]))
    {
      unset($this->entries[$name]);
      return;
    }
    throw new \OutOfBoundsException('No entry named '.$name);
  }

  /**
   * @author Magnus Nordlander
   **/
  public function getEntryNames()
  {
    return array_keys($this->entries);
  }
}