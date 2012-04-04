<?php

namespace Gittern\Transport;

use Iodophor\Io\Reader;

/**
* 
*/
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

  protected function getOffsetForSha($sha)
  {
    $prefix = hexdec(substr($sha, 0, 2));
    $index = $this->readFanoutForPrefix($prefix)-1;

    $shas_start = $this->getShasStart();
    $start = $shas_start + $index*20;
    $shas_stop = $this->getShasStop();

    $counter = $index;
    $this->reader->setOffset($start);
    do {
      $read_sha = $this->reader->readHHex(20);
      if ($read_sha == $sha)
      {
        return $counter;
      }

      $read_prefix = hexdec(substr($read_sha, 0, 2));
      $counter++;
    } while ($read_prefix <= $prefix && $this->reader->getOffset() < $shas_stop);

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