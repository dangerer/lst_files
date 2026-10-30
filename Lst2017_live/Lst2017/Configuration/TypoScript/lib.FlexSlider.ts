lib.FlexSlider = COA
lib.FlexSlider {

	1 = LOAD_REGISTER
	1.counterItemSlider = 0

	2 = LOAD_REGISTER
	2.firstItemSliderClass = active

	10 = FILES
	10 {
		references {
			table = tt_content
			uid.data = field:uid
			fieldName = assets
		}
		stdWrap.dataWrap = <div class="flexslider"><ul class="slides">|

	}

	# 15 = LOAD_REGISTER
	# 15.firstItemSliderClass = active

	# Carousel items
	20 = FILES
	20 {
		references {
			table = tt_content
			uid.data = field:uid
			fieldName = assets
		}
		renderObj = COA
		renderObj {
			10 = TEXT
			10.value = <li class="item {register:firstItemSliderClass} itemno{register:counterItemSlider}">
			10.insertData = 1

			20 = IMAGE
			20 {
				file {
					import.data = file:current:originalUid // file:current:uid
					# height = 300c
					# width = 886
				}
			}

			30 = TEXT
			30 {
				value (
<div class="flex-caption">
	<div class="container-fluid">
		<h4>{file:current:title}</h4>
		<p>{file:current:description}</p>
		<p><a href="{file:current:link}" class="btn btn-primary btn-lst">{file:current:alternative}</a></p>
	</div>
</div>
				)
				if.isTrue.cObject = TEXT
				if.isTrue.cObject {
					# sobald nur ein Feld Inhalt hat, wird dem isTrue eine Zeichenkette zurückgegeben
					value = {file:current:title}{file:current:description}
					insertData = 1
				}
				insertData = 1
			}
			40 = TEXT
			40.value = </li>
			50 = RESTORE_REGISTER
			
			# Zähler um 1 erhöhen
			55  = LOAD_REGISTER
			55 {
				tempN {
					data = register:counterItemSlider
					wrap = |+1
				}
				counterItemSlider.data = register:tempN
				counterItemSlider.prioriCalc = 1
			}


			# Register um einen Eintrag erhöhen, der in der Schleife wieder gelöscht wird (Object 20)
			60  = LOAD_REGISTER
			60 {
				temp = 1
			}
			
		}
	}

	30 = TEXT
	30 {
		value (
	</ul></div>


		)
		insertData = 1
	}
	
}