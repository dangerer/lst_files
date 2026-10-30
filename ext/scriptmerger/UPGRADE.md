## Version 9 Breaking Changes

- Dropped TYPO3 10 and 11 Support
- Dropped PHP 7 support
- Remove the ContentPostProcHook, because it is not available anymore
- The default ignore rule was changed to only exclude files that are not ending with ?17|?18 to still match files
  from the TYPO3 page generation. The version number can't be removed anymore in TYPO3.

## Version 8 Breaking Changes

- Dropped TYPO3 9 Support
- Dropped php 7.3 Support
- addBeforeBody was finally removed

## Version 7 Breaking Changes

- Dropped TYPO3 8 support

- Moved logic from contentPostProc-output hook to middleware

The middleware is handling the intINCScript part of the scriptmerger logic, because
the contentPostProc-output hook does not exist anymore. The normal logic for applying
scriptmerger to the generated, cached page is still located in contentPostProc-all,
because the cached page content can be obtained from the response, but not written
back after processing it, via middleware approach.
