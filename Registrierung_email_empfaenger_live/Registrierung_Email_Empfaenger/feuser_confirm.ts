plugin.tx_mailactivitycheck_confirmation.settings {
  #!! WICHTIG !!#
  # Die definierten Tage dürfen nicht wärend einem Sende-Zyklus
  # verändert werden, d.h. datum_mail_erste darf nicht geändert werden,
  # wenn die Erinnerungs-Mail noch nicht gesendet wurde bzw. die Sperrung erfolgt ist.
  # Es werden sonst neue Bestätigungscodes für die User generiert und die Bestätigung
  # funktioniert dann nicht mehr.
  ##Format: Tag.Monat


  #Datum der Aussendung der 1. Mail
  datum_mail_erste = 15.05
  #Text der initialen Mail (Grußformel und Verabschiedung BEREITS IM MAIL ENTHALTEN, dises hier nicht berücksichtigen!)
  mail_erste_body_de = Sie erhalten diese automatische Nachricht, um die Gültigkeit Ihrer E-Mail-Adresse zu überprüfen. Bitte klicken Sie innerhalb von 30 Tagen auf den folgenden Link, um Ihren Zugang zum Downloadbereich aufrechtzuerhalten.
  mail_erste_body_en = you receive this automatic message, in order to verify the validity of your e-mail address. Please click on the link below within 30 days, in order to maintain your access to the download area.

  #Erinnerungs-Mail
  datum_mail_erinnerung = 04.06

  #Text der Erinnerungs-Mail (Grußformel und Verabschiedung BEREITS IM MAIL ENTHALTEN, diese hier nicht berücksichtigen!)
  mail_erinnerung_body_de = Sie erhalten diese automatische Nachricht, um die Gültigkeit Ihrer E-Mail-Adresse zu überprüfen. Bitte klicken Sie innerhalb von 10 Tagen auf den folgenden Link, da Ihr Zugang zum Downloadbereich sonst aus Sicherheitsgründen automatisch gesperrt wird.
  mail_erinnerung_body_en = you receive this automatic message, in order to verify the validity of your e-mail address. Please click on the link below within 10 days, as your access to the download area will be otherwise disabled for security reasons.

  #Tag der Sperrung
  datum_sperrung = 15.06
  ##Empfänger-Mail der .csv-Datei mit den deaktivierten Benutzern
  admin_mail = andreas.kurzweil@lst.at
}