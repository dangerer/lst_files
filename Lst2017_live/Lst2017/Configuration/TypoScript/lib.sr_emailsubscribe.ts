
plugin.tx_sremailsubscribe_pi1 {

	templateFile = fileadmin/Lst2017/Resources/Private/Templates/SubscribeHtmlTemplate.html
	
	table.name = tt_address
	create.fields >
	create {
		fields = gender,email,first_name,last_name,static_info_country,zip,company,module_sys_dmail_html,captcha_response,terms_acknowledged
		required = email,gender,first_name,last_name,static_info_country,terms_acknowledged
		preview = 0
		defaultValues.module_sys_dmail_html = 1
		overrideValues.module_sys_dmail_html = 1
		evalValues.module_sys_dmail_html = 
		evalValues.captcha_response = freecap
	}
	## setEmptyIfAbsent is required for any checkbox
	parseValues.module_sys_dmail_html = setEmptyIfAbsent
#	parseValues.gender =
	
	edit.fields = gender,email,first_name,last_name,static_info_country,zip,company,captcha_response
	edit.required = email,gender,first_name,last_name
	edit.preview = 0

	infomail.fields := addToList(captcha_response)
	infomail.evalValues.captcha_response = freecap

	pid = 114
	# pid = 397
	registerPID = 83
	confirmPID = 111
	editPID = 112
	
	email.from = noreply@lst.at
	email.fromName = Labor Strauss Gruppe
	email.admin = office@lst.at
	email.replyToAdmin = noreply@lst.at


}

[siteLanguage("languageId") == 1]
#[Gtn\GtnLsthooks\Condition\FeLanguageSelectedCondition = 1]
plugin.tx_sremailsubscribe_pi1.pid = 335
[end]

[regionSelected("9")]
	plugin.tx_sremailsubscribe_pi1.email.admin = {$newsletter_adminbenachrichtigung_wien}
[end]
[regionSelected("10")]
	plugin.tx_sremailsubscribe_pi1.email.admin = {$newsletter_adminbenachrichtigung_graz}
[end]
[regionSelected("11")]
	plugin.tx_sremailsubscribe_pi1.email.admin = {$newsletter_adminbenachrichtigung_innsbruck}
[end]
[regionSelected("12")]
	plugin.tx_sremailsubscribe_pi1.email.admin = {$newsletter_adminbenachrichtigung_mgb}
[end]
[regionSelected("13")]
	plugin.tx_sremailsubscribe_pi1.email.admin = {$newsletter_adminbenachrichtigung_hamburg}
[end]
[regionSelected("14")]
	plugin.tx_sremailsubscribe_pi1.email.admin = {$newsletter_adminbenachrichtigung_pockau}
[end]
[regionSelected("44")]
	plugin.tx_sremailsubscribe_pi1.email.admin = {$newsletter_adminbenachrichtigung_augsburg}
[end]
[regionSelected("15")]
	plugin.tx_sremailsubscribe_pi1.email.admin = {$newsletter_adminbenachrichtigung_intern}
[end]

# for developer
#	plugin.tx_sremailsubscribe_pi1.email.from = szavarzin@gtn-solutions.com
#	plugin.tx_sremailsubscribe_pi1.email.admin = szavarzin@gtn-solutions.com
#	plugin.tx_sremailsubscribe_pi1.email.replyToAdmin = szavarzin@gtn-solutions.com


