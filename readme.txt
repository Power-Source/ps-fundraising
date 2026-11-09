=== PS Fundraising ===
Contributors: DerN3rd (PSOURCE)
Tags: fundraising, spenden, crowdfunding, donations, fundraiser, kickstarter, indiegogo
Requires at least: 4.9
Requires CP: 2.7
Requires PHP: 8.0
Tested up to: 7.2.4
Stable tag: 1.0.2
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Das professionelle Fundraising- und Crowdfunding-Plugin für WordPress. Erstelle Spendenbuttons, komplexe Crowdfundingkampagnen oder lasse Deine Besucher Deine Projekte unterstützen - kostenlos ohne externe Gebühren!

== Description ==

PS Fundraising ist das ultimative WordPress-Plugin zur Verwaltung von Spendenaktionen, Crowdfunding-Kampagnen und Fundraising-Initiativen direkt auf Deiner Website. Mit voller Integration in BuddyPress und einer beeindruckenden Bandbreite an Features bietet es eine sichere, flexible und benutzerfreundliche Lösung für alle Fundraising-Anforderungen.

Erstelle Spendenbuttons, komplexe Crowdfundingkampagnen oder lasse Deine Besucher Deine Projekte unterstützen - ganz ohne die hohen Gebühren von externen Plattformen.

=== Spende oder Crowdfunding ===

Crowdfinanziere Dein nächstes Projekt mit Finanzierungszielen und Prämienpaketen oder erstelle schnell eine einzelne Spendenseite mit wiederkehrenden Zahlungsoptionen.

== Hauptfunktionen ==

* **Einfache Verwaltung:** Intuitives Admin-Interface zur Verwaltung aller Spendenaktionen und Kampagnen
* **Flexible Zahlungsoptionen:** Unterstützung für PayPal, Stripe und manuelle Zahlungen
* **Wiederkehrende Zahlungen:** Ermögliche Abonnements und regelmäßige Spenden
* **Prämienpakete:** Erstelle attraktive Reward-Tiers für Crowdfunding-Kampagnen
* **Kampagnenziele:** Setze Finanzierungsziele und verfolge den Fortschritt in Echtzeit
* **BuddyPress Integration:** Volle Integration mit BuddyPress für Community-Fundraising
* **Responsive Design:** Mobile-optimiert und anpassbar an Dein Theme
* **Widgets:** Vorgefertigte Widgets für Spendenbuttons und Fundraiser-Listen
* **Shortcodes:** Einfache Shortcodes zur Integration in Posts und Seiten
* **Benutzerfreundliche Verwaltung:** Umfangreiche Einstellungen ohne technische Vorkenntnisse notwendig
* **Customizable Styles:** 5+ vordefinierte Stile (Basic, Dark, Fresh, Material, Minimal, Note)
* **Bestätigung und Kommunikation:** Automatische Bestätigung und Benachrichtigungen für Spender

=== Spende oder Crowdfunding ===

Crowdfinanziere Dein nächstes Projekt mit Finanzierungszielen und Prämienpaketen oder erstelle schnell eine einzelne Spendenseite mit wiederkehrenden Zahlungsoptionen.

=== Mehr Geld für Dein Projekt ===

Services wie Kickstarter und Indiegogo erheben bis zu 11% Verwaltungsgebühr und Bearbeitungsgebühr. 
Vergiss die unverschämten Gebühren und stecke die Gelder stattdessen direkt in Deine gemeinnützige, Indie-Plattenveröffentlichung oder verrückte Erfindung.

Mit PS Fundraising behältst Du die volle Kontrolle über Deine Einnahmen und zahlst nur die Transaktionsgebühren der Zahlungsanbieter.

=== Zahlungsgateways ===

* **PayPal:** Direkte PayPal-Integration für Spenden und Abonnements
* **Stripe:** Professionelle Stripe-Integration mit Kartenzahlungen
* **Manuelle Zahlungen:** Manuelle Transaktionsverwaltung und Verwaltung von Bank-Überweisungen

=== Leistungsstark und einfache Einrichtung ===

Wenn Du nach einem leistungsstarken Fundraising-System mit Tausenden von Einstellungen suchst, ist dies nicht der Fall. 
Andere Plugins können das. 
Fundraising fügt nur die Power-Features für einfaches Crowdfunding und schnelles Setup hinzu.

