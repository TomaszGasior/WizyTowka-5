Autoloader
===

**Ta klasa znajduje się w zagnieżdżonej przestrzeni nazw `_Private` — nie należy ręcznie tworzyć jej instancji. Aby uzyskać dostęp do obiektu tej klasy, należy użyć składni `WT()->autoloader`.**

Automatyczna ładowarka klas z uprzednio zarejestrowanych przestrzeni nazw.

Ładowarka wymaga dodania każdej przestrzeni nazw indywidualnie wraz z ścieżką do folderu plików PHP. Nie zamienia podkreślników i kolejnych poziomów przestrzeni na podfoldery. Oczekiwane rozszerzenie pliku to `.php`; Wielkość liter oczekiwana w nazwie pliku względem nazwy klasy pozostaje niezmieniona.

## `addNamespace(string $namespace, string $pathToClasses) : bool`

Dodaje wskazaną przez `$namespace` przestrzeń nazw do zbioru przestrzeni nazw obsługiwanych przez ładowarkę. Jako `$pathToClasses` podać należy ścieżkę do folderu gromadzącego pliki klas.

Jeżeli podana przestrzeń nazw jest już zarejestrowana, metoda zwraca fałsz. W innym wypadku zwraca prawdę.

## `removeNamespace(string $namespace) : void`

Metoda usuwa wskazaną w `$namespace` przestrzeń nazw ze zbioru przestrzeni obsługiwanych przez ładowarkę.

## `namespaceExists(string $namespace) : bool`

Jeśli przestrzeń nazw `$namespace` została dodana do ładowarki, zwraca prawdę, w innym wypadku — fałsz.

## `autoload(string $FQCN) : bool`

Ładowarka klas. Powinna być ustawiana za pośrednictwem `spl_autoload_register()`.