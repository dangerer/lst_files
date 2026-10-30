


// activate typohead for search region
$(document).ready(function () {

	// activate modal region select window if no region selected and it is need for this page
	// look TS: plugin.lstHooks.pagesForShowSelectRegionModalIfNoRegionSelected = ...
	if (!(gtnSelectedRegion > 0) && showModalonEmptyRegion)
		$('#selectRegion').modal(); 

	$('.regionsuchebutton').on('click', function (e) {
		var input = $(this).closest('form').find('#regionsuchebyzip, #regionsuchebyzipDirect');
		input.typeahead('lookup');
		e.preventDefault();
	});

	checkMainRegionSelected(null);
	if ($('#st_zipserahcform').length) {
        checkMainRegionSelected($('#st_zipserahcform'));
    }
    //$('body').on('change', '#selectRegion input[name="mainregion"], #st_zipserahcform input[name="mainregion"]', function (e) {
    $('body').on('change', '#selectRegion input[name="mainregion"], #st_zipserahcform input[type="radio"]', function (e) {
        var mainRegion = $(this).val();
        // deselect all in the left side
        if ($('#selectRegion a.active').length /*&& $('#selectRegion a.active').attr('data-tscatuid') == 15 && mainRegion != 'inter'*/) {
            $('#selectRegion a.active').trigger('click');
        }
        var form = $(this).closest('form');
        checkMainRegionSelected(form);
    });

	$('#regionsuchebyzip, #regionsuchebyzipDirect').typeahead({
		items: 10,
		autoSelect: false,
		fitToElement: false,
		source: function (zip, result) {
		    // selected main region
		    var trigger = this.$element[0];
		    if ($(trigger).closest('form').attr('id') == 'st_zipserahcform') { // for Standorte page (popup window has another ID of this input)
                var mainRegion = $('form#st_zipserahcform').find('input[name="mainregion"]:checked').val();
            } else {
                var mainRegion = $('input[name="mainregion"]:checked').val();
            }
		    // console.log('gtn_lsthooks.js:36');console.log(mainRegion);// !!!!!!!!!! delete it
            if ($(trigger).attr('id') == 'regionsuchebyzipDirect') {
                var withLinks = '&withlinks=1';
            } else {
                var withLinks = '';
            }
			$.ajax({
				url: '',
				data: 'zip=' + zip + '&type=98374812' + withLinks + '&mainregion=' + mainRegion,
				dataType: "json",
				type: "GET",
				success: function (data) {
					result($.map(data, function (item) {
						return item;
					}));
				}
			});
		},
		matcher: function(item) {
			return item; // return all items from server's request, so search will be done on server
		},
		highlighter: function(item) {
			return item; // without any highlighter
		},
		afterSelect: function (item) {
			$('.list-group-region .list-group-item').removeClass('active');
            var trigger = this.$element[0];
            if ($(trigger).attr('id') == 'regionsuchebyzipDirect') {
                $('#regionsuchebyzipDirect').val('');
                document.location = item.link;
            } else {
                var selectedTab = $('.list-group-region .list-group-item[data-tscatuid='+item.id+']')[0];
                selectedTab.click();
                $('#regionsuchebyzip').val(item.zip);
            }
		}
		//updater: function(item) {
		//}
	});
});

