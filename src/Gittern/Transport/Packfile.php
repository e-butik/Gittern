<?php

namespace Gittern\Transport;

use Zend_Io_Reader as Reader;

/**
* 
*/
class Packfile
{
  protected $reader;

  protected $index;

  const OBJ_COMMIT = 0x01;
  const OBJ_TREE = 0x02;
  const OBJ_BLOB = 0x03;
  const OBJ_TAG = 0x04;
  const OBJ_OFS_DELTA = 0x06;
  const OBJ_REF_DELTA = 0x07;

  public function __construct(Reader $reader)
  {
    $this->reader = $reader;
  }

  public function setIndex(PackfileIndex $index)
  {
    $this->index = $index;
  }

  public function getIndex()
  {
    return $this->index;
  }

  public function hasSha($sha)
  {
    if ($this->index)
    {
      return $this->index->hasSha($sha);
    }
    else
    {
      throw new \LogicException("hasSha without index is not implemented yet.");
    }
  }

  public function getRawObjectForSha($sha)
  {
    if ($this->index)
    {
      $offset = $this->index->getPackfileOffsetForSha($sha);
      return $this->getRawObjectAtOffset($offset);
    }
    else
    {
      throw new \LogicException("getData without index is not implemented yet.");
    }
  }

  /**
   * @see http://git.rsbx.net/Documents/Git_Data_Formats.txt
   * @see http://www.opensource.apple.com/source/Git/Git-17/src/git-htmldocs/technical/pack-format.txt
   **/
  public function getRawObjectAtOffset($offset)
  {
    $this->reader->setOffset($offset);

    $header_part = $this->reader->readUInt8();
    $type = ($header_part >> 4) & 0x07;

    $size = $header_part & 0x0F;

    for ($size_shift_offset=4; $header_part & 0x80; $size_shift_offset += 7) 
    { 
      $header_part = $this->reader->readUInt8();
      $size |= (($header_part & 0x7F) << $size_shift_offset);
    }

    if ($type == self::OBJ_REF_DELTA)
    {
      $sha = $this->reader->readHHex(20);

      $base_object = $this->getRawObjectForSha($sha);
      $type = $base_object->getType();

      $delta = gzuncompress($this->reader->read($size+512), $size);

      $data = $this->patchDelta($delta, $base_object);
    }
    else if ($type == self::OBJ_OFS_DELTA)
    {
      $base_offset_part = $this->reader->readUInt8();
      $base_offset = $base_offset_part & 0x7F;
  
      while ($base_offset_part & 0x80)
      { 
        $base_offset_part = $this->reader->readUInt8();
        $base_offset += 1;
        $base_offset = $base_offset << 7;
        $base_offset |= $base_offset_part & 0x7F;
      }

      $base_offset = $offset - $base_offset;

      $delta = gzuncompress($this->reader->read($size+512), $size);

      $base_object = $this->getRawObjectAtOffset($base_offset);
      $type = $base_object->getType();

      $data = $this->patchDelta($delta, $base_object);
    }
    else
    {
      $data = gzuncompress($this->reader->read($size+512), $size);
    }

    return new RawObject($type, $size, $data);
  }

  protected function patchDelta($delta, RawObject $base_object)
  {
    $base = $base_object->getData();

    list($src_size, $pos) = $this->patchDeltaHeaderSize($delta, 0);

    if ($src_size != strlen($base))
    {
      throw new \RuntimeException("Packfile delta is invalid");
    }

    list($dest_size, $pos) = $this->patchDeltaHeaderSize($delta, $pos);
    $dest = "";
    while ($pos < strlen($delta)) 
    {
      $c = ord($delta[$pos++]);
      if ($c & 0x80)
      {
        $cp_off = $cp_size = 0;
        if ($c & 0x01)
        {
          $cp_off = ord($delta[$pos++]);           
        }
        if ($c & 0x02)
        {
          $cp_off |= ord($delta[$pos++]) << 8;
        }
        if ($c & 0x04)
        {
          $cp_off |= ord($delta[$pos++]) << 16;
        }
        if ($c & 0x08)
        {
          $cp_off |= ord($delta[$pos++]) << 24;
        }

        if ($c & 0x10) 
        {
          $cp_size = ord($delta[$pos++]);
        }
        if ($c & 0x20) 
        {
          $cp_size |= ord($delta[$pos++]) << 8;
        }
        if ($c & 0x40) 
        {
          $cp_size |= ord($delta[$pos++]) << 16;
        }

        if ($cp_size == 0)
        {
          $cp_size = 0x10000;          
        }
        $dest .= substr($base, $cp_off, $cp_size);
      }
      elseif ($c != 0)
      {
        $dest .= substr($delta, $pos, $c);
        $pos += $c;
      }
      else
      {
        throw new \RuntimeException("Packfile delta is invalid");
      }
    }

    return $dest;
  }

  public function patchDeltaHeaderSize($delta, $pos)
  {
    $size = 0;
    $shift = 0;
    do
    {
      $c = ord($delta[$pos]);
      if (!$c)
      {
        throw new \RuntimeException("Packfile delta header is invalid");
      }
      $pos++;
      $size |= (($c & 0x7f) << $shift);
      $shift += 7;
    } while ($c & 0x80);
    return array($size, $pos);
  }
}