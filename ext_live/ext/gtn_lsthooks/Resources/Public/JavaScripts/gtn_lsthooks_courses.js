// for CALENDAR REGISTRATION FORM
// value of participantIndex is always NEW index.
var participantIndex = 1;
$(document).ready(function () {

    // change LANG menu links if location url has special paramaters
    var currUrl = new URL(document.location);
    var params = new URLSearchParams(currUrl.search);
    var revertLangBackUrlDe = params.get('revertLangBackUrlDe');
    var revertLangBackUrlEn = params.get('revertLangBackUrlEn');
    if (revertLangBackUrlDe && revertLangBackUrlEn) {
        $('.nav-item.dropdown-lang .dropdown-menu a.dropdown-lang-de').attr('href', revertLangBackUrlDe);
        $('.nav-item.dropdown-lang .dropdown-menu a.dropdown-lang-en').attr('href', revertLangBackUrlEn);
    }


    // hide total price input
    $('.powermail_fieldwrap_totalcost').hide();

    // fix a bug with checkbox names for participants:
    $('.powermail_fieldwrap_gleicheanschriftgleichefirma').each(function() {
        var checkbox = $(this).find('input:checkbox');
        var name = checkbox.attr('name');
        var newName = name.replace('[]', '');
        checkbox.attr('name', newName);
    });

    // get last participant number + 1
    $('.participantFieldset').each(function() {
        var index = parseInt($(this).attr('data-participantUid'));
        if (index >= participantIndex)
            participantIndex = index + 1;
    });

    checkLeftParticipants();

    // Change input names for using as array of Participants
    $('.courseInputItem').find('input, select, textarea').each(function() {
        // add validations.
        if ($('body').hasClass('lang-en')) {
            var tooManyString = 'Invalid entry (too many/not enough characters)!';
            var pleaseDecrease = 'Please decrease number!';
        } else {
            var tooManyString = 'Ungültige Eingabe (zu viele/zu wenige Zeichen)!';
            var pleaseDecrease = 'Bitte eine kleinere Zahl eingeben.';
        }
        var participantUid = $(this).closest('.participantFieldset').attr('data-participantUid');
        if (!participantUid) {
            participantUid = 1;
        }
        var name = $(this).attr('name');
        $(this).attr('name', name+'['+participantUid+']');
    });
    // If the data already exists, for example after validation: create many participants from existing data
    if (typeof participantsData !== 'undefined') {
        var existsParticipants = JSON.parse(participantsData);
        // if (existsParticipants.length > 0) {
        if (Object.keys(existsParticipants).length > 0) {
            // $.each(existsParticipants, function (index, participant) {
            for (var index in existsParticipants) {
                var participant = existsParticipants[index];
                if (index > 0) { // firm checkbox issue
                    var ind = parseInt(index);
                    participantClone(ind, participant);
                    participantIndex = participantIndex + 1;
                }
            };
            checkCourseSelfRegistrationCheckbox();
        } else {
            // reset empty participant form: shy there is a JSON code instead of empty?
            // participantReset(1);
        }
    }
    // "new participant" button
    $('.newParticipantBtn').on('click', function(e) {
        e.preventDefault();
        // clone all participant fields from index=1
        participantClone(participantIndex, '');
        // if (participantClone(participantIndex, '')) {
            participantIndex = participantIndex + 1;
        // }
        updateParticipantDeleteButtons();
        updateParticipantIndexes();
        updateParticipantLabelNumbers();
        // checkCourseSelfRegistrationCheckbox(true);
        updateTotalPrice();
    });
    // delete participant
    updateParticipantDeleteButtons();
    updateParticipantLabelNumbers();
    $('body').on('click', '.lst-delete-participant', function(e) {
        e.preventDefault();
        var participantuid = $(this).closest('[data-participantUid]').attr('data-participantUid');
        var participantCount = $('.powermail_fieldset_122').length; // participant count
        var pause = 200;
        if (participantCount == 1) {
            var participantWrapper = $('.participantFieldset[data-participantUid=' + participantuid + ']');
            participantWrapper.hide(); // only hide [1] participant
            // empty inputs
            participantWrapper.find('input, select, textarea').each(function() {
                $(this).val('');
                $(this).removeAttr('required');
                $(this).find('.parsley-error').removeClass('parsley-error');
                $(this).find('.parsley-errors-list').remove();
            });
        } else {
            $('.participantFieldset[data-participantUid=' + participantuid + ']').find('.delete-participant').tooltip('dispose');
            $('.participantFieldset[data-participantUid=' + participantuid + ']').hide();
            setTimeout(() => {
                $('.participantFieldset[data-participantUid=' + participantuid + ']').remove()
            }, pause);
        }
        setTimeout(() => {
            updateParticipantDeleteButtons();
            updateParticipantIndexes();
            updateParticipantLabelNumbers();
        }, pause);
    });

    // form print button
    $('.lst-printButton').on('click', function(){
        var printContentNeed = $('.tx-powermail .powermail_create').first();
        var printContent = printContentNeed.clone()
        $('.add-print-header', printContent).prependTo(printContent).show();
        $('.add-print-footer', printContent).appendTo(printContent).show();
        var headNeed = $('head').clone();
        $('.lst-printButton', printContent).remove();
        var WindowPrint = window.open('', '', 'left=0,top=0,width=800,height=900,toolbar=0,scrollbars=1,status=0');
        WindowPrint.document.write('<html>'+ headNeed.html() + '<body class="powermail_print_page"><div class="powermail_create_print">' + printContent.html() + '</div>');
        WindowPrint.document.write('<script type="text/javascript">$(window).load(function() { window.print(); window.close(); });</script>');
        WindowPrint.document.write('</body></html>');
        WindowPrint.document.close();
    });

    // register self on the course
    checkCourseSelfRegistrationCheckbox();
    $('body').on('change', '.powermail_fieldwrap_selbstfuerkursanmelden input:checkbox', function() {
        checkCourseSelfRegistrationCheckbox();
        updateParticipantDeleteButtons();
        updateParticipantLabelNumbers();
    });

    // person/firme checkbox
    $('body').on('change', '.powermail_fieldwrap_gleicheanschriftgleichefirma input:checkbox', function() {
        checkPersonFirmCheckbox($(this));
    });
    $('.powermail_fieldwrap_gleicheanschriftgleichefirma input:checkbox').each(function() {
        checkPersonFirmCheckbox($(this));
    });

    // another payment address checkbox
    checkPaymentAddressCheckbox();
    $('body').on('change', '#powermail_field_abweichenderechnungsadresse_1', function() {
        checkPaymentAddressCheckbox();
    });
});

