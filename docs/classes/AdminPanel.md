*abstract* AdminPanel
===

Kontroler reprezentujący panel administracyjny systemu WizyTówka. Dziedziczy po klasie `Controller`.

Klasa umożliwia rejestrowanie z zewnątrz dodatkowych stron panelu administracyjnego oraz pobranie nazwy kontrolera w zależności od bieżącego adresu URL. Stanowi też fundament dla wszelkich klas kontrolerów stron panelu administracyjnego — zarządza dostępem w zależności od oczekiwanego poziomu uprawnień użytkownika i szablonem HTML panelu administracyjnego.

Znajdujące się w zagnieżdżonej przestrzeni nazw `AdminPages` kontrolery poszczególnych stron panelu administracyjnego powinny dziedziczyć po tej klasie.

## *abstract protected* `_prepare()`

Ekwiwalent dla metody `__construct()` dla klas dziedziczących.

## *abstract protected* `_output()`

Ekwiwalent dla metody `output()` (z klasy `Controller`) dla klas dziedziczących.

## *static* `URL($target, $arguments = [])`

Zwraca adres URL kierujący do strony panelu administracyjnego określonej w argumencie `$target`. Argument `$arguments` określa parametry dodane do query stringa odnośnika.

Nie należy określać w query stringu argumentu o kluczu `c`, gdyż nazwa ta jest używana wewnętrznie przez panel administracyjny. W wypadku takiego użycia, zostanie rzucony wyjątek `ControllerException` #2.

## *static* `registerPage($name, $controller)`

Umożliwia zarejestrowanie w panelu administracyjnym dodatkowej strony. Argument `$name` określa nazwę strony — jest ona używana w adresie URL (atrybut `$tagret` metody `URL()`). Argument `$controller` określa pełną nazwę kwalifikowaną klasy kontrolera rejestrowanej strony. **Klasa ta musi dziedziczyć po niniejszej klasie `AdminPanel`.**

Zostanie rzucony wyjątek `AdminPanelException` #1, jeśli nazwa `$name` jest już zajęta. Zostanie rzucony wyjątek `AdminPanelException` #2, jeśli klasa `$controller` nie dziedziczy po klasie `AdminPanel`.

## *static* `getControllerClass()`

Używana wewnętrznie przez system metoda zwracająca nazwę kontrolera w zależności od bieżącego adresu URL.