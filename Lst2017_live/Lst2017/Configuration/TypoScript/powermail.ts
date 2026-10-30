
#lib.genderTitle = CASE
#lib.genderTitle {
##	key.data = TSFE:fe_user|user|gender	
#	key = TEXT
#	key.data = DB:fe_users:1806:gender
#	0 = TEXT
#	0.value = Herr
#	1 = TEXT
#	1.value = Frau			
#}

# CASE does not work. WHY???
lib.genderTitle = COA
lib.genderTitle.10 = TEXT
lib.genderTitle.10 {
	value = Herr
	if.value.data = TSFE:fe_user|user|gender	
	if.equals = 0
}
lib.genderTitle.20 = TEXT
lib.genderTitle.20 {
	value = Frau
	if.value.data = TSFE:fe_user|user|gender	
	if.equals = 1
}



[siteLanguage("languageId") == 1]
#[Gtn\GtnLsthooks\Condition\FeLanguageSelectedCondition = 1]
	lib.genderTitle.10.value = Mr
	lib.genderTitle.20.value = Ms
[end]

plugin.tx_powermail {
        view {
                templateRootPaths {
                        0 = EXT:powermail/Resources/Private/Templates/
                        1 = fileadmin/Lst2017/Resources/Private/powermail/Templates/
                }
                partialRootPaths {
                        0 = EXT:powermail/Resources/Private/Partials/
                        1 = fileadmin/Lst2017/Resources/Private/powermail/Partials/
                }
                layoutRootPaths {
                        0 = EXT:powermail/Resources/Private/Layouts/
                        1 = fileadmin/Lst2017/Resources/Private/powermail/Layouts/
                }
        }
	
	settings {
		setup {
			captcha.use = captcha
			prefill {				
#				anrede = TEXT
#				anrede.data = TSFE:fe_user|user|gender
#				anrede.value = Herr
				anrede < lib.genderTitle
				andrede < lib.genderTitle
				# Allgemeine Anfragen
				land = TEXT
				land.data = TSFE:fe_user|user|static_info_country
				# RAN
				land2 = TEXT
				land2.data = TSFE:fe_user|user|static_info_country
				# RAN
				land_01 = TEXT
				land_01.data = TSFE:fe_user|user|static_info_country
				# Course registration
				kundennummer = TEXT
				kundennummer.data = TSFE:fe_user|user|customer_number
			}
		}
	}

	_LOCAL_LANG {
		de {
#			error_wrong_captcha = err1
#			validationerror_captcha = err2

			validationerror_validation.9 = Ungültige Eingabe (zu viele/zu wenige Zeichen)!
            validationerror_validation.10 = Ungültige Eingabe!
            # confirmation_next = Zahlungspflichtig bestellen
		}
		en {
			validationerror_mandatory = This field must be filled in!
			
			validationerror_validation.9 = Invalid entry (too many/not enough characters)! 
            validationerror_validation.10 = Ungültige Eingabe!
            # confirmation_next = Place an order with obligation to pay
		}
	}
}



# for RAN
[32 in tree.rootLineIds]
#[page["uid"] == 32] 
plugin.tx_powermail {
        view {
                templateRootPaths {                        
                        33 = fileadmin/Lst2017/Resources/Private/powermail_RAN/Templates/
                }
        }
}
[end]

# for COURSE REGISTRATION form
[12213 in tree.rootLineIds]
plugin.tx_powermail {
        view {
                templateRootPaths {                        
                        33 = fileadmin/Lst2017/Resources/Private/powermail_Courses/Templates/
                }
                partialRootPaths {                        
                        33 = fileadmin/Lst2017/Resources/Private/powermail_Courses/Partials/
                }
        }
}
[end]

plugin.tx_powermail {
  settings {
    mail {
      receiver {
        myCustomVar = TEXT
        myCustomVar.value = Laborstrauss1
      }
    }
  }
}


# for DayCode form
[11249 in tree.rootLineIds]
#plugin.tx_powermail.settings.setup.validation.native = 0
#plugin.tx_powermail.settings.setup.validation.client = 0
plugin.tx_powermail.settings.setup.validation.server = 0
#plain mails to admin
plugin.tx_powermail.settings.setup.receiver.mailformat = plain
[end]

