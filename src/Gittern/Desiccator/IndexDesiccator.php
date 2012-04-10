<?php

namespace Gittern\Desiccator;

use Gittern\Repository;
use Gittern\Entity\Index;
use Gittern\Entity\IndexEntry;
use Gittern\Proxy\BlobProxy;

use Iodophor\Io\StringWriter;
use Iodophor\Io\Writer;

/**
* @author Magnus Nordlander
**/
class IndexDesiccator
{
  /**
   * @author Magnus Nordlander
   **/
  public function desiccate(Index $index)
  {
    $writer = new StringWriter();

    $entries = $index->getEntries();

    $writer->writeString8(Index::SIGNATURE);
    $writer->writeUInt32BE(Index::VERSION);
    $writer->writeUInt32BE(count($entries));

    $contents_offset = $writer->getOffset();

    usort($entries, function($a, $b)
    {
      $cmp = strcmp($a->getName(), $b->getName());
      if ($cmp == 0) 
      {
        return ($a->getStage() - $b->getStage());
      }
      return $cmp;
    });

    foreach ($entries as $entry) 
    {
      $start = $writer->getOffset();

      $this->writeEntryTime($writer, $entry->getCtime());
      $this->writeEntryTime($writer, $entry->getMtime());
      $writer->writeUInt32BE($entry->getDev());
      $writer->writeUInt32BE($entry->getInode());
      $writer->writeUInt32BE($entry->getMode());
      $writer->writeUInt32BE($entry->getUid());
      $writer->writeUInt32BE($entry->getGid());
      $writer->writeUInt32BE($entry->getFileSize());
      $writer->writeHHex($entry->getBlob()->getSha());

      //FIXME: Stage
      $writer->writeUInt16BE(strlen($entry->getName()));

      $stop = $writer->getOffset();

      $length = ($stop-$start)+strlen($entry->getName())+1; //We need at least 1 NUL
      $padded_length = ceil($length/8)*8;

      $rest_length = $padded_length-$length;

      $writer->writeString8($entry->getName(), strlen($entry->getName())+1+$rest_length);
    }

    $writer->writeHHex(sha1($writer->toString()));

    return $writer->toString();
  }

    /**
   * @author Magnus Nordlander
   **/
  public function writeEntryTime(Writer $writer, $time)
  {
    $writer->writeInt32BE((int)$time);
    //FIXME
    $writer->writeUInt32BE(0);
  }
}