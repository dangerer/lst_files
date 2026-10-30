
# Speicherort einstellen
module.tx_news.persistence.storagePid = 16

plugin.tx_news {
        view {
                templateRootPaths >
                templateRootPaths {
                        0 = EXT:news/Resources/Private/Templates/
                        1 = fileadmin/Lst2017/Resources/Private/News/Templates/
                }
                partialRootPaths >
                partialRootPaths {
                        0 = EXT:news/Resources/Private/Partials/
                        1 = fileadmin/Lst2017/Resources/Private/News/Partials/
                }
                layoutRootPaths >
                layoutRootPaths {
                        0 = EXT:news/Resources/Private/Layouts/
                        1 = fileadmin/Lst2017/Resources/Private/News/Layouts/
                }
        }
}


plugin {
	#tx_news.templateLayouts {
    #    1 = A custom layout
    #    2 = d
    #    99 = LLL:fileadmin/somelocallang/locallang.xml:someTranslation
	#}
	tx_news {
		templateLayouts {
        	# Definiert in TS-config
		}
		predefine {
			archive = 1
		}
		settings {
			# eventRestriction 3 = no constraint (show both news and events news)
			eventRestriction = 3
			detailPid = 33
			facebookLocale = de_DE
			displayDummyIfNoMedia = 0
			list {
				media.image.maxHeight = 400
				media.image.maxWidth = 400
				media.image.Height = 435c
				media.image.Width = 600c
				
				media.Height = 435c
				media.Width = 600c
				listOrderBy = random
				paginate{
					itemsPerPage = 9
					insertAbove=FALSE
					insertBelow=TRUE
					pagesBefore=3
					pagesAfter=3 
				}
			}
			detail {
				
				media.image.maxWidth = 555
				media.image.Width = 190c
				media.image.Height = 125c
			}
			isLatest = 1
		}
		lst_settings {
			# categories for jobs filters. It is list of Direct of job, NOT region! All another = job categories
			jobThemeCategories = 28,29
		}

		# Speicherort einstellen
		persistence.storagePid = 16
		# Templates in Constants
		_LOCAL_LANG {
			default {
				read_more = Read more
				more-link = Read more
				related-news = Ähnliche Beiträge
				related-files = Datein
				related-links = Links
				# author = fds |
				paginate_next = Weiter
				back-link = zurück
			}
			de {
				more-link = weiterlesen
				related-news = Ähnliche Beiträge
				paginate_overall = Seite %s von %s
				paginate_next = Weiter
				paginate_previous = Zurück
				related-links = Links
				related-links = Links
				related-files = Download
				
			}
			en {
				more-link = Read more
				related-news = Ähnliche Beiträge
				paginate_overall = Page %s of %s
				paginate_next = Next
				paginate_previous = Previous
				related-links = Links
				related-files = Download
				back-link = back
			}
		}

	}
}

# Damit wird die Pagination auch aus dem richtigen Template Ordner geladen
plugin.tx_news.view {
	widget.Tx_News_ViewHelpers_Widget_PaginateViewHelper.templateRootPath = fileadmin/Lst2017/Resources/Private/news/Templates/
}

# Crop Teaser Text
plugin.tx_news.settings.cropMaxCharacters = 150

plugin.tx_news.settings.list.media.image.homeHeight = 433c
plugin.tx_news.settings.list.media.image.homeWidth = 720c

# plugin.tx_news.settings.list.media.image.Height = 144c
# plugin.tx_news.settings.list.media.image.Width = 206c

plugin.tx_news._LOCAL_LANG.de.dateFormat = %d.%B %Y

plugin.tx_news._LOCAL_LANG.de.dateFormatEvent = %d.%B %Y, %H:%M

plugin.tx_news._LOCAL_LANG.de.dateFormatEventBeginnTime = %H:%M

plugin.tx_news._LOCAL_LANG.en.dateFormat = %d.%B %Y 1

plugin.tx_news._LOCAL_LANG.en.dateFormatEvent = %d.%B %Y, %H:%M

plugin.tx_news._LOCAL_LANG.en.dateFormatEventBeginnTime = %H:%M

# News Startseite
#[globalVar = TSFE:id = 1]
#	plugin.tx_news.settings.list.media.image.Height = 286c
#	plugin.tx_news.settings.list.media.image.Width = 420c
#[end]

# Placeholder Projekte 
#[globalVar = TSFE:id = 51]
#Displays a dummy image if the news have no media items
#plugin.tx_news.settings.displayDummyIfNoMedia = 1
#plugin.tx_news.settings.list.media.dummyImage = fileadmin/Lst2017/Resources/Public/Images/placeholder.png
#[end]

# Variante 1
plugin.tx_news._CSS_DEFAULT_STYLE >
plugin.tx_news._CSS_DEFAULT_STYLE =

# Variante 2
plugin.tx_news.settings.cssFile =
plugin.tx_news.settings.cssFileConf.disable = 1
                
