HTMLHead
===

Klasa generująca kod HTML znacznika `<head>`. Umożliwia przejrzyste i proste dodawanie metatagów, arkuszy stylów i skryptów do nagłówka dokumentu HTML. Generowana jest zawartość znacznika `<head>` bez samego znacznika dookoła.

Jeśli nie wskazano inaczej, każda metoda zwraca `$this`, co umożliwia tworzenie łańcucha poleceń.

## `__toString()`

Aby wygenerować kod HTML znacznika `<head>`, należy rzutować obiekt na ciąg znaków. Kod jest zwracany, nie wyświetlany.

## `setAssetsPath($assetsPath)`

Umożliwia ustawienie określonej w argumencie `$assetsPath` ścieżki do zewnętrznych arkuszy stylów i skryptów. Ścieżka jest dołączana do nazwy każdego dodawanego skryptu i arkusza stylów. Domyślnie ścieżka przyjmuje wartość `system/assets`.

Uwaga: ścieżka jest dołączana do nazwy pliku w momencie dodawania skryptu bądź arkusza stylów (w momencie wywołania metody `addScript()` bądź `addStyle()`). Oznacza to, że jeśli najpierw dodasz skrypty i arkusze stylów, a potem określisz ścieżkę, ścieżka ta nie zostanie uwzględniona w dodanych już skryptach i stylach.

## `setBase($base)`

Określa zawartość atrybutu `href` znacznika `<base>`. Jeśli nie zostanie określona, znacznik nie zostanie dołączony do kodu.

## `setTitle($title)`

Określa tytuł witryny umieszczany w znaczniku `<title>`. Jeśli nie zostanie wskazany, użyty zostanie domyślny: `Untitled HTML document`.

Tytuł jest filtrowany za pomocą funkcji `htmlspecialchars()`.

## `setMeta($name, $value)`

Umożliwia określenie metatagu o nazwie określonej argumentem `$name` i wartości określonej argumentem `$value`.

Wartość jest filtrowana za pomocą funkcji `htmlspecialchars()`.

## `setHttpEquiv($name, $value)`

Umożliwia określenie ekwiwalentu nagłówka HTTP wskazanego w argumencie `$name` z wartością `$value`.

Wartość jest filtrowana za pomocą funkcji `htmlspecialchars()`.

## `addStyle($stylePath, $media = null)`

Dodaje zewnętrzny arkusz stylów do nagłówka. Do ścieżki/nazwy pliku określonej argumentem `$stylePath` dodawana jest ścieżka wskazana za pomocą metody `setAssetsPath()`.

Opcjonalny argument `$media` umożliwia określenie reguły `@media`, dokładniej: argumentu `media` znacznika `<link rel="stylesheet">`.

## `addScript($scriptPath, $asyncInsteadDefer = false)`

Dodaje zewnętrzny skrypt JavaScript do nagłówka. Do ścieżki/nazwy pliku określonej argumentem `$stylePath` dodawana jest ścieżka wskazana za pomocą metody `setAssetsPath()`.

Domyślnie skrypty zewnętrzne otrzymują atrybut `defer`. Opcjonalny argument `$asyncInsteadDefer` ustawiony na prawdę umożliwia zastąpienie tego atrybutu atrybutem `async`.

## `addInlineStyle($styleCode)`

Dodaje do nagłówka styl CSS inline określony w argumencie `$styleCode`.

## `addInlineScript($scriptCode)`

Dodaje do nagłówka skrypt inline określony w argumencie `$scriptCode`.