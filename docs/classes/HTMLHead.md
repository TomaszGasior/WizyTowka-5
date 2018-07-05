HTMLHead
===

Klasa generująca zawartość znacznika HTML `<head>`. Umożliwia przejrzyste i proste dodawanie metatagów, arkuszy stylów i skryptów do nagłówka dokumentu HTML. Klasa dziedziczy po klasie `HTMLTag`. Ustawienie klasy CSS w konstruktorze klasy `HTMLTag` w przypadku tej klasy nie ma zastosowania.

Klasa posiada metodę magiczną `__debugInfo()` dla debugowania przy użyciu funkcji `var_dump()`.

Jeśli nie wskazano inaczej, każda metoda zwraca `$this`, co umożliwia tworzenie łańcucha poleceń.

## `base(string $href = null, array $HTMLAttributes = []) : HTMLHead`

Dodaje znacznik `<base>` o atrybucie `href` równym `$href`. Można dodać tylko jeden znacznik `<base>` — jeżeli dodano ten znacznik wcześniej, poprzedni egzemplarz zostanie usunięty. Jeśli `$href` jest puste, usuwa znacznik.

Opcjonalny argument `$HTMLAttributes` umożliwia określenie dodatkowych atrybutów znacznika HTML.

## `title(string $title = null, array $HTMLAttributes = []) : HTMLHead`

Dodaje tytuł strony `$title` w znaczniku `<title>`. Można dodać tylko jeden znacznik `<title>` — jeżeli dodano ten znacznik wcześniej, poprzedni egzemplarz zostanie usunięty. Tytuł jest filtrowany za pomocą `HTML::escape()`. Jeśli `$title` jest puste, usuwa znacznik.

Za pomocą metody `setTitlePattern()` można określić wzór tytułu strony. Gdy wzór jest określony, tytuł `$title` wstawiany jest w określone miejsce wzoru, a do znacznika `<title>` trafia cały zbudowany z jego pomocą ciąg znaków.

Opcjonalny argument `$HTMLAttributes` umożliwia określenie dodatkowych atrybutów znacznika HTML.

## `meta(string $name, string $content, array $HTMLAttributes = []) : HTMLHead`

Dodaje metatag `<meta>` o atrybucie `name` równym `$name` i atrybucie `content` równym `$content`. Atrybut `content` jest filtrowany za pomocą `HTML::escape()`.

Opcjonalny argument `$HTMLAttributes` umożliwia określenie dodatkowych atrybutów znacznika HTML.

## `httpEquiv(string $header, string $content, array $HTMLAttributes = []) : HTMLHead`

Dodaje ekwiwalent nagłówka HTTP poprzez znacznik `<meta>` o atrybucie `http-equiv` równym `$header` i atrybucie `content` równym `$content`. Atrybut `content` jest filtrowany za pomocą `HTML::escape()`.

Opcjonalny argument `$HTMLAttributes` umożliwia określenie dodatkowych atrybutów znacznika HTML.

## `link(string $rel, string $href, array $HTMLAttributes = []) : HTMLHead`

Dodaje znacznik `<link>` o atrybucie `rel` równym `$rel` i atrybucie `href` równym `$href`.

Jeżeli adres określony w argumencie `$href` jest względny, doklejana jest do niego ścieżka określona za pomocą metody `setAssetsPath()`.

Opcjonalny argument `$HTMLAttributes` umożliwia określenie dodatkowych atrybutów znacznika HTML.

## `script(string $src, array $HTMLAttributes = []) : HTMLHead`

Dodaje skrypt poprzez znacznik `<script>` o atrybucie `src` równym `$src`.

Jeżeli adres określony w argumencie `$src` jest względny, doklejana jest do niego ścieżka określona za pomocą metody `setAssetsPath()`.

Opcjonalny argument `$HTMLAttributes` umożliwia określenie dodatkowych atrybutów znacznika HTML.

## `stylesheet(string $href, array $HTMLAttributes = []) : HTMLHead`

Dodaje arkusz stylów CSS. Alias dla `link('stylesheet', $href, ...)`.

## `inlineScript(string $code, array $HTMLAttributes = []) : HTMLHead`

Dodaje skrypt inline w znaczniku `<script>`. Dodanych skryptów inline nie można usunąć.

