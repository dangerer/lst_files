tt_content.swfobject = COA
tt_content.swfobject {
	5 = TEXT
	5.value = dfasdf
 	10 = < lib.stdheader
 
 	20 = SWFOBJECT
 	20 {
 		wrap = <div class="responsiveContainer">|</div>
 		file =
 		width =
 		height =
 
 		flexParams.field = pi_flexform
 
 		alternativeContent.field = bodytext
 
 		layout = ###SWFOBJECT###
 
 		video {
 			player = {$styles.content.media.videoPlayer}
 
 			defaultWidth  = {$styles.content.media.defaultVideoWidth}
 			defaultHeight  = {$styles.content.media.defaultVideoHeight}
 
 			default {
 				params.quality = high
 				params.menu = false
 				params.allowScriptAccess = sameDomain
 				params.allowFullScreen = true
 			}
 			mapping {
 
 			}
 		}
 
 		audio {
 			player = {$styles.content.media.audioPlayer}
 
 			defaultWidth = {$styles.content.media.defaultAudioWidth}
 			defaultHeight = {$styles.content.media.defaultAudioHeight}
 
 			default {
 				params.quality = high
 				params.allowScriptAccess = sameDomain
 				params.menu = false
 			}
 			mapping {
 				flashvars.file = soundFile
 			}
 		}
 
 	}
 	20.stdWrap {
 		editIcons = tt_content: multimedia, imagewidth, imageheight, pi_flexform, bodytext
 		editIcons.iconTitle.data = LLL:EXT:css_styled_content/pi1/locallang.xml:eIcon.multimedia
 
 		prefixComment = 2 | SWFobject element:
 	}
 }