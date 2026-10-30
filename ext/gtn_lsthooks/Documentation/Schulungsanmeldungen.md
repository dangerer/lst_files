# Backend-Modul Schulungsanmeldungen

Für TYPO3 12.4.28. Das Modul erscheint unter **Web → Schulungsanmeldungen**.

## Installation

Die ergänzten Dateien und die geänderte `Configuration/Services.yaml` auf den Server übernehmen. Anschließend den TYPO3-Systemcache einschließlich Dependency-Injection-Container leeren (z. B. `vendor/bin/typo3 cache:flush`) und das Backend neu laden. Keine Datenbankänderung erforderlich.

Das Modul ist zunächst ausschließlich für Backend-Administratoren im Live-Workspace verfügbar. Es zeigt eine websiteweite Übersicht, unabhängig von der ausgewählten Seite. Für einen späteren Redakteurszugriff muss auch der gewünschte Datenumfang/Berechtigungsumfang festgelegt werden; nicht einfach nur `access` ändern.

## Inhalt und Zählweise

- Nur gespeicherte Powermail-Anmeldungen der Formular-UIDs 20 und 21.
- Eine Zeile pro Kombination aus `kursuid` und gespeichertem `termin` (äußere Leerzeichen werden entfernt).
- Kurstitel und Course-ID stammen aus den zuletzt gespeicherten, nicht leeren Angaben dieser Gruppe.
- Teilnehmer = gesetzte Selbstanmeldung (`selbstfuerkursanmelden`) plus nicht leere Einträge im JSON-Feld `vorname_01`, entsprechend der bestehenden Zählweise in Utils.php.
- Frühere Termine sind ausdrücklich enthalten. Die Übersicht zeigt historische Teilnehmerzahlen, nicht ausschließlich die derzeit für die Buchungssperre gezählten Teilnehmer.
- Fehlende Termine erscheinen als „Ohne Terminangabe“. Anmeldungen ohne Kurs-UID werden nicht dargestellt.
- Kurse ohne gespeicherte Anmeldung werden nicht aufgeführt.
- Unterschiedlich geschriebene Termintexte ergeben getrennte Zeilen. DE/EN werden zusammengezählt, sofern Kurs-UID und Termintext übereinstimmen.
- Die Standard-Datensatzbeschränkungen des TYPO3 QueryBuilders bleiben aktiv. Gelöschte Datensätze werden nicht absichtlich wieder eingeblendet.
- Alle Inhalte werden durch Fluid escaped. Es werden keine Namen oder E-Mail-Adressen angezeigt und keine Daten verändert.

Die frühere Änderung an `Classes/Utils/Utils.php` bleibt unverändert. Das neue Modul erweitert auch nicht nachträglich die Formularauswahl der bestehenden Frontend-Zählung.

## Prüfung

Standalone-Test der Gruppierung und Teilnehmerzählung (PHP 8.1+):

```sh
php Tests/Standalone/CourseRegistrationSummaryTest.php
```

Vor dem produktiven Einsatz im TYPO3-Testsystem prüfen:

1. Modul erscheint nach Cache-Leerung unter Web für Administratoren; Nicht-Administratoren haben keinen Zugriff, auch nicht per direkter URL.
2. Eine Anmeldung mit Selbstanmeldung und zwei zusätzlichen Personen ergibt drei Teilnehmer.
3. Eine weitere Anmeldung für dieselbe Kurs-UID und denselben Termin erhöht dieselbe Zeile.
4. Derselbe Kurs mit einem anderen Termin erscheint separat; vergangene Termine bleiben sichtbar.
5. Je eine Anmeldung aus Formular 20 und 21 prüfen, einschließlich der tatsächlich gespeicherten Feldmarker.
6. Gelöschte Anmeldungen, fehlende Termine und eine leere Liste prüfen.

Lokal war kein PHP-/TYPO3-Laufzeitsystem verfügbar. Deshalb sind PHP-Testausführung, Datenbankabfrage und Darstellung im echten Backend noch auf dem Server zu prüfen.

Die Modulregistrierung und Darstellung richten sich nach der [TYPO3-12.4-Dokumentation](https://docs.typo3.org/m/typo3/reference-coreapi/12.4/en-us/ExtensionArchitecture/HowTo/BackendModule/CreateModule.html).
