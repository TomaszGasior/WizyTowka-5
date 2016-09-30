#*abstract* Model

Klasa abstrakcyjna stanowiąca podstawę dla klas reprezentujących obiekty bazy danych. Innymi słowy, jest to ORM w uproszczonej formie. Umożliwia operacje CRUD — tworzenie, odczyt, zmianę i usuwanie rekordów.

Konkretna instancja klasy dziedziczącej po klasie `Model` jest reprezentacją obiektową jednego rekordu określonej tabeli bazy danych. 
Stworzenie nowego rekordu polega na utworzeniu nowej instancji klasy (zwyczajnie, za pomocą operatora `new`). Pobranie istniejących w bazie rekordów polega na użyciu statycznych metod `getAll()` lub `getById()` zwracających tablicę z przygotowanymi instancjami klasy bądź przygotowaną instancję klasy.
Klasy dziedziczące mogą oferować inne sposoby pobierania rekordów z wykorzystaniem klauzuli `WHERE` języka SQL za pośrednictwem chronionej metody `_getByWhereCondition()`.

**Klasy dziedziczące muszą nadpisywać statyczne chronione pola `$_tableName`, `$_tablePrimaryKey`, `$_tableColumns`.** `$_tableName` określa nazwę tabeli bazy danych, `$_tablePrimaryKey` definiuje nazwę kolumny klucza podstawowego (domyślnie `id`), `$_tableColumns` to tablica zawierająca nazwy kolumn tabeli (bez kolumny klucza podstawowego!).

Klasa `Model` implementuje metody magiczne `__get`, `__set`, `__isset`, umożliwiając operowanie na polach rekordu jak na polach obiektu, oraz interfejs `IteratorAggregate`, pozwalając na iterowanie po polach rekordu w pętli.
Implementuje również metodę `__debugInfo` dla funkcji `var_dump()` (dostępne od PHP 5.6).

##`__construct()`

Tworzy nowy rekord tabeli. Wszystkie pola rekordu otrzymują domyślną wartość `null`.

##`save()`

Zapisuje rekord. Jeśli rekord jest nowo utworzonym rekordem, używane jest zapytanie SQL `INSERT`, a po pomyślnym dodaniu wartość klucza podstawowego jest uzupełniana. Jeśli rekord już istnieje, jest aktualizowany przy użyciu zapytania `UPDATE`.

##`delete()`

Usuwa rekord. Po pomyślnym usunięciu z tabeli bazy danych, zachowując aktualne wartości pól (za wyjątkiem klucza podstawowego), staje się nowo utworzonym rekordem (jak za pomocą konstruktora; można go zapisać, by dodać go na nowo do tabeli z inną wartością klucza podstawowego).

Nie można usunąć rekordu, jeśli jest nowo utworzony (jeszcze nie zapisany w bazie danych).

##*static* `getAll()`

Zwraca tablicę gromadzącą wszystkie rekordy tabeli (każdy rekord jest indywidualną instancją klasy).

##*static* `getById($id)`

Zwraca rekord o wartości klucza podstawowego podanej w argumencie `$id` (instancję klasy). Jeśli taki rekord nie istnieje, zwraca fałsz.

##*static protected* `_getByWhereCondition($sqlQueryWhere = null, $parameters = [], $mustBeOnlyOneRecord = false)`

Metoda stanowiąca podstawę dla innych metod pobierających istniejące rekordy. Wykonuje zapytanie `SELECT` w celu pobrania rekordów, wykorzystując przy tym [przypinanie parametrów wejściowych PDO](http://php.net/manual/en/pdo.prepared-statements.php).

Argument `$sqlQueryWhere` określa fragment zapytania SQL umieszczony po klauzuli `WHERE`, może być pusty. Argument `$parameters` to tablica używana przez PDO do przypięcia wartości do odpowiadających parametrów obecnych w `$sqlQueryWhere`, może być pustą tablicą.

Standardowo zwracana jest tablica rekordów (instancji klas), a jeśli rekordów brak — pusta tablica. 
Jeśli argument `$mustBeOnlyOneRecord` jest prawdą, zwracany jest tylko pierwszy rekord bądź fałsz, jeśli brak rekordów. Jednakże, gdy baza danych zwróci więcej niż jeden rekord, rzucany jest wyjątek.