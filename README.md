# Sandwich Tree – Design-Vorschau

Statische Vorschau des Shop-Designs (Startseite, Shop, Kategorien, die Shearling-Fliegerjacke als erstes echtes Produkt, 16 Beispielprodukte, Infoseiten).
Warenkorb, Suche, Filter und Kundenkonto sind deaktiviert und zeigen einen Hinweis. Kaufen funktioniert erst im fertigen WordPress-Shop.

## Auf GitHub Pages veröffentlichen

1. Auf GitHub ein **neues, öffentliches** Repository anlegen, z. B. `sandwichtree-vorschau`.
2. **Upload 1:** Auf **uploading an existing file** klicken. Im entpackten Ordner alles **außer** `wp-content` markieren und hineinziehen, also die Ordner `product`, `shop`, `product-category`, `wp-includes` … und `index.html`. Unten auf **Commit changes**.
3. **Upload 2:** **Add file → Upload files** und den Ordner `wp-content` als Ganzes hineinziehen. **Commit changes**.
   (GitHub nimmt höchstens 100 Dateien pro Upload an, deshalb zwei Durchgänge.)
4. **Settings → Pages:** bei *Source* **Deploy from a branch** wählen, Branch **main**, Ordner **/ (root)**, **Save**.
5. Nach 1–2 Minuten ist die Seite erreichbar unter `https://DEIN-NAME.github.io/sandwichtree-vorschau/`.

Kontrolle: Auf der Startseite des Repositorys müssen Ordner wie `product`, `shop` und `wp-content` zu sehen sein.
Liegen dort lauter einzelne Dateien, wurden die Ordner beim Hochladen aufgelöst. Dann die Ordner selbst hineinziehen, nicht ihren Inhalt.

Die Seite ist für Suchmaschinen gesperrt (noindex), damit die Platzhalter nicht bei Google landen.
