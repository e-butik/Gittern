<?php

namespace Gittern\Transport;

use Gittern\Exception\InvalidTypeException;

/**
* @author Magnus Nordlander
**/
class RawObject
{
  const NUMERIC_TYPE_COMMIT = 0x01;
  const NUMERIC_TYPE_TREE = 0x02;
  const NUMERIC_TYPE_BLOB = 0x03;
  const NUMERIC_TYPE_TAG = 0x04;

  protected $type, $data, $sha;

  protected function convertNumericType($type)
  {
    switch ($type)
    {
      case self::NUMERIC_TYPE_COMMIT:
        return 'commit';
      case self::NUMERIC_TYPE_TREE:
        return 'tree';
      case self::NUMERIC_TYPE_BLOB:
        return 'blob';
      case self::NUMERIC_TYPE_TAG:
        return 'tag';
    }

    throw new InvalidTypeException(sprintf("Numeric type 0x%x unknown", $type));
  }

  public function __construct($type, $data)
  {
    if (is_numeric($type))
    {
      $this->type = $this->convertNumericType($type);
    }
    else
    {
      $this->type = $type;
    }
    $this->data = $data;
  }

  public function getType()
  {
    return $this->type;
  }

  public function getData()
  {
    return $this->data;
  }

  public function getSha()
  {
    if (!$this->sha)
    {
      $this->sha = sha1($this->type.' '.$this->getLength()."\0".$this->data);
    }
    return $this->sha;
  }

  public function getLength()
  {
    return strlen($this->data);
  }
}