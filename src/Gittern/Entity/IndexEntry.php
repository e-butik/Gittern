<?php

namespace Gittern\Entity;

use Gittern\Entity\GitObject\Node\BlobNode;
use Gittern\Entity\GitObject\Blob;

/**
* @author Magnus Nordlander
**/
class IndexEntry
{
  protected $ctime;

  protected $mtime;

  protected $dev;

  protected $inode;

  protected $mode;

  protected $uid;

  protected $gid;

  protected $file_size;

  protected $blob;

  protected $stage;

  protected $name;

  public static function createFromBlobNode(BlobNode $node)
  {
    $entry = new IndexEntry();
    $entry->setBlob($node->getBlob());
    $entry->setMode($node->getIntegerMode());
    return $entry;
  }

  public function createBlobNode()
  {
    $blob_node = new BlobNode;
    $blob_node->setBlob($this->getBlob());
    $blob_node->setIntegerMode($this->getMode());
    return $blob_node;
  }

  /**
   * @author Magnus Nordlander
   **/
  public function setCtime($ctime)
  {
    $this->ctime = $ctime;
  }

  /**
   * @author Magnus Nordlander
   **/
  public function getCtime()
  {
    return $this->ctime;
  }

  /**
   * @author Magnus Nordlander
   **/
  public function setMtime($mtime)
  {
    $this->mtime = $mtime;
  }

  /**
   * @author Magnus Nordlander
   **/
  public function getMtime()
  {
    return $this->mtime;
  }

  /**
   * @author Magnus Nordlander
   **/
  public function setDev($dev)
  {
    $this->dev = $dev;
  }

  /**
   * @author Magnus Nordlander
   **/
  public function getDev()
  {
    return $this->dev;
  }

  /**
   * @author Magnus Nordlander
   **/
  public function setInode($inode)
  {
    $this->inode = $inode;
  }

  /**
   * @author Magnus Nordlander
   **/
  public function getInode()
  {
    return $this->inode;
  }

  /**
   * @author Magnus Nordlander
   **/
  public function setMode($mode)
  {
    $this->mode = $mode;
  }

  /**
   * @author Magnus Nordlander
   **/
  public function getMode()
  {
    return $this->mode;
  }

  /**
   * @author Magnus Nordlander
   **/
  public function setUid($uid)
  {
    $this->uid = $uid;
  }

  /**
   * @author Magnus Nordlander
   **/
  public function getUid()
  {
    return $this->uid;
  }

  /**
   * @author Magnus Nordlander
   **/
  public function setGid($gid)
  {
    $this->gid = $gid;
  }

  /**
   * @author Magnus Nordlander
   **/
  public function getGid()
  {
    return $this->gid;
  }

  /**
   * @author Magnus Nordlander
   **/
  public function setFileSize($size)
  {
    $this->file_size = $size;
  }

  /**
   * @author Magnus Nordlander
   **/
  public function getFileSize()
  {
    return $this->file_size;
  }

  /**
   * @author Magnus Nordlander
   **/
  public function setBlob(Blob $blob)
  {
    $this->blob = $blob;
  }

  /**
   * @author Magnus Nordlander
   **/
  public function getBlob()
  {
    return $this->blob;
  }

  /**
   * @author Magnus Nordlander
   **/
  public function setStage($stage)
  {
    $this->stage = $stage;
  }

  /**
   * @author Magnus Nordlander
   **/
  public function getStage()
  {
    return $this->stage;
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
}