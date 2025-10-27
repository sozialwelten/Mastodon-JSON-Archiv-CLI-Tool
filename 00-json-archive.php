#!/usr/bin/env php
<?php
/**
 * Mastodon Archive Reader
 * Liest große JSON-Archive effizient und zeigt Beiträge formatiert an
 * 
 * Verwendung:
 *   php mastodon_reader.php <datei.json> [suchbegriff]
 *   php mastodon_reader.php outbox.json
 *   php mastodon_reader.php outbox.json "Pizza"
 */

if ($argc < 2) {
    echo "Verwendung: php {$argv[0]} <archive.json> [suchbegriff]\n";
    exit(1);
}

$file = $argv[1];
$search = $argv[2] ?? null;

if (!file_exists($file)) {
    echo "Fehler: Datei '$file' nicht gefunden.\n";
    exit(1);
}

echo "Lese Archiv: $file\n";
if ($search) {
    echo "Suche nach: '$search'\n";
}
echo str_repeat("=", 80) . "\n\n";

// JSON in Chunks lesen für große Dateien
$content = file_get_contents($file);
$data = json_decode($content, true);

if (!$data || !isset($data['orderedItems'])) {
    echo "Fehler: Ungültige JSON-Struktur.\n";
    exit(1);
}

$items = $data['orderedItems'];
$total = count($items);
$found = 0;

echo "Gefundene Beiträge: $total\n\n";

foreach ($items as $idx => $item) {
    if (!isset($item['object'])) continue;
    
    $obj = $item['object'];
    
    // HTML entfernen und Text dekodieren
    $content = strip_tags($obj['content'] ?? '');
    $content = html_entity_decode($content, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    
    $published = $obj['published'] ?? 'Unbekannt';
    
    // Suchfilter - auch in contentMap (Originaltext) und Alt-Texten suchen
    if ($search) {
        $searchIn = $content;
        
        // Auch Original-Content durchsuchen
        if (isset($obj['contentMap']['de'])) {
            $searchIn .= ' ' . strip_tags($obj['contentMap']['de']);
        }
        
        // Auch Alt-Texte durchsuchen
        if (isset($obj['attachment'])) {
            foreach ($obj['attachment'] as $att) {
                if (isset($att['name'])) {
                    $searchIn .= ' ' . $att['name'];
                }
            }
        }
        
        if (stripos($searchIn, $search) === false) {
            continue;
        }
    }
    
    $found++;
    
    // Formatierte Ausgabe
    echo str_repeat("-", 80) . "\n";
    echo "Beitrag #" . ($idx + 1) . " | Veröffentlicht: $published\n";
    echo str_repeat("-", 80) . "\n";
    
    // Text-Inhalt
    echo "\n📝 TEXT-INHALT:\n";
    echo wordwrap($content, 76) . "\n";
    
    // Angehängte Medien
    if (isset($obj['attachment']) && is_array($obj['attachment'])) {
        echo "\n📎 ANGEHÄNGTE MEDIEN (" . count($obj['attachment']) . "):\n";
        
        foreach ($obj['attachment'] as $i => $att) {
            $type = $att['mediaType'] ?? 'unbekannt';
            $url = $att['url'] ?? 'keine URL';
            $name = $att['name'] ?? '';
            
            echo "\n  [" . ($i + 1) . "] Typ: $type\n";
            echo "      URL: $url\n";
            
            if ($name) {
                echo "      Alt-Text:\n";
                $altLines = explode("\n", trim($name));
                foreach ($altLines as $line) {
                    echo "        " . $line . "\n";
                }
            } else {
                echo "      Alt-Text: (nicht vorhanden)\n";
            }
        }
    }
    
    echo "\n" . str_repeat("=", 80) . "\n\n";
    
    // Bei vielen Ergebnissen: Pagination
    if ($found % 5 == 0) {
        echo "--- Weiter mit ENTER, 'q' zum Beenden ---\n";
        $input = trim(fgets(STDIN));
        if ($input === 'q') break;
    }
}

echo "\n✓ Fertig! ";
if ($search) {
    echo "$found von $total Beiträgen enthalten '$search'.\n";
} else {
    echo "$total Beiträge angezeigt.\n";
}
