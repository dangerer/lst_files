
plugin.tx_srfeuserregister_pi1 {

	templateFile = fileadmin/Lst2017/Resources/Private/Templates/RegisterHtmlTemplate.html

	pid = 47
	userGroupsPidList = 1
	registerPID = 67
	editPID = 67
	confirmPID = 67
	loginPID = 38
	
	email.from = office@lst.at
	email.fromName = Labor Strauss Gruppe
	email.admin = office@lst.at
	email.replyToAdmin = office@lst.at

	parseValues.gender =

	create {
		preview = 0
		# captcha_response
		fields = email, first_name, last_name, password, company, gender, address, city, zip, country, static_info_country, terms_acknowledged, telephone, lst_newsletter, technical_newsletter, module_sys_dmail_html, module_sys_dmail_newsletter,fe_language,customer_number
#		fields = email, first_name, last_name, password, company, gender, address, city, zip, country, static_info_country, terms_acknowledged, telephone, lst_newsletter, technical_newsletter, module_sys_dmail_html, module_sys_dmail_newsletter,fe_language
		required = email, first_name, last_name, password, company, gender, address, city, zip, static_info_country, terms_acknowledged, telephone, technical_newsletter
		allowUserGroupSelection = 0
		allowedUserGroups = 1
		defaultValues {
			usergroup = 1
			lst_newsletter = 0
			technical_newsletter = 0
			module_sys_dmail_html = 1
			module_sys_dmail_newsletter = 1
#			gender = 99
			fe_language = 0
		}
		overrideValues {
			module_sys_dmail_html = 1
			module_sys_dmail_newsletter = 1
			usergroup = 1
#			gender = 99
		}
		evalValues {
			# at least one upercase, one lowercase, special character
			# preg[^(?=.*)(?=.*[a-z])(?=.*[A-Z])(?=.*[`~@#$%^&+=.,:;?!\-_*()\\\[\]"'|])([a-zA-Z0-9`~@#$%^&+=*.,:;?!\-_()\\\[\]"'|]){1,100}$]
			# preg does not work because preg contains ']'
			password = twice,atLeast[6],gtnLstHook
			# captcha_response = freecap
			email = email,atMost[255],uniqueGlobal
		}
	}

	setfixed.APPROVE.usergroup >
	setfixed.ACCEPT.usergroup >

	lstSettings {
		# disabled. use TS conditions below
		zonePidRelations {
			# rule:  zoneID.userPidId  =  pid
			# default
#			0.userPidId = 47
			# Wien
#			9.userPidId = 49
			# Graz
#			10.userPidId = 50
			# Innsbtuck
#			11.userPidId = 54
			# Mönchengladbach
#			12.userPidId = 52
			# Hamburg
#			13.userPidId = 53
			# Pockau-Lengefeld
#			14.userPidId = 55
			# international
#			15.userPidId = 56
		}
		regionDescriptions {
			# rule: zoneId = properties
			# Wien
			9 {
				shortPath = Österreich/Wien
			}
			# Graz
			10 {
				shortPath = Österreich/Graz
			}
			# Innsbtuck
			11 {
				shortPath = Österreich/Innsbruck
			}
			# Mönchengladbach
			12 {
				shortPath = Deutschland/Mönchengladbach
			}
			# Hamburg
			13 {
				shortPath = Deutschland/Hamburg
			}
			# Pockau-Lengefeld
			14 {
				shortPath = MEP/Pockau
			}
			# international
			15 {
				shortPath = International
			}

		}
		# link to Impressum
		agbPid = 11
		kontaktPid = 8
	}

	
}

[siteLanguage("languageId") == 1]
#[Gtn\GtnLsthooks\Condition\FeLanguageSelectedCondition = 1]
plugin.tx_srfeuserregister_pi1.email.fromName = Labor Strauss Group
plugin.tx_srfeuserregister_pi1.create.defaultValues.fe_language = 1
[global]

# Wien
[regionSelected("9")]
	plugin.tx_srfeuserregister_pi1 {
		pid = 49
		# Reciever Email Addresses are in constants file fileadmin/Registrierung_Email_Empfaenger/empfaengerliste.ts
		email.admin = {$empfaenger_wien}
	}
[global]