# for Tech support form. becasue the country marker is 'marker'
[86 in tree.rootLineIds]
    plugin.tx_powermail.settings.setup.prefill {
	marker = TEXT
	marker.data = TSFE:fe_user|user|static_info_country
}
[end]

[11490 in tree.rootLineIds]
    plugin.tx_powermail {
        settings {
            setup {

                # Manipulate values from {powermail_all} by markername
                manipulateVariablesInPowermailAllMarker {
                    # On Confirmation Page (if activated)
                    confirmationPage {
                        # manipulate values by given marker (e.g. firstname, email, referrer) with TypoScript - available fieldnames (access with .field=): value, valueType, uid, pid
                        anrede = CASE
                        anrede {
                            key.field = value

                            m = TEXT
                            m.value = Herr

                            default = TEXT
                            default.value = Frau
                        }

                        newsletter = CASE
                        newsletter {
                            key.field = value

                            0 = TEXT
                            0.value = Nein

                            default = TEXT
                            default.value = Ja
                        }
                    }

                }

                dbEntry {

                    #####################################################
                    ### EXAMPLE for adding values to table tt_address ###
                    #####################################################

                    1 {
                        # Enable or disable db entry for table tt_address
                        _enable = TEXT
                        _enable.value = 1
                        _enable.if.isTrue.field = newsletter

                        # Set tableName to "tt_address"
                        _table = TEXT
                        _table.value = tt_address

                        # Write only if any field is not yet filled with current value (e.g. test if an email is already in database)
                        # default: always add new records (don't care about existing values)
                        # update: update record if there is an existing entry (e.g. if email is already there)
                        # none: no entry if field is filled (do nothing if record already exists)
                        _ifUnique.email = update

                        # optional: add additional where clause (only in mode "update") for search if a record still exists. You could use a plain string (see example below) or a cObject if needed
                        _ifUniqueWhereClause = AND pid = 114

                        # Fill tt_address.email with a static value => mail@mail.com
                        email = TEXT
                        email.field = e_mail

                        # Fill tt_address.pid with the current pid (e.g. 12)
                        pid = TEXT
                        pid.value = 114

                        # Fill tt_address.tstamp with the current time as timestamp (like 123456789)
                        tstamp = TEXT
                        tstamp.data = date:U

                        crdate = TEXT
                        crdate.data = date:U

                        # Fill tt_address.address with the current formatted time (like "Date: 20.01.2013")
                        address = TEXT
                        address.data = date:U
                        address.strftime = Date: %d.%m.%Y

                        gender = TEXT
                        gender.field = anrede

                        # Fill tt_address.name with the value from powermail {firstname}
                        first_name = TEXT
                        first_name.field = vorname

                        # Fill tt_address.last_name with the value from powermail {lastname}
                        last_name = TEXT
                        last_name.field = nachname

                        # Fill tt_address.company with the value from powermail {company}
                        company = TEXT
                        company.field = firma


                        zip = TEXT
                        zip.field = plz

                        city = TEXT
                        city.field = ort

                        country = TEXT
                        country.field = land

                        static_info_country = TEXT
                        static_info_country.field = land



                    }
                }

            }

        }

    }
[end]

