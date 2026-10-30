
// Can also be used with $(document).ready()
$(window).load(function () {
	$('.flexslider').flexslider({
		animation: "fade",
		prevText: "",
		nextText: "",
		directionNav: false,
		controlNav: true,
		slideshow: false, 
		slideshowSpeed: 4200,
		animationSpeed: 800,
		slideshow: true
	});
});

$('#myTab a').on('click', function (e) {
  e.preventDefault()
  $(this).tab('show')
})

/*
$('.dropdown').on('show.bs.dropdown', function(e){
  $(this).find('.dropdown-menu').first().stop(true, true).slideDown(300);
});

$('.dropdown').on('hide.bs.dropdown', function(e){
  $(this).find('.dropdown-menu').first().stop(true, true).slideUp(200);
});
*/

$('.dropdown').on({
    mouseenter: function () {
	// dropdown effect only if .dropdown-toggle is not visible.  .dropdown-toggle - is chevron - is visible only for window width <= 768
	if (!$(this).find('.dropdown-toggle:visible').length && !$(this).is('.dropdown-lang'))
		$(this).find('.dropdown-menu').first().stop(true, true).slideDown(300);	
    },
    mouseleave: function () {
	var tempVar = $(this).find('.dropdown-menu').first();
	if ($(this).find('.dropdown-toggle:visible')) {
		// for main menu with page with < 720
		tempVar.slideUp(200, function() {
			tempVar.removeAttr('style').removeAttr('x-placement').removeClass('show'); 				
		});					
	} else if ($(this).is('.dropdown-lang')) {		
		// it is need because SlideUp works with another attributes instead Bootstrap. But we use and Bootstrap and SlideUp in language menu		
		tempVar.slideUp(200, function() {
			tempVar.removeAttr('style').removeAttr('x-placement').removeClass('show'); 				
		});			
	} else {
		tempVar.stop(true, true).slideUp(200);
	}		
    }
});

//$('.dropdown.dropdown-lang').on('click', function () {
//	console.log(	$(this).closest('.nav-item').find('.dropdown-menu').first());
//	$(this).closest('.nav-item').find('.dropdown-menu').first().css('display', 'block');
//})

// tooltips
$(document).ready(function() {
  $('[data-toggle="tooltip"]').tooltip()
})

function setCookie(key, value, lifetime) {
        var expires = new Date();
	if (lifetime == 'session') {
		var expiresStr = '';
	} else if (lifetime == null) {
		var life = (1 * 24 * 60 * 60 * 1000) * 366 / 2; // half of year
		expires.setTime(expires.getTime() + life);
		var expiresStr = ';expires=' + expires.toUTCString();
	} else {
		var life = lifetime;
		expires.setTime(expires.getTime() + life);
		var expiresStr = ';expires=' + expires.toUTCString();
	}

	document.cookie = key + '=' + value + ';path=/' + expiresStr;
//            document.cookie = key + '=' + value + ';path=/;expires=-1';
}

function getCookie(key) {
	var keyValue = document.cookie.match('(^|;) ?' + key + '=([^;]*)(;|$)');
        return keyValue ? keyValue[2] : null;
}

