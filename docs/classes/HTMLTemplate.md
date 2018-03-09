HTMLTemplate
===

Klasa przeznaczona do obsługi szablonów HTML. W systemie WizyTówka szablonem HTML jest plik PHP zawierający kod HTML oraz polecenia operujące na zmiennych. Zmienne są przypisywane do obiektu szablonu, a następnie eksportowane do skryptu szablonu przy renderowaniu.

Przykładowy plik szablonu może prezentować się następująco:

	<!doctype html>
	<html lang="pl">
		<head>
			<meta charset="utf-8">
			<title><?= $title ?></title>
		</head>
		<body>
			<h1><?= $header ?></h1>
			<?= $content ?>
		</body>
	</html>

Klasa implementuje metody magiczne `__get()`, `__set()`, `__isset()`, `__unset()`, umożliwiając operowanie na poszczególnych zmiennych szablonu jak na polach obiektu — aby dodać zmienną do szablonu, należy utworzyć nową zmienną w obiekcie. Implementuje też interfejsy `Countable` i `IteratorAggregate`, by umożliwiać iterowanie w pętli oraz policzenie zmiennych, a także metodę `__debugInfo()` dla funkcji `var_dump()`.

Aby ustawić zmienną szablonu, należy stworzyć nową zmienną w obiekcie szablonu. **Zmienne szablonu są automatycznie escapowane za pomocą `HTML::escape()`.** Tablice są escapowane rekurencyjnie. Instancje klasy `stdClass` i iteratory (obiekty klas implementujących interfejs `Traversable`) są konwertowane do tablic, escapowane, a następnie konwertowane do obiektów `stdClass`. Instancje klas `HTMLTemplate` i `HTMLTag` nie są escapowane. Zostanie rzucony wyjątek `HTMLTemplateException` #2, jeśli podana dla zmiennej wartość będzie nieoczekiwanego typu. Aby uniknąć escapowania, należy użyć metody `setRaw()`.

## `__construct($templateName = null, $templatePath = null)`

Konstruktor klasy umożliwia określenie globalnej nazwy szablonu (argument `$templateName`) oraz ścieżki do katalogu gromadzącego szablony (argument `$templatePath`).

## `__toString()`

Jeśli zostanie dokonane rzutowanie obiektu na ciąg znaków, wykonana zostanie metoda `render()`. Wyrenderowany szablon jest wtedy zwracany, nie wyświetlany.

## `setRaw($variable, $value)`

Ustawia zmienną szablonu `$variable` o wartości `$value` podobnie jak zwyczajnie utworzenie zmiennej na obiekcie, ale **z pominięciem escapowania**. Używając tej metody, jesteś odpowiedzialny za prawidłowe zabezpieczenie wartości zmiennej przed niechcianym kodem HTML.

## `setTemplate($templateName)`

Metoda umożliwia określenie globalnej nazwy szablonu. Nazwa szablonu nie powinna zawierać rozszerzenia pliku. Jeśli nazwa nie zostanie określona przy wywołaniu metody `render()`, zostanie użyta globalna nazwa szablonu.

## `getTemplate()`

Metoda zwraca globalną nazwę szablonu określoną przez `setTemplate()` bądź w konstruktorze.

## `setTemplatePath($templatePath)`

Metoda umożliwia określenie ścieżki do katalogu gromadzącego pliki szablonów.

## `getTemplatePath()`

Metoda zwraca ścieżkę do katalogu szablonów określoną przez `setTemplatePath()` bądź w konstruktorze.

## `render($templateName = null)`

Metoda renderuje szablon — eksportuje zmienne do skryptu PHP szablonu, uruchamia go, kierując go na wyjście. Aby zwrócić wyrenderowany szablon np. celem zapisania go do zmiennej, należy zamiast wywołania metody `render()`, rzutować obiekt na ciąg znaków.

Jeśli argument `$templateName` jest określony, używany jest szablon o wskazanej nazwie. W innym wypadku używany jest globalny szablon, którego nazwę określa się za pośrednictwem konstruktora bądź metody `setTemplate()`. Nazwa nie powinna zawierać rozszerzenia `.php`. Jeśli nazwa szablonu nie zostanie w ogóle określona, zostanie rzucony wyjątek `HTMLTemplateException` #1.

Wszelkie klasy wbudowane w system WizyTówka (np. `HTMLFormFields`, `HTMLMessage`, `HTMLElementsList`) mogą być wywoływane w formie skróconej — bez podawania przestrzeni nazw. Na przykład zamiast `WizyTowka\HTMLFormFields` można stworzyć instancję klasy `HTMLFormFields`.

Uwaga: jeśli wewnątrz kodu szablonu zostanie rzucony wyjątek, zostanie on obsłużony wewnętrznie przez metodę. Szablon nie zostanie wtedy w ogóle wyrenderowany, a w jego miejscu pojawi się uproszczony komunikat o błędzie.