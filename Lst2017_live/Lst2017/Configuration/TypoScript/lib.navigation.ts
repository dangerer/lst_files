lib.navigation_main = COA
lib.navigation_main {
	
	# Hierarchisches Menü
	20 = HMENU 
	20 {
		# Was befindet sich im Menu
		excludeUidList = 121
		# Menu wird definiert
		1 = TMENU
		1 {
			expAll = 1
			
			# Normalzustand der Menuitems
			NO = TMENUITEM
			NO {
				#allWrap.insertData=1
				#allWrap=<li class="dropdown" id="menuitem{field:uid}">|
				wrapItemAndSub = <li class="nav-item">|</li>
				# Umwandeln der Sonderzeichen
				stdWrap.htmlSpecialChars = 1
				ATagParams.dataWrap = id="menuitem{field:uid}" class="nav-link"
			}
			IFSUB = TMENUITEM
			IFSUB {
				#ATagTitle.field = subtitle//title
				allWrap.insertData = 1
				allWrap = <li class="nav-item dropdown">|<a href="" class="nav-link dropdown-toggle" data-toggle="dropdown" id="menuitem{field:uid}" aria-haspopup="true" aria-expanded="false"><i class="icon ion-chevron-down"></i>
				wrapItemAndSub = |</li>
				# wrapItemAndSub =  <li class="dropdown"><a href="" class="dropdown-toggle" data-toggle="dropdown">|</li>
				# doNotLinkIt = 0
				ATagParams.dataWrap = id="menuitem{field:uid}" class="nav-link nav-link-dropdown"
				# ATagParams.dataWrap = id="menu{field:uid}"
				#allWrap = | <!--<![endif]-->
				#linkWrap = |<!--[if gte IE 7]><!-->
				#ATagBeforeWrap = 1
				
			}
			ACT < .NO
			ACT = TMENUITEM
			ACT {
				wrapItemAndSub = <li class="nav-item active">|</li>
			}
			# Currentzustand Normal wird kopiert
			CUR < .NO
			CUR {
				wrapItemAndSub = <li class="nav-item active">|</li>
			}
			CURIFSUB < .IFSUB
			CURIFSUB {
				allWrap = <li class="nav-item dropdown active">|<a href="" class="nav-link dropdown-toggle" data-toggle="dropdown" id="menuitem{field:uid}" aria-haspopup="true" aria-expanded="false"><i class="icon ion-chevron-down"></i></a>
			}
			ACTIFSUB < .IFSUB
			ACTIFSUB {
				allWrap = <li class="nav-item dropdown active activesub">|<a href="" class="nav-link dropdown-toggle" data-toggle="dropdown" id="menuitem{field:uid}" aria-haspopup="true" aria-expanded="false"><i class="icon ion-chevron-down"></i></a>
			}
		}
		2 < .1
		2 {
			wrap = </a><div class="dropdown-menu" aria-labelledby="menuitem1">|</div>
			ACT < .NO
			NO = TMENUITEM
			NO {
				wrapItemAndSub = |
				ATagParams.dataWrap = id="menuitem{field:uid}" class="dropdown-item"
			}
			CUR < .NO
			CUR = TMENUITEM
			CUR {
			 	wrapItemAndSub = |
				doNotLinkIt = 0
				ATagParams.dataWrap = id="menuitem{field:uid}" class="dropdown-item active"
			}
			ACT = TMENUITEM
			ACT {
			 	wrapItemAndSub = |
				doNotLinkIt = 0
				ATagParams.dataWrap = id="menuitem{field:uid}" class="dropdown-item active"
			}
			IFSUB >
			ACTIFSUB >
			CURIFSUB >
		}
		3 >
	}
}

