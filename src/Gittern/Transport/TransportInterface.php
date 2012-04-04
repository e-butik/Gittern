<?php

// @codeCoverageIgnoreStart

namespace Gittern\Transport;

interface TransportInterface
{
  public function resolveRawObject($sha);

  public function resolveTreeish($treeish);

  public function resolveHead($head_name);

  public function setBranch($branch, $sha);

  public function hasIndexData();

  public function getIndexData();

  public function putIndexData($data);
}