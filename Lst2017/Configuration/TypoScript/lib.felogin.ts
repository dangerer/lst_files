
# Template FE Login
plugin.tx_felogin_login {
	feloginBaseURL = https://www.laborstrauss.com/
	view {
		templateRootPaths.20 = fileadmin/Lst2017/Resources/Private/Felogin/Templates/
		partialRootPaths.20 = fileadmin/Lst2017/Resources/Private/Felogin/Partials/

	} 
#	redirectPageLogin = 28
#	redirectFirstMethod = 0
#	redirectMode = getpost,groupLogin,login
#	redirectDisable = 0
#	showLogoutFormAfterLogin = 0
	email_from = noreply@lst.at
	email_fromName = Labor Strauss Gruppe
	redirectMode = referer
	redirectFirstMethod = GET

}




plugin.tx_felogin_login {
	_LOCAL_LANG {
		de {
			username = Benutzername
			enter_your_data = Benutzername
			password = Passwort
			forgot_header = Passwort vergessen?
			forgot_reset_message = Geben Sie hier Username oder Mail-Adresse ein. Sie erhalten umgehend ein E-Mail zur Rücksetzung des Passworts.
			change_password_message = Bitte geben Sie das neue Passwort zweimal ein. Das Passwort muss mindestens 6 Zeichen enthalten und mindestens einen Großbuchstaben, einen Kleinbuchstaben und eine Ziffer beinhalten!
			welcome_message = Geben Sie bitte hier Ihren Benutzernamen und Ihr Passwort ein:
			forgot_reset_message = Geben Sie bitte hier Ihren Benutzernamen ein. Wir senden Ihnen umgehend Informationen zum Zurücksetzen des Passworts zu.
			welcome_header = Benutzeranmeldung
			logout_header = Sie sind ausgeloggt.
			status_message = Sie sind eingeloggt.
			status_header = Ihr Status
			password_recovery_link = Link zum Zurücksetzen des Passworts
			password_recovery_mail_header = Labor Strauss Passwort vergessen
			logout_message = Sie sind nun von der aktuellen Sitzung ausgeloggt. Über das unten angegebene Formular kann ein erneuter Login erfolgen.
			forgot_validate_reset_password (
Passwort zurücksetzen 
Sehr geehrte/geehrter ###USER_FIRSTNAME### ###USER_LASTNAME###<!###DELETE###>%s</!###DELETE###>,

Sie erhalten diese Nachricht, da Sie Ihr Passwort zurücksetzen lassen wollen. Zur Bestätigung bitte nachstehenden Link aufrufen:
%s

Aus Sicherheitsgründen ist dieser Link nur bis %s aktiv. Falls Sie den Link nicht bis dahin aufgerufen haben, müssen Sie die Schritte zum Zurücksetzen des Passworts wiederholen.

Mit freundlichen Grüßen 

Labor Strauss Gruppe		
)
			forgot_validate_reset_password_html (
			Passwort zurücksetzen 
			Sehr geehrte/geehrter %s,
			
			Sie erhalten diese Nachricht, da Sie Ihr Passwort zurücksetzen lassen wollen. Zur Bestätigung bitte nachstehenden Link aufrufen:
			%s
			
			Aus Sicherheitsgründen ist dieser Link nur bis %s aktiv. Falls Sie den Link nicht bis dahin aufgerufen haben, müssen Sie die Schritte zum Zurücksetzen des Passworts wiederholen.
			
			Mit freundlichen Grüßen 
			
			Labor Strauss Gruppe		
			)
			error_message = Während der Anmeldung ist ein Fehler aufgetreten. Wahrscheinlich haben Sie Ihren Benutzernamen oder das Passwort falsch eingegeben. Vergewissern Sie sich, dass Sie Ihre Zugangsdaten korrekt eingegeben haben - Groß-/Kleinschreibung wird unterschieden. Eine andere Möglichkeit ist, dass Sie die Cookies in Ihrem Web-Browser deaktiviert haben.
			forgot_reset_message_emailSent = Es wurde eine E-Mail mit einem Link zum Zurücksetzen des Passworts an die in Ihrem Benutzerkonto gespeicherte E-Mail-Adresse geschickt. Sollten Sie keine E-Mail erhalten, wurde Ihr Konto oder die E-Mail-Adresse nicht gefunden.
			error_header = Login fehlgeschlagen
			forgot_header_backToLogin = Zurück zum Login
			password_requirement = Stelle sicher, dass dein neues Passwort die folgenden Anforderungen erfüllt:
			change_password_description = Bitte gib dein neues Passwort zweimal ein.
		}
		en {
			welcome_message = Please enter your user name and your password here:
			welcome_header = User login
			forgot_header = Forgot your password?
			username = User name
			enter_your_data = Username
			ll_change_password_message = Please enter your new password twice. The password must contain at least 6 characters and at least one uppercase letter, one lowercase letter and one digit.
			status_message = You are logged in.
			status_header = Current status
			reset_password = Reset password
			error_message = During login, an error has occurred. You have probably entered your user name or the password incorrectly. Check whether you have entered your login credentials correctly - we differentiate between uppercase and lowercase letters. It is also possible that you have deactivated cookies in your web browser.

			# ll_forgot_reset_message = An e-mail has been sent to the address stored in your account and contains a link to reset your password. If you do not receive an e-mail, your account or e-mail address was not found.
			ll_forgot_reset_message = Please enter your username. Instructions for resetting the password will be immediately sent to you. 
			ll_forgot_reset_message_emailSent = An e-mail has been sent to the address stored in your account and contains a link to reset your password. If you do not receive an e-mail, your account or e-mail address was not found.
			ll_forgot_validate_reset_password (
Reset password 
Dear ###USER_FIRSTNAME### ###USER_LASTNAME###<!###DELETE###>%s</!###DELETE###>,

This e-mail was sent in response to your request to reset your password. Please click on the link below.:
%s

For security reasons, this link is only active until  %s. If you do not visit the link before then, you will need to repeat the password reset steps.

Kind regards, 

Labor Strauss Gruppe		
)
		}
		default {
		}

	}
}