// for RAN form
var articleIndex = 1;
$(document).ready(function () {
    // hide all articles
    // $('.ranArticleFieldset:not(:has(h2.ran-article))').fadeOut();
    // show only first
    // $('.ranArticleFieldset[data-articleuid="1"]').fadeIn();
	// get last article number + 1
	$('.ranArticleFieldset').each(function() {
		var index = parseInt($(this).attr('data-articleuid'));
		if (index >= articleIndex)
			articleIndex = index + 1;
	});
	// Change input names for using as array of Articles
    $('.ranInputItem').find('input, select, textarea').each(function() {
    	// add validations.
        if ($('body').hasClass('lang-en')) {
            var tooManyString = 'Invalid entry (too many/not enough characters)!';
            var pleaseDecrease = 'Please decrease number!';
        } else {
            var tooManyString = 'Ungültige Eingabe (zu viele/zu wenige Zeichen)!';
            var pleaseDecrease = 'Bitte eine kleinere Zahl eingeben.';
        }
        // count
        var searchName = '[anzahl]';
        if ($(this).attr('name').indexOf(searchName) !== -1) { // get count elements
            $(this).attr('max', '9999');
            $(this).attr('data-parsley-error-message', pleaseDecrease);
        }
        // type
        var searchName = '[type]';
        if ($(this).attr('name').indexOf(searchName) !== -1) { // get type elements
            $(this).attr('data-parsley-length', '[1, 128]');
            $(this).attr('data-parsley-error-message', tooManyString);
        }
		// article number:
        var searchName = '[artikelnummer]';
        if ($(this).attr('name').indexOf(searchName) !== -1) { // get articlenumbers elemetns
        	$(this).attr('data-parsley-length', '[1, 8]');
        	$(this).attr('data-parsley-error-message', tooManyString);
		}
		// order number:
        var searchName = '[auftragsnummer]';
        if ($(this).attr('name').indexOf(searchName) !== -1) { // get auftragsnummer elemetns
        	// $(this).attr('data-parsley-length', '[1, 8]');
        	// $(this).attr('data-parsley-error-message', tooManyString);
		}
		// delivery number:
        var searchName = '[deliverynumber]';
        if ($(this).attr('name').indexOf(searchName) !== -1) { // get deliverynumber elemetns
        	$(this).attr('data-parsley-length', '[1, 32]');
        	$(this).attr('data-parsley-error-message', tooManyString);
		}
		// serialnumber:
        var searchName = '[seriennummer]';
        if ($(this).attr('name').indexOf(searchName) !== -1) { // get seriennummer elemetns
        	$(this).attr('data-parsley-length', '[1, 1024]');
        	$(this).attr('data-parsley-error-message', tooManyString);
		}
		// beschreibung:
        var searchName = '[beschreibung]';
        if ($(this).attr('name').indexOf(searchName) !== -1) { // get beschreibung elemetns
        	$(this).attr('data-parsley-length', '[1, 4096]');
        	$(this).attr('data-parsley-error-message', tooManyString);
		}
    	var articleUid = $(this).closest('.ranArticleFieldset').attr('data-articleUid');
        // console.log($(this).closest('.ranArticleFieldset'));
    	if (!articleUid) {
            articleUid = 1;
        }
		var name = $(this).attr('name');
        $(this).attr('name', name+'['+articleUid+']');
    });
    // If the data already exists, for example after validation: create many articles from existing data
	if (typeof articlesData !== 'undefined') {
        var existsArticles = $.parseJSON(articlesData);
        $.each(existsArticles,  function(index, article) {
        	// console.log(index);
        	// console.log(article);
            articleClone(index, article);
            articleIndex = parseInt(index) + 1;
		});
    }
	// "new article" button
	$('.newArticleBtn').on('click', function(e) {
		e.preventDefault();
		// clone all article fields from index=1
        articleClone(articleIndex, '');
        articleIndex = articleIndex + 1;
		// $('.newArticleBtn').closest('.powermail_fieldset').insertBefore(clone);
	});
	// collapsible
	$('body').on('click', '.ranArticleFieldset h2.ran-article', function() {
		var parentFieldSet = $(this).closest('.ranArticleFieldset');
		var targetIndex = parentFieldSet.attr('data-articleuid');
        // hide all articles
        // $('.ranArticleFieldset:not(:has(h2.ran-article))').fadeOut();
		if ($(this).attr('data-shown') == 1) {
            $('.ranArticleFieldset[data-articleuid="' + targetIndex + '"]:not(:has(h2.ran-article))').fadeOut();
            $(this).attr('data-shown', 0);
        } else {
            // show only needed
            $('.ranArticleFieldset[data-articleuid="' + targetIndex + '"]').fadeIn();
            $(this).attr('data-shown', 1);
		}
	});
	// show all articles on field error
	if ($('.powermail_form').length) {
        // $('.powermail_form').parsley().on('field:error', function () {
        //     $('.ranArticleFieldset:not(:has(h2.ran-article))').fadeIn();
        //     $('.ranArticleFieldset h2.ran-article').attr('data-shown', 1);
        // });
    }
    // delete article
    updateArticleDeleteButtons();
    $('body').on('click', '.ran-article i.lst-delete-article', function() {
		var articleuid = $(this).closest('.ran-article').attr('data-articleuid');
        var articleCount = $('h2.ran-article').length; // articles count
        if (articleCount > 1) {
            $('.ranArticleFieldset[data-articleuid=' + articleuid + ']').remove();
            // articleIndex = Math.max.apply(null, $('.ranArticleFieldset[data-articleuid]').map(function(){ return $(this).attr('data-articleuid'); }).get()) + 1;
            updateArticleDeleteButtons();
            updateArticleIndexes();
        }
    });
    // form print button
	$('.lst-printButton').on('click', function(){
        var printStyles = `
            <style>
                @page {
                    size: A4 portrait;
                    margin: 15mm;
                }
        
                @media print {
                    html,
                    body {
                        width: 210mm;
                        min-height: 297mm;
                        margin: 0;
                        padding: 0;
                    }
        
                    .powermail_print_page {
                        print-color-adjust: exact;
                        -webkit-print-color-adjust: exact;
                    }
                }
            </style>
        `;
        var printContentNeed = $('.tx-powermail .powermail_create').first();
        var printContent = printContentNeed.clone()
        var printHeader = $('.add-print-header', 'body').clone();
        var printFooter = $('.add-print-footer', 'body').clone();
        printHeader.prependTo(printContent).show();
        printFooter.appendTo(printContent).show();
        
        var userAddress = printContent.find('.UserAdress-ran').first();
        var headerBodytext = printContent
            .find('.ce-printheaderadress')
            .first()
            .children('.ce-bodytext')
            .first();
        
        if (userAddress.length && headerBodytext.length) {
            userAddress.insertBefore(headerBodytext);
        }
        
		var headNeed = $('head').clone();
        $('.lst-printButton', printContent).remove();
        var WindowPrint = window.open('', '', 'left=0,top=0,width=800,height=900,toolbar=0,scrollbars=1,status=0');
        WindowPrint.document.write(
            '<html><head>' +
            headNeed.html() +
            printStyles +
            '</head><body class="powermail_print_page">' +
            '<div class="powermail_create_print">' +
            printContent.html() +
            '</div>'
        );
	    WindowPrint.document.write('<script type="text/javascript">$(window).load(function() { window.print(); window.close(); });</script>');
	    WindowPrint.document.write('</body></html>');
        WindowPrint.document.close();
        // WindowPrint.focus();
        // WindowPrint.setTimeout('console.log(window.top); window.top.print();', 1000);
        // WindowPrint.print();
	// WindowPrint.setTimeout('WindowPrint.print();', 1000);
        // WindowPrint.close();
	});

    // if ($('body').hasClass('lang-de')) {
    //     // DE
    //     var approveButton = 'Annehmen';
    //     var declineTextareaTitle = 'Grund für Ablehnung';
    //     var declineButton = 'Ablehnen';
    // } else {
    //     // EN
    //     var approveButton = 'Approve';
    //     var declineTextareaTitle = 'Reason for rejection';
    //     var declineButton = 'Decline';
    // }
    // Alternative way to get Language - by already existing button!!!
    var pageLang = 'DE';
    if ($('header.header nav.navbar-service .nav-item.dropdown-lang .dropdown-toggle .nav-servTxft').text() == 'English') {
        pageLang = 'EN';
    }
    var approveButton = 'Annehmen';
    var declineTextareaTitle = 'Grund für Ablehnung';
    var declineButton = 'Ablehnen';
    if (pageLang == 'EN') {
        approveButton = 'Approve';
        declineTextareaTitle = 'Reason for rejection';
        declineButton = 'Decline';
    }

    // after BE declining we need to have a field for a reason of declining. BE code gives us only 'hidden' field
    // make it as textarea
    if ($('input[name="tx_powermail_pi1\[field\]\[declineReason\]"]').length) {
        $('input[name="tx_powermail_pi1\[field\]\[declineReason\]"]').remove();
        // add textarea before the button
        var button = $('input[type="submit"][data-powermail-form-ajax="submit"]');
        if (button.length) {
            button.val(declineButton);
            var textarea = $('<div class="row">' +
                '<div class="powermail_fieldwrap powermail_fieldwrap_type_textarea powermail_fieldwrap_declineReason ranInputItem tempCss form-group w-100">' +
                '<label for="powermail_field_declineReason" class="powermail_label" title="">' +
                declineTextareaTitle +
                '</label>' +
                '<div class="powermail_field">' +
                '<textarea rows="5" cols="20" placeholder="'+declineTextareaTitle+'" class="powermail_textarea  form-control" id="powermail_field_declineReason" name="tx_powermail_pi1[field][declineReason]"></textarea>' +
                '</div>' +
                '</div>' +
                '</div>');
            textarea.prependTo(button.closest('form'));
        }
    }
    // change the "Approve" button value:
    if ($('input[name="tx_powermail_pi1\[field\]\[forceApprove\]"]').length) {
        // add textarea before the button
        var button = $('input[type="submit"][data-powermail-form-ajax="submit"]');
        if (button.length) {
            button.val(approveButton);
        }
    }

});

