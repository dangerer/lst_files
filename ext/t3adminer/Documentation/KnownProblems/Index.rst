.. include:: /Includes.rst.txt

.. _knownproblems:

Known problems
==============

* If your backend language is not supported by Adminer the configuration will switch to English. There is a language
  selector in the upper righthand corner where you can select a suitable language

* If a different port than the default port is used in the database configuration in TYPO3 then this might not be used
  correctly. This feature needs to be tested better to see if fixes are necessary

* The button :guilabel:`Load more data` will load extra rows without applying the readable date plugin. This is a bug
  in Adminer

* If you use the search field in the start screen of Adminer the search operation might take a long time. It will search
  in every column of every table. For large databases this will take extremely long.

Issues in the backend module, integration in TYPO3 and styling can be found at `the bug tracker`_.

.. _the bug tracker: https://gitlab.com/jigal/t3adminer/-/issues/
