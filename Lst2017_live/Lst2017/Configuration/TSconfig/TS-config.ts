# <INCLUDE_TYPOSCRIPT: source="FILE: fileadmin/phooe/Configuration/TSconfig/TS-config_oldPHOOE.ts">

TCAdefaults.tx_news_domain_model_news.hidden=1

# Rahmen unter Indention and Frames entfernen
TCEFORM.tt_content.section_frame {
     removeItems = 1,5,6,10,11,12,20,21
}

# Appearance Layouts definieren
TCEFORM.tt_content {
    layout {
        altLabels {
            1 = Normal
            2 = blank
			3 = Slider
			4 = Kachel groß (4/12)
			5 = Headerbild
			6 = Kachel klein (3/12)
			7 = Person Card
			8 = versteckter Inhalt
			9 = Header versteckter Inhalt
			10 = Person Card Sidebar
			11 = Gallery (4spaltig)
			12 = Alert (Danger)
			13 = Alert
			14 = Print Page Header Address
			15 = Info PopUp
			16 = Video (Youtube) responsiv einbetten

        }
        addItems{
			4 = Produktgruppe
			5 = Headerbild
			6 = Kachel klein (3/12)
			7 = Person Card
			8 = versteckter Inhalt
			9 = Header versteckter Inhalt
			10 = Person Card Sidebar
			11 = Gallery (4spaltig)
			12 = Alert (Danger)
			13 = Alert
			14 = Print Page Header Address
			15 = Info PopUp
			16 = Video (Youtube) responsiv einbetten
			17 = Newsletter: 2 Spalten, Text und Bild rechts
	        18 = Newsletter: Bild unter Text 
	        19 = Newsletter: Bild über Text 
	        20 = Newsletter: 2 Spalten, Text und Bild links
            21 = Kachel sehr groß (6/12)
        }
       }
}

# Frontend Layouts Seiteneigenschaften
#TCEFORM.pages.layout {
#	addItems {
#
#	}
#	altLabels {
#		1 = Jobseeker
#		2 = Employer
#		3 = -
#	}
#}

# Example:
tx_news.templateLayouts {
    1 = News List Home
    2 = News List
    3 = News List Stellenausschreibungen
    4 = News Stellenausschreibungen Detail
    5 = News List Schulungen
    6 = News Schulungen Detail
    7 = News List Schulungen - Hamburg ganzjährig
}

mod.SHARED.defaultLanguageLabel = Deutsch

# Eigenen Header Style hinzufügen um versteckten Inhalt anzuzeigen
TCEFORM.tt_content.header_layout {
    addItems {
     7 = Header Slider H4, render HTML
     8 = Header Kachel
     9 = Header Person Card
     10 = Header Ansprechpartner Gruppe
     11 = Header öffnet versteckten Inhalt
     12 = Header Grauer HG
     13 = Header H2 Plakativ
     14 = Header H2 rot
    }
}

# Powermail Layout Field
TCEFORM {
        tx_powermail_domain_model_form {
                css {
                        removeItems = layout1, layout2, layout3
                        addItems {
                                form-row = Form Row
                                form-row-participants = Form Row Teilnehmer
                                form-group = Form Group
                                col-md-4 = Form Group md 4
                                col-md-5 = Form Group md 5
                                col-md-6 = Form Group md 6
                                col-md-2 = Form Group md 2
                                col-md-10 = Form Group md 10
                                col-md-8 = Form Group md 8
				                col-md-12 = Form Group md 12
                                info-small = Small Info Text
                        }
                }
        }
        tx_powermail_domain_model_page < .tx_powermail_domain_model_form
        tx_powermail_domain_model_field < .tx_powermail_domain_model_form
}

TCEMAIN.table.tt_content {
 disablePrependAtCopy = 1
 //disableHideAtCopy = 1
}

# Fügt die Konfiguration "full" allen RTE's hinzu
# RTE.default.preset = full
RTE.default.preset = gtn_default
# Fügt die Konfiguration "minimal" dem bodytext von Inhaltselementen des Typs textmedia hinzu
RTE.tt_content.types.textmedia.bodytext.preset = gtn_default
RTE.config.tt_content.bodytext.types.textmedia.preset = gtn_default
# Fügt die Konfiguration "default" dem bodytext von News-Datensätzen hinzu
RTE.config.tx_news_domain_model_news.bodytext.preset = gtn_default

config.admPanel = 0

tx_powermail.flexForm.feUserProperty.addFieldOptions.first_name = First name 
tx_powermail.flexForm.feUserProperty.addFieldOptions.last_name = Last name 

# Setzen der Seitenbaumrechte per TypoScript
TCEMAIN {
	# Besitzergruppe festlegen (ID der Gruppe „Seitenbaumrechte“):
	permissions.groupid = 1 

	# Rechte Besitzer:
	permissions.user = show, editcontent, edit, delete, new

	# Rechte Besitzergruppe:
	permissions.group = show, editcontent, edit, new

	# Rechte "everybody" (kann ungesetzt bleiben):
	permissions.everybody =
}

# Unbennenen des Translate to Tags
TCEMAIN {
    translateToMessage = %s
}

# Entfernen des Kopie-Tags bei tt_content
TCEMAIN.table.tt_content {
    disablePrependAtCopy = 1
   # disableHideAtCopy = 1
}

# Entfernen des Kopie-Tags bei pages
TCEMAIN.table.pages {
    disablePrependAtCopy = 1
   # disableHideAtCopy = 1
}

TCEFORM.sys_file_reference.crop.config.cropVariants {
  default {
    title = Default desktop
    selectedRatio = NaN
    allowedAspectRatios {
      NaN {
        title = Frei
        value = 0.0
      }
      1:1 {
        title = 1:1
        value = 1.0
      }
      3:2 {
        title = 3:2
        value = 1.5
      }
      4:3 {
        title = 4:3
        value = 1.3333333333
      }
      16:9 {
        title = 16:9
        value = 1.7777777778
      }
      730:658 {
	      title = Kacheln 
		  value = 1.10942249
      }
      1400:750 {
	      title = Slider/Header 
		  value = 1.86666667
      }
    }
  }
}