[11602 in tree.rootLineIds]
    plugin.tx_powermail {
        settings {
            setup {

                # Manipulate values from {powermail_all} by markername
                manipulateVariablesInPowermailAllMarker {
                    # On Confirmation Page (if activated)
                    confirmationPage {
                        # manipulate values by given marker (e.g. firstname, email, referrer) with TypoScript - available fieldnames (access with .field=): value, valueType, uid, pid
                        anrede = CASE
                        anrede {
                            key.field = value

                            m = TEXT
                            m.value = Herr

                            default = TEXT
                            default.value = Frau
                        }

#                        newsletter = CASE
#                        newsletter {
#                            key.field = value
#
#                            0 = TEXT
#                            0.value = Nein
#
#                            default = TEXT
#                            default.value = Ja
#                        }
                    }

                }

                dbEntry {

                    #####################################################
                    ### EXAMPLE for adding values to table tt_address ###
                    #####################################################

                    1 {
                        # Enable or disable db entry for table tt_address
                        _enable = TEXT
                        _enable.value = 1
#                        _enable.if.isTrue.field = newsletter

                        # Set tableName to "tt_address"
                        _table = TEXT
                        _table.value = tt_address

                        # Write only if any field is not yet filled with current value (e.g. test if an email is already in database)
                        # default: always add new records (don't care about existing values)
                        # update: update record if there is an existing entry (e.g. if email is already there)
                        # none: no entry if field is filled (do nothing if record already exists)
                        _ifUnique.email = update

                        # optional: add additional where clause (only in mode "update") for search if a record still exists. You could use a plain string (see example below) or a cObject if needed
                        _ifUniqueWhereClause = AND pid = 114

                        # Fill tt_address.email with a static value => mail@mail.com
                        email = TEXT
                        email.field = e_mail

                        # Fill tt_address.pid with the current pid (e.g. 12)
                        pid = TEXT
                        pid.value = 114

                        # Fill tt_address.tstamp with the current time as timestamp (like 123456789)
                        tstamp = TEXT
                        tstamp.data = date:U

                        crdate = TEXT
                        crdate.data = date:U

                        # Fill tt_address.address with the current formatted time (like "Date: 20.01.2013")
                        address = TEXT
                        address.data = date:U
                        address.strftime = Date: %d.%m.%Y

                        gender = TEXT
                        gender.field = anrede

                        # Fill tt_address.name with the value from powermail {firstname}
                        first_name = TEXT
                        first_name.field = vorname

                        # Fill tt_address.last_name with the value from powermail {lastname}
                        last_name = TEXT
                        last_name.field = nachname

                        # Fill tt_address.company with the value from powermail {company}
                        company = TEXT
                        company.field = firma


                        zip = TEXT
                        zip.field = plz

                        city = TEXT
                        city.field = ort

                        country = TEXT
                        country.field = land

                        static_info_country = TEXT
                        static_info_country.field = land



                    }
                }

            }

        }

    }
[end]

[siteLanguage("languageId") == 1 && 11490 in tree.rootLineIds]
    ## English people to english newsletter users
    plugin.tx_powermail.settings.setup.dbEntry.1._ifUniqueWhereClause = AND pid = 335
    plugin.tx_powermail.settings.setup.dbEntry.1.pid.value = 335
    ## English texts confirmation page

    plugin.tx_powermail.settings.setup.manipulateVariablesInPowermailAllMarker.confirmationPage {
        anrede = CASE
        anrede {
            key.field = value

            m = TEXT
            m.value = Mr

            default = TEXT
            default.value = Ms
        }

        newsletter = CASE
        newsletter {
            key.field = value

            0 = TEXT
            0.value = No

            default = TEXT
            default.value = Yes
        }
    }
