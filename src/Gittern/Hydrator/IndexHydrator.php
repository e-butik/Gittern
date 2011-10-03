<?php

namespace Gittern\Hydrator;

use Gittern\Repository;
use Gittern\Index;
use Gittern\IndexEntry;
use Gittern\Proxy\BlobProxy;

use Zend_Io_Reader;
use Zend_Io_StringReader;

/**
* @author Magnus Nordlander
**/
class IndexHydrator
{
  protected $repo;

  /**
   * @author Magnus Nordlander
   **/
  public function __construct(Repository $repo)
  {
    $this->repo = $repo;
  }

  /**
   * @author Magnus Nordlander
   **/
  public function hydrate($data)
  {
    $index = new Index;

    $reader = new Zend_Io_StringReader($data);

    $signature = $reader->readString8(4);

    $version = $reader->readUInt32BE();

    if ($signature != Index::SIGNATURE || $version != Index::VERSION)
    {
      throw new \Exception("IndexHydrator only supports indexes with signature ".Index::SIGNATURE." and version ".Index::VERSION);
    }

    $entries = $reader->readUInt32BE();

    $extended_contents_start = $reader->getOffset();

    for ($i=0; $i < $entries; $i++) 
    { 
      $entry = new IndexEntry();

      $start = $reader->getOffset();

      $entry->setCtime($this->readEntryTime($reader));
      $entry->setMtime($this->readEntryTime($reader));
      $entry->setDev($reader->readUInt32BE());
      $entry->setInode($reader->readUInt32BE());
      $entry->setMode($reader->readUInt32BE());
      $entry->setUid($reader->readUInt32BE());
      $entry->setGid($reader->readUInt32BE());
      $entry->setFileSize($reader->readUInt32BE());

      $entry->setBlob(new BlobProxy($this->repo, $reader->readHHex(20)));

      $flags = $reader->readUInt16BE();

      $entry->setName(rtrim($reader->readString16(($flags & 0x0FFF) + 1, $foo, true), "\0")); //+1 is to capture mandatory NUL
      $entry->setStage(($flags & 0x3000) >> 12);

      $stop = $reader->getOffset();

      $length = $stop-$start;
      $padded_length = ceil($length/8)*8;

      $reader->setOffset($start+$padded_length);

      $index->addEntry($entry);
    }

    while ($reader->getOffset() < ($reader->getSize() - 20))
    {
      $name = $reader->readString8(4);
      if (ord($name[0]) >= 0x41 && ord($name[0]) <= 0x5a)
      {
        // Optional extension, just skip it
        $data_size = $reader->readUInt32BE();
        $reader->skip($data_size);
      }
      else
      {
        throw new \Exception("IndexHydrator doesn't support the mandatory ".$name." extension");
      }
    }

    $extended_contents_stop = $reader->getOffset();

    $reader->setOffset(0);
    $hash = hash_init('sha1');
    hash_update($hash, $reader->readString8($extended_contents_stop));
    $calculated_sha = hash_final($hash, false);
//    $calculated_sha = sha1();

    $checksum = $reader->readHHex(20);

    var_dump($calculated_sha);
    var_dump($checksum);

/*//    $reader->setOffset($extended_contents_start);
    $reader->setOffset(0);

    $length = $extended_contents_stop;//-$extended_contents_start;

    $calculated_sha = sha1($reader->readString8($length));

    if ($checksum != $calculated_sha)
    {
      var_dump("Checksum fail");
    }*/

    return $index;
  }

  /**
   * @author Magnus Nordlander
   **/
  public function readEntryTime(Zend_Io_Reader $reader)
  {
    $time_lsb32 = $reader->readInt32BE();
    $time_nsec = $reader->readUInt32BE();

    $time = (double)$time_lsb32 + (double)("0.".$time_nsec);

    return $time;
  }
}