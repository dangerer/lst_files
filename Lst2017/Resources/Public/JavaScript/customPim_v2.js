jQuery(document).ready(function() {
  
	jQuery(".button-collapse").on('click', function(){
		jQuery("#slide-out").toggleClass('fixed');
	});

	menuMobile_child();

	function menuMobile_child() {
		jQuery('ul.sk-mobile-menu ul.sub-menu  > li:has(ul)').addClass("has-sub");
		jQuery('ul.sk-mobile-menu ul.sub-menu > li:has(ul) > a').after('<span class="caret"><i class="fa icon fa icon-angle-down ion-chevron-down"></i></span>');
		jQuery(document).on('click', ' ul.sk-mobile-menu ul.sub-menu > li > .caret', function(e) {
			e.preventDefault();
			var checkElement = jQuery(this).next();
			jQuery('ul.sk-mobile-menu ul.sub-menu > li').removeClass('menu-active');
			jQuery(this).closest('li').addClass('menu-active');

			if ((checkElement.is('.sub-menu')) && (checkElement.is(':visible'))) {
				jQuery(this).closest('li').removeClass('menu-active');
				checkElement.slideUp('fast');
			}

			if ((checkElement.is('.sub-menu')) && (!checkElement.is(':visible'))) {
				jQuery('ul.sk-mobile-menu ul.sub-menu .sub-menu:visible').slideUp('normal');
				checkElement.slideDown('fast');
			}

			if (checkElement.is('.sub-menu')) {
				return false;
			} else {
				return true;
			}
		});

	}


	menuMobile();

	function menuMobile() {
		jQuery('ul.sk-mobile-menu > li:has(ul)').addClass("has-sub");
		jQuery('ul.sk-mobile-menu > li:has(ul) > a').after('<span class="caret"><i class="fa icon fa icon-angle-down ion-chevron-down"></i></span>');
		jQuery(document).on('click', ' ul.sk-mobile-menu > li > .caret', function(e) {
			e.preventDefault();
			var checkElement = jQuery(this).next();
			jQuery('ul.sk-mobile-menu > li').removeClass('menu-active');
			jQuery(this).closest('li').addClass('menu-active');

			if ((checkElement.is('.sub-menu')) && (checkElement.is(':visible'))) {
				jQuery(this).closest('li').removeClass('menu-active');
				checkElement.slideUp('fast');
			}

			if ((checkElement.is('.sub-menu')) && (!checkElement.is(':visible'))) {
				jQuery('ul.sk-mobile-menu .sub-menu:visible').slideUp('normal');
				checkElement.slideDown('fast');
			}

			if (checkElement.is('.sub-menu')) {
				return false;
			} else {
				return true;
			}
		});

	}

	menuMobile_parent();

	function menuMobile_parent() {
		jQuery('ul.collapsible > li:has(.collapsible-body)').addClass("has-sub");
		jQuery('ul.collapsible > li:has(.collapsible-body) > a').after('<span class="sk-caret"><i class="fa icon fa icon-angle-down ion-chevron-down"></i></span>');
		jQuery(document).on('click', ' ul.collapsible > li > .sk-caret', function(e) {
			e.preventDefault();
			var checkElement = jQuery(this).next();
			jQuery('ul.collapsible > li').removeClass('sk-active');
			jQuery(this).closest('li').addClass('sk-active');

			if ((checkElement.is('.collapsible-body')) && (checkElement.is(':visible'))) {
				jQuery(this).closest('li').removeClass('sk-active');
				checkElement.slideUp('fast');
			}

			if ((checkElement.is('.collapsible-body')) && (!checkElement.is(':visible'))) {
				jQuery('ul.collapsible .collapsible-body:visible').slideUp('normal');
				checkElement.slideDown('fast');
			}

			if (checkElement.is('.collapsible-body')) {
				return false;
			} else {
				return true;
			}
		});

		jQuery('.sidenav-bg').on('click',function() {
			jQuery("#slide-out").removeClass('fixed');
		});

	}

	if (jQuery(window).width() < 1020) {
	   jQuery("#slide-out").removeClass('fixed');
	}
	else {
	   jQuery("#slide-out").addClass('fixed');
	}

	setTimeout(function(){
		if( jQuery('.collapsible.collapsible-accordion > li.menu-item.active').length > 0 ){
			jQuery('.collapsible.collapsible-accordion li.menu-item.menu-item-first-level.active .sk-caret:first').trigger('click');
			jQuery('.collapsible.collapsible-accordion li.menu-item.menu-item-second-level.active .sk-caret:first').trigger('click');
			jQuery('.collapsible.collapsible-accordion li.menu-item.menu-item-third-level.active .sk-caret:first').trigger('click');
			jQuery('.collapsible.collapsible-accordion > li.menu-item .active').addClass('menu-active');
		}
	}, 100);

});

jQuery(window).on('load', function() {
	if (jQuery(window).width() < 1020) {
	   jQuery("#slide-out").removeClass('fixed');
	}
	else {
	   jQuery("#slide-out").addClass('fixed');
	}
});

jQuery(window).on('resize', function() {
 
	if (jQuery(window).width() < 1020) {
	   jQuery("#slide-out").removeClass('fixed');
	}
	else {
	   jQuery("#slide-out").addClass('fixed');
	}

});

jQuery(window).on('scroll', function() {
	
});


