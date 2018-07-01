Autoloader
===

**Ta klasa znajduje się w zagnieżdżonej przestrzeni nazw `_Private` — nie należy ręcznie tworzyć jej instancji. Aby uzyskać dostęp do obiektu tej klasy, należy użyć składni `WT()->autoloader`.**

Automatyczna ładowarka klas z uprzednio zarejestrowanych przestrzeni nazw.

Ładowarka wymaga dodania każdej przestrzeni nazw indywidualnie wraz z ścieżką do folderu plików PHP. Nie zamienia podkreślników i kolejnych poziomów przestrzeni na podfoldery. Oczekiwane rozszerzenie pliku to `.php`; Wielkość liter oczekiwana w nazwie pliku względem nazwy klasy pozostaje niezmieniona.

## `addNamespace($namespace, $pathToClasses)`

Dodaje wskazaną przez `$namespace` przestrzeń nazw do zbioru przestrzeni nazw obsługiwanych przez ładowarkę. Jako `$pathToClasses` podać należy ścieżkę do folderu gromadzącego pliki klas.

Jeżeli podana przestrzeń nazw jest już zarejestrowana, metoda zwraca fałsz. W innym wypadku zwraca prawdę.

## `namespaceExists($namespace)`

Jeśli przestrzeń nazw `$namespace` została dodana do ładowarki, zwraca prawdę, w innym wypadku — fałsz.

## `removeNamespace($namespace)`

Metoda usuwa wskazaną w `$namespace` przestrzeń nazw ze zbioru przestrzeni obsługiwanych przez ładowarkę.

## `autoload($fullyQualifiedName)`

Ładowarka klas. Powinna być ustawiana za pośrednictwem `spl_autoload_register()`.