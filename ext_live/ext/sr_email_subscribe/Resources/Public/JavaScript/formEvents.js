/*
 * Javascript functions for TYPO3 extension sr_email_subscribe
 *
 */
(function () {
	window.addEventListener('load', (event) => {
		// Click on label should always focus on element
		var labels = document.getElementsByTagName("label");
		for (let i = 0; i < labels.length; i++) {
			labels[i].addEventListener('click', (event) => {
				var htmlFor = event.target.htmlFor;
				if (htmlFor) {
					var element = document.getElementById(htmlFor);
					if (element !== null) {
						element.focus();
						return false;
					}
			    }
		    });
		}
		// Click event when cancelling delete
		var submit = document.getElementById("tx_sremailsubscribe_pi1_delete_doNotSave");
		if (submit !== null) {
			submit.addEventListener('click', (event) => {
				var form = submit.form;
				if (form) {
					var element = form.elements.namedItem("tx_sremailsubscribe_pi1[cmd]");
					if (element) {
						element.setAttribute('value', 'edit');
					}
				}
				return true;
			});
		}
	});
})();