function updateArticleDeleteButtons() {
    // delete button is accessible only if we have single article
    if ($('h2.ran-article').length > 1) {
        $('.powermail_form .ranArticleFieldset[data-articleuid] .lst-delete-article').show();
    } else {
        $('.powermail_form .ranArticleFieldset[data-articleuid] .lst-delete-article').first().hide();
	}
}

function updateArticleIndexes() {
    // update article indexes to right order 1,2,3....
	var ind = 1;
    $('h2.ran-article').each(function() {
        var articleuid = $(this).attr('data-articleuid');
		var mainWrapper = $('.ranArticleFieldset[data-articleuid='+articleuid+']'); // set of fieldsets for this article
		mainWrapper.each(function() {
			var mainDiv = $(this);
			$('*[name]', mainDiv).each(function() {
				var sourceName = $(this).attr('name');
				var newName = sourceName.replace('['+articleuid+']', '['+ind+']');
                $(this).attr('name', newName);  // change name attribute
                // $('h2.ran-article', mainDiv).attr('data-articleuid', ind); // change number in article's title

			});
			mainDiv.attr('data-articleuid', ind);
            $('h2.ran-article .article-number', mainDiv).html(ind); // change number in article's title
			$('[data-articleuid]', mainDiv).attr('data-articleuid', ind); // change data-articleuid atribute
		});
		ind = ind + 1;
    });
    articleIndex = Math.max.apply(null, $('.ranArticleFieldset[data-articleuid]').map(function(){ return $(this).attr('data-articleuid'); }).get()) + 1;
}

