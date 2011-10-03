<?php

namespace Gittern;

use Gaufrette\Adapter;

/**
* @author Magnus Nordlander
**/
class GitternIndexAdapter implements Adapter
{
  protected $repo;
  protected $autoflush = true;

  /**
   * @author Magnus Nordlander
   **/
  public function __construct(Repository $repo, $autoflush = true)
  {
    $this->repo = $repo;
    $this->autoflush = $autoflush;
  }

  /**
   * @author Magnus Nordlander
   **/
  public function flushIfSupposedTo()
  {
    if ($this->autoflush)
    {
      $this->repo->flush();      
    }
  }

  /**
   * Reads the content of the file
   *
   * @param  string $key
   *
   * @return string
   */
  public function read($key)
  {
    $entry = $this->repo->getIndex()->getEntryNamed($key);

    if ($entry)
    {
      return $entry->getBlob()->getContents();
    }

    throw new \RuntimeException(sprintf('Could not read the \'%s\' file.', $key));
  }

  /**
   * Writes the given content into the file
   *
   * @param  string $key
   * @param  string $content
   *
   * @return integer The number of bytes that were written into the file
   *
   * @throws RuntimeException on failure
   */
  public function write($key, $content, array $metadata = null)
  {
    $blob = new GitObject\Blob();

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

    $this->repo->getIndex()->addEntry($entry);

    $this->flushIfSupposedTo();
  }


  /**
   * Indicates whether the file exists
   *
   * @param  string $key
   *
   * @return boolean
   */
  public function exists($key)
  {
    return array_search($key, $this->keys()) !== false;
  }

  /**
   * Returns an array of all keys matching the specified pattern
   *
   * @return array
   */
  public function keys()
  {
    return $this->repo->getIndex()->getEntryNames();
  }

  /**
   * Returns the last modified time
   *
   * @param  string $key
   *
   * @return integer An UNIX like timestamp
   */
  public function mtime($key)
  {
    $entry = $this->repo->getIndex()->getEntryNamed($key);

    if ($entry)
    {
      return (int)$entry->getMtime();
    }

    throw new \RuntimeException(sprintf('Could not read the \'%s\' file.', $key));
  }

  /**
   * Returns the checksum of the file
   *
   * @param  string $key
   *
   * @return string
   */
  public function checksum($key)
  {
    $file = $this->read($key);
    return md5($file);
  }

  /**
   * Deletes the file
   *
   * @param  string $key
   *
   * @throws RuntimeException on failure
   */
  public function delete($key)
  {
    $this->repo->getIndex()->removeEntryNamed($key);
    $this->flushIfSupposedTo();
  }

  /**
   * Renames a file
   *
   * @param string $key
   * @param string $new
   *
   * @throws RuntimeException on failure
   */
  public function rename($key, $new)
  {
    $entry = $this->repo->getIndex()->getEntryNamed($key);

    if ($entry)
    {
      $entry->setName($new);
      $this->repo->getIndex()->removeEntryNamed($key);
      $this->repo->getIndex()->addEntry($entry);
      $this->flushIfSupposedTo();
      return;
    }

    throw new \RuntimeException(sprintf('Could not read the \'%s\' file.', $key));
  }

  public function supportsMetadata()
  {
    return true;
  }
}