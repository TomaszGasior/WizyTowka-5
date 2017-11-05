*abstract* ContentTypeAPI
===

Klasa abstrakcyjna gromadząca współdzieloną logikę potrzebną w klasach typów zawartości i określająca metody wymagane do zaimplementowania.

Klasy typu zawartości `WebsitePageBox`, `SettingsPage`, `EditorPage` muszą dziedziczyć po tej klasie.

Instancje klas dziedziczących po tej klasie powinny być tworzone wyłącznie wewnętrznie przez system poprzez klasę `ContentType`.

Klasa dziedziczące otrzymuje następujące pola chronione:

- `$_contentType` — instancja klasy `ContentType` typu zawartości;
- `$_contents` — instancja klasy `\stdClass` gromadząca treść strony w witrynie;
- `$_settings` — instancja klasy `\stdClass` gromadząca ustawienia strony typu zawartości w witrynie;
- `$_HTMLHead` — instancja klasy `HTMLHead` nagłówka `<head>` witryny;
- `$_HTMLTemplate` — instancja klasy `HTMLTemplate` szablonu typu zawartości;
- `$_HTMLMessage` — instancja klasy `HTMLMessage` komunikatu strony.

## *final* `__construct(ContentType $myContentTypeInstance)`

Konstruktor wymaga określenia w argumencie `$myContentTypeInstance` instancji klasy `ContentType`, z której instancja klasy dziedziczącej po klasie `ContentTypeAPI` jest tworzona.

Tutaj jest wywoływana metoda `_prepare()`.

## *final* `setPageData(\stdClass $contents, \stdClass $settings)`

Setter używany wewnętrznie przez system, ustawiający pola `$_settings` i `$_contents`.

## *final* `setHTMLParts(HTMLTemplate $template, HTMLHead $head, HTMLMessage $message)`

Setter używany wewnętrznie przez system, ustawiający pola `$_template`, `$_head`, `$_message`.

## *protected* `_prepare()`

Ekwiwalent dla metody `__construct()` dla klas dziedziczących. Metoda jest domyślnie pusta.

## `POSTQuery()`

Obsługuje żądania POST. Nie powinna być wywoływana poza nimi.

Domyślnie metoda rzuca wyjątek `ContentTypeAPIException` #1, co oznacza, że dana klasa zapytań POST nie obsługuje.

## *abstract* `HTMLContent()`

Miejsce na wykonywanie operacji związanych z szablonem HTML. Klasa dziedzicząca określa tutaj sposób renderowania typu zawartości. Żaden tekst nie powinien być kierowany tutaj na wyjście, należy operować na szablonie `$_template`, nagłówku `<head>` `$_head` i komunikacie `$_message`.

Domyślnie używany jest szablon o nazwie klasy, ładowany z podfolderu `template` folderu typu zawartości. Domyślnie także nagłówek `<head>` jako ścieżkę dla stylów CSS i skryptów JavaScript ma ustawiony adres URL do podfolderu `assets` folderu typu zawartości.