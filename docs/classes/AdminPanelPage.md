*abstract* AdminPanelPage
===

Podstawa kontrolerów stron panelu administracyjnego systemu WizyTówka. Dziedziczy po klasie `Controller`.

Kontroler pośredniczący `AdminPanel` inicjuje przy uruchamianiu panelu administracyjnego odpowiedni kontroler dziedziczący po klasie `AdminPanelPage` w zależności od adresu URL.

Znajdujące się w zagnieżdżonej przestrzeni nazw `AdminPages` kontrolery poszczególnych stron panelu administracyjnego powinny dziedziczyć po tej klasie. W celach konfiguracyjnych mogą one określać pola chronione:

- `$_pageTitle` — tytuł strony panelu administracyjnego widoczny na pasku tytułowym przeglądarki;
- `$_userRequiredPermissions` — flagi uprawnień użytkownika (stałe `PERM_*` klasy `User`) wymaganych do umożliwienia użytkownikowi korzystania ze strony panelu;
- `$_userMustBeLoggedIn` — określenie wymagania zalogowania się użytkownika w celu dostępu do strony, typ logiczny, domyślnie prawda.

Klasa określa też dla klas dziedziczących następujące pola chronione:

- `$_currentUser` — instancja klasy `User` konta bieżącego użytkownika, jeśli jest zalogowany;
- `$_HTMLHead` — instancja klasy `HTMLHead` nagłówka strony panelu administracyjnego;
- `$_HTMLMessage` — instancja klasy `HTMLMessage` komunikatu strony.
- `$_HTMLTemplate` — instancja klasy `HTMLTemplate` szablonu głównej treści strony;
- `$_HTMLContextMenu` — instancja klasy `HTMLMenu` menu dodatkowego strony.

## `__construct()`

Konstruktor przekierowuje do strony logowania panelu administracyjnego, jeśli wymagane jest zalogowanie użytkownika, a użytkownik nie jest zalogowany, a także przekierowuje do komunikatu o błędzie, jeśli obecnie zalogowany użytkownik nie posiada wymaganych uprawnień.

Tutaj jest wywoływana metoda `_prepare()`.

## `output() : void`

Metoda generuje nagłówek strony HTML, menu główne, menu dodatkowe oraz ładuje główny szablon panelu administracyjnego, a także szablon strony panelu.

Szablon strony ładowany jest z folderu `system/templates/adminPages`, nazwa pliku szablonu zawiera nazwę klasy i rozszerzenie `php`. Można zmienić ścieżkę do folderu szablonów, używając metody `setTemplatePath()` klasy `HTMLTemplate`.

Tutaj jest wywoływana metoda `_output()`.

## *protected* `_prepare() : void`

Ekwiwalent dla metody `__construct()` dla klas dziedziczących. Metoda jest domyślnie pusta.

## *protected* `_output() : void`

Ekwiwalent dla metody `output()` (z klasy `Controller`) dla klas dziedziczących. Metoda jest domyślnie pusta.

## *static* `URL(...)`

Alias dla `AdminPanel::URL(...)`