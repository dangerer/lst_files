
# Klasse bodytext vom Text entfernen
lib.parseFunc_RTE.nonTypoTagStdWrap.encapsLines.addAttributes.P >

# Table 
lib.parseFunc_RTE.externalBlocks.table.stdWrap >

config {
	disablePrefixComment = 0
}

# Pop-up-Funktion entfernen
# tt_content.image.20.1.imageLinkWrap.directImageLink = 1
# tt_content.image.20.1.imageLinkWrap.JSwindow = 0

# lib.parseFunc_RTE.externalBlocks.table.stdWrap.HTMLparser.tags.table.fixAttrib.class >

# tt_content.image.20.1.imageLinkWrap.width = 3000

## p mit style versehen
lib.parseFunc_RTE.nonTypoTagStdWrap.encapsLines.addAttributes.P.class = bodytext
lib.parseFunc_RTE.nonTypoTagStdWrap.encapsLines.addAttributes.P.style=font-size: 14px;mso-line-height-rule:exactly;-ms-text-size-adjust:100%;-webkit-text-size-adjust:100%;overflow:visible !important;font-family: Verdana, Arial, Helvetica, sans-serif;color: #000000; Margin: 0;margin-bottom:13px;line-height: 19px;font-weight: normal; padding: 0; text-align: left;

# A Tag Stylen
lib.parseFunc.tags.a.typolink.ATagParams.data >    
lib.parseFunc.tags.a.typolink.ATagParams = style="font-size:14px;mso-line-height-rule:exactly;-ms-text-size-adjust:100%;-webkit-text-size-adjust:100%;text-decoration:none;color:#da251d;"

## a tag style hinzufügen
lib.parseFunc_RTE.tags.link.typolink.ATagParams = style="font-size:14px;mso-line-height-rule:exactly;-ms-text-size-adjust:100%;-webkit-text-size-adjust:100%;text-decoration:none;color:#da251d;"



lib.contentElement {
   templateRootPaths {
      200 = fileadmin/Lst2017/Newsletter/FluidStyledContentNewsletter/Resources/Private/Templates
   }
   partialRootPaths {
     200 = fileadmin/Lst2017/Newsletter/FluidStyledContentNewsletter/Resources/Private/Partials
   }
   layoutRootPaths {
      200 = fileadmin/Lst2017/Newsletter/FluidStyledContentNewsletter/Resources/Private/Layouts
   }
}


