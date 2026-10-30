[siteLanguage("languageId") == 1]
    plugin.tx_cookieconsent.settings.url = https://lst.at/index.php?id=11&L=1&fL=1
    plugin.tx_cookieconsent.settings.target = https://lst.at/index.php?id=11&L=1&fL=1
[end]

plugin.tx_cookieconsent.settings {

    url = https://lst.at/index.php?id=11#c1798
    target = https://lst.at/index.php?id=11#c1798
    theme = block
    position = bottom-right
    autoOpen = true
    type = opt-in
    reloadOnRevoke = true
    layout = dpextend
    # show Iframe overlay
    overlay {
        # Enable Iframe overlay
        notice = true

        box {
            # Overlay: Background
            background = rgba(0,0,0,.8)
            background = rgba(75,87,98,.8)
            background = rgba(61,72,83,.85)
            # Overlay: Text
            text = #fff
        }
        button {
            # Overlay Button: Background
            background = #da251d
            # Overlay Button: Text
            text = #fff
        }
    }

    # Cookiehint Style
    palette {
        popup {
            # Bar: Background color
            background = rgba(0,0,0,.8)
            background = rgba(75,87,98,.8)
            background = rgba(61,72,83,.85)
            # Bar: text color
            text = #fff
        }
        button {
            # Button: Background color
            background = #da251d
            # Button: text color
            text = #fff
        }
    }
}