.. include:: /Includes.rst.txt

.. _introduction:

Introduction
============

During development or while debugging it's often useful to access the database in a comfortable way.
Adminer is a pretty compact (a single PHP script of a few hundred KB, including all language texts, CSS, JS) solution.
It supports a wide range of database systems (at least the ones that TYPO3 supports) and contains labels in quite a few
languages. The t3adminer extension embeds Adminer in a backend modules. The styling of the TYPO3 backend is copied as
much as possible.

The module is only available to system maintainers. So, even normal Admin user can't use it. The adminer script is
present in the extension as a txt file. This makes it very unlikely that it can be run directly as a PHP file and since
Adminer is Open Source it is no issue that the contents can be seen.

.. image:: /Images/adminer-start.png
   :alt: Starting screen of adminer
   :class: with-shadow
   :scale: 29