[end]



    plugin.tx_powermail {
 	settings {
		setup {
			# Exclude values from {powermail_all} by markername or fieldtype
                        excludeFromPowermailAllMarker {
                                # On Confirmation Page (if activated)
                                confirmationPage {
                                        # add some markernames (commaseparated) which should be excluded
                                        excludeFromMarkerNames = articlesdata, datenschutz, participantsdata, mp, kursuid, gleicheanschriftgleichefirma, gleicheanschriftgleichefirma_1, courseid, showsertfield, abweichenderechnungsadresse, agbs, forceApprove, forceDecline

                                        # add some fieldtypes (commaseparated) which should be excluded
                                        excludeFromFieldTypes = hidden, captcha
                                }

                                # On Submitpage
                                submitPage {
                                        # add some markernames (commaseparated) which should be excluded
                                        excludeFromMarkerNames = articlesdata, datenschutz, participantsdata, mp, kursuid, gleicheanschriftgleichefirma, gleicheanschriftgleichefirma_1, courseid, showsertfield, abweichenderechnungsadresse, agbs, forceApprove, forceDecline

                                        # add some fieldtypes (commaseparated) which should be excluded
                                        excludeFromFieldTypes = captcha
                                }

                                # In Mail to receiver
                                receiverMail {
                                        # add some markernames (commaseparated) which should be excluded
                                        excludeFromMarkerNames = articlesdata, datenschutz, participantsdata, mp, kursuid, gleicheanschriftgleichefirma, gleicheanschriftgleichefirma_1, showsertfield, abweichenderechnungsadresse, agbs, forceApprove, forceDecline

                                        # add some fieldtypes (commaseparated) which should be excluded
                                        excludeFromFieldTypes = captcha
                                }

                                # In Mail to sender (if activated)
                                senderMail {
                                        # add some markernames (commaseparated) which should be excluded
                                        excludeFromMarkerNames = articlesdata, datenschutz, participantsdata, mp, kursuid, gleicheanschriftgleichefirma, gleicheanschriftgleichefirma_1, courseid, showsertfield, abweichenderechnungsadresse, agbs, forceApprove, forceDecline

                                        # add some fieldtypes (commaseparated) which should be excluded
                                        excludeFromFieldTypes = captcha
                                }

                                # In double-opt-in Mail to sender (if activated)
                                optinMail {
                                        # add some markernames (commaseparated) which should be excluded
                                        excludeFromMarkerNames = articlesdata, datenschutz, participantsdata, mp, kursuid, gleicheanschriftgleichefirma, gleicheanschriftgleichefirma_1, showsertfield, abweichenderechnungsadresse, agbs, forceApprove, forceDecline

                                        # add some fieldtypes (commaseparated) which should be excluded
                                        excludeFromFieldTypes = captcha
                                }
                        }
#			dataProcessors {
#				143 {
#					# Classname that should be called with method *DataProcessor()
#					class = Gtn\GtnLsthooks\Hooks\AddCsvDataProcessor
#				}
#			}
		}
	}
}

# add content for RAN print modal
lib.addContentForPrint = COA
lib.addContentForPrint {   
    10 = CONTENT
    10 {
        table = tt_content
        select.where = colPos = 0
        select.pidInList = 320
    }
    10.stdWrap.wrap = <div class="add-print-header" style="display:none;">|</div>
    20 = CONTENT
    20 {
        table = tt_content
        select.where = colPos = 2
        select.pidInList = 320
    }
    20.stdWrap.wrap = <div class="add-print-footer" style="display:none;">|</div>
}

// used for Print page for RAN form
[page["uid"] == 32]
	page.2010 < lib.addContentForPrint
[global]


# looks like some bug in ppowermail. Even our hook (signal) does not help
plugin.tx_powermail.settings.setup.receiver.default.senderEmail = TEXT
plugin.tx_powermail.settings.setup.receiver.default.senderEmail.value = noreply@laborstrauss.com
plugin.tx_powermail.settings.setup.receiver.overwrite.senderEmail = TEXT
plugin.tx_powermail.settings.setup.receiver.overwrite.senderEmail.value = noreply@laborstrauss.com
plugin.tx_powermail.settings.setup.receiver.overwrite.senderName = TEXT
plugin.tx_powermail.settings.setup.receiver.overwrite.senderName.value = Labor Strauss Gruppe