# Graz
[regionSelected("10")]
	plugin.tx_srfeuserregister_pi1 {
		pid = 50
		# Reciever Email Addresses are in constants file fileadmin/Registrierung_Email_Empfaenger/empfaengerliste.ts
		email.admin = {$empfaenger_graz}
	}
[global]

# Innsbruck
[regionSelected("11")]
	plugin.tx_srfeuserregister_pi1 {
		pid = 54
		# Reciever Email Addresses are in constants file fileadmin/Registrierung_Email_Empfaenger/empfaengerliste.ts
		email.admin = {$empfaenger_innsbruck}
	}
[global]

# Mönchengladbach
[regionSelected("12")]
	plugin.tx_srfeuserregister_pi1 {
		pid = 52
		# Reciever Email Addresses are in constants file fileadmin/Registrierung_Email_Empfaenger/empfaengerliste.ts
		email.admin = {$empfaenger_moenchengladbach}
	}
[global]

# Hamburg
[regionSelected("13")]
	plugin.tx_srfeuserregister_pi1 {
		pid = 53
		# Reciever Email Addresses are in constants file fileadmin/Registrierung_Email_Empfaenger/empfaengerliste.ts
		email.admin = {$empfaenger_hamburg}
	}
[global]

# Pockau-Lengefeld 	
[regionSelected("14")]
	plugin.tx_srfeuserregister_pi1 {
		pid = 55
		# Reciever Email Addresses are in constants file fileadmin/Registrierung_Email_Empfaenger/empfaengerliste.ts
		email.admin = {$empfaenger_pockau}
	}
[global]

# Augsburg Kontakt
[regionSelected("44")]
	plugin.tx_srfeuserregister_pi1 {
		pid = 12101
		email.admin = {$empfaenger_augsburg}
	}
[global]

# Internationaler Kontakt
[regionSelected("15")]
	plugin.tx_srfeuserregister_pi1 {
		pid = 56
		email.admin = {$empfaenger_international}
	}
[global]


# developer
#plugin.tx_srfeuserregister_pi1.email.admin >
#plugin.tx_srfeuserregister_pi1.email.admin = szavarzin@gtn-solutions.com

plugin.tx_srfeuserregister_pi1.extraLabels = lst_danke, lst_danke_with_region, lst_registration_confirm_instructions, lst_link_kontakt, lst_email_confirmed, lst_admin_will_process, lst_agb_confirmation_label, lst_passwordRulesForTooltip

