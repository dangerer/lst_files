


# Default Header umändern:
# Im Konstanteneditor -> unter default Header Type - Wert umändern
content.defaultHeaderType = 1

# den default Wrap löschen und einen neuen h2 Wrap hinzufügen
# Hier eine Neue Klasse für die Überschriften definieren
lib.parseFunc_RTE.nonTypoTagStdWrap.encapsLines.addAttributes.P >
lib.stdheader.10.2.dataWrap >
lib.stdheader.10.2.wrap = <h2>|</h2>

# um den Default Wrap der Überschriften zu entfernen 
lib.stdheader.stdWrap.dataWrap >