page = PAGE
page {
	typeNum = 0 
	config {
		htmlTag_langKey = de
		simulateStaticDocuments = 0
		baseURL = https://www.lst.at/
		# spamProtectEmailAddresses = 6
		# spamProtectEmailAddresses_atSubst = <span>&#064;</span>
		removeDefaultJS = 1
		additionalHeaders = Content-Type:text/html;charset=utf-8
		doctypeSwitch=1
		htmlTag_setParams = xmlns="http://www.w3.org/1999/xhtml" xml:lang="de" lang="de" xmlns:o="urn:schemas-microsoft-com:office:office"
		xhtml_cleaning = all
		doctype = xhtml_trans
		xmlprologue = none
	}
	
	# body Tag
	bodyTag >
	bodyTagCObject = TEXT
	bodyTagCObject.field = uid
	bodyTagCObject.wrap = <body class="page-|" id="archivebody" marginwidth="0" marginheight="0" offset="0" topmargin="0" leftmargin="0" style="-moz-box-sizing: border-box; -ms-text-size-adjust: 100%; -webkit-box-sizing: border-box; -webkit-text-size-adjust: 100%; Margin: 0; box-sizing: border-box; color: #000000; font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 14px; font-weight: normal; line-height: 19px; margin: 0; min-width: 100%; padding: 0; text-align: left; width: 100% !important;"> 
	
	meta {
		# Description und Keywords aus den Seiten holen
		description {
			data = TSFE:page|description
		}
		keywords {
			data = TSFE:page|keywords
			# Bereinigen Leerzeichen weg und Umbruch = Komma
			keywords = 1
		}
		X-UA-Compatible = IE=edge
		X-UA-Compatible.httpEquivalent = 1
		viewport = width=device-width, initial-scale=1
	} 

	
	# RSS Feed in den Header eintragen damit der RSS Feed im Browser angezeigt wird
	headerData {
		
		# Headerdata 10 auch im Template in Typo3 drinnen
		10 = TEXT 
		10.value (
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
			<style type="text/css">
				body{
					font-family:Arial,Helvetica;font-size:100%;
				}
				body{
					background-color:  #ffffff;
					background-position:center top;
					background-repeat:repeat-x;
				    padding-top:0px;margin-top:0px;
				}
				.container_12 ,.container_12 .grid_12 {
				  width: 980px;
				}
				
				div.header_logo{
					padding-top:8px;
				}
				a {
					color: #da251d;
					font-family: Verdana, Arial, Helvetica, sans-serif;
					font-weight: normal;
					line-height: 22px;
					margin: 0;
					padding: 0;
					text-align: left;
					text-decoration: none;
				}
				a.newsletterbutton {
					color: #ffffff !important;		
				}
				a.button,
				.btn-primary {
					background-color: #da251d;
					color: #fff;
					padding: 0.35rem 1.3rem;
				}
			</style>
		
			
			
		)

		
		15 = TEXT 
		15.value (
			<!--[if gte mso 9]>
			<xml>
				<o:OfficeDocumentSettings>
				<o:AllowPNG/>
				<o:PixelsPerInch>96</o:PixelsPerInch>
				</o:OfficeDocumentSettings>
			</xml>
			<![endif]-->
		)
		
		16 = TEXT
		16.value (

		)

		
	}
	
	includeCSS {
		# file1 = fileadmin/Lst2017/Newsletter/Resources/Public/StyleSheets/newsletterFoundation.css

	}

	
	10 = FLUIDTEMPLATE
	10 {
		 file.cObject = CASE
		 file.cObject {
		 	key {
		 		# levelfield: -1 = schaut nach ob es einen Eintrag gibt, durch das ,slide wird nachgesehen ob ein Eintrag zu finden ist
		 		data = levelfield: -1 , backend_layout_next_level, slide
		 		# damit wird obriges überschrieben wenn es unter backendlayout einen eintrag gibt überschreibt er data
		 		override.data = TSFE:page|backend_layout
		 		# override.field = backend_layout
		 	}
		 	default = TEXT
			default.value = fileadmin/Lst2017/Newsletter/Resources/Private/Templates/newsletterFluid.html
		 	
		 	64 = TEXT
		 	64.value = fileadmin/Lst2017/Newsletter/Resources/Private/Templates/newsletterFluid.html
		 	
	#	 	30 = TEXT
	#	 	30.value = fileadmin/Lst2017/Newsletter/Resources/Private/Templates/newsletterFluid_test1.html
		 	
		 	
		 	}
		
		partialRootPath = fileadmin/Lst2017/Newsletter/Resources/Private/Partials/
		layoutRootPath = fileadmin/Lst2017/Newsletter/Resources/Private/Layouts
		variables {
			
		


			# Kopie von content.get erstellen
			content_0 < styles.content.get
			#content_1 < styles.content.getLeft
			#content_2 < styles.content.getRight
			#content_3 < styles.content.getBorder


			content_1 < styles.content.get
			content_1 {
				select.where = colPos = 1
				
			}
			
			lst_logo = TEXT
			lst_logo.value (
				<a href="https://www.lst.at/" target="_blank" style="display: block; margin: 0; padding: 0;" height="86">	
					<img src="https://www.lst.at/fileadmin/Lst2017/Newsletter/Resources/Public/Images/lst-gruppe_de_2021.png" width="149" height="86" alt="" style="-ms-interpolation-mode: bicubic; clear: both; display: block; width: 149px; height: 86px; outline: none; text-decoration: none;border: 0;" height="86">
				</a>
			)
			
			
			# einleitungsinhalt >
			
			shownewsletterinbrowser = TEXT
			shownewsletterinbrowser.value = Newsletter im Webbrowser anzeigen
			shownewsletterinbrowser.typolink.parameter.data=field:uid
			shownewsletterinbrowser.typolink.additionalParams = &ttahash=###SYS_AUTHCODE###

			shownewsletterinbrowser.typolink.ATagParams = style="color:#fff;"
			shownewsletterinbrowser.typolink.ATagParams = style="Margin: 0; color: #ffffff; font-family: Verdana, Arial, Helvetica, sans-serif; font-weight: normal; line-height: 19px; margin: 0; padding: 0; text-align: left; text-decoration: none;"

			
			seitentitel = TEXT
			seitentitel.data = page : title
			seitentitel.field = nav_title // title
		
			
			
			
			####### LST #######
			
			
			footer_sitename = TEXT
			footer_sitename.value = Labor Strauss Gruppe
			
			footer_imprint = TEXT
			footer_imprint.value = <a href="https://www.laborstrauss.com/impressum/" style="color: #da251d; Margin: 0; color: #da251d; font-family: Verdana, Arial, Helvetica, sans-serif; font-weight: normal; line-height: 19px; margin: 0; padding: 0; text-align: left; text-decoration: none;">Impressum & Datenschutz</a>
			
			
			footer_logo = TEXT
			footer_logo.value (
				<a href="https://www.laborstrauss.com/"><img id="brand" width="101" height="58" border="0" title="Labor Strauss Gruppe" alt="Labor Strauss Gruppe" src="http://www.laborstrauss.com/fileadmin/Lst2017/Resources/Public/Images/newsl_footerlstlogo_en_2.png"></a>
			)
			
			footer_logo_neu = TEXT
			footer_logo_neu.value (
				<a href="https://www.laborstrauss.com/"><img id="brand" width="120" height="44" border="0" title="Labor Strauss Gruppe" alt="Labor Strauss Gruppe" src="http://www.laborstrauss.com/fileadmin/Lst2017/Newsletter/Resources/Public/Images/lst-logo-nl3.jpg"></a>
			)
		
			footer_mep_logo = TEXT
			footer_mep_logo.value (
				<a href="https://www.mep-pockau.de/"><img id="brand" width="101" height="58" border="0" title="Labor Strauss Gruppe" alt="Labor Strauss Gruppe" src="http://www.laborstrauss.com/fileadmin/Lst2017/Resources/Public/Images/newsl_footermeplogo.png"></a>
			)
			
			unsubscribe_fpnewsletter = TEXT
			unsubscribe_fpnewsletter.value =  Möchten Sie den LST-Newsletter nicht mehr erhalten, klicken Sie bitte
			
			unsubscribe_fpnewsletter_link = TEXT
			unsubscribe_fpnewsletter_link.value = <a href="https://www.laborstrauss.com/index.php?id=112###USER_fax###&tx_sremailsubscribe_pi1%5BrU%5D=###USER_uid###&tx_sremailsubscribe_pi1[fetch]=###USER_email###&tx_sremailsubscribe_pi1%5BsFK%5D=DELETE&tx_sremailsubscribe_pi1%5BaC%5D=###SYS_AUTHCODE###&tx_sremailsubscribe_pi1%5Bcmd%5D=infomail&fD%5B_FIELDLIST%5D=uid" style="Margin: 0; color: #da251d; font-family: Verdana, Arial, Helvetica, sans-serif; font-weight: normal; line-height: 19px; margin: 0; padding: 0; text-align: left; text-decoration: none;">hier zur Abmeldung.</a>
			
			

			

		}
	}


}
[325 in tree.rootLineIds || 336 in tree.rootLineIds || 388 in tree.rootLineIds || 389 in tree.rootLineIds || 390 in tree.rootLineIds || 391 in tree.rootLineIds ||  11606 in tree.rootLineIds || 11607 in tree.rootLineIds]

