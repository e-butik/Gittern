<?php

namespace Gittern\Gaufrette;

use Gittern\Entity\IndexEntry;
use Gittern\Repository;
use Gittern\Entity\GitObject\Blob;

use Gaufrette\Adapter as AdapterInterface;
use Gaufrette\Adapter\ChecksumCalculator;

/**
* @author Magnus Nordlander
**/
class GitternIndexAdapter implements AdapterInterface, ChecksumCalculator
{
  protected $repo;
  protected $autoflush = true;

  public function __construct(Repository $repo, $autoflush = true)
  {
    $this->repo = $repo;
    $this->autoflush = $autoflush;
  }

  protected function getIndex()
  {
    return $this->repo->getIndex();
  }

  public function flushIfSupposedTo()
  {
    if ($this->autoflush)
    {
      $this->repo->flush();
    }
  }

  public function read($key)
  {
    $entry = $this->getIndex()->getEntryNamed($key);

    if ($entry)
    {
      return $entry->getBlob()->getContents();
    }

    throw new \RuntimeException(sprintf('Could not read the \'%s\' file.', $key));
  }

  public function write($key, $content, array $metadata = null)
  {
    $blob = new Blob();

    $blob->setContents($content);

    $this->repo->desiccateGitObject($blob);

    $entry = new IndexEntry();

    $entry->setCtime(time().".0");
    $entry->setMtime(time().".0");
    $entry->setDev(0);
    $entry->setInode(0);
    $entry->setMode(0100644);
    $entry->setUid(0);
    $entry->setGid(0);
    $entry->setFileSize(strlen($content));

    $entry->setBlob($blob);

    $entry->setName($key);
    $entry->setStage(0);

    $this->getIndex()->addEntry($entry);

    $this->flushIfSupposedTo();
  }

  public function exists($key)
  {
    return array_search($key, $this->keys()) !== false;
  }

  public function keys()
  {
    return $this->getIndex()->getEntryNames();
  }

  public function mtime($key)
  {
    $entry = $this->getIndex()->getEntryNamed($key);

    if ($entry)
    {
      return (int)$entry->getMtime();
    }

    throw new \RuntimeException(sprintf('Could not read the \'%s\' file.', $key));
  }

  public function checksum($key)
  {
    $entry = $this->getIndex()->getEntryNamed($key);

    if ($entry)
    {
      return $entry->getBlob()->getSha();
    }

    throw new \RuntimeException(sprintf('Could not read the \'%s\' file.', $key));
  }

  public function delete($key)
  {
    $this->getIndex()->removeEntryNamed($key);
    $this->flushIfSupposedTo();
  }

  public function rename($key, $new)
  {
    $entry = $this->getIndex()->getEntryNamed($key);

    if ($entry)
    {
      $entry->setName($new);
      $this->getIndex()->removeEntryNamed($key);
      $this->getIndex()->addEntry($entry);
      $this->flushIfSupposedTo();
      return;
    }

    throw new \RuntimeException(sprintf('Could not read the \'%s\' file.', $key));
  }

  public function isDirectory($directory_key)
  {
    $directory_key = trim($directory_key, '/');
    foreach ($this->getKeys() as $key)
    {
      if (strpos($key, $directory_key.'/') === 0)
      {
        return true;
      }
    }

    return false;
  }

  public function supportsMetadata()
  {
    return true;
  }
}