plugin.tx_srfeuserregister {
	_LOCAL_LANG {
		de {
			lst_danke = Vielen Dank für Ihre Registrierung.
			lst_danke_with_region = Vielen Dank für Ihre Registrierung bei Labor Strauss
			lst_registration_confirm_instructions = <p>Sie erhalten in Kürze ein E-mail, um Ihre Anmeldung zu bestätigen.<br />Ein Mitarbeiter wird Ihre Anmeldung bearbeiten und Sie für den Downloadbereich freischalten.</p><p>Bei Rückfragen werden wir uns erlauben, Sie via E-Mail zu kontaktieren.</p>
			lst_registration_confirm_instructions = <p>Ihr Benutzerkonto für den geschützten Downloadbereich der LST-Webseite wurde erstellt. Wir senden ihnen eine E-Mail, damit Sie die Registrierung bestätigen können. Beachten Sie die Hinweise in diesem E-Mail. </p><p>Wir ersuchen um Kenntnisnahme, dass die Registrierung vom Administrator bestätigt werden muss, bevor Sie Zugriff auf den Downloadbereich unserer Webseite erhalten.</p>
			
			lst_link_kontakt = Für Fragen stehen wir Ihnen gerne zur Verfügung.
			lst_email_confirmed = Ihre E-Mail-Adresse ist nun bestätigt.
			lst_admin_will_process = Ein Mitarbeiter wird Ihre Anmeldung bearbeiten und Sie für den Downloadbereich freischalten. Bei Rückfragen werden wir uns erlauben, Sie via E-Mail zu kontaktieren. 			
			lst_agb_confirmation_label = Ich habe die <a href="%s#c1798" target="_blank">Datenschutzerklärung</a> gelesen und erkläre mich mit der automationsgestützten Verarbeitung meiner oben angeführten Daten einverstanden. <span>*</span>
			lst_passwordRulesForTooltip = Das Passwort muss mindestens 6 Zeichen enthalten und mindestens einen Großbuchstaben, einen Kleinbuchstaben und eine Ziffer beinhalten!

			LST_GENDER_M = Herr			
			fe_users.gender = Anrede
			fe_users.gender.I.0 = Herr
			fe_users.gender.I.1 = Frau
#			fe_users.gender.I.55 = Firma
#			fe_users.gender.I.99 = ...
			email = E-Mail
			city = Ort
			zip = PLZ
			password_again = Passwort wiederholen
			evalErrors_gtnLstHook_password = Das Passwort muss mindestens einen Großbuchstaben, einen Kleinbuchstaben und eine Ziffer beinhalten!
			missing_password = Bitte geben Sie ein gültiges Passwort ein!
			v_registration_initiated_review1 =	wir danken für Ihre Registrierung bei %1$s. Damit Sie vollen Zugang zu unserer Website erhalten, bitten wir Sie, Ihre Registrierung zu bestätigen. Klicken Sie dazu bitte auf folgenden Link bzw. kopieren Sie ihn in die Adresszeile Ihres Browsers. Ihre Registrierung muss danach noch vom Administrator freigeschalten werden, sie werden per E-Mail informiert wenn ihr Zugang aktiviert wurde.
			v_registration_initiated_review1 = wir danken für Ihre Registrierung für den Downloadbereich der LST-Webseite. Um die Registrierung abzuschließen, klicken Sie bitte auf folgenden Link bzw. kopieren ihn in die Adresszeile Ihres Browsers. Ihr Zugang muss danach noch vom Administrator freigeschaltet werden. Sie werden per E-Mail informiert, wenn ihr Zugang aktiviert wurde.
			v_registration_initiated_review2 = Sie erhalten diese Mail, um Datenmissbrauch auszuschließen und zugleich sicherzustellen, dass die eingegebene E-Mail-Adresse korrekt ist. Sollten Sie diese Mail erhalten haben, ohne dass Sie sich bei uns anmelden wollten, senden Sie einfach eine kurze Nachricht an <a href="mailto:office@lst.at">office@lst.at</a>. 
			v_registration_initiated_review2 = Sie erhalten dieses Mail, um Datenmissbrauch auszuschließen und zugleich sicherzustellen, dass die eingegebene E-Mail-Adresse korrekt ist. Sollten Sie dieses Mail erhalten haben, ohne dass Sie sich bei uns anmelden wollten, senden Sie einfach eine kurze Nachricht an <a href="mailto:office@lst.at">office@lst.at</a>. 
			copy_paste_link =
			v_registration_initiated_message2 = 
			excuse_us = Mit freundlichen Grüßen
			v_registration_initiated_message3 = 
			kind_regards_ini = Labor Strauss Gruppe
			v_registration_review_subject = Registrierung von <b>%3$s</b> im Downloadbereich von <b>%1$s</b>.
			v_registration_confirmed_subject = Wir danken für Ihre Registrierung für den Downloadbereich der LST-Webseite. Ihre E-Mail-Adresse ist nun bestätigt.
			v_registration_confirmed_review1 = 
			v_registration_confirmed_review2 = Ein Mitarbeiter wird Ihre Anmeldung bearbeiten und Ihren Zugang zum Downloadbereich nach positiver Prüfung freischalten. Sie werden anschließend per E-Mail verständigt.
		 
			v_to_the_administrator = An den Administrator für Registrierungen bei %1$s:
			v_registration_review_message3 = Bitte prüfen Sie die Registrierung von %2$s bei %1$s. Die folgenden Daten wurden bei der Registrierung eingegeben:
			v_registration_review_message1 = Wenn die Registrierung von %1$s angenommen werden soll, bitte folgenden Link aufrufen:
			v_registration_review_message2 = Wenn die Registrierung von %1$s abgelehnt werden soll, bitte folgenden Link aufrufen:
			v_registration_accepted_subject2 = Ihre Registrierung bei %1$s wurde angenommen.
			v_registration_accepted_message3 = Wir freuen uns, Ihnen mitteilen zu können, dass Ihre Registrierung von %1$s angenommen wurde.
			v_registration_refused_subject2 = Ihre Registrierung bei %1$s wurde abgelehnt.
			v_registration_accepted_message3 = Es tut uns leid, Ihnen mitteilen zu müssen, dass Ihre Registrierung auf %1$s abgelehnt wurde.
			v_registration_accepted_message4 = Bitte zögern Sie nicht, sich bei Fragen, Bemerkungen oder Verbesserungsvorschlägen an uns zu wenden.

			v_your_account_was_created = Ihr Benutzerkonto für den geschützten Downloadbereich der LST-Webseite wurde erstellt. Wir senden ein E-Mail an die Adresse <b>%2$s</b>, damit Sie die Registrierung bestätigen können. Beachten Sie die Hinweise in diesem E-Mail.
			v_follow_instructions_review1 = Wir ersuchen um Kenntnisnahme, dass die Registrierung vom Administrator bestätigt werden muss, bevor Sie Zugriff auf den Downloadbereich unserer Webseite erhalten.
			v_follow_instructions_review2 = 

			
			evalErrors_freecap = Ungültige Eingabe!
			evalErrors_captcha = Ungültige Eingabe!
			evalErrors_gender = Bitte wählen Sie die Anrede aus!
			missing_gender = Bitte wählen Sie die Anrede aus!

			evalErrors_uniqueLocal_email = Wir haben bereits einen Benutzer mit dieser E-Mail-Adresse registriert. Vielleicht haben Sie Ihr Passwort vergessen?
			evalErrors_uniqueGlobal = Wir haben bereits einen Benutzer mit dieser E-Mail-Adresse registriert. Vielleicht haben Sie Ihr Passwort vergessen?			
			
			note_required_fields = Hinweis: Felder mit * sind Pflichtfelder.
			
			v_registration_initiated_subject = Bitte bestätigen Sie Ihre Registrierung
			
			v_registration_entered_subject = Ihre Registrierung für den Downloadbereich der LST-Webseite
			v_registration_entered_subject_informal = Ihre Registrierung für den Downloadbereich der LST-Webseite

			v_registration_created_subject = Ihre Registrierung für den Downloadbereich der LST-Webseite

			lst_newsletter = Ich möchte den LST-Newsletter erhalten. Eine Abmeldung ist jederzeit möglich.
			technical_newsletter = Ich stimme der Zusendung von wichtigen technischen Informationen über die Produkte der Labor Strauss Gruppe zu. Meine Zustimmung zum Erhalt relevanter technischer Informationen ist eine Grundbedingung für die Zurverfügungstellung des Downloadbereichs. * 
			marketing_newsletter = Ich stimme der Zusendung des Marketing Bulletin über die Produkte der Labor Strauss Gruppe zu.					
			missing_technical_newsletter =  Bitte stimmen Sie der Zusendung von wichtigen technischen Informationen zu!
			fe_language = Sprache Newsletter
                        customer_number = Kundennummer
		}
		default {
			lst_agb_confirmation_label = I have read the <a href="%s#c1798" target="_blank">Privacy Policy</a> and agree to the automated processing of my data mentioned above. <span>*</span>
			fe_users.gender = Salutation
			email = E-Mail
			password_again = Re-enter password
			evalErrors_freecap = Invalid entry!
			evalErrors_captcha = Invalid entry!
			
			fe_users.gender.I.0 = Mr
			fe_users.gender.I.1 = Ms
			
			city = Place
			v_dear_male = Dear Mr %3$s,
			v_dear_female = Dear Ms %3$s,
			
			evalErrors_uniqueLocal_email = We have already registered a user with this e-mail address. Maybe you have forgotten your password?
			evalErrors_uniqueGlobal = We have already registered a user with this e-mail address. Maybe you have forgotten your password?
			missing_email = A valid e-mail address is required.
			missing_password = Please enter a valid password!
			evalErrors_atLeast_password = The password must contain at least %s characters.
			evalErrors_twice_password = You have to enter the same password twice.

			note_required_fields = Note: Fields marked with * are required.
			
			evalErrors_gender = Please choose the salutation!
			missing_gender = Please choose the salutation!
			missing_zip = Please enter your postcode!

			v_registration_initiated_review1 = thank you for registering for the download area of the LST website. In order to finish the registration, please click on the link below or copy it into the address line of your browser. After that, your access must be activated by the administrator. After the activation of your access, you will be informed by e-mail.
			v_registration_initiated_review2 = You receive this e-mail in order to exclude data abuse and to make sure that the entered e-mail address is correct. If you have received this e-mail although you did not want to register with us, just send a short message to office@lst.at.

			v_registration_confirmed_subject = thank you for registering for the download area of the LST website. Now your e-mail address <b>%2$s</b> has been confirmed.
			v_registration_confirmed_review1 = 
			v_registration_confirmed_review2 = A member of our staff will process your registration and will activate your access to the download area after a positive check. Afterwards you will be notified by e-mail.

			v_registration_initiated_subject = Please confirm your registration
			
			kind_regards_ini = Labor Strauss Group
			
			v_registration_entered_subject = Your registration for the download area of the LST website
			v_registration_entered_subject_informal = Your registration for the download area of the LST website
			fe_language = Newsletter language

                        customer_number = Customer number

		}
	}
}