lib.navigation_service = COA_INT
lib.navigation_service {
	# Hierarchisches Menü
	20 = HMENU 
	20 {
		# Was befindet sich im Menu
		special = list
		# special.value = 38,62
		special.value = 38
		includeNotInMenu = 1
		# Menu wird definiert
		1 = TMENU
		1 {
			expAll = 1
			# Normalzustand der Menuitems
			NO = 1
			NO {
				#allWrap.insertData=1
				#allWrap=<li class="dropdown" id="menuitem{field:uid}">|
				wrapItemAndSub = <li class="nav-item">|</li>
				# Umwandeln der Sonderzeichen
				stdWrap.htmlSpecialChars = 1
				ATagParams.dataWrap = id="menuitem{field:uid}" class="nav-link"
				stdWrap.wrap = <i class="icon ion-android-person"></i><span class="nav-servTxt">|</span>
			}
		}
	}
#	30 = TEXT
#	30.value (
#		<li class="nav-item"><a href="" class="nav-link" data-toggle="modal" data-target="#selectRegion"><i class="icon ion-android-globe"></i><span class="nav-servTxt">Region wählen</span></a></li>
#	)
	30 = FLUIDTEMPLATE
	30.file = fileadmin/Lst2017/Resources/Private/Templates/RegionSelector.html
	
	35 = COA
	35 {
			10 = TEXT
			10.value (
				<li class="nav-item dropdown dropdown-lang"><a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><img src="https://www.laborstrauss.com/fileadmin/Lst2017/Resources/Public/Images/flag_atde.png"><span class="nav-servTxft">Deutsch</span></a><div class="dropdown-menu">
			)
			
			20 = HMENU
			20 {
				special = language
				special.value = 0,1
				addQueryString = 1
				addQueryString.exclude = id, L, cHash
				1 = TMENU
				1 {    
					noBlur = 1
					NO = 1
					NO {      
						allWrap =  |
						linkWrap = |
						stdWrap.override = Deutsch || English
						stdWrap.htmlSpecialChars = 1
						ATagParams.dataWrap = class="dropdown-item dropdown-lang-de" || class="dropdown-item dropdown-lang-en"
						additionalParams.cObject = COA
						additionalParams.cObject.10 = TEXT
						additionalParams.cObject.10.value = &fL=1
						additionalParams.cObject.20 = TEXT
						additionalParams.cObject.20 {
							data = GP:tx_news_pi1|news
							stdWrap.wrap = &tx_news_pi1[news]=|
							stdWrap.required = 1
						}
					}
				    ACT = 1
				    ACT < .NO
				    ACT {      
				    	#doNotLinkIt = 1
						linkWrap = |
						ATagParams.dataWrap = class="dropdown-item dropdown-lang-de" || class="dropdown-item dropdown-lang-en"
				    }
				    CUR = 1
				    CUR < .NO
				    CUR {      
				    	#doNotLinkIt = 1
						linkWrap = |
						ATagParams.dataWrap = class="dropdown-item dropdown-lang-de" || class="dropdown-item dropdown-lang-en"
				    }
				}
			}
			

			30 = TEXT
			30.value (
				</div></li>
			)
	}
	
	
#	40 = TEXT
#	40.value (
#		<li class="nav-item dropdown"><a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="nav-servTxft">Deutsch</span></a>
#			<div class="dropdown-menu">
#		      <a class="dropdown-item" href="#">Deutsch</a>
#		      <a class="dropdown-item" href="#">English</a>
#		    </div>
#		</li>
#	)
}

[siteLanguage("languageId") == 1]
#[Gtn\GtnLsthooks\Condition\FeLanguageSelectedCondition = 1]
lib.navigation_service.35.10.value (
	<li class="nav-item dropdown dropdown-lang"><a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><img src="https://laborstrauss.com/fileadmin/Lst2017/Resources/Public/Images/flag_en2.png"><span class="nav-servTxft">English</span></a><div class="dropdown-menu">
)

[end]

# Hide Login Page after Login
[frontend.user.isLoggedIn]
lib.navigation_service.20.special.value = 62
[end]


lib.navigation_footer = COA
lib.navigation_footer {
	# Hierarchisches Menü
	20 = HMENU 
	20 {
		# Was befindet sich im Menu
		special = list
		special.value = 9,11,12,357
		includeNotInMenu = 1 
		# Menu wird definiert
		1 = TMENU
		1 {
			expAll = 1
			# Normalzustand der Menuitems
			NO = 1
			NO {
				#allWrap.insertData=1
				#allWrap=<li class="dropdown" id="menuitem{field:uid}">|
				wrapItemAndSub = <li class="nav-item">|</li>
				# Umwandeln der Sonderzeichen
				stdWrap.htmlSpecialChars = 1
				ATagParams.dataWrap = id="menuitem{field:uid}" class="nav-link"
			}
		}
	}
}

			navigation_footer1_title = TEXT
			navigation_footer1_title.data = DB:pages:3:title
			navigation_footer1_title.wrap = <h4>|</h4>