page.includeJSFooter {
	replace_tta = fileadmin/Lst2017/Newsletter/Resources/Public/Javascript/replace_ttahash.js
}
[end]

# English
[12028 in tree.rootLineIds || 12030 in tree.rootLineIds || 12032 in tree.rootLineIds || 12034 in tree.rootLineIds || 336 in tree.rootLineIds || 389 in tree.rootLineIds || 391 in tree.rootLineIds || 11607 in tree.rootLineIds]
page.10.variables {
		
		lst_logo = TEXT
		lst_logo.value (
				<a href="https://www.laborstrauss.com/en/" target="_blank" style="display: block; margin: 0; padding: 0;" height="86">	
					<img src="https://www.lst.at/fileadmin/Lst2017/Resources/Public/Images/lst-gruppe-en_2021.png" width="149" height="86" alt="" style="-ms-interpolation-mode: bicubic; clear: both; display: block; width: 149px; height: 86px; outline: none; text-decoration: none;border: 0;" height="86">
				</a>
		)
		
		footer_logo_neu = TEXT
		footer_logo_neu.value (
			<a href="https://www.laborstrauss.com/"><img id="brand" width="151" height="76" border="0" title="Labor Strauss Gruppe" alt="Labor Strauss Gruppe" src="http://www.laborstrauss.com/fileadmin/Lst2017/Newsletter/Resources/Public/Images/lst-logo-nl2_en.jpg"></a>
		)
		
		shownewsletterinbrowser.value = Display newsletter in webbrowser.
			
		footer_sitename = TEXT
		footer_sitename.value = Labor Strauss Group
		
		footer_imprint = TEXT
		footer_imprint.value = <a href="https://www.laborstrauss.com/en/legal-notice/" style="color: #da251d; Margin: 0; color: #da251d; font-family: Verdana, Arial, Helvetica, sans-serif; font-weight: normal; line-height: 19px; margin: 0; padding: 0; text-align: left; text-decoration: none;">Legal notice & Privacy</a>
	}