function articleClone(artIndex, data) {
    $('.ranArticleFieldset[data-articleuid="1"]').each(function() {
    	if (artIndex == 1) {
            var clone = $(this);
        } else {
            var clone = $(this).clone();
		}
		// change for new values
        clone.find('.lst-delete-article').show();
		clone.attr('data-articleuid', artIndex);
		clone.find('.article-number').text(artIndex);
		clone.find('[data-articleuid="1"]').attr('data-articleuid', artIndex);
		clone.find('input, select, textarea').each(function() {
            var newName = $(this).attr('name').replace('[1]', '['+artIndex+']');
			$(this).attr('name', newName);
			// change values
			if (data == '') {
                $(this).val('');
                clone.find('.parsley-error').removeClass('parsley-error');
                clone.find('.parsley-errors-list').remove();
            }

		});
		if (data != '') {
            for (var prop in data) {
                if (data.hasOwnProperty(prop)) {
                    var value = data[prop];
                    var inputName = 'tx_powermail_pi1\[field\]\['+prop+'\]\['+artIndex+'\]'
                    $('*[name="'+inputName+'"]').val(value);
                }
            }
        }
		// insert before button
		$('.newArticleBtn').closest('.powermail_fieldset').before(clone);
		// hide all articles, show this
		$('.ranArticleFieldset:not(:has(h2.ran-article))').fadeOut();
		$('.ranArticleFieldset h2.ran-article').attr('data-shown', 0);
		$('.ranArticleFieldset[data-articleuid="' + artIndex + '"]').fadeIn();
        updateArticleDeleteButtons();
    });
}