Die Einrichtung ist unkompliziert:
1. Plugin aktivieren
2. Zahlungsgateway konfigurieren
3. Erste Spendenaktionen erstellen
4. Auf Spenden warten

== Installation ==

1. Lade das PS Fundraising Plugin in Dein wp-content/plugins Verzeichnis hoch
2. Aktiviere das Plugin über das "Plugins"-Menü in WordPress
3. Konfiguriere die Zahlungsgateways (PayPal/Stripe) unter Einstellungen → Fundraising
4. Erstelle Deine erste Spendenaktionen unter dem neuen "Fundraising"-Menüpunkt
5. Nutze Widgets oder Shortcodes zur Integration in Deiner Website

== Anforderungen ==

* ClassicPress 2.7+
* WordPress 4.9+ 
* PHP 8.0+
* Ein PayPal oder Stripe Konto für Zahlungsverarbeitung

== Entwickler ==

PS Fundraising bietet umfangreiche Möglichkeiten zur Anpassung für Entwickler.

=== Verfügbare Widgets ===

Das Plugin enthält mehrere vorgefertigte Widgets:
* Fundraiser Panel: Zeigt Details einer Spendenaktionen
* Fundraisers List: Listet alle aktiven Spendenaktionen auf
* Pledges Panel: Zeigt Spenden/Versprechen an
* Recent Fundraisers: Zeigt die neuesten Spendenaktionen
* Simple Donation: Einfacher Spendendbutton

=== Verfügbare Shortcodes ===

* `[fundraiser id="123"]` - Zeigt eine einzelne Spendenaktionen
* `[fundraisers]` - Zeigt alle Spendenaktionen
* `[fundraiser_list]` - Listet Spendenaktionen auf
* `[simple_donation]` - Einfacher Spendendbutton

Du kannst im Stammordner Deines aktiven Themas neue Dateien erstellen, die beim Anzeigen einer Spendenaktion verwendet werden.

Es gibt 3 Arten von Vorlagen, die Du erstellen kannst.

=== Vorlagenhierarchie ===

Einzelne Spendenaktion
wdf_funder-{ID}.php
wdf_funder-{slug}.php
wdf_funder.php
 
Kasse für Spendenaktionen
wdf_checkout-{ID}.php
wdf_checkout-{slug}.php
wdf_checkout.php

Bestätigungsseite für die Spendenaktion
wdf_confirm-{ID}.php
wdf_confirm-{slug}.php
wdf_confirm.php

=== Verfügbare Hooks und Filter ===

Das Plugin bietet zahlreiche Hooks und Filter für Entwickler:

* **wdf_fundraiser_title** - Filter für Titel der Spendenaktionen
* **wdf_fundraiser_description** - Filter für Beschreibung
* **wdf_before_checkout** - Action vor dem Checkout
* **wdf_after_checkout** - Action nach dem Checkout
* **wdf_before_payment** - Action vor der Zahlung
* **wdf_after_payment** - Action nach der Zahlung
* **wdf_custom_style_name** - Filter für benutzerdefinierte Stilnamen
* **wdf_gateway_settings** - Filter für Gateway-Einstellungen
* **wdf_email_notification** - Filter für E-Mail-Benachrichtigungen

=== Benutzerdefinierte Vorlagenfunktionen ===

Du kannst den Speicherort der Template-Funktionen, die in Fundraising geladen werden, an einen externen Speicherort ändern.
Dadurch wird sichergestellt, dass alle benutzerdefinierten Änderungen, die Du an Deinen Vorlagenfunktionen vornimmst, bei zukünftigen Aktualisierungen des Plugins nicht beschädigt werden.

Füge in Deiner Datei wp-config.php eine neue define-Anweisung hinzu, die eine Zeichenfolge enthält, die auf den Speicherort verweist.
define('WDF_CUSTOM_TEMPLATE_FUNCTIONS','/full-server-path/and/filename.php');


=== Benutzerdefinierte Stile ===

Füge eine beliebige CSS-Datei zu Deinem Fundraising-/styles/-Ordner hinzu, damit sie in Deinen Einstellungen verfügbar ist.