[end]

# LST Newsletter - Deutsch (neu) - alt 325
[12028 in tree.rootLineIds || 325 in tree.rootLineIds]
	page.10.variables {
		
	}
[end]

# LST Newsletter - English (neu) - alt 336
[12028 in tree.rootLineIds || 336 in tree.rootLineIds]
	page.10.variables {
		unsubscribe_fpnewsletter = TEXT
		unsubscribe_fpnewsletter.value =  If you no longer wish to receive the LST newsletter, please click
			
		unsubscribe_fpnewsletter_link = TEXT
		unsubscribe_fpnewsletter_link.value = <a href="https://www.laborstrauss.com/news/newsletter/edit/?L=1&fL=1&tx_sremailsubscribe_pi1%5BrU%5D=&cHash=030c2b07062da76bd6650019a841e14c" style="Margin: 0; color: #da251d; font-family: Verdana, Arial, Helvetica, sans-serif; font-weight: normal; line-height: 19px; margin: 0; padding: 0; text-align: left; text-decoration: none;">here.</a>
	}
[end]

# Marketing Newsletter - Deutsch (neu) - alt 388
[12029 in tree.rootLineIds || 388 in tree.rootLineIds]
	page.10.variables {
		unsubscribe_fpnewsletter = TEXT
		unsubscribe_fpnewsletter.value =  Diesen Newsletter erhalten Sie, weil Sie sich für den LST-Downloadbereich und den Empfang der Bulletins registriert haben. Möchten Sie den Marketing Newsletter nicht mehr erhalten, senden Sie einfach eine kurze Nachricht an 
			
		unsubscribe_fpnewsletter_link = TEXT
		unsubscribe_fpnewsletter_link.value = <a href="mailto:office@lst.at" style="Margin: 0; color: #da251d; font-family: Verdana, Arial, Helvetica, sans-serif; font-weight: normal; line-height: 19px; margin: 0; padding: 0; text-align: left; text-decoration: none;">office@lst.at</a>
	}
[end]

# Marketing Newsletter - English (neu) - alt 389
[12030 in tree.rootLineIds || 389 in tree.rootLineIds]
	page.10.variables {
		unsubscribe_fpnewsletter = TEXT
		unsubscribe_fpnewsletter.value =  You receive this newsletter because you have registered for the LST download area and the receipt of the Bulletins. If you no longer wish to receive the Marketing Bulletin, just send a short message to 
			
		unsubscribe_fpnewsletter_link = TEXT
		unsubscribe_fpnewsletter_link.value = <a href="mailto:office@lst.at" style="Margin: 0; color: #da251d; font-family: Verdana, Arial, Helvetica, sans-serif; font-weight: normal; line-height: 19px; margin: 0; padding: 0; text-align: left; text-decoration: none;">office@lst.at</a>
	}
[end]

# Technischer Newsletter - Deutsch (neu) - alt 390
[12031 in tree.rootLineIds || 390 in tree.rootLineIds]
	page.10.variables {
		unsubscribe_fpnewsletter >
			
		unsubscribe_fpnewsletter_link >
	}
[end]

# Technischer Newsletter - English (neu) - alt 391
[12032 in tree.rootLineIds || 391 in tree.rootLineIds]
	page.10.variables {
		unsubscribe_fpnewsletter >
			
		unsubscribe_fpnewsletter_link >
	}
[end]

# Newsletter CSV - Deutsch (neu) - alt 11606
[12033 in tree.rootLineIds || 11606 in tree.rootLineIds]

	page.10.variables {
		unsubscribe_fpnewsletter.value = Möchten Sie den LST-Newsletter nicht mehr erhalten, senden Sie einfach eine kurze Nachricht an
		unsubscribe_fpnewsletter_link.value = <a href="mailto:office@lst.at" style="Margin: 0; color: #da251d; font-family: Verdana, Arial, Helvetica, sans-serif; font-weight: normal; line-height: 19px; margin: 0; padding: 0; text-align: left; text-decoration: none;">office@lst.at.</a>
	}
