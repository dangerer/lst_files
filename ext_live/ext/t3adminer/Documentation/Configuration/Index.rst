.. include:: /Includes.rst.txt

.. _configuration:

Configuration
=============

The extension does not need any configuration to function. The type of database, the server, crendentials, language are
all fetched from the TYPO3 configuration.

There are three optional settings. Switch to the module :guilabel:`Admin Tools > Settings`.
Switch to :guilabel:`Extension Configuration` and open the section :guilabel:`t3adminer`.

.. image:: /Images/adminer-settings.png
   :alt: Extension settings for t3adminer
   :class: with-shadow
   :scale: 29

* **Access from IPs** lets you limit access by IP addresses

* **Apply devIpMask** limits access to the IPs listed in the devIpMask setting in the Install Tool. This has priority
  over the previous setting!

* **Export directory** sets the directory where exports will be saved. It defaults to the fileadmin directory. It is
  only relevant if you let exports be saved to the file system instead of downloading them directory. Make sure that
  this directory is on the local file system; Adminer doesn't know about FAL and other TYPO3 specific features.

