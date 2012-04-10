<?php

// @codeCoverageIgnoreStart

namespace Gittern\Hydrator;

use Gittern\Transport\RawObject;

interface HydratorInterface
{
  public function hydrate(RawObject $raw_object);
}