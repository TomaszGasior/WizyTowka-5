#Autoloader

Automatyczna ładowarka klas z uprzednio zarejestrowanych przestrzeni nazw.

Ładowarka wymaga dodania każdej przestrzeni nazw indywidualnie wraz z ścieżką do folderu plików PHP. Nie zamienia podkreślników i kolejnych poziomów przestrzeni na podfoldery. Oczekiwane rozszerzenie pliku to `.php`; Wielkość liter oczekiwana w nazwie pliku względem nazwy klasy pozostaje niezmieniona.

##*static* `addNamespace($namespace, $pathToClasses)`

Metoda ta dodaje wskazaną przez `$namespace` przestrzeń nazw do zbioru przestrzeni nazw obsługiwanych przez ładowarkę. Jako `$pathToClasses` podać należy ścieżkę do folderu gromadzącego pliki klas.

##*static* `removeNamespace($namespace)`

Metoda usuwa wskazaną w `$namespace` przestrzeń nazw ze zbioru przestrzeni obsługiwanych przez ładowarkę.

##*static* `autoload($fullyQualifiedName)`

Ładowarka klas. Powinna być ustawiana za pośrednictwem `spl_autoload_register()`.