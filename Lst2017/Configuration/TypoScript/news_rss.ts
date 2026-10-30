config {
	disableAllHeaderCode = 1
	xhtml_cleaning = none
	admPanel = 0
	metaCharset = utf-8
	additionalHeaders = Content-Type:text/xml;charset=utf-8
	disablePrefixComment = 1
	absRefPrefix = https://www.laborstrauss.com
}

page >
page = PAGE 
page {
	10 < tt_content.list.20.news_pi1
	10 {
		settings < plugin.tx_news.setting
		settings {
			format = xml 
			limit = 30
			# wichtig hier angeben wo die Singel Seite ist
			detailPid = 17
		}
	}
}



# Am besten in den News Ordner noch folgende TSconfig eintragen
# tx_news.singlePid = 19
# TCEMAIN.clearCacheCmd = all


# dieses dann auf der Seite einbinden wo ich die RSS ausgabe haben will
# Seitentemplate anlegen und: 
# <INCLUDE_TYPOSCRIPT: source="FILE: fileadmin/elc_template/Configuration/TypoScript/news_rss.ts">
# Wichtig: unter Eigenschaften der Seite in Index Suche einbinden raus

# dann erweitertes Seitentemplate anlegen 