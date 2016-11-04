#*abstract* DatabaseObject

Klasa abstrakcyjna stanowiąca podstawę dla klas reprezentujących obiekty bazy danych. Innymi słowy, jest to ORM w uproszczonej formie. Umożliwia operacje CRUD — tworzenie, odczyt, zmianę i usuwanie rekordów.

Konkretna instancja klasy dziedziczącej po klasie `DatabaseObject` jest reprezentacją obiektową jednego rekordu określonej tabeli bazy danych.
Stworzenie nowego rekordu polega na utworzeniu nowej instancji klasy (zwyczajnie, za pomocą operatora `new`). Pobranie istniejących w bazie rekordów polega na użyciu statycznych metod `getAll()` lub `getById()` zwracających tablicę z przygotowanymi instancjami klasy bądź przygotowaną instancję klasy.
Klasy dziedziczące mogą oferować inne sposoby pobierania rekordów z wykorzystaniem klauzuli `WHERE` języka SQL za pośrednictwem chronionej metody `_getByWhereCondition()`.

**Konfiguracja klasy dziedziczącej polega na określeniu statycznych i chronionych pól:**

- `$_tableName` — nazwa tabeli bazy danych;
- `$_tableColumns` — tablica nazw poszczególnych kolumn tabeli (bez klucza podstawowego!).
- `$_tableColumnsJSON` — tablica nazw kolumn tabeli przechowujących obiekty zakodowane w formacie JSON (kod JSON jest automatycznie dekodowany przy odczycie i kodowany przy zapisie rekordu), opcjonalne.
- `$_tableColumnsTimeAtInsert` — tablica nazw kolumn tabeli z uniksowym znacznikiem czasu automatycznie umieszczanym przy tworzeniu rekordu (operacji `INSERT`), opcjonalne.
- `$_tableColumnsTimeAtUpdate` — jak wyżej, ale przy aktualizacji rekordu (operacji `UPDATE`), opcjonalne.
- `$_tablePrimaryKey` — nazwa kolumny klucza podstawowego, domyślnie `id`, opcjonalnie;

Klasa `DatabaseObject` jest zależna od klasy `Database`. Przed użyciem tej klasy, należy rozpocząć połączenie z bazą danych za pomocą `Database::connect()`.

Klasa `DatabaseObject` implementuje metody magiczne `__get()`, `__set()`, `__isset()`, umożliwiając operowanie na polach rekordu jak na polach obiektu, oraz interfejs `IteratorAggregate`, pozwalając na iterowanie po polach rekordu w pętli.
Implementuje również metodę `__debugInfo()` dla funkcji `var_dump()` (dostępne od PHP 5.6).

##`__construct()`

Tworzy nowy rekord tabeli. Wszystkie pola rekordu otrzymują domyślną wartość `null`. Kolumnom określonym w polu `$_tableColumnsJSON` przypisywany jest pusty obiekt (instancja klasy `stdClass`).

##`save()`

Zapisuje rekord. Jeśli rekord jest nowo utworzonym rekordem, używane jest zapytanie SQL `INSERT`, a po pomyślnym dodaniu wartość klucza podstawowego jest uzupełniana. Jeśli rekord już istnieje, jest aktualizowany przy użyciu zapytania `UPDATE`.

Przed zapisem wartości kolumn zdefiniowanych w polu `$_tableColumnsJSON` zamieniane są na ciąg w formacie JSON, a do pól określonych w `$_tableColumnsTimeAtInsert` lub `$_tableColumnsTimeAtUpdate`, w zależności od kontekstu, zapisywany jest aktualny uniksowy znacznik czasu. Jeśli przy zapisie kodu JSON wystąpi błąd, zostanie rzucony wyjątek #14.

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

Standardowo zwracana jest tablica rekordów (instancji klas), a jeśli rekordów brak — pusta tablica. Jeśli argument `$mustBeOnlyOneRecord` jest prawdą, zwracany jest tylko pierwszy rekord bądź fałsz, jeśli brak rekordów. Jednakże, gdy baza danych zwróci więcej niż jeden rekord, rzucany jest wyjątek #11.

Przy pobieraniu rekordów kolumny określone w `$_tableColumnsJSON` są dekodowane do obiektów (instancji klasy `stdClass`). Jeśli przy parsowaniu kodu JSON wystąpi błąd, zostanie rzucony wyjątek #13.