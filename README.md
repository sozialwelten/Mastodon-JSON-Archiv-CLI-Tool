# Mastodon Archive Reader

Ein einfaches PHP CLI-Tool zum Durchsuchen und Anzeigen von Mastodon-Archiven.

## Features

- 📖 Liest große JSON-Archive (mehrere GB)
- 🔍 Volltextsuche in Beiträgen
- 🖼️ Zeigt angehängte Medien mit Alt-Texten
- 📄 Übersichtliche, formatierte Ausgabe
- ⚡ Pagination bei vielen Ergebnissen

## Voraussetzungen

- PHP 7.0 oder höher
- CLI-Zugriff

## Installation

```bash
git clone <dein-repo-url>
cd <repo-name>
```

## Verwendung

### Alle Beiträge anzeigen
```bash
php 00-json-archive.php outbox.json
```

### Nach Begriff suchen
```bash
php 00-json-archive.php outbox.json "Suchbegriff"
```

### Beispiele
```bash
# Nach "Pizza" suchen
php 00-json-archive.php outbox.json "Pizza"

# Nach Hashtags suchen
php 00-json-archive.php outbox.json "Köln"
```

## Ausgabeformat

Jeder Beitrag zeigt:
- Beitragsnummer und Veröffentlichungsdatum
- Text-Inhalt (HTML-Tags entfernt)
- Angehängte Medien mit:
  - Medientyp (Bild, Video, etc.)
  - URL
  - Alt-Text (falls vorhanden)

## Mastodon-Archiv exportieren

1. In Mastodon einloggen
2. Einstellungen → Import und Export → Datenexport
3. "Archiv anfordern" klicken
4. Download-Link per E-Mail erhalten
5. Archiv entpacken → `outbox.json` verwenden

## Lizenz

GPL-3.0

## Autor

Michel Karbacher
