<?php

namespace Gittern;

use Gaufrette\Adapter;

/**
* @author Magnus Nordlander
**/
class GitternIndexAdapter implements Adapter
{
  protected $repo;
  protected $index;

  /**
   * @author Magnus Nordlander
   **/
  public function __construct(Repository $repo)
  {
    $this->repo = $repo;
    $this->index = $repo->getIndex();
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
    $entry = $this->index->getEntryNamed($key);

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
  public function write($key, $content)
  {
    throw new \RuntimeException("This adapter is read-only.");
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
    return $this->index->getEntryNames();
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
    $entry = $this->index->getEntryNamed($key);

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
    throw new \RuntimeException("This adapter is read-only.");
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
    throw new \RuntimeException("This adapter is read-only.");
  }
}