function checkCourseSelfRegistrationCheckbox(forceShow = false) {
    var checkbox = $('#powermail_field_selbstfuerkursanmelden_1');
    var checked = $(checkbox).prop('checked');
    var participantContainers = $('.participantFieldset');
    var participantElements = participantContainers.find('.powermail_fieldwrap_vorname_01 input, .powermail_fieldwrap_nachname_01 input, .powermail_fieldwrap_email input, .powermail_fieldwrap_zertifikat input');
    // only if it is the starting of participant creating:
    if (participantIndex > 2 || forceShow) {  // 2 - first already shown
        checked = false; // to force show of participants
    }
    if (checked) {
        // hide participant containers if not custom added yet
        participantContainers.hide(); // really it hides only first participant data
        // empty data
        participantElements.val('');
        // remove parsley rules
        participantElements.find('.parsley-error').removeClass('parsley-error');
        participantElements.find('.parsley-errors-list').remove();
        participantElements.removeAttr('required');
    } else {
        // show
        participantContainers.show();
        // add parsley rules
        participantElements.attr('required', 'required');
        // remove/add asterix
        participantElements.closest('.powermail_fieldwrap').find('label .mandatory').remove();
        participantElements.closest('.powermail_fieldwrap').find('label').append('<span class="mandatory">*</span>');
        // 'zertifikate' field
        if (!$('#powermail_field_showsertfield').length || $('#powermail_field_showsertfield').val() != 1) {
            $('.powermail_fieldwrap_zertifikat.powermail_fieldwrap').find('label .mandatory').remove();
            $('.powermail_fieldwrap_zertifikat label').find('.parsley-error').removeClass('parsley-error');
            $('.powermail_fieldwrap_zertifikat label').find('.parsley-errors-list').remove();
            $('.powermail_fieldwrap_zertifikat label').removeAttr('required');
            $('.powermail_fieldwrap_zertifikat label').closest('.powermail_fieldwrap').hide();
            $('.powermail_fieldwrap_zertifikatinfo').hide();
        }
    }
    updateTotalPrice();
}

function checkPersonFirmCheckbox(checkbox) {
    var show = $(checkbox).prop('checked');
    var currContainer = $(checkbox).closest('.participantFieldset');
    var firmElementContainers = currContainer.find('.powermail_fieldwrap_firma_01, .powermail_fieldwrap_plz_01, .powermail_fieldwrap_ort, .powermail_fieldwrap_adresse, .powermail_fieldwrap_land');
    var firmElements = firmElementContainers.find('input, select');
    if (show) {
        // show
        firmElementContainers.show();
        // add parsley rules
        firmElements.attr('required', 'required');
        // remove/add asterix
        firmElements.closest('.powermail_fieldwrap').find('label .mandatory').remove();
        firmElements.closest('.powermail_fieldwrap').find('label').append('<span class="mandatory">*</span>');
    } else {
        firmElementContainers.hide();
        // empty data
        firmElements.val('');
        // remove parsley rules
        firmElements.find('.parsley-error').removeClass('parsley-error');
        firmElements.find('.parsley-errors-list').remove();
        firmElements.removeAttr('required');
    }
}

