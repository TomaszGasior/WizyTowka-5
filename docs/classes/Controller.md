*abstract* Controller
===

Klasa abstrakcyjna będąca podstawą dla kontrolerów. W systemie WizyTówka kontroler reprezentuje część systemu (panel administracyjny bądź witrynę internetową) i zajmuje się obsługą żądania: renderowaniem strony za pomocą szablonów, filtrowaniem i obsługą żądań POST oraz wykonywaniem wynikających z kontekstu zadań za pośrednictwem innych klas narzędziowych.

Klasy dziedziczące muszą definiować metodę `URL()`, powinny definiować `output()`, a jeśli potrzebna jest obsługa zapytań POST — także metodę `POSTQuery()`.

## `filterPOSTData()`

Filtruje wszystkie dane znajdujące się w tablicy `$_POST`, modyfikując ją bezpośrednio i rekurencyjnie przy użyciu funkcji `htmlspecialchars()`. Metoda ta powinna być wywoływana przed użyciem metody `POSTQuery()`.

Aby wykluczyć pole formularza z filtrowania, należy jego nazwę poprzedzić przedrostkiem `nofilter_`, na przykład: `nofilter_pageContent`.

## `POSTQuery()`

Metoda zajmuje się obsługą żądania POST. Powinna być wywoływana tylko w kontekście żądania POST. Klasy dziedziczące mogą, przesłaniając tę metodę, definiować działanie występujące tylko przy zapytaniach POST.

Domyślnie metoda ta rzuca wyjątek `ControllerException` #1, co oznacza, że dany kontroler zapytań POST nie obsługuje.

## `output()`

Metoda renderująca stronę. Klasy dziedziczące, przesłaniając tę metodę, określają sposób renderowania strony za pomocą szablonów.

Domyślnie metoda ta nie robi nic.

## *abstract static* `URL($target, array $arguments = [])`

Metoda abstrakcyjna, w której klasa dziedzicząca musi zdefiniować sposób generowania odnośników URL do swoich zasobów. Argument `$target` określa miejsce docelowe odnośnika (np. ID strony witryny bądź nazwę strony panelu administracyjnego). Argument `$arguments` określa parametry dodane do query stringa odnośnika (np. `?arg1=val1&arg2=val2`).

Metoda powinna być publiczna i statyczna, by inne kontrolery i klasy mogły uzyskać w razie potrzeby adres URL do danego zasobu (np. link do strony witryny w panelu administracyjnym).

## *protected* `_redirect($target, array $arguments = [])`

Dokonuje przekierowania za pośrednictwem nagłówka HTTP `Location` i przerywa dalsze wykonanie skryptu. Jeśli w argumencie `$target` określony jest pełny adres URL (np. `http://example.org`), przekierowuje do niego bezpośrednio. W innym przypadku obydwa argumenty są wewnętrznie przekazywane do statycznej metody `URL()`, a przekierowanie następuje do uzyskanego od niej adresu.