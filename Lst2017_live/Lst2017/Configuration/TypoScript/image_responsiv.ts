
# remove Table from Image with caption
tt_content.image.20 {
	rendering {
		singleCaption {
	 			# Just one image with a caption
	 			fallbackRendering < tt_content.image.20.rendering.singleNoCaption.fallbackRendering.10
	 			singleStdWrap {
	 				wrap = <div class="csc-textpic-image###CLASSES###">|<p>###CAPTION###</p></div>
	 				wrap {
	 					override = <figure class="csc-textpic-image###CLASSES###">|###CAPTION###</figure>
	 					override {
	 						if {
	 							value = html5
	 							equals.data = TSFE:config|config|doctype
	 						}
	 					}
	 				}
	 			}
	 			caption {
	 				required = 1
	 				wrap = <caption class="csc-textpic-caption###CLASSES###"> | </caption>
	 				wrap {
	 					override = <figcaption class="csc-textpic-caption###CLASSES###"> | </figcaption>
	 					override {
	 						if {
	 							value = html5
	 							equals.data = TSFE:config|config|doctype
	 						}
	 					}
	 				}
	 			}
	 		}
	}	
}