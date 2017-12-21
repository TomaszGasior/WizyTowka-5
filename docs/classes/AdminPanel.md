*abstract* AdminPanel
===

Podstawa kontrolerów reprezentujących panel administracyjny systemu WizyTówka. Dziedziczy po klasie `Controller`.

Klasa umożliwia rejestrowanie z zewnątrz dodatkowych stron panelu administracyjnego oraz pobranie nazwy właściwego kontrolera w zależności od bieżącego adresu URL. Stanowi też fundament dla wszelkich klas kontrolerów stron panelu administracyjnego — zarządza dostępem w zależności od oczekiwanego poziomu uprawnień użytkownika i szablonem HTML panelu administracyjnego.

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

## *protected* `_preventFromAccessWithoutPermission($requiredUserPermission)`

Jeżeli bieżący użytkownik nie posiada uprawnienia określonego w argumencie `$requiredUserPermission`, metoda natychmiast przerywa wykonywanie skryptu i przekierowuje użytkownika do strony informującej o błędzie niewystarczających uprawnień.

Poziom uprawnienia należy określić za pomocą stałych klasowych `PERM_*` klasy `User`.

## `output()`

Metoda generuje nagłówek strony HTML, menu główne, menu dodatkowe oraz ładuje główny szablon panelu administracyjnego, a także szablon strony panelu.

Szablon strony ładowany jest z folderu `system/templates/adminPages`, nazwa pliku szablonu zawiera nazwę klasy i rozszerzenie `php`. Można zmienić ścieżkę do folderu szablonów, używając metody `setTemplatePath()` klasy `HTMLTemplate`.

Tutaj jest wywoływana metoda `_output()`.

## *protected* `_prepare()`

Ekwiwalent dla metody `__construct()` dla klas dziedziczących. Metoda jest domyślnie pusta.

## *protected* `_output()`

Ekwiwalent dla metody `output()` (z klasy `Controller`) dla klas dziedziczących. Metoda jest domyślnie pusta.

## *static* `URL($target, $arguments = [])`

Zwraca adres URL kierujący do strony panelu administracyjnego określonej w argumencie `$target`. Jeżeli argument `$target` ma wartość `null`, adres URL kieruje do domyślnej strony panelu. Argument `$arguments` określa parametry dodane do query stringa odnośnika.

Nie należy określać w query stringu argumentu o kluczu `c`, gdyż nazwa ta jest używana wewnętrznie przez panel administracyjny. W wypadku takiego użycia, zostanie rzucony wyjątek `ControllerException` #2.

## *static* `registerPage($name, $controller)`

Umożliwia zarejestrowanie w panelu administracyjnym dodatkowej strony. Argument `$name` określa nazwę strony — jest ona używana w adresie URL (atrybut `$tagret` metody `URL()`). Argument `$controller` określa pełną nazwę kwalifikowaną klasy kontrolera rejestrowanej strony. **Klasa ta musi dziedziczyć po niniejszej klasie `AdminPanel`.**

Zostanie rzucony wyjątek `AdminPanelException` #1, jeśli nazwa `$name` jest już zajęta. Zostanie rzucony wyjątek `AdminPanelException` #2, jeśli klasa `$controller` nie dziedziczy po klasie `AdminPanel`.

## *static* `getControllerClass()`

Używana wewnętrznie przez system metoda zwracająca nazwę kontrolera w zależności od bieżącego adresu URL.