lib.footer_sitemap1 = COA
lib.footer_sitemap1 { 
	# Hierarchisches Menü
	10 = TEXT
	10.value = Unternehmen
	# 10.data = DB:pages:2:title
	10.wrap = <h4>|</h4>
	10.typolink.parameter = 2
	20 = HMENU 
	20 {
		# Was befindet sich im Menu
		special = directory
		special.value = 2
		# Menu wird definiert
		1 = TMENU
		1 {
			expAll = 1
			wrap = <ul class="nav nav-sitemap flex-column">|</ul>
			# Normalzustand der Menuitems
			NO = 1
			NO {
				#allWrap.insertData=1
				#allWrap=<li class="dropdown" id="menuitem{field:uid}">|
				wrapItemAndSub = <li class="nav-item">|</li>
				# Umwandeln der Sonderzeichen
				stdWrap.htmlSpecialChars = 1
				ATagParams.dataWrap = id="menuitem{field:uid}" class="nav-link"
			}
			ACT < .NO
			ACT = 1
			ACT {
				ATagParams.dataWrap = id="menuitem{field:uid}" class="nav-link active"
			}
			# Currentzustand Normal wird kopiert
			CUR < .NO
			CUR {
				ATagParams.dataWrap = id="menuitem{field:uid}" class="nav-link active"
			}
		}
	}
}

lib.footer_sitemap2 < lib.footer_sitemap1
lib.footer_sitemap2 {
	# 10.data = DB:pages:3:title
	10.value = Produkte
	10.typolink.parameter = 3
	20.special.value = 3
}

lib.footer_sitemap3 < lib.footer_sitemap1
lib.footer_sitemap3 {
	# 10.data = DB:pages:4:title
	10.value = Dienstleistungen
	10.typolink.parameter = 4
	20.special.value = 4
}

lib.footer_sitemap4 < lib.footer_sitemap1
lib.footer_sitemap4 {
	# 10.data = DB:pages:5:title
	10.value = Kundencenter
	10.typolink.parameter = 5
	20.special.value = 5
}

lib.footer_sitemap5 < lib.footer_sitemap1
lib.footer_sitemap5 {
	# 10.data = DB:pages:6:title
	10.value = Karriere
	10.typolink.parameter = 6
	20.special.value = 6
}

lib.footer_sitemap6 < lib.footer_sitemap1
lib.footer_sitemap6 {
	# 10.data = DB:pages:7:title
	10.value = News
	10.typolink.parameter = 7
	10.wrap = <h4 class="sm-childh4">|</h4>
	20.special.value = 7
}

lib.footer_sitemap7 < lib.footer_sitemap1
lib.footer_sitemap7 {
	# 10.data = DB:pages:8:title
	10.value = Kontakt
	10.typolink.parameter = 8
	20.special.value = 8
}

[siteLanguage("languageId") == 1]
#[Gtn\GtnLsthooks\Condition\FeLanguageSelectedCondition = 1]
lib.footer_sitemap1.10.value = Company
lib.footer_sitemap2.10.value = Products
lib.footer_sitemap3.10.value = Services
lib.footer_sitemap4.10.value = Customer center
lib.footer_sitemap5.10.value = Career
lib.footer_sitemap6.10.value = News
lib.footer_sitemap7.10.value = Contact
[end]

lib.navigation_customerarea = COA
lib.navigation_customerarea {
	# Hierarchisches Menü
	10 = TEXT
	10.data = DB:pages:57:title
	10.wrap = <h4 class="nav-login-title">|</h4>
	10.typolink.parameter = 57
	20 = HMENU 
	20 {
		# Was befindet sich im Menu
		special = directory
		special.value = 57
		# Menu wird definiert
		1 = TMENU
		1 {
			expAll = 1
			wrap = <div class="nav flex-column nav-pills nav-login">|</div>
			# Normalzustand der Menuitems
			NO = 1
			NO {
				#allWrap.insertData=1
				#allWrap=<li class="dropdown" id="menuitem{field:uid}">|
				# Umwandeln der Sonderzeichen
				stdWrap.htmlSpecialChars = 1
				ATagParams.dataWrap = id="menuitem{field:uid}" class="nav-link"
			}
			ACT < .NO
			ACT = 1
			ACT {
				ATagParams.dataWrap = id="menuitem{field:uid}" class="nav-link active"
			}
			# Currentzustand Normal wird kopiert
			CUR < .NO
			CUR {
				ATagParams.dataWrap = id="menuitem{field:uid}" class="nav-link active"
			}
		}
	}
}