Du kannst auch einen Ordner namens "wdf-styles" in Deinem wp-content-Ordner erstellen

ODER

Du kannst einen externen Speicherort mit dem folgenden Code in Deiner wp-config.php-Datei festlegen.

define('WDF_EXTERNAL_STYLE_DIRECTORY','/full/server/path/to/styles/')

Standardmäßig ist der Name des Stils der Name der Datei. Du kannst dies manuell mit dem Filter "wdf_custom_style_name" anpassen

=== Beispiel: ===

add_filter('wdf_custom_style_name', 'my_custom_filter_style_name', 10, 2);

function my_custom_filter_style_name($name, $file_name) {
	if($file_name == 'my-file.css') {
		$name = "Mein benutzerdefinierter Stil";
	}
	return $name;
}

=== Dateistruktur ===

Das Plugin ist modular aufgebaut:

* `/lib/` - Kern-Funktionen und Template-Funktionen
* `/lib/bp/` - BuddyPress spezifische Integration und Funktionen
* `/lib/classes/` - PHP Klassen für Gateways und Admin
* `/lib/gateways/` - Zahlungsgateways (PayPal, Stripe, Manual)
* `/lib/widgets/` - WordPress Widgets
* `/js/` - JavaScript für Frontend und Admin
* `/css/` - Stylesheet-Dateien
* `/styles/` - Vordefinierte Theme-Stile
* `/templates/` - BuddyPress Template-Dateien

== Häufig gestellte Fragen ==

= Kann ich mehrere Zahlungsgateways verwenden? =
Ja! Du kannst PayPal, Stripe und manuelle Zahlungen gleichzeitig aktivieren und nutzen.

= Sind Transaktionsgebühren im Plugin enthalten? =
Nein, Du zahlst nur die Gebühren des jeweiligen Zahlungsanbieters (PayPal oder Stripe). Das Plugin selbst ist kostenlos.

= Kann ich Abonnements/wiederkehrende Zahlungen anbieten? =
Ja! PayPal und Stripe unterstützen beide wiederkehrende Zahlungen und Abonnements.

= Funktioniert das Plugin mit meinem Theme? =
Ja! Das Plugin ist mit den meisten Themes kompatibel. Du kannst Vorlagen auch an Dein Theme anpassen.

= Kann ich die Spendenaktionen in BuddyPress anzeigen? =
Ja! Das Plugin ist vollständig mit BuddyPress integriert und zeigt Aktivitäten, Notificationen und Profilintegration.

= Wie kann ich einen Custom Style erstellen? =
Erstelle eine CSS-Datei und speichere sie im `/styles/` Ordner oder in `wp-content/wdf-styles/`. Der Filter `wdf_custom_style_name` erlaubt Dir einen benutzerdefinierten Namen.

== Changelog ==

= 1.0.2 =
* Der Shortcode-Button im klassischen Editor öffnet den Dialog wieder als Popup, statt zur Einfüge-Seite weiterzuleiten.
* Optionale PS Community-Integration ergänzt: Profil-Tabs für Kampagnen und eigene Unterstützungen sowie freiwillige Aktivitäten für eingeloggte Unterstützer.
* Shortcode-Dialog optimiert: Alle Einfügeformulare werden einmal geladen; Tabwechsel erfolgen ohne erneuten Server-Request.

= 1.0.1 =
* Überweisungsgateway überarbeitet: Bankverbindung und Verwendungszweck lassen sich konfigurieren; auf der Bestätigungsseite werden die Zahlungsdaten angezeigt.
* Überweisungen erhalten den Status „Ausstehend“ und werden erst nach Zahlungseingang als abgeschlossen gezählt.
* EPC-/SEPA-QR-Codes werden lokal aus den Zahlungsdaten erzeugt, ohne Bankdaten an einen externen QR-Dienst zu senden.
* Stripe-Checkout und Webhook-Verarbeitung für erfolgreiche und unbezahlte Zahlungen vervollständigt.
* PHP-8-Kompatibilität verbessert, unter anderem durch explizite Deklaration zuvor dynamischer Eigenschaften und Korrekturen an Status- und Prämienübergängen.
* PHP- und Plattformanforderungen aktualisiert; Regressionstests für die Zahlungsabläufe ergänzt.

= 1.0.0 =
* Release
