AdminPanel
===

Kontroler panelu administracyjnego systemu WizyTówka. Dziedziczy po klasie `Controller`.

Klasa `AdminPanel` w rzeczywistości nie jest kontrolerem poszczególnych stron panelu administracyjnego, a jedynie formą proxy. Odpowiada ona za wewnętrzne zainicjowanie właściwego kontrolera strony panelu w zależności od adresu URL, udostępniając poszczególne metody kontrolera strony jako swoje własne.

Kontrolery stron panelu znajdują się w zagnieżdżonej przestrzeni nazw `AdminPages`. Każdy z nich musi dziedziczyć po klasie `AdminPanelPage`. Klasa `AdminPanel` umożliwia dla wtyczek rejestrowanie z zewnątrz dodatkowych stron panelu administracyjnego oraz nadpisywanie systemowych stron.

## *static* `URL($target, $arguments = [])`

Zwraca adres URL kierujący do strony panelu administracyjnego określonej w argumencie `$target`. Jeżeli argument `$target` ma wartość `null`, adres URL kieruje do domyślnej strony panelu. Argument `$arguments` określa parametry dodane do query stringa odnośnika.

Nie należy określać w query stringu argumentu o kluczu `c`, gdyż nazwa ta jest używana wewnętrznie przez panel administracyjny. W wypadku takiego użycia, zostanie rzucony wyjątek `ControllerException` #2.

## *static* `registerPage($name, $controller)`

Umożliwia zarejestrowanie w panelu administracyjnym dodatkowej strony bądź nadpisanie systemowej strony. Argument `$name` określa nazwę strony — jest ona używana w adresie URL (atrybut `$tagret` metody `URL()`). Argument `$controller` określa pełną nazwę kwalifikowaną klasy kontrolera rejestrowanej strony. **Klasa ta musi dziedziczyć po klasie `AdminPanelPage`.**

Zostanie rzucony wyjątek `AdminPanelException` #1, jeśli nazwa `$name` jest już zajęta. Zostanie rzucony wyjątek `AdminPanelException` #2, jeśli klasa `$controller` nie dziedziczy po klasie `AdminPanelPage`.