# receivers for Course registrations
plugin.tx_powermail.settings.setup.receiver.predefinedReceiver {
	receivers167.email = CASE
	receivers167.email {

	        key.data = GP:tx_powermail_pi1|field|kursorganizer
		key.stdWrap.replacement {
			10 {
				# spaces
				search.char = 32
				replace = _
			}
			20 {
				# spaces
				search = #\s#i
				replace = _
				useRegExp = 1
			}
			30 {
				# /
				search.char = 47
				replace = _
			}
			40 {
				# -
				search.char = 45
				replace = _
			}
			# umlauts
			50 {		
				search = /ä|Ä/
			        replace = ae
			        useRegExp = 1
			}
			60 {
				search = /ö|Ö/
				replace = oe
				useRegExp = 1
			}
			70 {
				search = /ü|Ü/
				replace = ue
				useRegExp = 1
			}
			80 {
				search = /ß/
				replace = ss
				useRegExp = 1
			}
		}

        
        # Schulungzentrum Emails für kursorganizer from field Organizer
        
	    LST_Firmenzentrale_Wien = TEXT
        #LST_Firmenzentrale_Wien.value = office@lst.at
        LST_Firmenzentrale_Wien.value = Anmeldung-Wien@laborstrauss.com

	    LST_Niederlassung_Graz = TEXT
        #LST_Niederlassung_Graz.value = office-graz@lst.at
        LST_Niederlassung_Graz.value = Anmeldung-Graz@laborstrauss.com

		LST_Niederlassung_Innsbruck = TEXT
		#LST_Niederlassung_Innsbruck.value = office-ibk@lst.at
        LST_Niederlassung_Innsbruck.value = Anmeldung-Innsbruck@laborstrauss.com
		
		MEP_Firmenzentrale_Pockau = TEXT
		#MEP_Firmenzentrale_Pockau.value = annett.morgenstern@mep-pockau.de
        MEP_Firmenzentrale_Pockau.value = Anmeldung-Pockau-lengefeld@laborstrauss.com
		
		LST_Niederlassung_Augsburg = TEXT
		#LST_Niederlassung_Augsburg.value = office@lst-sued.de
        LST_Niederlassung_Augsburg.value = Anmeldung-Augsburg@laborstrauss.com

		LST_Niederlassung_Moenchengladbach = TEXT
		#LST_Niederlassung_Moenchengladbach.value = office@laborstrauss.de
        LST_Niederlassung_Moenchengladbach.value = Anmeldung-Moenchengladbach@laborstrauss.com 	
		
		LST_Niederlassung_Hamburg = TEXT
		#LST_Niederlassung_Hamburg.value = office@lst-nord.de
        LST_Niederlassung_Hamburg.value = Anmeldung-Hamburg@laborstrauss.com

		MEP_Verkaufsbuero_Erfurt = TEXT
		#MEP_Verkaufsbuero_Erfurt.value = annett.morgenstern@mep-pockau.de
        MEP_Verkaufsbuero_Erfurt.value = Anmeldung-Erfurt@laborstrauss.com
        
        LST_Niederlassung_Internationaler_Verkauf = TEXT
        LST_Niederlassung_Internationaler_Verkauf.value = Anmeldung-Export@laborstrauss.com
#        LST_Niederlassung_Internationaler_Verkauf.value = szavarzin@gtn-solutions.com

	    default = TEXT
	    default.value = office@lst.at
#	    default.value = szavarzin@gtn-solutions.com
	}
}

# Conditions if kursorganizer is empty use region 

# Wien
[regionSelected("9") && traverse(request.getParsedBody(), "tx_powermail_pi1/field/kursorganizer") == ""]
	plugin.tx_powermail.settings.setup.receiver.predefinedReceiver.receivers167.email >
	plugin.tx_powermail.settings.setup.receiver.predefinedReceiver.receivers167.email = TEXT
	plugin.tx_powermail.settings.setup.receiver.predefinedReceiver.receivers167.email.value = Anmeldung-Wien@laborstrauss.com
[global]

# Graz
[regionSelected("10") && traverse(request.getParsedBody(), "tx_powermail_pi1/field/kursorganizer") == ""]
	plugin.tx_powermail.settings.setup.receiver.predefinedReceiver.receivers167.email >
	plugin.tx_powermail.settings.setup.receiver.predefinedReceiver.receivers167.email = TEXT
	plugin.tx_powermail.settings.setup.receiver.predefinedReceiver.receivers167.email.value = Anmeldung-Graz@laborstrauss.com
[global]

# Innsbruck
[regionSelected("11") && traverse(request.getParsedBody(), "tx_powermail_pi1/field/kursorganizer") == ""]
	plugin.tx_powermail.settings.setup.receiver.predefinedReceiver.receivers167.email >
	plugin.tx_powermail.settings.setup.receiver.predefinedReceiver.receivers167.email = TEXT
	plugin.tx_powermail.settings.setup.receiver.predefinedReceiver.receivers167.email.value = Anmeldung-Innsbruck@laborstrauss.com
[global]