// For DayCode form (Tagescode)
function actDayCodeForm(hand) {
    $('#powermail_field_seriennummer').removeClass('parsley-error');

    var typeValue = $(hand).val();
    if (typeValue == 'BC600' || typeValue == 'BC216' || typeValue == 'BC016' || typeValue == 'BZK20 / BC216/D6') {
        $('.powermail_fieldwrap_aktuellesanlagendatum').show();
        $('#powermail_field_aktuellesanlagendatum').attr('required', 'required');
        $('.powermail_fieldwrap_schluesselzahl').show();
        $('#powermail_field_schluesselzahl').attr('required', 'required');
        // seriennummer is NOT mandatory
        $('.powermail_fieldwrap_seriennummer span.mandatory').hide();
        $('#powermail_field_seriennummer').removeAttr('required');
    } else if (typeValue == 'BC06' || typeValue == 'BC08') {
        $('.powermail_fieldwrap_aktuellesanlagendatum').hide();
        $('.powermail_fieldwrap_aktuellesanlagendatum input').val('');
        $('#powermail_field_aktuellesanlagendatum').removeAttr('required');
        $('.powermail_fieldwrap_schluesselzahl').hide();
        $('.powermail_fieldwrap_schluesselzahl input').val('');
        $('#powermail_field_schluesselzahl').removeAttr('required');
        // seriennummer is mandatory
        $('.powermail_fieldwrap_seriennummer span.mandatory').show();
        $('#powermail_field_seriennummer').attr('required', 'required');
    }
}

function actDayCodeFormFromRegion() {
    // console.log(gtnSelectedRegion);
    if (gtnSelectedRegion == 15) { // Internationaler Verkauf
        // console.loadData('show');
        // Errichterfirma, Bisherige Wartungsfirma, Neue Wartungsfirma, Grund für Wechsel der Wartungsfirma
        $('.powermail_fieldwrap_errichterfirma, .powermail_fieldwrap_bisherigewartungsfirma, .powermail_fieldwrap_neuewartungsfirma, .powermail_fieldwrap_grundfuerwechselderwartungsfirma').show();
        $('#powermail_field_errichterfirma, #powermail_field_bisherigewartungsfirma, #powermail_field_neuewartungsfirma, #powermail_field_grundfuerwechselderwartungsfirma').attr('required', 'required');
    } else {
        $('.powermail_fieldwrap_errichterfirma, .powermail_fieldwrap_bisherigewartungsfirma, .powermail_fieldwrap_neuewartungsfirma, .powermail_fieldwrap_grundfuerwechselderwartungsfirma').hide();
        $('.powermail_fieldwrap_errichterfirma input, .powermail_fieldwrap_bisherigewartungsfirma input, .powermail_fieldwrap_neuewartungsfirma input, .powermail_fieldwrap_grundfuerwechselderwartungsfirma textarea').val('');
        $('#powermail_field_errichterfirma, #powermail_field_bisherigewartungsfirma, #powermail_field_neuewartungsfirma, #powermail_field_grundfuerwechselderwartungsfirma').removeAttr('required');
    }
}

