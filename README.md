# Eatable
> ⚠️ Die Weiterentwicklung des Projekts ist zur Zeit pausiert. Bist du interessiert daran, die vorhandene Grundlage weiterzuentwickeln? Dann melde dich bei [Tobija Fischer](mailto:webdesign@tobija.ch)

Offizielles Repository von [eatable.ch](https://eatable.ch/) – eine Schweizer App für nachhaltige Ernährung. Der gesamte Code wurde zwischen Sommer 2021 und Frühling 2022 geschrieben.

## Über das Projekt
Eatable ist eine App, die das Ziel hat, Ernährung in der Schweiz nachhaltiger zu machen. Du wirst von eatable auf verschiedenste Weisen unterstützt und inspiriert. Als digitales Kochbuch mit nachhaltigen Rezepten, die auf vorhandene Vorräte abgestimmt sind. Als interaktiver Einkaufsratgeber, der aufzeigt, welche Zutaten wie ökologisch sind und was gerade Saison hat. Als Inspirationsquelle, wo sich nachhaltige Läden und andere spannende Projekte zum Thema Ernährung und Nachhaltigkeit entdecken lassen.

## Technischer Aufbau
Das technische Konstrukt der Applikation besteht aus 3 Teilen, die alle komplett voneinander entkoppelt sind. Backend-Oberfläche, API Schnittstelle und das Frontend. Die aktuelle Test-App lässt sich unter [alpha.eatable.ch](https://alpha.eatable.ch/) aufrufen. Dazu kommt die Wordpress-Seite [eatable.ch](https://eatable.ch/), die das Projekt vorstellt und zu Marketing-Zwecken verwendet wird. Folgende Abbildung zeigt alle Domains des Projekts:

![eatable-domain-configuration](https://github.com/tobijafischer/Eatable/assets/45046461/e6fef01f-9b3c-4513-b040-694645cab4af)

### 1. Backend
Das Backend (admin.eatable.ch) ist nicht Teil der Open Source Codebase. Genau wie die API Schnittstelle ist das Backend von Grund auf mit Objekt-orientiertem PHP-Code geschrieben. Es ermöglicht mittels übersichtlicher Benutzeroberfläche die Verwaltung jeglicher Datenbank- und App-Inhalte von eatable.

### 2. Schnittstelle
Die Schnittstelle (api.eatable.ch) war ursprünglich Teil des Backends, wurde dann aber entkoppelt. Sie stellt dem Frontend durch einen zentralen Entry-Point (_index.php_) alle nötigen Inhalte und Funktionen zur Verfügung, um mit der MySQL Datenbank zu kommunizieren.

### 3. Frontend
Das Frontend (app.eatable.ch) basiert auf [Vue.js](https://github.com/vuejs/core) mit [Typescript](https://github.com/microsoft/TypeScript) und dem [Ionic Framework](https://github.com/ionic-team/ionic-framework).

## Lokales Development
1. Die API-Schnittstelle (_/02\_API/_) mit lokalem PHP-Server öffnen und mit einer lokalen MySQL Datenbank verbinden. Benenne dazu das File _"/02\_API/core/init-example.php"_ um in _"init.php"_ und ändere die folgenden Zeilen zur lokalen Dankenbankverbindung:
```
$GLOBALS['config'] = array(
  'mysql' => array(
    'host' => 'localhost',
    'dbuser' => 'root',
    'password' => '12345678',
    'db' => 'eatable_dev'
  )
);
```

2. Öffne den Frontend-Folder (_/03\_Frontend/_) im Terminal und installiere alle Dependencies mit:
```
npm install
```

3. Ändere die Variable _"baseApiUrl"_ unter _"/03\_Frontend/src/variables/environmentVariables.ts"_ zur URL der API-Schnittstelle, die auf dem lokalen PHP-Server läuft.

4. Starte die Vue App mit:
```
npm run serve
```

5. Die App sollte nun lokal funktionieren. Für Fragen und Unterstützung gerne bei [Tobija Fischer](mailto:webdesign@tobija.ch) melden.