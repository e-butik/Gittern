<?php

// @codeCoverageIgnoreStart

namespace Gittern\Transport;

/**
* @author Magnus Nordlander
**/
interface TransportInterface
{
  public function fetchRawObject($sha);

  public function putRawObject(RawObject $raw_object);

  public function resolveTreeish($treeish);

  public function resolveTag($tag_name);

  public function resolveHead($head_name);

  public function setBranch($branch, $sha);

  public function removeBranch($branch);

  public function hasIndexData();

  public function getIndexData();

  public function putIndexData($data);
}