// different behaviour on different showing of region selecting modal
$('#selectRegion').on('show.bs.modal', function (e) {
	// if no selected region - hide 'save button'
  //if (!gtnSelectedRegion) 
  //$('.lst-region-save-button').hide();
  $('.lst-region-save-button').attr('disabled', 'disabled');
  var currentRegion = getCookie('selectedRegion');
  // clicking on regions
  $('#selectRegion a').filter(function() {return $(this).attr("data-tscatuid") > 0;}).on('click', function(e) {
	  var selectedRegion = $(this).attr('data-tscatuid');
	  //$('.lst-region-save-button').show();
	  $('.lst-region-save-button').removeAttr('disabled');
//	  if (selectedRegion == 15 && !$('input[name="mainregion"][value="inter"]').attr('checked')) {	  
//		$('input[name="mainregion"][value="inter"]').prop('checked', true);
//  	  } else {
//		checkMainRegionSelected();
//	  };  	 
	  // deselect already active region
	  if ($(this).hasClass('active')) {
		  e.stopPropagation();
		  e.preventDefault();
		  var id = $(this).attr('id');		  
		  //console.log($(this));
		  $(this).removeClass('active');//.attr('aria-expanded', false);
		  $('#selectRegion .tab-pane[aria-labelledby = "'+id+'"]').removeClass('active').addClass('fade');
		  if ($('body').hasClass('lang-en')) {			
			changeRegionTitles('Select Region');
		  } else {
		  	changeRegionTitles('Region wählen');
	  	  }
		  setCookie('selectedRegion', null);
		  $('.lst-region-save-button').attr('disabled', 'disabled'); // todo: is it need?
	  };	 
  })
  if (!currentRegion) {
	// only for EN
	$('body.lang-en #selectRegion a[data-tscatuid=15]').trigger('click');
  }
  var invoker = $(e.relatedTarget);
  if (invoker.attr('data-noPageReload') == 1)
	reloadPageForRegionSave = false;
  else
	reloadPageForRegionSave = true;
  checkMapFromRegionRadioBox($('input:radio[name="mainregion"]:checked'));
});

$('body').on('click', 'a[data-mainregion]', function() {
	var region = $(this).attr('data-mainregion');
	$(this).attr('disable-trigger', 1);
	$('#mainregion_'+region).prop('checked', true);
	checkMainRegionSelected();
});

//checkMapFromRegionRadioBox($('input:radio[name="mainregion"]:selected')); 
$('body').on('change', 'input:radio[name="mainregion"]', function() {
	checkMapFromRegionRadioBox($(this));
});


function checkMapFromRegionRadioBox(element) {
	var region = element.val();
	switch (region) {
		case 'au':
			$('#nav-tabContent .tab-pane').removeClass('active');
			$('#list-au').tab('show');
			break;
		case 'de':
			$('#nav-tabContent .tab-pane').removeClass('active');
			$('#list-de').tab('show');
			break;
	};
}

$('.lst-region-save-button').on('click', function() {
	var selectedRegion = $('#selectRegion').find('a.list-group-item-action.active[role="tab"]').first().attr('data-tsCatUid');
	var selectedRegionTitle = $('#selectRegion').find('a.list-group-item-action.active[role="tab"]').first().text();	
	//console.log(selectedRegionTitle);
	if (selectedRegion)
		setCookie('selectedRegion', selectedRegion);
	else {
		setCookie('selectedRegion', null);
		reloadPageForRegionSave = false;
	}
	// reload current page
	if (reloadPageForRegionSave) { // reload page - all markers and data will be for new region
		location.reload();
	} else { // change only some elements on the page. need sometimes for forms		
		gtnSelectedRegion = selectedRegion;		
		changeRegionTitles(selectedRegionTitle);
		// For Day Code form 
		var dayCodeDoings = window['actDayCodeFormFromRegion']; 
		if (typeof dayCodeDoings !== 'undefined' && typeof dayCodeDoings === 'function') {
			actDayCodeFormFromRegion();
		}
		$('#selectRegion').modal('hide');
	}
	
})

function changeRegionTitles(selectedRegionTitle) {
	$('.lst-regionTitle').text(selectedRegionTitle);
	ShowRegisterFormBySelectedRegion();
}

function ShowRegisterFormBySelectedRegion() {
	if (!(gtnSelectedRegion > 0)) {
		// $('#gtn-register-fields').hide();
	} else {
		// $('#gtn-register-fields').show();		
		var country = '';
		switch(gtnSelectedRegion.toString()) {
			case '9':  
			case '10':  
			case '11':  
				country = 'AUT';
				break;
			case '12':  
			case '13':  
			case '14':  
				country = 'DEU';
				break;
		}
		$('#tx-srfeuserregister-pi1-static_info_country').val(country);
		$('#tx-sremailsubscribe-pi1-static_info_country').val(country);
//		$('#powermail_field_marker_country').val(country);
//		$('#powermail_field_land_01_country').val(country);
//		$('#powermail_field_land2_country').val(country);
//		$('#powermail_field_land_country').val(country);
	}
	
}

