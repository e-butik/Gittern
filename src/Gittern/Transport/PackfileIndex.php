<?php

namespace Gittern\Transport;

use Iodophor\Io\Reader;

/**
* @author Magnus Nordlander
**/
class PackfileIndex
{
  protected $reader;

  protected $size;

  const HEADER_SIZE = 8;

  public function __construct(Reader $reader)
  {
    $this->reader = $reader;
  }

  protected function readFanoutForPrefix($prefix)
  {
    $this->reader->setOffset(self::HEADER_SIZE + $prefix * 4);
    return $this->reader->readUInt32BE();
  }

  public function getSize()
  {
    if (!$this->size)
    {
      $this->size = $this->readFanoutForPrefix(0xFF);
    }
    return $this->size;
  }

  protected function getShasStart()
  {
    return self::HEADER_SIZE + 0x100*4;
  }

  protected function getShasStop()
  {
    return $this->getShasStart() + $this->getSize()*20;
  }

  protected function getCrcsStart()
  {
    return $this->getShasStop();
  }

  protected function getCrcsStop()
  {
    return $this->getCrcsStart() + $this->getSize() * 4;
  }

  protected function getSmallPackfileOffsetsStart()
  {
    return $this->getCrcsStop();
  }

  public function getShas()
  {
    $start = $this->getShasStart();
    $stop = $this->getShasStop();
    $shas = array();

    $this->reader->setOffset($start);
    while ($this->reader->getOffset() < $stop)
    {
      $shas[] = $this->reader->readHHex(20);
    }

    return $shas;
  }

  protected function getStartOffsetForPrefix($prefix)
  {
    if ($prefix == 0)
    {
      return $this->getShasStart();
    }
    else
    {
      return $this->getStopOffsetForPrefix($prefix - 1);
    }
  }

  protected function getStopOffsetForPrefix($prefix)
  {
    $fanout = $this->readFanoutForPrefix($prefix);
    return $this->getShasStart() + ($fanout * 20);
  }

  protected function getStartCounterForPrefix($prefix)
  {
    if ($prefix == 0)
    {
      return 0;
    }
    else
    {
      return $this->readFanoutForPrefix($prefix-1);
    }
  }

  protected function getOffsetForSha($sha)
  {
    $prefix = hexdec(substr($sha, 0, 2));

    $start = $this->getStartOffsetForPrefix($prefix);
    $stop = $this->getStopOffsetForPrefix($prefix);

    $counter = $this->getStartCounterForPrefix($prefix);
    $this->reader->setOffset($start);
    do {
      $read_sha = $this->reader->readHHex(20);
      if ($read_sha == $sha)
      {
        return $counter;
      }

      $read_prefix = hexdec(substr($read_sha, 0, 2));
      $counter++;
    } while ($read_prefix <= $prefix && $this->reader->getOffset() < $stop);

    throw new \RuntimeException("SHA $sha is not in packfile index");
  }

  public function getCrcForSha($sha)
  {
    $offset = $this->getCrcsStart() + $this->getOffsetForSha($sha)*4;
    $this->reader->setOffset($offset);
    return $this->reader->readHHex(4);
  }

  public function getPackfileOffsetForSha($sha)
  {
    $offset = $this->getSmallPackfileOffsetsStart() + $this->getOffsetForSha($sha)*4;
    $this->reader->setOffset($offset);
    return $this->reader->readUInt32BE(4);
  }

  public function hasSha($sha)
  {
    $shas = $this->getShas();

    return in_array($sha, $shas);
  }
}