# Mönchengladbach
[regionSelected("12") && traverse(request.getParsedBody(), "tx_powermail_pi1/field/kursorganizer") == ""]
	plugin.tx_powermail.settings.setup.receiver.predefinedReceiver.receivers167.email >
	plugin.tx_powermail.settings.setup.receiver.predefinedReceiver.receivers167.email = TEXT
	plugin.tx_powermail.settings.setup.receiver.predefinedReceiver.receivers167.email.value = Anmeldung-Moenchengladbach@laborstrauss.com
[global]

# Hamburg
[regionSelected("13") && traverse(request.getParsedBody(), "tx_powermail_pi1/field/kursorganizer") == ""]
	plugin.tx_powermail.settings.setup.receiver.predefinedReceiver.receivers167.email >
	plugin.tx_powermail.settings.setup.receiver.predefinedReceiver.receivers167.email = TEXT
	plugin.tx_powermail.settings.setup.receiver.predefinedReceiver.receivers167.email.value = Anmeldung-Hamburg@laborstrauss.com
[global]

# Pockau
[regionSelected("14") && traverse(request.getParsedBody(), "tx_powermail_pi1/field/kursorganizer") == ""]
	plugin.tx_powermail.settings.setup.receiver.predefinedReceiver.receivers167.email >
	plugin.tx_powermail.settings.setup.receiver.predefinedReceiver.receivers167.email = TEXT
	plugin.tx_powermail.settings.setup.receiver.predefinedReceiver.receivers167.email.value = Anmeldung-Pockau-lengefeld@laborstrauss.com
[global]

# Intern
[regionSelected("15") && traverse(request.getParsedBody(), "tx_powermail_pi1/field/kursorganizer") == ""]
	plugin.tx_powermail.settings.setup.receiver.predefinedReceiver.receivers167.email >
	plugin.tx_powermail.settings.setup.receiver.predefinedReceiver.receivers167.email = TEXT
	plugin.tx_powermail.settings.setup.receiver.predefinedReceiver.receivers167.email.value = Anmeldung-Export@laborstrauss.com
[global]

# noRegion
[regionSelected("17") && traverse(request.getParsedBody(), "tx_powermail_pi1/field/kursorganizer") == ""]
	plugin.tx_powermail.settings.setup.receiver.predefinedReceiver.receivers167.email >
	plugin.tx_powermail.settings.setup.receiver.predefinedReceiver.receivers167.email = TEXT
	plugin.tx_powermail.settings.setup.receiver.predefinedReceiver.receivers167.email.value = office@lst.at
[global]

# Augsburg
[regionSelected("44") && traverse(request.getParsedBody(), "tx_powermail_pi1/field/kursorganizer") == ""]
	plugin.tx_powermail.settings.setup.receiver.predefinedReceiver.receivers167.email >
	plugin.tx_powermail.settings.setup.receiver.predefinedReceiver.receivers167.email = TEXT
	plugin.tx_powermail.settings.setup.receiver.predefinedReceiver.receivers167.email.value = Anmeldung-Augsburg@laborstrauss.com
[global]


# just for testing!
[page["uid"] == 12435]
	plugin.tx_powermail.settings.setup.db.enable = 0
#	plugin.tx_powermail.settings.setup.receiver.enable = 0
[global]


# for dev/test only
[ip("91.123.151.20")]
#	plugin.tx_powermail.settings.setup.db.enable = 0
#	plugin.tx_powermail.settings.setup.sender.enable = 0
#	plugin.tx_powermail.settings.setup.finishers >
[END]

plugin.tx_powermail.settings.setup.misc.file.extension = jpg,jpeg,gif,png,tif,txt,doc,docx,xls,xlsx,ppt,pptx,pdf,mpg,mpeg,avi,mp3,zip,rar,ace,csv,svg,mp4
plugin.tx_powermail.settings.setup.misc.file = jpg,jpeg,gif,png,tif,txt,doc,docx,xls,xlsx,ppt,pptx,pdf,mpg,mpeg,avi,mp3,zip,rar,ace,csv,svg,mp4
plugin.tx_powermail.settings.Pi2.misc.file.extension = jpg,jpeg,gif,png,tif,txt,doc,docx,xls,xlsx,ppt,pptx,pdf,mpg,mpeg,avi,mp3,zip,rar,ace,csv,svg,mp4