# Gittern
Making Git like music for PHP's ears. [![Build Status](https://secure.travis-ci.org/e-butik/Gittern.png)](http://travis-ci.org/e-butik/Gittern)

Version 0.8 under development (because starting out at 0.1 or 1.0 is for squares)

Documentation is available at [Read the Docs](http://gittern.readthedocs.org/).

## What is Gittern?
Gittern is a PHP library for reading from and writing to Git repositories. It doesn't depend on the ```git``` binary, it directly acccesses the repo files.

Gittern provides several interfaces for interacting with your Git repos. Firstly, there's a low level interface, where you manually create blobs, trees and commits. This can however become cumbersome very quickly. As such, there's also two different Gaufrette adapters included.

The first one, ```GitternCommitishReadOnlyAdapter``` is pretty much what it sounds like. It's an adapter to which you supply a commitish, and from which you may read the files associated with that commit.

The second one, ```GitternIndexAdapter``` allows you to read from and write to the Git index. The Git index is the staging area in which you stage new changes for a commit. Then, when you're ready, creating a commit from the index is quite simple. Just get the tree from the index, and use it to create your commit.

In addition to all of this functionality, Gittern is created with extensibility in mind. How Gittern reads data from the repository on disk is cleanly separated using the adapter pattern, so that if you have the need you could easily implement e.g. an adapter capable of maintaining multiple indexes, or Git object caching in something like MongoDB or Redis (in fact, if you don't care about accessing the repo from the git binary, you could just use this as a fast, distributed backend).

## Who's behind all this?
E-butik.se, one of Sweden's foremost e-commerce platform providers. Visit our [developer blog](http://developer.e-butik.se).

## How do I install Gittern?
Use [Composer](http://getcomposer.org/). More detailed instructions are available in the docs.

## How stable is the API?
Not very. We're still refactoring the API pretty much whenever we feel like it. What is semi-stable though is that the Gaufrette adapters will remain compatible with the Gaufrette master branch, so if you're mainly using the Gaufrette adapters, you'll not experience a lot of API breakage.

## Kinda-sorta bugs
* The index flags field (see http://opensource.apple.com/source/Git/Git-26/src/git-htmldocs/technical/index-format.txt) contains an assume-valid flag that's not represented
* The file name length of the flags field should only be written in full if it's less than 0xFFF.
* The stage flag isn't desiccated properly.
* Indexes in conflict (i.e. with stage > 0, see http://opensource.apple.com/source/Git/Git-26/src/git-htmldocs/technical/index-format.txt) should be handled somehow, even if just by an exception.
* The mtime of a file in GitternCommitishReadOnlyAdapter is always the same as for the commit, regardless of whether the file has been changed in the given commit or not.

## Planned features
There are several planned features, which didn't make it in to version 0.8.

* Subclassing Repository with a git binary dependent subclass to allow some advanced commands
* Packfile creation (currently packfiles can only be read)
* Support for resolving lightweight tags
* Support for reading annotated tags
* Support for the link tree entry type
* Support for the commit tree entry type (i.e. submodules)
* Making the Gittern\Entity\GitObject\User class into an interface
* Support for all kinds of [Git Treeishes](http://book.git-scm.com/4_git_treeishes.html)
* Support for updating the reflog when moving a branch head