plugin.tx_sremailsubscribe {
	_LOCAL_LANG {
		de {
			tt_address.gender = Anrede
#			fe_users.gender = Anrede
#			fe_users.gender.I.0 = Herr
#			fe_users.gender.I.1 = Frau
#			fe_users.gender.I.99 = ...
			email = E-Mail
			city = Ort
			zip = PLZ
			company = Firma
			country = Land

			v_dear = Sehr geehrte/geehrter %3$s,
			v_dear_male = Sehr geehrter Herr %3$s,
			v_dear_female = Sehr geehrte Frau %3$s,
			v_please_confirm = Bitte bestätigen Sie Ihre Anmeldung			
			
			# email: user confirmation
			v_registration_initiated_subject = Bitte bestätigen Sie Ihr Abonnement für den LST-Newsletter
			
			v_your_account_was_created = Vielen Dank für Ihre Anmeldung für den LST-Newsletter. Sie erhalten in Kürze ein Bestätigungsmail über Ihr kostenloses Abonnement. Sie können den Newsletter jederzeit wieder abbestellen.
			
			v_registration_initiated_message1 = wir senden Ihnen diese Nachricht, weil Sie sich für den LST-Newsletter registriert haben. Wir möchten sicher gehen, dass Ihre E-Mail-Adresse korrekt ist und die Anmeldung tatsächlich von Ihnen stammt. Folgen Sie bitte diesem Link, um die Registrierung zu bestätigen:
			
			v_registration_initiated_message2 = Sollten Sie sich nicht selbst registriert haben, folgen Sie bitte diesem Link:
			
			# email: after user confirmation
			# v_registration_confirmed_message2 = Ihre Registrierung war erfolgreich. Ihre E-Mail-Adresse <b>%4s</b> wurde zum Verteiler hinzugefügt.
			v_registration_confirmed_message1 = Ihre Anmeldung wurde bestätigt. Sie erhalten ab sofort regelmäßig den LST-Newsletter mit aktuellen Informationen rund um Brandschutz und Gefahrenmeldetechnik. Für weitere Fragen stehen wir Ihnen gerne zur Verfügung.
			
			v_registration_confirmed_subject = Vielen Dank für Ihr Abonnement des LST-Newsletters
			
			evalErrors_freecap = Ungültige Eingabe!
			evalErrors_captcha = Ungültige Eingabe!
			
			# Daten ändern
			v_already_subscribed = Sie erhalten bereits unseren Newsletter und möchten Ihr Abonnement ändern oder abbestellen?
			v_wish_to_update_or_delete = Daten ändern/Newsletter abbestellen
			v_enter_subscribed_email = Möchten Sie Ihr Newsletter-Abonnement ändern oder kündigen? Geben Sie bitte die E-Mail-Adresse ein, an die Sie bisher den Newsletter erhalten haben.
			
			v_sending_infomail = Daten ändern/Newsletter abbestellen
			v_sending_infomail_message1 = Wir senden Ihnen eine E-Mail an die Adresse %4$s. Die Nachricht enthält zwei Links, mit denen Sie Ihre Daten ändern oder den LST-Newsletter abbestellen können.
			
			v_sending_infomail_message2 = Bitte folgen Sie den Anweisungen in diesem E-Mail, um Ihr Abonnement zu ändern oder zu kündigen.
			
			v_infomail_subject = Abonnement des LST-Newsletters ändern oder kündigen
			v_infomail_reason = Sie erhalten diese Nachricht, weil Sie Ihre Daten ändern oder Ihr Abonnement des LST-Newsletters kündigen möchten.
			
			# Gekündigt
			
			v_registration_cancelled_subject = Ihr Abonnement des LST-Newsletters wurde gekündigt
			
			v_registration_cancelled_message1 = wir haben Ihre Newsletter-Abmeldung erhalten. Wunschgemäß entfernen wir Ihre E-Mail-Adresse aus dem Verteiler. Wir freuen uns, wenn Sie uns gegebenenfalls den Grund Ihrer Abmeldung nennen möchten und stehen Ihnen für weitere Fragen gerne zur Verfügung.
			
			# Daten ändern
			
			v_registration_updated_subject = Ihr Abonnement des LST-Newsletters wurde aktualisiert
			v_registration_updated_message1 = Vielen Dank, wir haben Ihre Änderungen erhalten und aktualisiert. 

			missing_gender = Bitte wählen Sie die Anrede aus!
			missing_first_name = Bitte geben Sie Ihren Vornamen ein!
			missing_last_name = Bitte geben Sie Ihren Nachnamen ein!
			missing_terms_acknowledged = Bitte akzeptieren Sie die Bedingungen!

			newsletter_subscribe = Anmeldung für den Newsletter
			newsletter_subscribe_message1 = Mit dem LST-Newsletter versorgen wir Sie regelmäßig mit neuesten Informationen rund um Brandschutz und Gefahrenmeldetechnik. Wenn Sie den Newsletter regelmäßig erhalten möchten, melden Sie sich unten an.
			newsletter_subscribe_message2 = Eine Abmeldung ist jederzeit hier möglich.
			gender_m = Herr
			gender_w = Frau
			fe_language_0 = Deutsch
			fe_language_1 = Englisch
			kind_regards = Mit freundlichen Grüßen 
			
			button_send_link = Abschicken
			
			evalErrors_uniqueLocal_email = Unter dieser E-Mail-Adresse haben wir schon einen Benutzer registriert. Haben Sie sich bereits angemeldet? 
			
			v_infomail_norecord_subject = Ihre E-Mail-Adresse ist nicht für den LST-Newsletter registriert
			
			v_infomail_norecord_message1 = Es scheint, als ob Ihre E-Mail-Adresse nicht für den LST-Newsletter registriert ist.
			
			v_infomail_norecord_message2 = Sollten Sie Probleme mit Ihrem Abonnement haben, kontaktieren Sie bitte die zuständige LST-Niederlassung.
		}
		
		default {
			v_dear = Sehr geehrte/geehrter %3$s,
			v_dear_male = Sehr geehrter Herr %3$s,
			v_dear_female = Sehr geehrte Frau %3$s,
			
			button_send_link = Abschicken
		}
		
		default {
			tt_address.gender = Salutation
			email = E-mail
			missing_gender = Please enter your salutation!
			missing_email = A valid e-mail address is required.
			evalErrors_uniqueLocal_email = We have already registered a user with this e-mail address.
			missing_first_name = Please enter your first name
			missing_last_name = Please enter your last name
			missing_terms_acknowledged = Please acknowledge the terms!
			v_already_subscribed = Do you already receive our newsletter and want to update your profile or unsubscribe?

			newsletter_subscribe = Subscribe to the newsletter
			newsletter_subscribe_message1 = With the LST newsletter, we regularly provide you with the latest information about fire protection and hazard detection technology. If you would like to receive the newsletter on a regular basis, please subscribe below.
			newsletter_subscribe_message2 = You can unsubscribe at any time here.
			gender_m = Mr
			gender_w = Ms
			fe_language_0 = Deutsch
			fe_language_1 = English
			company = Company
			kind_regards = Kind regards
			
			v_registration_initiated_subject = Please confirm your subscription to the LST newsletter
			v_registration_confirmed_subject = Thank you for subscribing to the LST newsletter
			
			v_dear = Dear %3$s,
			v_dear_male = Dear Mr %3$s,
			v_dear_female = Dear Ms %3$s,
			
			v_your_account_was_created = Thank you for subscribing to the LST newsletter. You will soon receive an e-mail confirming your cost-free subscription. You can unsubscribe from the newsletter at any time.
			
			v_registration_confirmed_message1 = your subscription has been confirmed. From now on you will regularly receive the LST newsletter with current information about fire protection and danger detection technology. We will gladly respond to any further questions.
			
			v_enter_subscribed_email = Enter the e-mail address by which you originally subscribed.
			
			button_send_link = Send
			
			v_sending_infomail = Update profile/Unsubscribe newsletter
			
			v_sending_infomail_message1 = We send you a message to your e-mail address. The message contains two links that allow you to edit your data or to unsubscribe from the LST newsletter.
			
			v_registration_initiated_message1 = we are sending you this message to confirm that you wish to subscribe to the LST newsletter. We wish to ensure that the e-mail address is correct and also that no one may subscribe with an address other than his(her) own. To confirm your subscription, please follow this link.
			
			v_registration_initiated_message2 = If for some reason you have received this message without having subscribed, please follow this link.
			
			v_sending_infomail_message2 = Please follow the instructions given in this e-mail.
			
			button_confirm_update = Update profile
			
			v_registration_cancelled_subject = Your subscription has been cancelled
			
			v_registration_cancelled_message1 = your subscription to our newsletter has been cancelled.
			
			v_infomail_subject = About your subscription to the LST newsletter
			
			v_infomail_reason = you are receiving this message because you asked to edit your profile or terminate your subscription to the LST newsletter.
			
			v_registration_updated_subject = Your subscription to the LST newsletter has been updated
			
			v_registration_updated_message1 = thank you for keeping your subscription up-to-date.
			
			v_infomail_norecord_subject = Your e-mail address is not subscribed to the LST newsletter
			
			v_infomail_norecord_message1 = It seems that your e-mail address is not subscribed to the LST newsletter.
			
			v_infomail_norecord_message2 = If you are encountering problems with your subscription, please contact your LST representative.
		}
	}
}
