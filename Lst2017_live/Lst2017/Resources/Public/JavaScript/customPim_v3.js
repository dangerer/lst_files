jQuery(document).ready(function() {

  jQuery(".button-sidebar-collapse").on('click', function(){
	jQuery("#slide-out").toggleClass('fixed');
	jQuery(".navbar-sidebar-toggle").toggleClass('open');
	if (jQuery(".navbar-sidebar-toggle").hasClass("open")) {
		jQuery(".toggle-text").text("Produktnavigation einklappen");
	} else {
		jQuery(".toggle-text").text("Produktnavigation ausklappen");
	}
  });

  menuMobile_child();

  function menuMobile_child() {
	// --- INSERT CARETS nur einmal + .has-sub setzen (CHANGED) ---
	jQuery('ul.sk-mobile-menu ul.sub-menu > li:has(> ul)')
	  .addClass("has-sub")
	  .each(function(){
		var $li = jQuery(this);
		if (!$li.children('.caret').length) {
		  $li.children('a').after('<span class="caret"><i class="fa icon fa icon-angle-down ion-chevron-down"></i></span>');
		}
	  });

	// Delegierter Click für ALLE Ebenen innerhalb von ul.sub-menu (CHANGED)
	jQuery(document).on('click', 'ul.sk-mobile-menu ul.sub-menu > li > .caret', function(e) {
	  e.preventDefault();
	  var $li = jQuery(this).closest('li');
	  var checkElement = $li.children('ul.sub-menu').first();

	  // Nur Geschwister derselben Ebene schließen (CHANGED)
	  $li.siblings('.has-sub').each(function(){
		var $sibSub = jQuery(this).children('ul.sub-menu:visible');
		if ($sibSub.length) {
		  jQuery(this).removeClass('menu-active');
		  $sibSub.slideUp('normal');
		}
	  });

	  // Aktiv-Klasse setzen und eigenes Submenu togglen (UNCHANGED Logik, aber scoped)
	  jQuery('ul.sk-mobile-menu ul.sub-menu > li').removeClass('menu-active');
	  $li.addClass('menu-active');

	  if (checkElement.is(':visible')) {
		$li.removeClass('menu-active');
		checkElement.slideUp('fast');
	  } else {
		checkElement.slideDown('fast');
	  }

	  return false;
	});

  }


  menuMobile();

  function menuMobile() {
	// Top-Level Carets nur einmal einfügen (CHANGED)
	jQuery('ul.sk-mobile-menu > li:has(> ul)')
	  .addClass("has-sub")
	  .each(function(){
		var $li = jQuery(this);
		if (!$li.children('.caret').length) {
		  $li.children('a').after('<span class="caret"><i class="fa icon fa icon-angle-down ion-chevron-down"></i></span>');
		}
	  });

	// Akkordeon scoped auf Top-Level (CHANGED)
	jQuery(document).on('click', 'ul.sk-mobile-menu > li > .caret', function(e) {
	  e.preventDefault();
	  var $li = jQuery(this).closest('li');
	  var checkElement = $li.children('ul.sub-menu').first();

	  // Nur unmittelbare Geschwister auf derselben Ebene schließen
	  $li.siblings('.has-sub').each(function(){
		var $sibSub = jQuery(this).children('ul.sub-menu:visible');
		if ($sibSub.length) {
		  jQuery(this).removeClass('menu-active');
		  $sibSub.slideUp('normal');
		}
	  });

	  jQuery('ul.sk-mobile-menu > li').removeClass('menu-active');
	  $li.addClass('menu-active');

	  if (checkElement.is(':visible')) {
		$li.removeClass('menu-active');
		checkElement.slideUp('fast');
	  } else {
		checkElement.slideDown('fast');
	  }

	  return false;
	});

  }

  menuMobile_parent();

  function menuMobile_parent() {
	// Caret nur einmal einfügen (CHANGED)
	jQuery('ul.collapsible > li:has(> .collapsible-body)')
	  .addClass("has-sub")
	  .each(function(){
		var $li = jQuery(this);
		if (!$li.children('.sk-caret').length) {
		  $li.children('a').after('<span class="sk-caret"><i class="fa icon fa icon-angle-down ion-chevron-down"></i></span>');
		}
	  });

	// Akkordeon scoped auf Collapsible-Geschwister (CHANGED)
	jQuery(document).on('click', 'ul.collapsible > li > .sk-caret', function(e) {
	  e.preventDefault();
	  var $li = jQuery(this).closest('li');
	  var checkElement = $li.children('.collapsible-body').first();

	  $li.siblings('.has-sub').each(function(){
		var $sibSub = jQuery(this).children('.collapsible-body:visible');
		if ($sibSub.length) {
		  jQuery(this).removeClass('sk-active');
		  $sibSub.slideUp('normal');
		}
	  });

	  jQuery('ul.collapsible > li').removeClass('sk-active');
	  $li.addClass('sk-active');

	  if (checkElement.is(':visible')) {
		$li.removeClass('sk-active');
		checkElement.slideUp('fast');
	  } else {
		checkElement.slideDown('fast');
	  }

	  return false;
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

  // Active-Trail bis zur 5. Ebene aufklappen (EXTENDED)
  setTimeout(function(){
	if( jQuery('.collapsible.collapsible-accordion > li.menu-item.active').length > 0 ){
	  jQuery('.collapsible.collapsible-accordion li.menu-item.menu-item-first-level.active .sk-caret:first').trigger('click');
	  jQuery('.collapsible.collapsible-accordion li.menu-item.menu-item-second-level.active .sk-caret:first, .collapsible.collapsible-accordion li.menu-item.menu-item-second-level.active .caret:first').trigger('click');
	  jQuery('.collapsible.collapsible-accordion li.menu-item.menu-item-third-level.active .sk-caret:first, .collapsible.collapsible-accordion li.menu-item.menu-item-third-level.active .caret:first').trigger('click');
	  // Neu: 4. & 5. Ebene (haben in deinem Markup i.d.R. .caret)
	  jQuery('.collapsible.collapsible-accordion li.menu-item.menu-item-fourth-level.active .caret:first').trigger('click');
	  jQuery('.collapsible.collapsible-accordion li.menu-item.menu-item-fifth-level.active .caret:first').trigger('click');

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

jQuery(window).on('scroll', function() { });