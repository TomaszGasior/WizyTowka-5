*abstract* Addon
===

Klasa abstrakcyjna będąca podstawą dla klas reprezentujących poszczególne typy dodatków systemu WizyTówka. Wtyczki, typy zawartości i motywy są dodatkami.

Klasa dziedzicząca po tej klasie jest abstrakcyjną reprezentacją rodzaju dodatku. Klasa dziedzicząca powinna określać chronione i statyczne pole `$_addonsSubdir` zawierające nazwę podfolderu katalogu `addons` przeznaczonego na dany typ dodatku (np. klasa `Themes` może określać podfolder `themes`).

Klasa implementuje metody `__get()` i `__isset()`, umożliwiając operowanie na opcjach konfiguracyjnych dodatku jak na polach obiektu. Udostępnia też metodę `__debugInfo()` dla debugowania przy użyciu funkcji `var_dump()`.

Konstruktor tej klasy jest prywatny — nie można tworzyć nowych dodatków z wnętrza kodu. Aby pobrać instancję klasy konkretnego typu dodatku należy użyć metody statycznej `getByName()` lub `getAll()`.

## `getName()`

Zwraca nazwę dodatku (dokładniej: nazwę podfolderu danego dodatku znajdującego się w katalogu określonym w `$_addonsSubdir`).

## `isFromSystem()`

Zwraca prawdę, jeśli dodatek jest integralnym elementem systemu WizyTówka i znajduje się w folderze systemu (domyślnie `system`; pełna ścieżka to `(katalog systemu)/addons/(folder typu dodatków)/(folder dodatku)`, na przykład `system/addons/themes/systemtheme`).

Zwraca fałsz, jeśli dodatek nie jest elementem systemu WizyTówka i znajduje się w folderze danych witryny (domyślnie `data`; pełna ścieżka to `(katalog danych)/addons/(folder typu dodatków)/(folder dodatku)`, na przykład `data/addons/themes/systemtheme`).

## `isFromUser()`

Zwraca odwrotność wartości z metody `isFromSystem()`.

## *static* `getByName($name)`

Zwraca dodatek (instancję klasy) o nazwie `$name` bądź fałsz, jeśli dodatek o takiej nazwie nie istnieje.

Nazwa dodatku określona w argumencie `$name` jest w rzeczywistości nazwą podfolderu znajdującego się w folderze danego typu dodatków (ten folder określony jest w polu `$_addonsSubdir`).

Jeżeli dodatek o tej samej nazwie i typie znajduje się jednocześnie i w katalogu systemu (domyślnie `system`), i w katalogu danych witryny (domyślnie `data`), dodatek z katalogu danych witryny jest zwracany. Innymi słowy: dodatki z katalogu danych witryny nadpisują systemowe dodatki.

## *static* `getAll()`

Zwraca tablicę zawierającą wszystkie dodatki (instancje klasy) danego typu. Jeśli dodatków danego typu brak, zwracana jest pusta tablica.

Dodatki są posortowane alfabetycznie według nazwy, pierwsze w tablicy są dodatki z katalogu danych witryny, później systemowe. Dodatki z katalogu danych witryny nadpisują dodatki systemowe o tej samej nazwie.