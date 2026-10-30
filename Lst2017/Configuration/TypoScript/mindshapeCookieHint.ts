
plugin.tx_mindshapecookiehint {
  settings {
        #choose style between "dark" or "light" (optional, default: dark)
       # style = light
        #position on "top" or "bottom" of your website (optional, default: bottom)
        position = bottom
        #page-id for more details about your cookies (optional, default: - )
       readmore = 11
       #append the cookie to the bottom of your page so it doesn't overlap footer-content
       appendToBottom = 1
  }

  _LOCAL_LANG.de {
        hint.learnMore = Weitere Informationen.
        hint.dismiss = OK
        hint.message = Diese Website nutzt Cookies, um bestmögliche Funktionalität bieten zu können. Durch Nutzung dieser Seite sind Sie mit der Verwendung von Cookies einverstanden.
  }

  _LOCAL_LANG.default {
        hint.learnMore = More information.
        hint.dismiss = Got it
        hint.message = This website uses cookies to offer the full range of features on our website. By using this site you agree to the use of cookies. 
  }
}

#plugin.tx_mindshapecookiehint.view.templateRootPath = fileadmin/Lst2017/Resources/Private/mindshape_cookie_hint/Templates/
plugin.tx_mindshapecookiehint.view.templateRootPaths.1 = fileadmin/Lst2017/Resources/Private/mindshape_cookie_hint/Templates/

# plugin.tx_mindshapecookiehint.view.layoutRootPath = fileadmin/Lst2017/Resources/Private/mindshape_cookie_hint/Templates/
# plugin.tx_mindshapecookiehint.view.partialRootPath = fileadmin/Lst2017/Resources/Private/mindshape_cookie_hint/Templates/