[end]

# Newsletter CSV - English (neu) - alt 11607
[12034 in tree.rootLineIds || 11607 in tree.rootLineIds]
	page.10.variables {
		unsubscribe_fpnewsletter.value = If you no longer wish to receive the LST newsletter, just send a short message to
		unsubscribe_fpnewsletter_link.value = <a href="mailto:office@lst.at" style="Margin: 0; color: #da251d; font-family: Verdana, Arial, Helvetica, sans-serif; font-weight: normal; line-height: 19px; margin: 0; padding: 0; text-align: left; text-decoration: none;">office@lst.at.</a>
	}
[end]




plugin.tx_directmail_pi1 = USER
plugin.tx_directmail_pi1.userFunc = tx_directmail_pi1->main
plugin.tx_directmail_pi1 {
	  siteUrl = {$plugin.tx_directmail_pi1.siteUrl}
	  flowedFormat = {$plugin.tx_directmail_pi1.flowedFormat}
	  header.defaultType = 1
	  header.date = D-m-Y
	  header.datePrefix = |###HEADER_DATE_PREFIX### |
	  header.linkPrefix = | ###HEADER_LINK_PREFIX### |
	  header.1.preLineLen = 76
	  header.1.postLineLen = 76
	  header.1.preBlanks=1
	  header.1.stdWrap.case = upper
	  header.2 < .header.1
	  header.2.preLineChar=*
	  header.2.postLineChar=*
	  header.3.preBlanks=2
	  header.3.postBlanks=1
	  header.3.stdWrap.case = upper
	  
	
	  header.4 < .header.1
	  header.4.preLineChar= =
	  header.4.postLineChar= =
	  header.4.preLineBlanks= 1
	  header.4.postLineBlanks= 1
	  header.5.removeSplitChar = {$plugin.tx_directmail_pi1.removeSplitChar}
	  header.5.preBlanks=1
	  header.5.autonumber=1
	  header.5.prefix = |: >> |
	  defaultOutput (
		|
		[###UNRENDERED_CONTENT### ###CType### ]
		|
	  )
	
	  uploads.header = |###UPLOADS_HEADER###|
	  images.header = |###IMAGES_HEADER###|
	  images.linkPrefix = | ###IMAGE_LINK_PREFIX### |
	  images.captionHeader = |###CAPTION_HEADER###|
	
	  bulletlist.0.bullet = |*  |
	  bulletlist.1.bullet = |#  |
	  bulletlist.2.bullet = | - |
	  bulletlist.3.bullet = |>  |
	  bulletlist.3.secondRow = |.  |
	  bulletlist.3.blanks = 1
	
	  menu =< tt_content.menu.20
	
	  shortcut =< tt_content.shortcut.20
	  shortcut.0.conf.tt_content =< plugin.tx_directmail_pi1
	  shortcut.0.tables = tt_content
	
	  bodytext.doubleLF = {$plugin.tx_directmail_pi1.doubleLF}
	  bodytext.stdWrap.parseFunc.tags {
	    link =< lib.parseFunc_RTE.tags.link
	    typolist = USER
	    typolist.userFunc = tx_directmail_pi1->typolist
	    typolist.siteUrl = {$plugin.tx_directmail_pi1.siteUrl}
	    typolist.bulletlist =< plugin.tx_directmail_pi1.bulletlist
	    typohead = USER
	    typohead.userFunc = tx_directmail_pi1->typohead
	    typohead.siteUrl = {$plugin.tx_directmail_pi1.siteUrl}
	    typohead.header =< plugin.tx_directmail_pi1.header
	    typocode = USER
	    typocode.userFunc = tx_directmail_pi1->typocode
	    typocode.siteUrl = {$plugin.tx_directmail_pi1.siteUrl}
	  }
}

includeLibs.tx_directmail_pi1 = EXT:direct_mail/pi1/class.tx_directmail_pi1.php

tx_directmail_pi1 >
tx_directmail_pi1 = PAGE
tx_directmail_pi1.typeNum=99
tx_directmail_pi1.config {
	  disableAllHeaderCode = 1
	  additionalHeaders = Content-type:text/plain
}


