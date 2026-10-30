#Wien = 9, Graz = 10, Innsbruck = 11, Mönchengladbach = 12, Hamburg = 13, Pockau = 14, Intern = 15, noRegion = 17, Augsburg = 44

#Wien = 9
[regionSelected("9")]
	plugin.tx_powermail.settings.setup.receiver.subject = {$schulungen_subjectAdmin_wien}
	plugin.tx_powermail.settings.setup.receiver.email = {$schulungen_empfaenger_wien}
	plugin.tx_powermail.settings.setup.sender.subject = {$schulungen_subject_wien}
	plugin.tx_powermail.settings.setup.sender.email = {$schulungen_senderEmail_wien}
	plugin.tx_powermail.settings.setup.sender.name = {$schulungen_senderName_wien}
	plugin.tx_powermail.settings.setup.receiver.overwrite.senderEmail.value = {$schulungen_senderEmail_wien}
	plugin.tx_powermail.settings.setup.receiver.overwrite.senderName.value = {$schulungen_senderName_wien}
[end]

[siteLanguage("languageId") == 1 && regionSelected("9")]
	plugin.tx_powermail.settings.setup.sender.subject = {$schulungen_subject_EN_wien}
[end]

#Graz = 10
[regionSelected("10")]
	plugin.tx_powermail.settings.setup.receiver.subject = {$schulungen_subjectAdmin_graz} 
	plugin.tx_powermail.settings.setup.receiver.email = {$schulungen_empfaenger_graz}
	plugin.tx_powermail.settings.setup.sender.subject = {$schulungen_subject_graz}
	plugin.tx_powermail.settings.setup.sender.email = {$schulungen_senderEmail_graz}
	plugin.tx_powermail.settings.setup.sender.name = {$schulungen_senderName_graz}
	plugin.tx_powermail.settings.setup.receiver.overwrite.senderEmail.value = {$schulungen_senderEmail_graz}
	plugin.tx_powermail.settings.setup.receiver.overwrite.senderName.value = {$schulungen_senderName_graz}
[end]

[siteLanguage("languageId") == 1 && regionSelected("10")]
	plugin.tx_powermail.settings.setup.sender.subject = {$schulungen_subject_EN_graz}
[end]

#Innsbruck = 11
[regionSelected("11")]
	plugin.tx_powermail.settings.setup.receiver.subject = {$schulungen_subjectAdmin_innsbruck} 
	plugin.tx_powermail.settings.setup.receiver.email = {$schulungen_empfaenger_innsbruck}
	plugin.tx_powermail.settings.setup.sender.subject = {$schulungen_subject_innsbruck} 
	plugin.tx_powermail.settings.setup.sender.email = {$schulungen_senderEmail_innsbruck}
	plugin.tx_powermail.settings.setup.sender.name = {$schulungen_senderName_innsbruck}
	plugin.tx_powermail.settings.setup.receiver.overwrite.senderEmail.value = {$schulungen_senderEmail_innsbruck}
	plugin.tx_powermail.settings.setup.receiver.overwrite.senderName.value = {$schulungen_senderName_innsbruck}
[end]

[siteLanguage("languageId") == 1 && regionSelected("11")]
	plugin.tx_powermail.settings.setup.sender.subject = {$schulungen_subject_EN_innsbruck}
[end]

#Mönchengladbach = 12
[regionSelected("12")]
	plugin.tx_powermail.settings.setup.receiver.subject = {$schulungen_subjectAdmin_moenchengladbach} 
	plugin.tx_powermail.settings.setup.receiver.email = {$schulungen_empfaenger_moenchengladbach}
	plugin.tx_powermail.settings.setup.sender.subject = {$schulungen_subject_moenchengladbach} 
	plugin.tx_powermail.settings.setup.sender.email = {$schulungen_senderEmail_moenchengladbach}
	plugin.tx_powermail.settings.setup.sender.name = {$schulungen_senderName_moenchengladbach}
	plugin.tx_powermail.settings.setup.receiver.overwrite.senderEmail.value = {$schulungen_senderEmail_moenchengladbach}
	plugin.tx_powermail.settings.setup.receiver.overwrite.senderName.value = {$schulungen_senderName_moenchengladbach}
[end]

[siteLanguage("languageId") == 1 && regionSelected("12")]
	plugin.tx_powermail.settings.setup.sender.subject = {$schulungen_subject_EN_moenchengladbach}