function checkPaymentAddressCheckbox() {
    var checkbox = $('#powermail_field_abweichenderechnungsadresse_1');
    var show = $(checkbox).prop('checked');
    var container = $(checkbox).closest('form').find('.powermail_fieldset_133');
    var paymentElementContainers = container.find('.powermail_fieldwrap_firma_02, .powermail_fieldwrap_plz_02, .powermail_fieldwrap_ort_01, .powermail_fieldwrap_adresse_02, .powermail_fieldwrap_land_02');
    var paymentElements = paymentElementContainers.find('input, select');
    if (show) {
        // show
        container.show();
        // add parsley rules
        paymentElements.attr('required', 'required');
        // remove/add asterix
        paymentElements.closest('.powermail_fieldwrap').find('label .mandatory').remove();
        paymentElements.closest('.powermail_fieldwrap').find('label').append('<span class="mandatory">*</span>');
    } else {
        container.hide();
        // empty data
        paymentElements.val('');
        // remove parsley rules
        paymentElements.find('.parsley-error').removeClass('parsley-error');
        paymentElements.find('.parsley-errors-list').remove();
        paymentElements.removeAttr('required');
    }
}

function checkLeftParticipants() {
    var self = 0;

    if ($('#powermail_field_selbstfuerkursanmelden_1').length) {
        var selfChecked = $('#powermail_field_selbstfuerkursanmelden_1').prop('checked');
        if (selfChecked) {
            self = 1;
        }
    }
    var leftParticipants = maxCourseParticipants - currentCourseParticipants - self;
    if (participantIndex > leftParticipants) {
        // it was last participant
        $('.newParticipantBtn').hide();
        updateTotalPrice();
        return false;
    } else {
        // ok. we can add more
        $('.newParticipantBtn').show();
        updateTotalPrice();
        return true
    }
}

function updateParticipantDeleteButtons() {
    // delete button is accessible only if we have more than single participant
    // (or if 'self-registration' is enabled)
    if ($('#powermail_field_selbstfuerkursanmelden_1').prop('checked')) {
        $('.powermail_form .participantFieldset[data-participantUid] .lst-delete-participant').show();
    } else if ($('.powermail_fieldset_122').length > 1) {
        $('.powermail_form .participantFieldset[data-participantUid] .lst-delete-participant').show();
    } else {
        $('.powermail_form .participantFieldset[data-participantuid] .lst-delete-participant').first().hide();
    }
}

function updateParticipantLabelNumbers() {
    // number in the label. If 'self-registration' is enabled - new participants numbers are +1
    $('.powermail_form .powermail_fieldset_122').each(function () {
        var participantLabelNumber = parseInt($(this).attr('data-participantUid'));
        if ($('#powermail_field_selbstfuerkursanmelden_1').prop('checked')) {
            participantLabelNumber += 1;
        }
        $(this).find('.participant-number').text(participantLabelNumber);
    })
}

function updateParticipantIndexes() {
    // update participant indexes to right order 1,2,3....
    var ind = 1;
    $('.powermail_fieldset_122').each(function() {
        var participantuid = $(this).attr('data-participantUid');
        var mainWrapper = $('.participantFieldset[data-participantUid='+participantuid+']'); // set of fieldsets for this participant
        mainWrapper.each(function() {
            var mainDiv = $(this);
            $('*[name]', mainDiv).each(function() {
                var sourceName = $(this).attr('name');
                var newName = sourceName.replace('['+participantuid+']', '['+ind+']');
                $(this).attr('name', newName);  // change name attribute
            });
            mainDiv.attr('data-participantUid', ind);
            $('.powermail_fieldwrap_participantheader .powermail-legend span .participant-number', mainDiv).html(ind); // change number in article's title
            $('[data-participantUid]', mainDiv).attr('data-participantUid', ind); // change data-participantUid attribute
        });
        ind = ind + 1;
    });
    participantIndex = Math.max.apply(null, $('.participantFieldset[data-participantUid]').map(function() { return $(this).attr('data-participantUid'); }).get()) + 1;
    checkLeftParticipants();
}

