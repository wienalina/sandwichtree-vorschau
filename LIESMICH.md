# Sandwichtree – WooCommerce-Theme

Getestet mit WordPress 6.9 und WooCommerce 10.9, PHP 7.4 oder neuer.

## Installation

1. WordPress auf dem Hosting installieren und unter **Einstellungen → Allgemein** die Sprache **Deutsch (Österreich)** wählen.
2. **Plugins → Installieren:** „WooCommerce“ installieren und aktivieren. Den Einrichtungsassistenten kannst du überspringen.
3. **Design → Themes → Theme hochladen:** `sandwichtree.zip` auswählen und aktivieren.
4. **Design → Sandwichtree einrichten → Jetzt einrichten.**
   Legt in etwa einer Minute an: Kategorien, filterbare Eigenschaften, 16 Beispielprodukte mit je 3–5 Platzhalterbildern, alle Seiten mit Platzhaltertexten, Menüs, Shop-Filter und Versandzonen (AT/DE).
5. **Einstellungen → Permalinks** einmal speichern.
6. Neue WooCommerce-Installationen starten im Modus **„Demnächst verfügbar“**: Besucher:innen sehen den Shop noch nicht. Zum Livegang unter **WooCommerce → Einstellungen → Sichtbarkeit der Website** auf **Live** stellen.

## Empfohlene Plugins

| Zweck | Plugin |
|---|---|
| Zahlungen (Karte, Apple/Google Pay, Klarna, EPS) | WooCommerce Payments oder „WooCommerce Stripe Gateway“ |
| PayPal | „WooCommerce PayPal Payments“ |
| Rechtssicherer Checkout AT/DE, Rechnungen | „Germanized for WooCommerce“ |
| Cookie-Einwilligung | z. B. „Complianz“ oder „Real Cookie Banner“ |
| Instagram-Feed (optional) | „Smash Balloon Instagram Feed“, Shortcode im Customizer eintragen |
| Kontaktformular | „Contact Form 7“ oder „WPForms Lite“ |

## Was das Theme kann

- **Unikate:** Neue Produkte starten automatisch mit Lagerbestand 1 und „Einzeln verkaufen“. Nach dem Kauf ist das Teil ausverkauft und erscheint als „Verkauft“.
- **Vintage-Details:** Marke, Größe, Zustand, Material, Farbe und Epoche pflegst du im Produkt unter **Eigenschaften**. Danach kann man im Shop filtern. Maße, Größenhinweis und Gebrauchsspuren stehen im Reiter **Vintage-Details**.
- **Vinted-Erinnerung:** Häkchen „Auch auf Vinted inseriert“ setzen. Bei einer Bestellung steht dann in der Bestellnotiz und in der Bestell-Mail an den Shop, dass das Inserat auf Vinted entfernt werden muss. In der Produktliste zeigt die Spalte „Vinted“ alle betroffenen Teile.
- **Zustand** in fünf Stufen wie bei Vinted: Neu mit Etikett, Neu ohne Etikett, Sehr gut, Gut, Zufriedenstellend.
- **Größe** dezent unten rechts in der Ecke jedes Produktbilds, zweites Foto beim Überfahren mit der Maus, Kennzeichnungen „Neu“, „Reduziert“ und „Verkauft“.
- **Customizer** (Design → Customizer → Sandwichtree): Hinweisleiste, Startseiten-Texte und -Bilder, Fragen-Kacheln, Laufband, Instagram/Vinted-Links, Kontakt, Dauer der „Neu“-Kennzeichnung, verkaufte Teile zeigen oder ausblenden.
- **Design:** Weißer Hintergrund, helles Grau für Flächen, Akzente in Himmel-, Auto- und Meeresblau, Rot und Stein. Schrift: Inter Tight (Überschriften in Versalien) und IBM Plex Mono für kleine Bildunterschriften. Beide Schriften sind lokal eingebunden, also ohne Verbindung zu Google-Servern (DSGVO). Keine abgerundeten Ecken.
- **Logo:** Ist im Theme enthalten (`assets/img/`). Ein eigenes Logo im Customizer ersetzt die Wortmarke im Kopfbereich. Die Logo-Dateien wurden aus einem Bild freigestellt. Liegt das Logo als Vektordatei (SVG/AI/PDF) vor, lässt es sich noch schärfer einbinden.
- **Startseite:** Einstieg mit zwei Bildern, Kategorien als große Liste, Laufband, versetzte Reihe „Neu eingetroffen“ und Fragen-Kacheln. Texte und Bilder sind im Customizer änderbar. Ohne eigene Bilder zeigt die Startseite automatisch die neuesten Produktfotos.
- **Über uns:** rundes Original-Logo groß auf beiger Fläche neben dem Text.

## Produkte anlegen: Tipps für Fotos

- Erstes Foto: Tragebild vor dem silbernen Wellblech. Weitere Fotos: flach liegend auf grauem Beton (vorne, hinten, Maße mit Zollstock, Etikett).
- Jeder konkrete Mangel bekommt ein eigenes Detailfoto und steht im Feld „Gebrauchsspuren“.
- Hochformat 3:4 (wie Handyfotos), mindestens 1086 × 1448 px, als JPG.
- Das erste Foto ist das Produktbild, die weiteren kommen in die Produktgalerie. Das zweite Foto erscheint beim Überfahren mit der Maus.
- Teile ohne Preis zeigen „Preis folgt“ und können noch nicht gekauft werden.

## Vor dem Livegang

- [ ] Beispielprodukte löschen: Design → Sandwichtree einrichten → **Beispielprodukte löschen**
- [ ] Impressum, Datenschutz, AGB und Widerrufsbelehrung durch geprüfte Texte ersetzen
- [ ] Platzhaltertexte (mit Kupferstrich markiert) auf allen Seiten ersetzen
- [ ] Zahlungsarten verbinden und eine Testbestellung machen
- [ ] Versandkosten unter WooCommerce → Einstellungen → Versand prüfen
- [ ] Steuern: Kleinunternehmerregelung bzw. Differenzbesteuerung mit Steuerberatung klären und unter WooCommerce → Einstellungen → MwSt. eintragen
- [ ] Shop auf **Live** stellen (WooCommerce → Einstellungen → Sichtbarkeit der Website)
- [ ] Startseiten-Bilder und Instagram/Vinted-Links im Customizer setzen
