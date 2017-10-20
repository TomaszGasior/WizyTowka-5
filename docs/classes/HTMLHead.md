HTMLHead
===

Klasa generująca zawartość znacznika HTML `<head>`. Umożliwia przejrzyste i proste dodawanie metatagów, arkuszy stylów i skryptów do nagłówka dokumentu HTML. Klasa dziedziczy po klasie `HTMLTag`. Ustawienie klasy CSS z klasy `HTMLTag` w przypadku tej klasy nie ma zastosowania.

Jeśli nie wskazano inaczej, każda metoda zwraca `$this`, co umożliwia tworzenie łańcucha poleceń.

## `base($href = null, array $HTMLAttributes = [])`

Dodaje znacznik `<base>` o atrybucie `href` równym `$href`. Jeśli `$href` jest puste, usuwa znacznik.

Opcjonalny argument `$HTMLAttributes` umożliwia określenie dodatkowych atrybutów znacznika HTML.

## `title($title = null, array $HTMLAttributes = [])`

Dodaje tytuł strony `$title` w znaczniku `<title>`. Tytuł jest filtrowany za pomocą funkcji `htmlspecialchars()`. Jeśli `$title` jest puste, usuwa znacznik.

Opcjonalny argument `$HTMLAttributes` umożliwia określenie dodatkowych atrybutów znacznika HTML.

## `meta($name, $content, array $HTMLAttributes = [])`

Dodaje metatag `<meta>` o atrybucie `name` równym `$name` i atrybucie `content` równym `$content`. Atrybut `content` jest filtrowany za pomocą funkcji `htmlspecialchars()`.

Opcjonalny argument `$HTMLAttributes` umożliwia określenie dodatkowych atrybutów znacznika HTML.

## `httpEquiv($header, $content, array $HTMLAttributes = [])`

Dodaje ekwiwalent nagłówka HTTP poprzez znacznik `<meta>` o atrybucie `http-equiv` równym `$header` i atrybucie `content` równym `$content`. Atrybut `content` jest filtrowany za pomocą funkcji `htmlspecialchars()`.

Opcjonalny argument `$HTMLAttributes` umożliwia określenie dodatkowych atrybutów znacznika HTML.

## `link($rel, $href, array $HTMLAttributes = [])`

Dodaje znacznik `<link>` o atrybucie `rel` równym `$rel` i atrybucie `href` równym `$href`.

Jeżeli adres określony w argumencie `$href` jest względny, doklejana jest do niego ścieżka określona za pomocą metody `setAssetsPath()`.

Opcjonalny argument `$HTMLAttributes` umożliwia określenie dodatkowych atrybutów znacznika HTML.

## `script($src, array $HTMLAttributes = [])`

Dodaje skrypt poprzez znacznik `<script>` o atrybucie `src` równym `$src`.

Jeżeli adres określony w argumencie `$src` jest względny, doklejana jest do niego ścieżka określona za pomocą metody `setAssetsPath()`.

Opcjonalny argument `$HTMLAttributes` umożliwia określenie dodatkowych atrybutów znacznika HTML.

## `stylesheet($href, array $HTMLAttributes = [])`

Dodaje arkusz stylów CSS. Alias dla `link('stylesheet', $href, ...)`.

## `inlineScript($code, array $HTMLAttributes = [])`

Dodaje skrypt inline w znaczniku `<script>`. Dodanych skryptów inline nie można usunąć.

Opcjonalny argument `$HTMLAttributes` umożliwia określenie dodatkowych atrybutów znacznika HTML.

## `inlineStylesheet($stylesheet, array $HTMLAttributes = []`

Dodaje arkusz stylów inline w znaczniku `<style>`. Dodanych stylów inline nie można usunąć.

Opcjonalny argument `$HTMLAttributes` umożliwia określenie dodatkowych atrybutów znacznika HTML.

## `removeMeta($name, $content = null)`

Usuwa metatagi `<meta>` o atrybucie `name` równym `$name` i, jeśli podano, atrybucie `content` jednocześnie równym `$content`.

## `removeHttpEquiv($header, $content = null)`

Usuwa ekwiwalent nagłówka HTTP w znaczniku `<meta>` o atrybucie `http-equiv` równym `$header` i, jeśli podano, atrybucie `content` jednocześnie równym `$content`.

## `removeScript($src)`

Usuwa skrypt `<script>` o atrybucie `src` równym `$src`.

## `removeLink($rel, $href = null)`

Usuwa znacznik `<link>` o atrybucie `rel` równym `$rel` i, jeśli podano, atrybucie `href` jednocześnie równym `$href`.

## `removeStylesheet($href)`

Usuwa arkusz stylów CSS. Alias dla `removeLink('stylesheet', $href)`.

## `setAssetsPath($assetsPath)`

Ustawia ścieżkę do zewnętrznych arkuszy stylów i skryptów na `$assetsPath`. Ścieżka jest dołączana do nazwy każdego dodawanego skryptu i arkusza stylów.

Uwaga: ścieżka jest dołączana do nazwy pliku już w momencie dodawania skryptu bądź arkusza stylów (w momencie wywołania metody `link()`, `stylesheet()` bądź `script()`), nie zaś w momencie generowania kodu HTML.

Metoda nie zwraca wartości.

## `getAssetsPath($assetsPath)`

Zwraca bieżącą ścieżkę do zewnętrznych arkuszy stylów i skryptów.

## `output()`

Renderuje kod HTML zawartości znacznika `<head>` (bez samego znacznika).

Metoda nie zwraca wartości.