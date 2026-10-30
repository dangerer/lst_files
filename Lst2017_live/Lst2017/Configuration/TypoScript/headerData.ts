##benötigte Konstanten, bitte individuelle Werte eingeben:
 
# url = TEXT
# url.value = www.pga.at

lib.url = COA
lib.url{
  10 = TEXT
  10{
    data = getIndpEnv:TYPO3_REQUEST_URL
  }
}
 
pageName = TEXT
pageName.value = exacloud
 
## Image Lib - baut Bildpfad aus Bild/Seiteneigenschaften auf
lib.socialimage = COA
lib.socialimage {
	10 < url
	15 = TEXT
	15.value = /
	20 = IMG_RESOURCE
	20.file {
		import.data = levelmedia:-1, slide
		treatIdAsReference = 1
		import.listNum = 0
		originalFile.name =
	}
}
 
## Erzeugt Zeilenumbruch im Quelltext
linebreak = TEXT
linebreak.value(
 
 
)

## Header Data lib ###############################################

lib.headerDataSocial = COA
lib.headerDataSocial {
## Google Autor markup


4 = TEXT
4.< lib.url
4.wrap = <link href=" | " rel="canonical">

5 < linebreak

10 = TEXT
10.value = https://plus.google.com/[Google+_Profile]/posts
10.wrap = <link rel="author" href=" | "/>

11 < linebreak

12 = TEXT
12.value = https://plus.google.com/[Google+_Page_Profile]
12.wrap = <link rel="publisher" href=" | "/>

13 < linebreak
14 < linebreak

## Google+ Markup
20 = TEXT
20.data = field:title
20.wrap = <meta itemprop="name" content=" |">

21 < linebreak

22 = TEXT
22.data = page:description
22.wrap = <meta itemprop="description" content=" | ">

23 < linebreak

24 = TEXT
24.< lib.socialimage
24.wrap = <meta itemprop="image" content=" | ">

25 < linebreak
26 < linebreak


## Facebook / Open Graph markup
30 = TEXT
30.data = field:title
30.wrap = <meta property="og:title" content="|" />



31 < linebreak

32 = TEXT
32.value = <meta property="og:type" content="article" />

33 < linebreak

34 = TEXT
34.< lib.url
34.wrap = <meta property="og:url" content=" | " />

35 < linebreak

36 < lib.socialimage
36.wrap = <meta property="og:image" content=' | ' />

37 < linebreak

38 = TEXT
38.data = page:description
38.wrap = <meta property="og:description" content=" | " />

39 < linebreak

40 < pageName
40.wrap = <meta property="og:site_name" content=" | " />

41 < linebreak

42 = TEXT
42.data = page:crdate
42.strftime =%Y-%m-%dT%H:%m:%S+01:00  
42.wrap = <meta property="article:published_time" content=" | " />

43 < linebreak

44 = TEXT
44.data = page:SYS_LASTCHANGED
44.strftime =%Y-%m-%dT%H:%m:%S+01:00 
44.wrap = <meta property="article:modified_time" content=" | " />

45 < linebreak
46 < linebreak



## Twitter markup - Twitter has fallback to og tags
50 = TEXT
50.wrap = <meta name="twitter:card" content="summary">

51 < linebreak

52 = TEXT
52.data = field:title
52.wrap = <meta name="twitter:title" content=" | " >

51 < linebreak

}

