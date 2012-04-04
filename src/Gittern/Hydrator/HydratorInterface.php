<?php

// @codeCoverageIgnoreStart

namespace Gittern\Hydrator;

interface HydratorInterface
{
  public function hydrate($object_sha, $data);
}