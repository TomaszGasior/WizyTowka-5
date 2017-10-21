Plugin
===

Klasa reprezentująca wtyczki (dodatki niebędące typami zawartości i motywami). Dziedziczy po klasie `Addon`.

Dodatki tego typu znajdują się w folderze `addons/plugins` w katalogu danych witryny oraz w katalogu systemu.

W pliku konfiguracyjnym `addon.conf` wtyczka powinna określać:

- `namespace` — przestrzeń nazw gromadząca wszystkie klasy wtyczki, będą one automatycznie ładowane z podfolderu `classes` wtyczki;
- `init` — callback w formie metody statycznej klasy uruchamiany przy inicjacji wtyczki; jako argument przekazywana jest do niej instancja klasy `Plugin`.

## `init()`

Inicjuje wtyczkę, konfigurując automatyczną ładowarkę systemu WizyTówka do obsługi klas wtyczki i uruchamiając callback określony w ustawieniu `init`.

Metoda ta jest uruchamiana podczas startu systemu. Nie należy używać jej ręcznie.