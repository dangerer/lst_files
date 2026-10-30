

plugin.tx_femanager {
        view {
                templateRootPath >
                templateRootPaths {
                        10 = EXT:femanager/Resources/Private/Templates/
                        20 = fileadmin/Lst2017/Resources/Private/femanager/Templates/
                }
                partialRootPath >
                partialRootPaths {
                        10 = EXT:femanager/Resources/Private/Partials/
                        20 = fileadmin/Lst2017/Resources/Private/femanager/Partials/
                }
                layoutRootPath >
                layoutRootPaths {
                        10 = EXT:femanager/Resources/Private/Layouts/
                        20 = fileadmin/Lst2017/Resources/Private/femanager/Layouts/
                }
        }
	settings {
		edit {
#			fillEmailWithUsername = 1
			validation {

			        _enable {
			          # Enable clientside Formvalidation (JavaScript)
			          client = 1
			
			          # Enable serverside Formvalidation (PHP)
			          server = 1
			        }


				# GTN doku: for password rules - import this patch: https://github.com/TYPO3-extensions/femanager/pull/2/files
				password {
					required = 1
					min = 6
					mustInclude = number,letter,uppercase,lowercase
				}
				password_repeat {
					required = 1
					sameAs = password
				}
				telephone {
					required = 1
				}
				firstName {
					required = 1
				}
				lastName {
					required = 1
				}
				company {
					required = 1
				}
				address {
					required = 1
				}
				zip {
					required = 1
				}
				city {
					required = 1
				}
				country {
					required = 1
				}
				gender {
					inList = 0,1,99
					required = 0
				}
#				customer_number {
#					required = 1
#				}
				fe_language {
					inList = 0,1
				}
#				email >
#				username {
#					required = 1
#					uniqueInDb = 1
#					email = 1
#				}
			}
		}
#		new {
#			fillEmailWithUsername = 1
#			validation {
#				email >
#				username {
#					required = 1
#					uniqueInDb = 1
#					email = 1
#				}
#			}
#		}
	}
        _LOCAL_LANG {
		en {
			tx_femanager_domain_model_user.city = Place
			tx_femanager_domain_model_user.zip = Postal code
			tx_femanager_domain_model_user.email = E-Mail
			tx_femanager_domain_model_user.username = User name
			tx_femanager_domain_model_user.firstName = First name
			tx_femanager_domain_model_user.lastName = Last name
			tx_femanager_domain_model_user.password_repeat = Repeat password
			submitEdit = Update profile
			tx_femanager_domain_model_user.password_repeat = Repeat password
			tx_femanager_domain_model_user.customer_number = Customer number
		}
		default {
			tx_femanager_domain_model_user.customer_number = Customer number
		}
	}
}

# page for password changing:
[page["uid"] == 278]
	plugin.tx_femanager._LOCAL_LANG {
		en {
			validationErrorMustInclude = The password must contain at least one uppercase letter, one lowercase letter and one digit!
		}
		de {
			validationErrorMustInclude = Das Passwort muss mindestens einen Großbuchstaben, einen Kleinbuchstaben und eine Ziffer beinhalten!
		}
	}
[end]

plugin.tx_sremailsubscribe {
	_LOCAL_LANG {
		de {
			city = Ort
		}
		en {
			city = Place
		}
	}
}

plugin.tx_femanager._LOCAL_LANG.de {
	tx_femanager_domain_model_user.city = Ort
	tx_femanager_domain_model_user.gender = Anrede
	tx_femanager_domain_model_user.email = E-Mail
	tx_femanager_domain_model_user.username = Benutzername
	tx_femanager_domain_model_user.gender.item0 = Herr
	tx_femanager_domain_model_user.gender.item1 = Frau
	submitEdit = Änderungen speichern
	tx_femanager_domain_model_user.customer_number = Kundennummer
	#tx_femanager_domain_model_log.state.201 = Änderung gespeichert
	update = Änderung gespeichert

}

# country -> static_info_country
# deprecated - look plugin gtn_lsthooks
#config.tx_extbase{
#    persistence {
#        classes {
#	    In2code\Femanager\Domain\Model\User {
#                mapping {
#                    columns {
#                        static_info_country.mapOnProperty = country
#####			gender.mapOnProperty = gender
#                    }
#                }
#            }
#	}
#   }
#}