[end]

#Hamburg = 13
[regionSelected("13")]
	plugin.tx_powermail.settings.setup.receiver.subject = {$schulungen_subjectAdmin_hamburg} 
	plugin.tx_powermail.settings.setup.receiver.email = {$schulungen_empfaenger_hamburg}
	plugin.tx_powermail.settings.setup.sender.subject = {$schulungen_subject_hamburg} 
	plugin.tx_powermail.settings.setup.sender.email = {$schulungen_senderEmail_hamburg}
	plugin.tx_powermail.settings.setup.sender.name = {$schulungen_senderName_hamburg}
	plugin.tx_powermail.settings.setup.receiver.overwrite.senderEmail.value = {$schulungen_senderEmail_hamburg}
	plugin.tx_powermail.settings.setup.receiver.overwrite.senderName.value = {$schulungen_senderName_hamburg}
[end]

[siteLanguage("languageId") == 1 && regionSelected("13")]
	plugin.tx_powermail.settings.setup.sender.subject = {$schulungen_subject_EN_hamburg}
[end]

#Pockau = 14
[regionSelected("14")]
	plugin.tx_powermail.settings.setup.receiver.subject = {$schulungen_subjectAdmin_pockau} 
	plugin.tx_powermail.settings.setup.receiver.email = {$schulungen_empfaenger_pockau}
	plugin.tx_powermail.settings.setup.sender.subject = {$schulungen_subject_pockau} 
	plugin.tx_powermail.settings.setup.sender.email = {$schulungen_senderEmail_pockau}
	plugin.tx_powermail.settings.setup.sender.name = {$schulungen_senderName_pockau}
	plugin.tx_powermail.settings.setup.receiver.overwrite.senderEmail.value = {$schulungen_senderEmail_pockau}
	plugin.tx_powermail.settings.setup.receiver.overwrite.senderName.value = {$schulungen_senderName_pockau}
[end]

[siteLanguage("languageId") == 1 && regionSelected("14")]
	plugin.tx_powermail.settings.setup.sender.subject = {$schulungen_subject_EN_pockau}
[end]

#Augsburg = 44
[regionSelected("44")]
	plugin.tx_powermail.settings.setup.receiver.subject = {$schulungen_subjectAdmin_augsburg}
	plugin.tx_powermail.settings.setup.receiver.email = {$schulungen_empfaenger_augsburg}
	plugin.tx_powermail.settings.setup.sender.subject = {$schulungen_subject_augsburg}
	plugin.tx_powermail.settings.setup.sender.email = {$schulungen_senderEmail_augsburg}
	plugin.tx_powermail.settings.setup.sender.name = {$schulungen_senderName_augsburg}
	plugin.tx_powermail.settings.setup.receiver.overwrite.senderEmail.value = {$schulungen_senderEmail_augsburg}
	plugin.tx_powermail.settings.setup.receiver.overwrite.senderName.value = {$schulungen_senderName_augsburg}
[end]

[siteLanguage("languageId") == 1 && regionSelected("44")]
	plugin.tx_powermail.settings.setup.sender.subject = {$schulungen_subject_EN_augsburg}
[end]

#Intern = 15
[regionSelected("15")]
	plugin.tx_powermail.settings.setup.receiver.subject = {$schulungen_subjectAdmin_international}
	plugin.tx_powermail.settings.setup.receiver.email = {$schulungen_empfaenger_international}
	plugin.tx_powermail.settings.setup.sender.subject = {$schulungen_subject_international} 
	plugin.tx_powermail.settings.setup.sender.email = {$schulungen_senderEmail_international}
	plugin.tx_powermail.settings.setup.sender.name = {$schulungen_senderName_international}
	plugin.tx_powermail.settings.setup.receiver.overwrite.senderEmail.value = {$schulungen_senderEmail_international}
	plugin.tx_powermail.settings.setup.receiver.overwrite.senderName.value = {$schulungen_senderName_international}
[end]

[siteLanguage("languageId") == 1 && regionSelected("15")]
	plugin.tx_powermail.settings.setup.sender.subject = {$schulungen_subject_EN_international}
[end]

# for test
[ip("91.123.151.20")]
	# disable all finishers
	plugin.tx_powermail.settings.setup.finishers >
[END]
