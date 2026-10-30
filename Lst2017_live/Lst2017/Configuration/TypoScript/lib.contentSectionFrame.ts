
tt_content.textpic.20.layout = CASE
tt_content.textpic.20.layout {
	key.field = section_frame

	250 = TEXT
	250.value = ###IMAGES######TEXT###
	250.stdWrap.typolink.wrap = |
	250.stdWrap.typolink.parameter.field = header_link
	250.stdWrap.typolink.ATagBeforeWrap = 1

	255 = TEXT
	255.value = <div class="ifper-inner cf">###IMAGES######TEXT###</div>

	default < tt_content.image.20.layout
}

# disable image wrapping
tt_content.textpic.20.imageStdWrap.dataWrap.cObject = CASE
tt_content.textpic.20.imageStdWrap.dataWrap.cObject {
	key.field = section_frame

	250 = TEXT
	250.value = |

	255 = TEXT
	255.value = |

	default < tt_content.textpic.20.allStdWrap.dataWrap
}

# disable header
tt_content.textpic.10.10 = CASE
tt_content.textpic.10.10 {
	key.field = section_frame

	250 = TEXT
	250.value = 
	255 = TEXT
	255.value = 
	default < lib.stdheader
}

tt_content.textpic.20.text.wrap.cObject = CASE
tt_content.textpic.20.text.wrap.cObject {
	key.field = section_frame

	# text wrapping with included <h3>header</h3>
	250 = COA
	250.10 = TEXT
	250.10.value = <div class="homeinfbx-text">
	250.20 = TEXT
	250.20.field = header
	250.20.wrap = <h3>|</h3>
	250.30 = TEXT
	250.30.value = |</div>

	255 < .250

	default < tt_content.textpic.20.text.wrap
}

#tt_content.image.20.rendering.infoboxTeaser < tt_content.image.20.rendering.singleNoCaption
tt_content.image.20.rendering.infoboxTeaser {
	imgTagStdWrap.wrap = <div class="homeinfbx-img">|</div>
}

tt_content.image.20.rendering.infoboxKontakt {
	imgTagStdWrap.wrap = <div class="ifper-img">|</div>
}

// select render method by section_frame
tt_content.image.20.renderMethod.cObject = CASE
tt_content.image.20.renderMethod.cObject {
	key.field = section_frame
	250 = TEXT
	250.value = infoboxTeaser
	255 = TEXT
	255.value = infoboxKontakt
	default = TEXT
	default.value = singleNoCaption
}

