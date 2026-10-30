..  include:: /Includes.rst.txt

.. _start:

=========
t3adminer
=========

:Extension key:
    t3adminer

:Package name:
    jigalt/t3adminer

:Version:
    |release|

:Language:
    en

:Author:
    Jigal van Hemert

:Email:
    jigal.van.hemert@typo3.org

:License:
    This document is published under the `Open Publication License <https://www.opencontent.org/openpub/>`__.

:Rendered:
    |today|

:Copyright:
    2011-2023

----

Provides a backend module with Adminer. The module is only accessible to system maintainers. Optional settings can limit the access even more.

Adminer is a lightweight database tool which support many types of databases. The configuration from TYPO3 is used to configure Adminer and
makes the module work automatically with the main database. The styling of the TYPO3 backend is matched (as close as possible). The backend
language of the current TYPO3 user is also set as language for Adminer (if the language is supported by Adminer).

Database tools should not be used in production environments! t3adminer is meant to easily work with queries and to analyze data during development.

----

**Table of Contents:**

..  toctree::
    :maxdepth: 2
    :titlesonly:

    Introduction/Index
    Installation/Index
    Configuration/Index
    AdditionalFeatures/Index
    KnownProblems/Index
    HowToGetHelp/Index
    Changelog/CHANGELOG

..  Meta Menu

..  toctree::
    :hidden:

    Sitemap
    genindex