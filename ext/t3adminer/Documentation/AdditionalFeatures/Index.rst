.. include:: /Includes.rst.txt

.. _additionalfeatures:

Additional Features
===================

Adminer also has a mechanism to load plugins. Several have been used/added/modified to provide a better experience.
People who are familiar with Adminer itself might worry about some of the characteristics of Adminer.

Version not verified
--------------------

Adminer originally checked if the version was still the latest. This required the script to "call home". In order to
enhance privacy a plugin was added to disable this check completely. Since adminer was embedded in the extension there
was no way to directly update Adminer itself without updating the extension.

Links not directed via adminer.org
----------------------------------

Adminer provided a feature that would redirect links through adminer.org. The idea was that adminer.org could be trusted
and external sites would only see requests from adminer.org. This would also the requested URL to that site and to
enhance privacy this plugin disables that feature completely.

Restore scroll position in tables list
--------------------------------------

If you select a table from the list on the left (the navigation pane) the scroll position is now saved and used for
each new request. The plugin prevents that the selected table might be outside the visible area of the navigation pave.

.. image:: /Images/adminer-scrollposition.png
   :alt: Remember scroll position in list of tables
   :class: with-shadow
   :scale: 40

Readable dates
--------------

This plugin turns Unix timestamps into readable dates. If you click on such a date the timestamp will be shown.
To find out which values are timestamps the plugin uses TCA data. Zero values are not turned into a date because these
are special values for TYPO3 (meaning: no date entered).

.. image:: /Images/adminer-readabledates.png
   :alt: Readable dates instead of timestamps
   :class: with-shadow
   :scale: 40