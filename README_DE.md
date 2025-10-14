# Turbo-Suchvorschläge

Verbessere dein Shopware-Sucherlebnis, indem du Kategorien und Landing Pages prominent in den Suchvorschlägen anzeigst und deinen Kunden so die Suche nach dem Gewünschten erleichterst.

## Was macht dieses Plugin?

Wenn Kunden die Suchfunktion deines Shops nutzen, sehen sie normalerweise nur Produktvorschläge. Mit **Turbo-Suchvorschläge** kannst du auch relevante **Kategorien** und **Landing Pages** prominent oben im Such-Dropdown anzeigen.

### Beispiel

Dein Kunde tippt "hem" in die Suchleiste:
- **Ohne dieses Plugin**: Zeigt nur Produktergebnisse an
- **Mit diesem Plugin**: Zeigt deine Kategorie "Hemden" prominent oben an, gefolgt von Produktvorschlägen

Dies hilft Kunden, direkt zur richtigen Kategorie oder Landing Page zu navigieren, verbessert ihr Einkaufserlebnis und erhöht die Conversion-Rate.

## Funktionen

- Kategorien in Suchvorschlägen anzeigen
- Landing Pages (CMS-Seiten) in Suchvorschlägen anzeigen
- Vollständig übersetzbare Titel und Teaser-Texte
- Intelligente Suchlogik (exakte Übereinstimmung und Präfix-Übereinstimmung)
- Prioritätsbasierte Sortierung (wichtigste Ergebnisse zuerst anzeigen)
- Verkaufskanal-spezifische Konfiguration
- Sprachspezifische Suchbegriffe
- Aktiv/Inaktiv-Umschalter für einzelne Suchbegriffe
- Benutzerfreundliche Administrationsoberfläche

## Installation

### Über den Shopware Extension Store

1. Öffne deine Shopware-Administration
2. Navigiere zu **Erweiterungen → Meine Erweiterungen**
3. Suche nach "Turbo Search Suggests"
4. Klicke auf **Installieren** und dann auf **Aktivieren**

### Über Shopware-Administration (Manueller Upload)

1. Lade die Plugin-ZIP-Datei herunter
2. Öffne deine Shopware-Administration
3. Navigiere zu **Erweiterungen → Meine Erweiterungen**
4. Klicke auf **Erweiterung hochladen**
5. Wähle die heruntergeladene ZIP-Datei aus
6. Klicke auf **Installieren** und dann auf **Aktivieren**

### Via Composer

```bash
composer require bepo/turbo-suggest
bin/console plugin:refresh
bin/console plugin:install --activate BepoTurboSuggest
```

## Verwendung

### 1. Plugin aufrufen

In deiner Shopware-Administration navigierst du zu:
**Marketing → Turbo-Suchvorschläge**

### 2. Such-Ziel erstellen

Klicke auf **"Neues Ziel hinzufügen"** und konfiguriere:

- **Titel** (optional): Überschreibt den in den Vorschlägen angezeigten Kategorie- oder Landing Page-Namen
- **Teaser-Text** (optional): Fügt eine kurze Beschreibung unter dem Titel hinzu
- **Kategorie ODER Landing Page**: Wähle entweder eine Kategorie oder eine Landing Page (nicht beides)
- **Verkaufskanal**: Wähle aus, für welchen Verkaufskanal dieses Ziel gilt
- **Priorität**: Höhere Zahlen erscheinen zuerst in den Suchergebnissen (z.B. 100 vor 50)

### 3. Suchbegriffe hinzufügen

Nach dem Speichern deines Ziels wechselst du zum Tab **"Suchbegriffe"**:

1. Klicke auf **"Suchbegriff hinzufügen"**
2. Gib den Suchbegriff ein (z.B. "hemden", "herren", "socken")
3. Wähle die Sprache
4. Schalte **"Aktiv"** ein/aus
5. Speichern

**Tipp**: Verwende vollständige Wörter als Suchbegriffe (z.B. "hemden", "t-shirts") - das Plugin findet automatisch Teilübereinstimmungen wie "hem", "hemd" oder "hemde". Füge nur mehrere Begriffe für unterschiedliche Wortvariationen hinzu.

