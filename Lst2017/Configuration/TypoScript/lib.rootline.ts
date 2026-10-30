# Breadcrump

lib.rootline = COA
lib.rootline {
	#	wrap = Sie sind hier: <ul class="breadcrumb">|<ul>
	stdWrap.wrap = <div class="container-fluid"><nav aria-label="breadcrumb" role="navigation"><ol class="breadcrumb">|</ol></nav></div>
	20 = HMENU
	20 {
		special = rootline
		# Von der Ebene 1 bis zur aktuellen Seite, -1 ist die Seite auf der man sich befindet -2 die übergeordnete etc
		special.range = 0|-1
		excludeUidList = 
		includeNotInMenu = 1
		# entryLevel=2
		1 = TMENU
		1 {
			# wrap = <ul class="breadcrumb">|</ul>
			NO = 1
			NO {
				# Option Split Divider 
				# linkWrap = |*||<span class="glyphicons glyphicons-paperclip" aria-hidden="true"></span>|*||
				wrapItemAndSub = <li class="breadcrumb-item">|</li>
				# aTag Parameter definieren wenn unter abstract nichts ist dann description usw
				ATagTitle.field = abstract//description//title
				# Tooltipp einbauen siehe Bootstrap Javascript Ref
				# ATagParams = data-toggle="tooltip" data-placement="bottom" class="tooltip-bottom"
				stdWrap.htmlSpecialChars = 1
			}
			
			IFSUB = 1
			IFSUB {
				#ATagTitle.field = subtitle//title
				wrapItemAndSub =  <li class="breadcrumb-item">|</li>
				# doNotLinkIt = 1
				#allWrap = | <!--<![endif]-->
				#linkWrap = |<!--[if gte IE 7]><!-->
				#ATagBeforeWrap = 1
				
				# Um die zweite Ebene falls Unterseiten bestehen nicht zu verlinken 
				# muss der Author Disable_Bread_Crumblink in die Seite eingetragen werden, Fallback ist ein Shortcut
				ATagParams.dataWrap = class="{field:author}"
			}
			
			
			#IFSUB = 1
			#IFSUB {
			#	#ATagTitle.field = subtitle//title
			#	linkWrap = |*||<span class="divider">/</span>|*||
			#	wrapItemAndSub =  <li>|</li>
			#	ATagTitle.field = abstract//description//title
			#	doNotLinkIt = 1
			#	#allWrap = | <!--<![endif]-->
			#	#linkWrap = |<!--[if gte IE 7]><!-->
			#	#ATagBeforeWrap = 1
			#	
			#}
			#IFSUB = 1
			#IFSUB {
			#	#ATagTitle.field = subtitle//title
			#	wrapItemAndSub =  <a href="#">|</a>
			#	doNotLinkIt = 1
			#	#allWrap = | <!--<![endif]-->
			#	#linkWrap = |<!--[if gte IE 7]><!-->
			#	#ATagBeforeWrap = 1
			#	
			#}
			CUR < .NO
			CUR {
				wrapItemAndSub = <li class="breadcrumb-item active" aria-current="page">|</li>
				# Aktuelle Seite nicht verlinken
				doNotLinkIt =1
			}
		}
		
		# 2.IFSUB = 0
		
	}
	
	# Add news title if on single view
    30 = RECORDS
    30 {
        stdWrap.if.isTrue.data = GP:tx_news_pi1|news
        dontCheckPid = 1
        tables = tx_news_domain_model_news
        source.data = GP:tx_news_pi1|news
        source.intval = 1
        conf.tx_news_domain_model_news = TEXT
        conf.tx_news_domain_model_news {
            field = title
            htmlSpecialChars = 1
        }
        wrap =  <li class="breadcrumb-item active">|</li>
    }

    # Add event title if on single view
    40 = RECORDS
    40 {
		stdWrap.if.isTrue.data = GP:tx_gtncachedevents_events|event
		dontCheckPid = 1
		tables = tx_gtncachedevents_domain_model_event
		source.data = GP:tx_gtncachedevents_events|event
		source.intval = 1
		conf.tx_gtncachedevents_domain_model_event = TEXT
		conf.tx_gtncachedevents_domain_model_event {
			field = summary
			htmlSpecialChars = 1
		}
		wrap =  <li>|</li>
	}
}

