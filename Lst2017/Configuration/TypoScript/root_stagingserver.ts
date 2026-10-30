

page.config.baseURL = https://test.laborstrauss.com/

page.10.variables {
			logo.10.value (
					<a class="navbar-brand" target="_top" href="https://test.laborstrauss.com">
				)
			}
}

[siteLanguage("languageId") == 1]
#[Gtn\GtnLsthooks\Condition\FeLanguageSelectedCondition = 1]
page.10.variables.logo {
	10.value (
		<a class="navbar-brand" target="_top" href="https://test.laborstrauss.com/en">
	)
}

[end]