function participantClone(participantNewIndex, data) {
    var addNewIndex = true;
    $('.participantFieldset[data-participantUid="1"]').each(function() {
        if (participantNewIndex == 1) {
            var clone = $(this);
        } else {
            // do not clone (just show) if the self-registration is checked and it is first clone
            if (data == "" // no existing data
                && participantNewIndex == 2 // try to add second participant
                && $('#powermail_field_selbstfuerkursanmelden_1').prop('checked') // self-registration checked
                && $('.participantFieldset[data-participantUid="1"]').is(':hidden') // 1th partisipant is hidden!
            ) {
                var clone = $(this);
                addNewIndex = false;
            } else {
                var clone = $(this).clone();
            }
        }
        // change for new values
        clone.find('.lst-delete-participant').show();
        // recreate the tooltip
        clone.find('.delete-participant').tooltip('dispose').tooltip({placement: 'top'});
        clone.attr('data-participantUid', participantNewIndex);
//        clone.find('.participant-number').text(participantLabelNumber);
        clone.find('[data-participantUid="1"]').attr('data-participantUid', participantNewIndex);
        clone.find('input, select, textarea').each(function() {
            var newName = $(this).attr('name').replace('[1]', '['+participantNewIndex+']');
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
                    var inputName = 'tx_powermail_pi1\[field\]\['+prop+'\]\['+participantNewIndex+'\]'
                    $('*[name="'+inputName+'"]').val(value);
                }
            }
        }
        // insert before button
        $('.newParticipantBtn').closest('.powermail_fieldset').before(clone);
        clone.show();
        updateParticipantDeleteButtons();
        updateParticipantLabelNumbers();
    });
    checkLeftParticipants();
    return addNewIndex;
}

function participantReset(participantIndex)
{
    if ($('.participantFieldset[data-participantUid="' + participantIndex + '"]').length) {
        var formGroup = $('.participantFieldset[data-participantUid="' + participantIndex + '"]');
        formGroup.find('input, select, textarea').each(function() {
            $(this).val('');
            formGroup.find('.parsley-error').removeClass('parsley-error');
            formGroup.find('.parsley-errors-list').remove();
        });
    }
    return true;
}

function updateTotalPrice() {
    var priceEl = $('#courseTotalPrice');

    // price for participant per day
    var priceString = $('#powermail_field_teilnahmegebuehr').val();
    var regex = /[+-]?\d+(?:[.,]\d+)?/g; // get only digital price
    var matches = priceString.match(regex);
    var itemPrice = 0;
    if (matches) {
        itemPrice = parseFloat(matches[0].replace(',', '.'));
    }

    // count of used DAYS
    var dateString = $('#powermail_field_termin').val();
    var dates = dateString.match(/\d+\.\d+\.\d+/g);
    if (dates === null) {
        // any date. only for calculating 1 day
        dates = ['10.10.2024'];
    }

    var startDateString = dates[0];
    var endDateString = dates[1];
    if (!endDateString) {
        endDateString = startDateString;
    }
    // Parse start and end dates
    var startDateParts = startDateString.split('.');
    var endDateParts = endDateString.split('.');
    var startDate = new Date(startDateParts[2], startDateParts[1] - 1, startDateParts[0]);
    var endDate = new Date(endDateParts[2], endDateParts[1] - 1, endDateParts[0]);
    // Calculate the difference in days
    var diffInMs = endDate - startDate + 1;
    var daysCount = Math.ceil(diffInMs / (1000 * 60 * 60 * 24));

    // get count of participants
    var participants = 0;
    // self checked
    if ($('#powermail_field_selbstfuerkursanmelden_1').length) {
        var selfChecked = $('#powermail_field_selbstfuerkursanmelden_1').prop('checked');
        if (selfChecked) {
            participants += 1;
        }
    }
    // add additional participants (check by "required" field!)
    // var additionalParticipants = $('.form-row-participants #powermail_field_vorname_01[required]').length;
    // by delete buttons number:
//    var additionalParticipants = $('.lst-delete-participant:visible').length;
    var additionalParticipants = $('.lst-delete-participant').filter(function() { // :visible is not working in all cases
  	return $(this).css('display') !== 'none' && $(this).css('visibility') !== 'hidden' && this.offsetWidth > 0 && this.offsetHeight > 0;
    }).length;
//    var additionalParticipants = $('.powermail_fieldset_121 #powermail_field_vorname_01').filter(function() {return $(this).val() !== '';}).length;
console.log('additional: '+additionalParticipants);
    participants += additionalParticipants;
console.log('all: '+participants);

    if (!participants) {
        participants = 1;
    }

    // TOTAL price
    var total = itemPrice * daysCount * participants;
    total = total.toFixed(2);
    total = total.toString().replace('.', ',');
    priceEl.text('€ ' + total);

    // change hidden var for powermail emails:
    $('#powermail_field_totalcost').val('€ ' + total);

    // change "additional cost info" text regarding its value in DCE
    var costAdditionalInfoText = $('#costAdditionalInfoText').text();
    // get only first part of the string (diveded by / or -), for example: plus tax / day / participant
    var info1 = costAdditionalInfoText.split(/[\/-]/)[0].trim();
    $('#coursePriceAddInfo').text(info1);


}