$(window).load(function () {	
	$('#tx-srfeuserregister-pi1-static_info_country, #tx-sremailsubscribe-pi1-static_info_country').addClass('form-control').removeAttr('size');
	ShowRegisterFormBySelectedRegion();	
	if (gtnSelectedRegion > 0)
		$('#gtnRegionSelect').val(gtnSelectedRegion);
	// $('#gtnRegionSelect').on('change', function() {
		// gtnSelectedRegion = $(this).val();
		// ShowRegisterFormBySelectedRegion();			
	// });	
	// AGB checkbox and submit button
//	checkAgb();
//	$('#agbCheck').on('change', checkAgb);
});

var checkAgb = function() {
	if ($('#agbCheck').is(':checked')) 
		$('#subscribeSubmit, #registerSubmit').removeAttr('disabled');
	else 
		$('#subscribeSubmit, #registerSubmit').attr('disabled', 'disabled');
};


$('.glyphicontoggle').on('click', function() {
    $(this).toggleClass('glyphicons-chevron-down glyphicons-chevron-right');
});

$('.openLayerBu').on('click', function() {
    $(this).toggleClass('openlayer');
});


jQuery(document).ready(function() {
  jQuery(".hide_contentbu").hide();
  jQuery(".hc_show").show();
  
  $('.hide_contentbu.hc_show').closest(".buOrga").addClass( "hc_open" )
  
  //toggle the componenet with class msg_body
  jQuery(".openLayerBu").click(function()
  {
    jQuery(this).next(".hide_contentbu").slideToggle(500);
    jQuery(this).parent(".buOrga").toggleClass("hc_open");
    
  });

});

$(document).ready(function() {
    $('.lightbox').magnificPopup({
      type:'image',
      tClose: 'Schließen (Esc)', // Alt text on close button
      tLoading: 'Bilder werden geladen ...', // Text that is displayed during loading. Can contain %curr% and %total% keys
      gallery: {
        enabled: false,
        tCounter: '<span class="mfp-counter">%curr% von %total%</span>',
        tPrev: 'Vorheriges (Linke Pfeil Taste)', // title for left button
        tNext: 'Nächstes (Rechte Pfeil Taste)', // title for right button
      }
    });
});

// reload captcha (for 'captcha' extension)
$(document).ready(function() {
	var captchaIndex = 1;
	$('.powermail_captchaimage').each(function() {
		$(this).attr('data-captcha-index', captchaIndex);
		if ($('body').hasClass('lang-en')) {			
			var link_text = "<span>If you can't read the word, <a class=\"captcha-reloader\" data-captcha-index=\""+captchaIndex+"\" href=\"#\">click here</a>.</span>";
		} else {
			var link_text = "<span>Wenn Sie das Wort nicht lesen können, <a class=\"captcha-reloader\" data-captcha-index=\""+captchaIndex+"\" href=\"#\">klicken Sie hier</a>.</span>";
			$('.powermail_captcha[data-parsley-required-message]').attr('data-parsley-required-message', 'Ungültige Eingabe!');
		}
		$(this).after(link_text);
		captchaIndex = captchaIndex + 1;
	});
	$('body').on('click', '.captcha-reloader', function(e) {
		e.preventDefault();
		var index = $(this).attr('data-captcha-index');
		var img = $('img.powermail_captchaimage[data-captcha-index="' + index + '"]').first();
		var d = new Date();
		var newSrc = img.attr('src').replace('rand=', 'randOld=') + '&rand=' + Math.floor(Math.random() * 100) + d.getTime(); 
		img.attr("src", newSrc);
	});
	if ($('.powermail_field_error').length) {
		$('.powermail_form').parsley().validate();
	};
});

// job filter form
$(document).ready(function() {
	$('.job-filter form').on('change', 'input:checkbox', function(){
		$(this).closest('form').submit();
	});
});

// modal on first start page
$(document).ready(function() {
	setTimeout(function() {
		var debug = $("#startModal").attr('data-debug');
		var already = getCookie('startModalShown');
        	if (!already || debug == "1") {
			$("#startModal").modal('show');
			if (debug != "1") {
				var lifetime = (1 * 24 * 60 * 60 * 1000) * 366;
				setCookie('startModalShown', true, lifetime);
			}
		} 
	}, 1000);
 });

 $(document).ready(function(){
	setTimeout(function() {
		var already = getCookie('showPopup');
        	if (!already) {
			$("#showpopup").modal('show');
			var lifetime = (1 * 24 * 60 * 60 * 1000); // lifetime = 1 day
			// var lifetime = 'session'; // just for session, but browsers have own settings!
			setCookie('showPopup', true, lifetime);			
		} 
	}, 1000);

 });
 