function checkMainRegionSelected(form) {
    if (form != null && form.length) {
        var selected = form.find('input[name="mainregion"]:checked');
    } else {
        var selected = $('input[name="mainregion"]:checked');
    }
    var mainRegion = selected.val();
    // for modal
    $('#zip_search_form').find(':input').removeAttr("disabled");
    // for location page
    $('#st_zipserahcform').find(':input').removeAttr("disabled");
    // DE + International: ZIP deaktivieren + Region auto-select
    if (mainRegion == 'inter' || mainRegion == 'de') {
    
        // ZIP-Suche deaktivieren (Modal + Standorte-Seite)
        $('#zip_search_form').find(':input').prop("disabled", true);
        $('#st_zipserahcform').find('input[type="text"], button').prop("disabled", true);
    
        // Ziel-Link je nach Region
        var $targetLink = (mainRegion == 'inter')
            ? $('#list-region-international')   // UID 15
            : $('#list-region-mep');            // UID 14 (Pockau-Lengefeld)
    
        // Wenn der Klick von links kam (a[data-mainregion]) soll kein Auto-Trigger passieren
        if ($targetLink.attr('disable-trigger') == 1) {
            $targetLink.removeAttr('disable-trigger');
        } else {
            if (!$targetLink.hasClass('active')) {
                $targetLink.trigger('click');
            }
        }
    
    } else {
        // AU (und sonstige): ZIP wieder aktiv
        $('#zip_search_form').find(':input').prop("disabled", false);
        $('#st_zipserahcform').find('input[type="text"], button').prop("disabled", false);
        $('#zip_search_form').show();
    }
    // location page: maps
    $('#location-map-de-outer, #location-map-at-outer, #location-map-inter-outer').hide();
    switch (mainRegion) {
        case 'au':
            $('#location-map-at-outer').show();
            $(window).trigger('resize');
            break;
        case 'de':
            $('#location-map-de-outer').show();
            $(window).trigger('resize');
            break;
        case 'inter':
            $('#location-map-inter-outer').show();
            break;

    }
}

$(document).ready(function () {
    // .powermail_form_10 - is class of needed form
	if ($('.powermail_form_10').length) {
        actDayCodeFormFromRegion();
	if ($('body').hasClass('lang-en')) {
            sutErrMess = 'must contain a 5 digit number'
        } else {
            sutErrMess = 'muss eine 5-stellig Ziffer beinhalten'
        }
/*
        window.Parsley.addValidator('dayCodeKeyLength', { // for powermail_field_schluesselzahl
                requirementType: 'string',
                validateNumber: function(value, requirement) {
                    value = value.toString();
                    if (value.length != requirement) {
                        return false;
                    }
                    if (isNaN(value)) {
                        return false
                    }
                    return true;
                },
                messages: {
                    en: sutErrMess,
                    de: sutErrMess
                }
            });
  */
	    // powermail_field_marker_02 - Schlüsselzahl
        $('#powermail_field_schluesselzahl').attr('data-parsley-day-code-key-length', '5');
        // $('#powermail_field_marker_02').attr('data-parsley-maxlength', '5').attr('maxLength', '5');
        // $('#powermail_field_marker_02').attr('data-parsley-minlength', '5').attr('minLength', '5');
        // $('#powermail_field_marker_02').attr('data-parsley-trigger', 'focusout');
        actDayCodeForm($('#powermail_field_zentraltype'));
    	$('.powermail_form_10').on('change', '#powermail_field_zentraltype', function() {
            actDayCodeForm($(this));
        })
	}
});

