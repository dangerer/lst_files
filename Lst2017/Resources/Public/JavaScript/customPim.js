jQuery(document).ready(function() {

  /* ---------------------------
	 Sidebar Toggle + Label
  ---------------------------- */
  jQuery(".button-sidebar-collapse").on('click', function(){
	jQuery("#slide-out").toggleClass('fixed');
	jQuery("#pim-main").toggleClass('main-open');
	jQuery(".navbar-sidebar-toggle").toggleClass('open');
  
	updateSidebarLabel(); // <<< hier ausgelagert
  });
  
  jQuery(".button-sidebar-close").on('click', function(){
	jQuery("#slide-out").removeClass('fixed');
	jQuery("#pim-main").removeClass('main-open');
	jQuery(".navbar-sidebar-toggle").removeClass('open');
  
	updateSidebarLabel(); // <<< auch hier
  });
  
  /* ---------------------------
	 Helpers
  ---------------------------- */
  function updateSidebarState() {
	if (jQuery(window).width() < 1020) {
	  jQuery("#slide-out").removeClass('fixed');
	  jQuery(".navbar-sidebar-toggle").removeClass('open');
	  jQuery("#pim-main").removeClass('main-open');
	} else {
	  jQuery("#slide-out").addClass('fixed');
	  jQuery(".navbar-sidebar-toggle").addClass('open');
	  jQuery("#pim-main").addClass('main-open');
	}
	updateSidebarLabel(); // <<< wichtig: Label immer mitziehen
  }
  
  function updateSidebarLabel() {
	var $btn = jQuery(".button-sidebar-collapse");
	var $text = $btn.find(".toggle-text");
	if (!$text.length) return;
  
	if (jQuery(".navbar-sidebar-toggle").hasClass("open")) {
	  $text.text($btn.data("label-open"));
	} else {
	  $text.text($btn.data("label-close"));
	}
  }

  function ensureTopCarets() {
	jQuery('ul.sk-mobile-menu > li:has(> ul)')
	  .addClass("has-sub")
	  .each(function(){
		var $li = jQuery(this);
		if (!$li.children('.caret').length) {
		  $li.children('a').after('<span class="caret"><i class="fa icon fa icon-angle-down ion-chevron-down"></i></span>');
		}
	  });
  }

  function ensureSubCarets() {
	jQuery('ul.sk-mobile-menu ul.sub-menu > li:has(> ul)')
	  .addClass("has-sub")
	  .each(function(){
		var $li = jQuery(this);
		if (!$li.children('.caret').length) {
		  $li.children('a').after('<span class="caret"><i class="fa icon fa icon-angle-down ion-chevron-down"></i></span>');
		}
	  });
  }

  /* ----------------------------------------
	 Menü-Logik: Top-Level (sk-mobile-menu)
  ----------------------------------------- */
  function menuMobile() {
	ensureTopCarets();

	// zuerst alte Handler lösen (Namespace!)
	jQuery(document).off('click.topmenu');

	// delegierter Click NUR für Top-Level Carets
	jQuery(document).on('click.topmenu', 'ul.sk-mobile-menu > li > .caret', function(e) {
	  e.preventDefault();
	  var $li = jQuery(this).closest('li');
	  var $submenu = $li.children('ul.sub-menu').first();
	  if (!$submenu.length) return true;

	  // nur Geschwister derselben Ebene schließen
	  $li.siblings('.has-sub').each(function(){
		var $sib = jQuery(this);
		$sib.removeClass('menu-active');
		$sib.children('ul.sub-menu:visible').slideUp('normal');
	  });

	  // eigenes Submenu togglen
	  $li.toggleClass('menu-active');
	  $submenu.stop(true, true).slideToggle('fast');

	  return false;
	});
  }

  /* ----------------------------------------
	 Menü-Logik: alle Sub-Level
  ----------------------------------------- */
  function menuMobile_child() {
	ensureSubCarets();

	jQuery(document).off('click.submenus');

	// delegierter Click für Carets in ALLEN Subebenen
	jQuery(document).on('click.submenus', 'ul.sk-mobile-menu ul.sub-menu li > .caret', function(e) {
	  e.preventDefault();

	  var $li = jQuery(this).closest('li');
	  var $submenu = $li.children('ul.sub-menu').first();
	  if (!$submenu.length) return true;

	  // nur Geschwister derselben Ebene schließen
	  $li.siblings('.has-sub').each(function(){
		var $sib = jQuery(this);
		$sib.removeClass('menu-active');
		$sib.children('ul.sub-menu:visible').slideUp('normal');
	  });

	  // eigenes Submenu togglen
	  $li.toggleClass('menu-active');
	  $submenu.stop(true, true).slideToggle('fast');

	  return false;
	});
  }

  /* ----------------------------------------
	 Collapsible Parent (Kategorieblöcke)
  ----------------------------------------- */
  function menuMobile_parent() {
	// Caret nur einmal einfügen
	jQuery('ul.collapsible > li:has(> .collapsible-body)')
	  .addClass("has-sub")
	  .each(function(){
		var $li = jQuery(this);
		if (!$li.children('.sk-caret').length) {
		  $li.children('a').after('<span class="sk-caret"><i class="fa icon fa icon-angle-down ion-chevron-down"></i></span>');
		}
	  });

	jQuery(document).off('click.parentmenu');

	jQuery(document).on('click.parentmenu', 'ul.collapsible > li > .sk-caret', function(e) {
	  e.preventDefault();

	  var $li = jQuery(this).closest('li');
	  var $body = $li.children('.collapsible-body').first();
	  if (!$body.length) return true;

	  // nur Geschwister derselben Ebene schließen
	  $li.siblings('.has-sub').each(function(){
		var $sib = jQuery(this);
		$sib.removeClass('sk-active');
		$sib.children('.collapsible-body:visible').slideUp('normal');
	  });

	  // eigenes Body togglen
	  $li.toggleClass('sk-active');
	  $body.stop(true, true).slideToggle('fast');

	  return false;
	});

	// Klick auf Overlay schließt Sidebar (synchron)
	jQuery('.sidenav-bg').off('click').on('click', function() {
	  jQuery("#slide-out").removeClass('fixed');
	  jQuery("#pim-main").removeClass('main-open');
	  jQuery(".navbar-sidebar-toggle").removeClass('open');
	
	  updateSidebarLabel(); // <<< Label mitziehen
	});
  }

  /* ----------------------------------------
	 Active-Trail korrekt öffnen (ohne Clicks)
  ----------------------------------------- */
  function openActiveTrail() {
	// 1) Collapsible-Tree (Elternblöcke)
	var $activeItems = jQuery('.collapsible.collapsible-accordion li.menu-item.active');

	if ($activeItems.length) {
	  $activeItems.each(function(){
		// Alle Vorfahren-LIs öffnen und ihre Bodies/Submenüs sichtbar machen
		jQuery(this)
		  .parents('li')
		  .each(function(){
			var $li = jQuery(this);

			// Collapsible-Ebene
			if ($li.children('.collapsible-body').length) {
			  $li.addClass('sk-active');
			  $li.children('.collapsible-body').show(); // sofort sichtbar, kein Slide beim Initialisieren
			}

			// Menü-Ebene
			if ($li.children('ul.sub-menu').length) {
			  $li.addClass('menu-active');
			  $li.children('ul.sub-menu').show();
			}
		  });
	  });

	  // 2) Für alle direkten Elternketten im sk-mobile-menu ebenso
	  var $deepActive = jQuery('ul.sk-mobile-menu li.menu-item.active');
	  $deepActive.parents('li.has-sub').each(function(){
		var $li = jQuery(this);
		$li.addClass('menu-active');
		$li.children('ul.sub-menu').show();
	  });
	}
  }

  /* ----------------------------------------
	 Initialisieren
  ----------------------------------------- */
  menuMobile_child();
  menuMobile();
  menuMobile_parent();

  updateSidebarState();
  openActiveTrail();

  /* ----------------------------------------
	 Load / Resize
  ----------------------------------------- */
  jQuery(window).on('load', function() {
	updateSidebarState();
	openActiveTrail(); // sicherstellen, dass nach Render alles offen ist
  });

  jQuery(window).on('resize', function() {
	updateSidebarState();
	// beim Resize NICHT neu schließen/öffnen (kein Trigger),
	// nur den State der Sidebar anpassen
  });

  jQuery(window).on('scroll', function() { });

});

