<?php

// @codeCoverageIgnoreStart

namespace Gittern\Hydrator;

use Gittern\Transport\RawObject;

/**
* @author Magnus Nordlander
**/
interface HydratorInterface
{
  public function hydrate(RawObject $raw_object);
}