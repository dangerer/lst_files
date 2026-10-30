RTE.default {
	proc {
		exitHTMLparser_db = 1
		exitHTMLparser_db {
			tags.b.remap = strong
			tags.i.remap = em
		}
		allowedClasses := addToList(btn btn-default btn-bizybee, btn btn-default btn-bizybee btn-lg,btn btn-default btn-bizybee btn-sm,zentriert,rechtsbuendig,zebraliste)
		allowedClasses := removeFromList()
	}
	buttons {
		blockstyle.tags.div.allowedClasses := addToList(zentriert) 
		textstyle.tags.span.allowedClasses := addToList() 
		link.properties.class.allowedClasses := addToList(btn btn-default btn-bizybee, btn btn-default btn-bizybee btn-lg,btn btn-default btn-bizybee btn-sm)
		
		blockstyle.tags.div.allowedClasses := removeFromList() 
		textstyle.tags.span.allowedClasses := removeFromList() 
		link.properties.class.allowedClasses := removeFromList()
		
		blockstyle.tags.table.allowedClasses := addToList(bizybee-table)
		
		# neue Block Formate hinzufügen
		formatblock.removeItems = article,footer,header,nav,aside,Container,Paragraph,section
		# formatblock.addItems = verstecke_inhalt
		# formatblock.items.verstecke_inhalt.addClass = hide_contentbu
		# formatblock.items.verstecke_inhalt.label = Versteckter Inhalt
		formatblock.items.verstecke_inhalt.tagName = div
}

	contentCSS = fileadmin/Bizybee2016/Resources/Public/StyleSheets/TYPO3_rte.css
}

RTE.classesAnchor {
internalLink {
class = internal-link
type = page

image >
}
externalLink {
class = external-link
type = url
image >
}
externalLinkInNewWindow {
class = external-link-new-window
type = url
image >
}
internalLinkInNewWindow {
class = internal-link-new-window
type = page
image >
}
download {
class = download
type = file
image >
}
mail {
class = mail
type = mail
image >
}
}

RTE.default.proc.entryHTMLparser_db.allowTags := addToList(iframe,embed,object,param)
RTE.default.proc.allowTagsOutside := addToList(iframe,embed,object,param)

plugin.tt_news.displaySingle.content_stdWrap.parseFunc {
nonTypoTagStdWrap.encapsLines.nonWrappedTag >
allowTags = script, iframe, param, embed, object, a, abbr, acronym, address, blockquote, b, br, caption, center, cite, code, div, em, font, h1, h2, h3, h4, h5, h6, hr, i, img, li, link, ol, p, pre, q, sdfield, span, strike, strong, sub, sup, table, thead, tbody, tfoot, td, th, tr, tt, u, ul
} 