### 4. Sprachunterstützung

Sowohl das Ziel (Titel und Teaser-Text) als auch die Suchbegriffe unterstützen mehrere Sprachen:

- Verwende den **Sprachumschalter** in der oberen Leiste, um Übersetzungen hinzuzufügen
- Erstelle Suchbegriffe für jede Sprache, die dein Shop unterstützt
- Übersetzungen werden automatisch basierend auf der vom Kunden gewählten Sprache angezeigt

## Wie die Suchlogik funktioniert

Das Plugin verwendet ein intelligentes zweistufiges Übereinstimmungssystem:

1. **Exakte Übereinstimmung (Priorität 1)**: Wenn ein Suchbegriff exakt mit der Kundeneingabe übereinstimmt, wird dieses Ziel angezeigt
2. **Präfix-Übereinstimmung (Priorität 2)**: Wenn keine exakte Übereinstimmung existiert, sucht das Plugin nach Begriffen, die mit der Kundeneingabe beginnen

Wenn mehrere Ziele übereinstimmen:
- Ziele mit dem **kürzesten übereinstimmenden Begriff** werden bevorzugt
- Ergebnisse werden nach **Priorität** sortiert (höchste zuerst)

## Beispiele

### Beispiel 1: Kategorievorschlag

**Ziel-Konfiguration:**
- Kategorie: "Herrenbekleidung"
- Priorität: 100
- Suchbegriffe: "herren", "männer", "men" (Englisch)

**Ergebnis**: Wenn Kunden "herr" eingeben, sehen sie "Herrenbekleidung" oben in den Vorschlägen.

### Beispiel 2: Landing Page Promotion

**Ziel-Konfiguration:**
- Landing Page: "Sommerschlussverkauf 2025"
- Titel: "☀️ Sommerschlussverkauf"
- Teaser-Text: "Bis zu 50% Rabatt auf ausgewählte Artikel"
- Priorität: 200
- Suchbegriffe: "sommer", "sale", "ssv"

**Ergebnis**: Wenn Kunden "som" eingeben, sehen sie deine prominente Sommerschlussverkauf-Landing Page mit dem benutzerdefinierten Titel und Teaser.

### Beispiel 3: Mehrere Kategorien

**Ziel 1:**
- Kategorie: "Damenhemden"
- Priorität: 80
- Suchbegriffe: "hemden"

**Ziel 2:**
- Kategorie: "Herrenhemden"
- Priorität: 70
- Suchbegriffe: "hemden"

**Ergebnis**: Wenn Kunden "hemden" eingeben, erscheinen beide Kategorien, mit "Damenhemden" zuerst (höhere Priorität).

## Best Practices

1. **Verwende aussagekräftige Prioritäten**: Reserviere hohe Zahlen (100+) für deine wichtigsten Kategorien oder Aktionen
2. **Verwende vollständige Wörter als Suchbegriffe**: Das Plugin findet Präfixe automatisch, sodass "hemden" mit "hem", "hemd", "hemde" usw. übereinstimmt. Füge nur separate Begriffe für unterschiedliche Wortvariationen hinzu (z.B. "hemden" und "t-shirts")
3. **Halte Teaser-Texte kurz**: Ein Satz ist normalerweise ausreichend
4. **Teste in mehreren Sprachen**: Stelle sicher, dass jede Sprache entsprechende Suchbegriffe hat
5. **Nutze den Aktiv-Umschalter**: Deaktiviere Suchbegriffe für saisonale Kampagnen vorübergehend, ohne sie zu löschen

## Voraussetzungen

- Shopware 6.6.0 oder höher

## Support

Für Fragen, Feature-Anfragen oder Fehlerberichte kontaktiere bitte:
- Website: https://www.poensgen.de
- Support: https://www.poensgen.de/support

## Lizenz

Dieses Plugin ist unter der MIT-Lizenz lizenziert. Details finden Sie in der LICENSE-Datei.

## Credits

Entwickelt von Benny Poensgen
Copyright (c) 2025 B. Poensgen IT-Dienstleistungen
