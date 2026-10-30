#
# PIM TS Template - überführen in root.ts
#
#	- Backendlayout, Tempalte HMTL, root.tx betemplate = auf beiden
#
#
#
#
#
#
#

## Keine Ahnung ob noch benötigt
lib.dceTitle = CONTENT
lib.dceTitle {
  table = tt_content
  select.uidInList.field = uid
  renderObj = COA
  renderObj {
	10 = TEXT     # zuerst Header, falls gesetzt
	10 {
	  field = header
	  if.isTrue.field = header
	}
	20 = USER     # dann DCE-Flexform-Feld "title"
	20 {
	  userFunc = TYPO3\CMS\Frontend\DataProcessing\FlexFormProcessor->getFlexFormValue
	  # (oder eigener UserFunc, je nach Setup)
	  # einfacher, wenn du Fix 1 nutzt – empfehlenswert.
	}
  }
}

page = PAGE
page {
	headerData.90 = TEXT
	headerData.90.value (
		<meta name="robots" content="noindex, nofollow">
		<meta name="googlebot" content="noindex">
	)
	
	includeCSS {
		pimCss = fileadmin/Lst2017/Resources/Public/StyleSheets/customPim.css
		# pimCssDLPL = fileadmin/Lst2017/Resources/Public/StyleSheets/customPim_dlpl.css
	}
	includeJSFooter {
		pimJs = fileadmin/Lst2017/Resources/Public/JavaScript/customPim.js

	}
	
	10 {
		# Pim Menu
		dataProcessing {
		  10 = TYPO3\CMS\Frontend\DataProcessing\MenuProcessor
		  10 {
			special = directory
			special.value = 12784
			levels = 5
			as = pimmenu
			expandAll = 0
			includeSpacer = 0
			titleField = nav_title // title
			dataProcessing {
			  20 = TYPO3\CMS\Frontend\DataProcessing\DatabaseQueryProcessor
			  20 {
				table = tt_content
				pidInList.field = uid
				orderBy = sorting
				where = colPos = 0
				as = placesInfoContent
			  }
			}
		  }
		}
		# Pim Menu
		dataProcessing {
		  20 = TYPO3\CMS\Frontend\DataProcessing\MenuProcessor
		  20 {
			special = directory
			special.value = 12784
			levels = 5
			as = headerMenu
			expandAll = 1
			includeSpacer = 0
			titleField = nav_title // title
		  }
		}
		variables {
			productlist_search = TEXT
			productlist_search.value (
				<form class="form-inline justify-content-end search-field-form-sidebar" method="get" action="/">
					<input type="hidden" name="id" value="121" />
					<label class="sr-only" for="Produktsuche">Produktsuche</label>
					<div class="input-group mb-1">
						<input class="form-control form-control-lg " id="Produktsuche" type="text" placeholder="Produktsuche" name="tx_kesearch_pi1[sword]">
						<div class="input-group-append">
							<button type="submit" class="btn btn-primary btn-lst btn-lstgrey btn-icon btn-icon-lg"><i class="icon ion-ios-search-strong"></i></button>
						</div>
					</div>
				</form>
			)
		}
	}
	
}