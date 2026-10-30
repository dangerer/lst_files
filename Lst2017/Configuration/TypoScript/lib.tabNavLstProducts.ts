lib.tabNavLstProducts = COA
lib.tabNavLstProducts {
	10 = TEXT
	10.value (
		<ul class="nav nav-pills nav-fill nav-productTabs" id="tab_lstProdlist" role="tablist">
		  <li class="nav-item">
		    <a class="nav-link active" id="products-tab" data-toggle="tab" href="#products" role="tab" aria-controls="products" aria-selected="true">
	)
	20 = TEXT
	20.value = Produkte
#	20.data = DB:pages:3:title	
	30 = TEXT
	30.value (
			</a>
		  </li>
		  <li class="nav-item">
		    <a class="nav-link" id="services-tab" data-toggle="tab" href="#services" role="tab" aria-controls="services" aria-selected="false">
	)
	40 = TEXT
	40.value = Dienstleistungen
#	40.data = DB:pages:4:title
	50 = TEXT
	50.value (
			</a>
		  </li>
		</ul>
	)
}

[siteLanguage("languageId") == 1]

#[Gtn\GtnLsthooks\Condition\FeLanguageSelectedCondition = 1]

lib.tabNavLstProducts.20.value = Products
lib.tabNavLstProducts.40.value = Services

[end]