Opcjonalny argument `$HTMLAttributes` umożliwia określenie dodatkowych atrybutów znacznika HTML.

## `inlineStylesheet(string $stylesheet, array $HTMLAttributes = []) : HTMLHead`

Dodaje arkusz stylów inline w znaczniku `<style>`. Dodanych stylów inline nie można usunąć.

Opcjonalny argument `$HTMLAttributes` umożliwia określenie dodatkowych atrybutów znacznika HTML.

## `removeMeta(string $name, string $content = null) : HTMLHead`

Usuwa metatagi `<meta>` o atrybucie `name` równym `$name` i, jeśli podano, atrybucie `content` jednocześnie równym `$content`.

## `removeHttpEquiv(string $header, string $content = null) : HTMLHead`

Usuwa ekwiwalent nagłówka HTTP w znaczniku `<meta>` o atrybucie `http-equiv` równym `$header` i, jeśli podano, atrybucie `content` jednocześnie równym `$content`.

## `removeScript(string $src) : HTMLHead`

Usuwa skrypt `<script>` o atrybucie `src` równym `$src`.

## `removeLink(string $rel, string $href = null) : HTMLHead`

Usuwa znacznik `<link>` o atrybucie `rel` równym `$rel` i, jeśli podano, atrybucie `href` jednocześnie równym `$href`.

## `removeStylesheet(string $href) : HTMLHead`

Usuwa arkusz stylów CSS. Alias dla `removeLink('stylesheet', $href)`.

## `setTitlePattern(string $titlePattern) : void`

Ustawia wzór tytułu strony uwzględniany przy dołączaniu znacznika `<title>` na `$titlePattern`. Wzór powinien zawierać miejsce na docelowy tytuł strony określono za pomocą symbolu `%s`.

Uwaga: wzór jest uwzględniany już w momencie dodawania tytułu (w momencie wywołania metody `title()`), nie zaś w momencie generowania kodu HTML.

Zwraca prawdę, jeśli tytuł jest prawidłowy, inaczej fałsz.

## `getTitlePattern() : string`

Zwraca bieżący wzór tytułu strony.

## `setAssetsPath(string $assetsPath) : void`

Ustawia ścieżkę do zewnętrznych arkuszy stylów i skryptów na `$assetsPath`. Ścieżka jest dołączana do nazwy każdego dodawanego skryptu i arkusza stylów.

Atrybut `$assetsPath` powinien zawierać wyłącznie samą ścieżkę adresu URL do folderu, nie domenę bądź protokół — na przykład `theme/assets`, a nie `http://example.org/theme/assets`. Początkowy fragment adresu URL określać należy za pomocą metody `setAssetsPathBase()`.

Uwaga: ścieżka jest dołączana do nazwy pliku już w momencie dodawania skryptu bądź arkusza stylów (w momencie wywołania metody `link()`, `stylesheet()` bądź `script()`), nie zaś w momencie generowania kodu HTML.

Metoda nie zwraca wartości.

## `restoreAssetsPath() : bool`

Ustawia ścieżkę do zewnętrznych arkuszy stylów i skryptów na poprzednią (przed ostatnią zmianą dokonaną przy użyciu metody `setAssetsPath()`). Zapamiętywana jest tylko ostatnia wartość, nie można wywołać tej metody kilkukrotnie by przywrócić wcześniejsze wartości.

Metoda zwraca prawdę, jeśli przywrócono poprzednią ścieżkę, inaczej — fałsz.

## `getAssetsPath() : string`

Zwraca bieżącą ścieżkę do zewnętrznych arkuszy stylów i skryptów.

## `setAssetsPathBase(string $assetsPathBase) : void`

Ustawia podstawową część ścieżki dla zewnętrznych arkuszy stylów i skryptów ustawianej za pomocą metody `setAssetsPath()`. Podstawowa część ścieżki doklejana jest przed samą ścieżką. Metoda ta służy głównie do określania początkowej części adresu URL skryptów i arkuszy stylów — na przykład `http://example.org`.

Metoda nie zwraca wartości.

## `getAssetsPathBase() : string`

Zwraca podstawową część ścieżki do zewnętrznych arkuszy stylów i skryptów.

## `output() : void`

Renderuje kod HTML zawartości znacznika `<head>` (bez samego znacznika).

Metoda nie zwraca wartości.