plugin.tx_srfeuserregister {
	_LOCAL_LANG {
		de {
			# for emails only?
			#fe_users.gender.I.0 = Herr
			#fe_users.gender.I.1 = Frau
			
			v_dear_male = Sehr geerhrter Herr %3$s,
			v_dear_female = Sehr geerhrte Frau %3$s,
			
			evalErrors_uniqueLocal_email = Wir haben bereits einen Benutzer mit dieser E-Mail-Adresse registriert. Vielleicht haben Sie Ihr Passwort vergessen? 
			evalErrors_uniqueGlobal = Wir haben bereits einen Benutzer mit dieser E-Mail-Adresse registriert. Vielleicht haben Sie Ihr Passwort vergessen?
			
			evalErrors_gtnLstHook_password = The password must contain at least one uppercase letter, one lowercase letter and one digit!
			lst_danke = Vielen Dank für Ihre Registrierung.
			lst_danke_with_region = Vielen Dank für Ihre Registrierung bei Labor Strauss
			lst_registration_confirm_instructions = <p>Sie erhalten in Kürze ein E-mail, um Ihre Anmeldung zu bestätigen.<br />Ein Mitarbeiter wird Ihre Anmeldung bearbeiten und Sie für den Downloadbereich freischalten.</p><p>Bei Rückfragen werden wir uns erlauben, Sie via E-Mail zu kontaktieren.</p>
			lst_link_kontakt = Für Fragen stehen wir Ihnen gerne zur Verfügung.
			lst_email_confirmed = Ihre E-Mail-Adresse ist nun bestätigt.
			lst_admin_will_process = Ein Mitarbeiter wird Ihre Anmeldung bearbeiten und Sie für den Downloadbereich freischalten. Bei Rückfragen werden wir uns erlauben, Sie via E-Mail zu kontaktieren. 			
			lst_agb_confirmation_label = Ich habe die <a href="%s" target="_blank">Datenschutzerklärung</a> gelesen und erkläre mich mit der automationsgestützten Verarbeitung meiner oben angeführten Daten einverstanden. <span>*</span>

			v_your_account_was_created = Ihr Benutzerkonto für den geschützten Downloadbereich der LST-Webseite wurde erstellt. Wir senden ein E-Mail an die Adresse <b>%2$s</b>, damit Sie die Registrierung bestätigen können. Beachten Sie die Hinweise in diesem E-Mail.
			v_follow_instructions_review1 = Wir ersuchen um Kenntnisnahme, dass die Registrierung vom Administrator bestätigt werden muss, bevor Sie Zugriff auf den Downloadbereich unserer Webseite erhalten.
			v_follow_instructions_review2 = 



			}
			
		default {
			v_your_account_was_created = your user account for the protected download area of the LST website has been created. We are sending an e-mail to the address <b>%2$s</b> so that you can confirm the registration. Please pay attention to the notes in this e-mail.
			v_follow_instructions_review1 = Please note that the registration must be confirmed by the administrator before you can access the download area of our website.
			lst_newsletter = I would like to receive the LST newsletter. I can unsubscribe at any time.
			technical_newsletter = I consent to receive important technical information about the products of the Labor Strauss Group. My consent to receive relevant technical information is a fundamental prerequisite for my access to the download area. *
			marketing_newsletter =  I consent to receive the marketing newsletter of the Labor Strauss Group.
			missing_technical_newsletter = Please agree to the sending of important technical information!
			lst_passwordRulesForTooltip = The password must contain at least 6 characters and at least one uppercase letter, one lowercase letter and one digit!
                        customer_number = Customer number
		}
	}
}	


# test conditions
#page.110 = TEXT
#page.110.value < plugin.tx_srfeuserregister_pi1.pid
#page.110.wrap = ==|==