lib.navigation_submenuLevel2 = COA
lib.navigation_submenuLevel2 {
	# Hierarchisches Menü
	10 = TEXT
	10.data = leveltitle:1
	10.wrap = <h4 class="nav-login-title">|</h4>
	10.typolink.parameter.data = leveluid : 1
	20 = HMENU 
	20 {
		# Was befindet sich im Menu
		special.range = 1|-1
		entryLevel=1
		# Menu wird definiert
		1 = TMENU
		1 {
			expAll = 1
			wrap = <div class="nav flex-column nav-pills nav-login">|</div>
			# Normalzustand der Menuitems
			NO = 1
			NO {
				#allWrap.insertData=1
				#allWrap=<li class="dropdown" id="menuitem{field:uid}">|
				# Umwandeln der Sonderzeichen
				stdWrap.htmlSpecialChars = 1
				ATagParams.dataWrap = id="menuitem{field:uid}" class="nav-link"
			}
			ACT < .NO
			ACT = 1
			ACT {
				ATagParams.dataWrap = id="menuitem{field:uid}" class="nav-link active"
			}
			# Currentzustand Normal wird kopiert
			CUR < .NO
			CUR {
				ATagParams.dataWrap = id="menuitem{field:uid}" class="nav-link active"
			}
		}
	}
}

lib.navigation_submenuLevel3 < lib.navigation_submenuLevel2
lib.navigation_submenuLevel3 {
	20 {
		# Was befindet sich im Menu
		special.range = 1|-1
		entryLevel=2
	}
}

lib.navigation_locations = COA
lib.navigation_locations {
	20 = HMENU 
	20 {
		# Was befindet sich im Menu
		#special = list
		#special.value = 75,76,77,104
		#includeNotInMenu = 1
		
		special.range = 1|-1
		entryLevel=2
		
		# Menu wird definiert
		1 = TMENU
		1 {
			expAll = 0
			# wrap = <ul class="list-group list-group-flush">|</ul>
			# Normalzustand der Menuitems
			NO = 1
			NO {
				#allWrap.insertData=1
				#allWrap=<li class="dropdown" id="menuitem{field:uid}">|
				# Umwandeln der Sonderzeichen
				stdWrap.htmlSpecialChars = 1
				ATagParams.dataWrap = id="menuitem{field:uid}" class="list-group-item list-group-item-action list-group-item-noparent"
			}
			IFSUB = 1 
			IFSUB {
				ATagParams.dataWrap = id="menuitem{field:uid}" class="list-group-item list-group-item-action list-group-item-parent"
			}
			ACT < .NO
			ACT = 1
			ACT {
				ATagParams.dataWrap = id="menuitem{field:uid}" class="list-group-item list-group-item-action active list-group-item-noparent"
			}
			ACTIFSUB < .ACT
			ACTIFSUB {
				ATagParams.dataWrap = id="menuitem{field:uid}" class="list-group-item list-group-item-action active list-group-item-parent"
				
			}
			# Currentzustand Normal wird kopiert
			CUR < .NO
			CUR {
				ATagParams.dataWrap = id="menuitem{field:uid}" class="list-group-item list-group-item-action active list-group-item-noparent"
			}
			CURIFSUB < .NO
			CURIFSUB {
				ATagParams.dataWrap = id="menuitem{field:uid}" class="list-group-item list-group-item-action active list-group-item-parent"
			}
		}
		
		2 < .1
		2 {
			expAll = 0
			NO = 1
			NO {
				#allWrap.insertData=1
				#allWrap=<li class="dropdown" id="menuitem{field:uid}">|
				# Umwandeln der Sonderzeichen
				stdWrap.htmlSpecialChars = 1
				ATagParams.dataWrap = id="menuitem{field:uid}" class="list-group-item list-group-item-sub list-group-item-action"
			}
			CUR < .NO
			CUR {
				ATagParams.dataWrap = id="menuitem{field:uid}" class="list-group-item list-group-item-sub list-group-item-action active"
			}
		}
	}
}

lib.navigation_locations_at < lib.navigation_locations 
lib.navigation_locations_at {
	20.excludeUidList = 78,74,79,80,108,12055
}

lib.navigation_locations_de < lib.navigation_locations 
lib.navigation_locations_de {
	# 20.special.value = 78,79,74
	20.excludeUidList = 75,76,77,80, 104, 105,106,107
}

lib.navigation_locations_ww < lib.navigation_locations 
lib.navigation_locations_ww {
	# 20.special.value = 80
	20.excludeUidList = 75,76,77,78,74,79, 104, 105,106,107,108,12055
}

# [globalVar = TSFE:id = 11249]

# lib.navigation_service.30 >

# [end]

