page.headerData.22 = TEXT
page.headerData.22.value (
    <style type="text/css">
      .navbar-barbara {
         padding-top: 20px;
      }
      .nav-service, #navbarBarbaraMain {
         display: none !important;
      }
      #typo3-previewInfo, footer {
         display: none !important;
       }
      #typo3-previewInfo {
         color: #fff; !important;
       }
       
       #maincontent .container-fluid h1 {
		    margin-top: 1.4rem;
	    	font-size: 2rem;
	    	margin-bottom: 25px;
	    }
       
       #maincontent .container-fluid h1.DokuHeader {
	    	margin-bottom: 2rem;
	    	margin-top: 3rem;
	    	font-size: 1.5rem;
	    }
	    
	    .maincol ul li {
		   padding-bottom: 8px;
	    }
       
       #maincontent .container-fluid h1.DokuHeader a {
	    	
	    	font-size: 1.7rem;
	    }
	    
	    #maincontent .container-fluid h2 {
		 
		    font-size: 1.5rem;
	    }
		    
	    #maincontent .container-fluid h2 {
		   font-size: 1.8rem;
		   }
	    .sidebar-maincol {
		    background-color: #faf9f6;
		    padding: 40px 30px;
	    }
	    .frame-type-menu_pages {
		    margin-bottom: 40px;
	    }
	    
	    .container-fluid .maincol h2 {
		    margin-top: 2rem;
	    }

    </style>
)

page.10.variables.breadcrumb = TEXT
page.10.variables.breadcrumb.value = <div class="container-fluid"><h1 class="DokuHeader"><a href="https://www.laborstrauss.com/index.php?id=246">LST Typo3 Dokumentation</a></h1></div>

page {
	10 {
		variables {
			content_1 < styles.content.get
			content_1 {
				select.where = colPos = 0
				select.pidInList=246
			}
			barbara_openinghours >
			navigation_main >
			navigation_service >
			kesearchbox >
			navigation_submenuLevel2 >
		}	
}