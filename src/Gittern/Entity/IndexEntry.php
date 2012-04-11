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

  public function setCtime($ctime)
  {
    $this->ctime = $ctime;
  }

  public function getCtime()
  {
    return $this->ctime;
  }

  public function setMtime($mtime)
  {
    $this->mtime = $mtime;
  }

  public function getMtime()
  {
    return $this->mtime;
  }

  public function setDev($dev)
  {
    $this->dev = $dev;
  }

  public function getDev()
  {
    return $this->dev;
  }

  public function setInode($inode)
  {
    $this->inode = $inode;
  }

  public function getInode()
  {
    return $this->inode;
  }

  public function setMode($mode)
  {
    $this->mode = $mode;
  }

  public function getMode()
  {
    return $this->mode;
  }

  public function setUid($uid)
  {
    $this->uid = $uid;
  }

  public function getUid()
  {
    return $this->uid;
  }

  public function setGid($gid)
  {
    $this->gid = $gid;
  }

  public function getGid()
  {
    return $this->gid;
  }

  public function setFileSize($size)
  {
    $this->file_size = $size;
  }

  public function getFileSize()
  {
    return $this->file_size;
  }

  public function setBlob(Blob $blob)
  {
    $this->blob = $blob;
  }

  public function getBlob()
  {
    return $this->blob;
  }

  public function setStage($stage)
  {
    $this->stage = $stage;
  }

  public function getStage()
  {
    return $this->stage;
  }

  public function setName($name)
  {
    $this->name = $name;
  }

  public function getName()
  {
    return $this->name;
  }
}