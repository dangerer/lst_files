plugin.tx_mailactivitycheck_confirmation.settings {
	##PID inkludiert alle child-pages
	fe_user_pids = 47

	#Zusätzl. Sicherheitsmaßnahme, wird als 1. geprüft,
	#wenn != 1 wird nichts geschickt/durchgeführt
	active = 1
	##Format: Tag.Monat
	#1. Mail !! Wird überschrieben !!
	datum_mail_erste = 17.04
	mail_erste_body_de = Sie erhalten diese automatische Nachricht, um die Gültigkeit Ihrer E-Mail-Adresse zu überprüfen. Bitte klicken Sie innerhalb von 10 Tagen auf den folgenden Link, um Ihren Zugang zum Downloadbereich aufrechtzuerhalten.
	mail_erste_body_en = you receive this automatic message, in order to verify the validity of your e-mail address. Please click on the link below within 10 days, in order to maintain your access to the download area.

	#Erinnerungs-Mail !! Wird überschrieben !!
	datum_mail_erinnerung = 18.04
	mail_erinnerung_body_de = Sie erhalten diese automatische Nachricht, um die Gültigkeit Ihrer E-Mail-Adresse zu überprüfen. Bitte klicken Sie innerhalb von 5 Tagen auf den folgenden Link, da Ihr Zugang zum Downloadbereich sonst aus Sicherheitsgründen automatisch gesperrt wird.
	mail_erinnerung_body_en = you receive this automatic message, in order to verify the validity of your e-mail address. Please click on the link below within 5 days, as your access to the download area will be otherwise disabled for security reasons.'

	#Tag der Sperrung !! Wird überschrieben !!
	datum_sperrung = 19.04
	
	record_table = fe_users
	confirmation_page_uid = 11278
	##for list of deactivated users !! Wird überschrieben !!
	admin_mail = meisenberg@gtn-solutions.com
}

# Überschreiben für Kurzweil
<INCLUDE_TYPOSCRIPT: source="FILE: fileadmin/Registrierung_Email_Empfaenger/feuser_confirm.ts">