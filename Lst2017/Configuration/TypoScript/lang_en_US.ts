config {
	locale_all = de_DE.UTF-8
	sys_language_uid = 0
	language = de
	locale_all = de_DE.UTF-8
	linkVars = L
	uniqueLinkVars = 0
	defaultGetVars.L = 0
	htmlTag_langKey = de_DE
	sys_language_mode = content_fallback
}

[siteLanguage("languageId") == 1]
#|| [Gtn\GtnLsthooks\Condition\FeLanguageSelectedCondition = 1]
config {	
	locale_all = en_US.UTF-8
	sys_language_uid = 1
	language = en
	# Linkparameter mitgeben
	linkVars = L(int)
	uniqueLinkVars = 1
}

plugin {
	tx_news {
		settings {
			facebookLocale = en_US
			}
	}
}
[end]