// Imagemap responsiv
$(document).ready(function(e) {

    $('img[usemap]').rwdImageMaps();
  
    // preload all images - for fast working
	$('<img src="/fileadmin/Lst2017/Resources/Public/Images/map_world_wien.png" style="display: none;" />').appendTo($('body'));
	$('area[alt]').each(function() {
		$('<img src="/fileadmin/Lst2017/Resources/Public/Images/' + $(this).attr('alt') + '" style="display: none;" />').appendTo($('body'));
	});

    $(document).on('touchstart mouseover', '#location-map-at area, #location-map-de area', function(e) {
	var mapEl = $(this).closest('map');
	var lan = $(this).attr('alt');
	var imgEl = $('#' + mapEl.attr('id') + '-img');
	imgEl.attr('src','/fileadmin/Lst2017/Resources/Public/Images/' + lan);
//	imgEl.trigger('load');
	return false;
    });

});

/*
// Change Image for location map  
window.addEventListener ('load', function () {
var map = document.querySelectorAll('#location-map-at area');
for (var i=0; i<map.length; i++) {
   if (map[i].addEventListener) {
      map[i].addEventListener('touchstart', swap, false);
      map[i].addEventListener('mouseover', swap, false);
   }
}

function swap(ev) {
   var lan = this.getAttribute('alt');
   document.getElementById('location-map-at-img').setAttribute('src','/fileadmin/Lst2017/Resources/Public/Images/' + lan);
   return false;
}
});

// Change Image for location map  
window.addEventListener ('load', function () {
var map = document.querySelectorAll('#location-map-de area');
for (var i=0; i<map.length; i++) {
   if (map[i].addEventListener) {
      map[i].addEventListener('touchstart', swap, false);
      map[i].addEventListener('mouseover', swap, false);
   }
}

function swap(ev) {
   var lan = this.getAttribute('alt');
   document.getElementById('location-map-de-img').setAttribute('src','/fileadmin/Lst2017/Resources/Public/Images/' + lan);
   return false;
}
});
*/

// hook for correct working of login link from login-form: 
// if I click: 
// 1. logout link - will see 'login' form
// 2. login link - will see 'login' form again
// 3. log in with user data - I will return into the page with login form with current information about logged in user. It is wrong. We need to have redirecting from here
$(document).ready(function(e) {

	if ($('body#page-38').length) { // we are in the page with login form
		var currentUrl = window.location.href;
		let paramStr = currentUrl.split('?')[1]; // get parameters
		let paramsArr = paramStr.split('&'); // get array
		let logoutForm = false;
		for (let i = 0; i < paramsArr.length; i++) {
			let pair = paramsArr[i].split('=');
			if (pair[0] == 'logintype' && pair[1] == 'logout') { // we just logged out! (TODO: correct rule?)
				logoutForm = true;
				break;
			}			
		}
		if (logoutForm) {
			// find 'login' link
			var loginEl = $('#menuitem38');
			// add referer into link
			var referer = $('.tx-felogin-pi1 form input[name="referer"]').val();
			var currUrl = loginEl.attr('href');
			if (currUrl.indexOf('?') !== -1) {
				currUrl = currUrl + '&';
			} else {
				currUrl = currUrl + '?';
			}
			currUrl = currUrl + 'referer=' + referer;
			loginEl.attr('href', currUrl); 
		}
	}

});

// New File Upload
document.addEventListener('DOMContentLoaded', function () {
  document
	.querySelectorAll('input.powermail_file[type="file"]')
	.forEach(input => {
	  FilePond.create(input, {
		allowMultiple: true,
		storeAsFile: true,
		instantUpload: false,
		allowRevert: false,
		labelIdle:
		  'Dateien hier ablegen oder <span class="filepond--label-action">durchsuchen</span>'
	  });
	});
});
