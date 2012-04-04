<?php

namespace Gittern\Transport;

/**
* 
*/
class RawObject
{
  const NUMERIC_TYPE_COMMIT = 0x01;
  const NUMERIC_TYPE_TREE = 0x02;
  const NUMERIC_TYPE_BLOB = 0x03;
  const NUMERIC_TYPE_TAG = 0x04;

  protected $type, $data;

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

    throw new \RuntimeException(sprintf("Numeric type 0x%x unknown", $type));
  }

  public function __construct($type, $length, $data)
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
}