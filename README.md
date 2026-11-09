# PS Fundraising

**Deutsch** | [English](README.en.md)

[![Version](https://img.shields.io/badge/Version-1.0.2-2271b1?style=flat-square)](readme.txt)
![PHP](https://img.shields.io/badge/PHP-8.0%2B-777bb4?style=flat-square&logo=php&logoColor=white)
![WordPress](https://img.shields.io/badge/WordPress-bis%207.2.4-21759b?style=flat-square&logo=wordpress&logoColor=white)
![ClassicPress](https://img.shields.io/badge/ClassicPress-2.7.4-03768e?style=flat-square)
[![Lizenz](https://img.shields.io/badge/Lizenz-GPL--2.0--or--later-2ea44f?style=flat-square)](https://www.gnu.org/licenses/gpl-2.0.html)

### Deine Spendenaktion. Deine Website. Deine Community.

Mit **PS Fundraising** organisierst Du Spendenaktionen und Crowdfunding direkt auf Deiner WordPress- oder ClassicPress-Website. Lege Kampagnen mit Finanzierungszielen und Prämien an, zeige den Fortschritt transparent und verwalte Unterstützungen zentral im Adminbereich.


## Das steckt drin

- **Fundraising und Crowdfunding:** Erstelle Kampagnen mit Finanzierungszielen, Laufzeiten und Prämienstufen.
- **Spenden verwalten:** Behalte Kampagnen und Unterstützungen an einem Ort im Blick.
- **Mehrere Zahlungswege:** Nutze PayPal, Stripe und manuelle Zahlungen – abhängig von Deiner Konfiguration.
- **Überweisung mit klaren Zahlungsinfos:** Hinterlege Kontoinhaber, IBAN, BIC, Bankname und einen Verwendungszweck-Präfix.
- **Ausstehende Überweisungen:** Manuelle Zahlungen bleiben zunächst „Ausstehend“ und werden nach Zahlungseingang von Dir bestätigt.
- **SEPA-QR-Code:** Für EUR-Überweisungen kann ein EPC-QR-Code direkt auf der Website erzeugt werden. Die QR-Erzeugung erfolgt lokal.
- **Einbindung nach Wunsch:** Verwende Shortcodes, Widgets oder Theme-Vorlagen.
- **BuddyPress-Integration:** Ergänze Deine Community um gemeinschaftliches Fundraising.
- **PS Community-Integration:** Zeige Kampagnen und eigene Unterstützungen in Community-Profilen; eingeloggte Unterstützer können Aktivitäten freiwillig veröffentlichen.
- **Ein responsives Kampagnendesign:** Kennzahlen stehen nebeneinander und passen sich kleineren Bildschirmen an.
- **Anpassbares Erscheinungsbild:** Passe Farben und Ecken über CSS-Variablen oder eigenes CSS an.

> Zahlungsanbieter können eigene Transaktionsgebühren erheben. PS Fundraising selbst erhebt keine Plattformgebühr.

## Schnellstart

1. Installiere und aktiviere PS Fundraising.
2. Öffne im Adminbereich **Fundraising → Einstellungen**.
3. Aktiviere und konfiguriere die gewünschten Zahlungs-Gateways.
4. Für Überweisungen trägst Du im Bereich **Überweisung** die Bankdaten und den Verwendungszweck-Präfix ein.
5. Erstelle unter **Fundraising** eine Kampagne und füge sie einer Seite oder einem Beitrag hinzu.

### Überweisung einrichten

Beim manuellen Gateway kannst Du Kontoinhaber und IBAN angeben; BIC und Bankname sind optional. Der Verwendungszweck erhält automatisch eine eindeutige Referenz zur jeweiligen Unterstützung. Sind Kontoinhaber, gültige IBAN und EUR-Betrag vorhanden, zeigt die Bestätigungsseite zusätzlich einen EPC-/SEPA-QR-Code an.

Nach Eingang des Geldes kannst Du den Status der Unterstützung in der Verwaltung auf **Komplett** setzen. Bis dahin wird sie als **Ausstehend** geführt und nicht als abgeschlossene Einnahme gezählt.

## Design anpassen

Alle Kampagnen verwenden dasselbe moderne, responsive Layout. Unter **Fundraising → Einstellungen → Präsentation → Benutzerdefinierte CSS** kannst Du Akzentfarbe, Flächenfarben, Textfarben, Rahmen und Rundungen anpassen:

```css
.wdf-modern {
    --wdf-modern-primary: #176b5b;
    --wdf-modern-primary-hover: #105347;
    --wdf-modern-surface: #ffffff;
    --wdf-modern-muted-surface: #f3f7f6;
    --wdf-modern-text: #172622;
    --wdf-modern-muted: #5b6d67;
    --wdf-modern-border: #dce7e3;
    --wdf-modern-radius: 16px;
}
```

Die bisherigen Farbstile werden nicht mehr als separate Kampagnen-Layouts angeboten. Das globale CSS-Feld bleibt für eigene Anpassungen verfügbar.

## Einbindung mit Shortcodes

Zeige eine Kampagne mit ihrer ID an:

```text
[fundraiser_panel id="123"]
```

Zeige die Unterstützungen einer Kampagne:

```text
[pledges_panel id="123"]
```

Ein einfacher Spendenbutton:

```text
[donate_button]
```

Die verfügbaren Widgets findest Du im WordPress-Bereich **Design → Widgets**. Die Ausgabe und Darstellung kann je nach Theme und Einstellungen variieren.

## Voraussetzungen

- PHP **8.0 oder neuer**
- WordPress **4.9 oder neuer** (getestet bis WordPress 7.2.4)
- ClassicPress **2.7 oder neuer**
- Für die lokale EPC-QR-Erzeugung: PHP-Erweiterung **mbstring**
- Für Online-Zahlungen: ein entsprechend eingerichtetes PayPal- oder Stripe-Konto

## Entwicklung und Tests

Der Code ist modular aufgebaut. Gateways liegen in `lib/gateways/`, Widgets in `lib/widgets/`; die Zahlungsabläufe lassen sich mit dem Test-Harness prüfen:

```bash
php tests/payment-gateway-flows.php
```

## Mitwirken

Fehlerberichte, Ideen und Beiträge sind willkommen:

- [Repository und Quellcode](https://github.com/Power-Source/ps-fundraising)
- [Issues und Funktionsvorschläge](https://github.com/Power-Source/ps-fundraising/issues)

PS Fundraising ist freie Software unter **GPL-2.0-or-later**.
