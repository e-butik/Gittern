=======
Gittern
=======

Making Git like music for PHP's ears.

Contents:

.. toctree ::
   :maxdepth: 2

   usage/index
   technical-docs/index

What is Gittern
---------------
Gittern is a PHP library for reading from and writing to Git repositories. It doesn't depend on the ``git`` binary, it directly acccesses the repo files.

Gittern provides several interfaces for interacting with your Git repos. Firstly, there's a low level interface, where you manually create blobs, trees and commits. This can however become cumbersome very quickly. As such, there's also two different Gaufrette adapters included.

The first one, ``GitternCommitishReadOnlyAdapter`` is pretty much what it sounds like. It's an adapter to which you supply a commitish, and from which you may read the files associated with that commit.

The second one, ``GitternIndexAdapter`` allows you to read from and write to the Git index. The Git index is the staging area in which you stage new changes for a commit. Then, when you're ready, creating a commit from the index is quite simple. Just get the tree from the index, and use it to create your commit.

In addition to all of this functionality, Gittern is created with extensibility in mind. How Gittern reads data from the repository on disk is cleanly separated using the adapter pattern, so that if you have the need you could easily implement e.g. an adapter capable of maintaining multiple indexes, or Git object caching in something like MongoDB or Redis (in fact, if you don't care about accessing the repo from the git binary, you could just use this as a fast, distributed backend).

Installation
------------
Gittern is installed via `Composer <http://getcomposer.org/>`_. It's available on Packagist as ``e-butik/gittern``.

Create composer.json file in the project root:

::

  {
      "require": {
          "e-butik/gittern": "*"
      }
  }

Then download composer.phar and run the install command:

::

  $ wget --quiet http://getcomposer.org/composer.phar
  $ php composer.phar install

If you want to be able to run the test suite, add ``--install-suggests`` to the install command.


.. Contents:

   .. toctree ::
      :maxdepth: 2



.. Indices and tables
   ==================

   * :ref:`genindex`
   * :ref:`modindex`
   * :ref:`search`

