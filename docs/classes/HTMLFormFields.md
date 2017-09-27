HTMLFormFields
===

Klasa generująca kod HTML pól formularzy (nie — całych formularzy).

Przykładowy kod generowany przez klasę (z dodanymi dla czytelności wcięciami i przełamaniami):

	<fieldset>
		<div>
			<label for="example1">Pole tekstowe</label>
			<span>
				<input type="text" name="example1" value="wartość" id="example1">
			</span>
		</div>
		<div>
			<label for="example2">Pole liczbowe</label>
			<span>
				<input type="number" name="example2" value="25" id="example2">
			</span>
		</div>
		<div>
			<label for="example3">Lista wyboru</label>
			<span>
				<select name="example3" id="example3">
					<option value="val1">Pole 1</option>
					<option value="val2" selected>Pole 2</option>
					<option value="val3">Pole 3</option>
				</select>
			</span>
		</div>
		<div>
			<input type="checkbox" name="example4" checked id="example4">
			<label for="example4">Checkbox</label>
		</div>
	</fieldset>

Pola zgrupowane są w znaczniku HTML `<fieldset>`. Każde pole wraz z etykietą otoczone jest blokiem `<div>`. Klasa generuje poprawny semantycznie kod, przypisując etykiety do pól za pośrednictwem identyfikatorów. Kontrolki pól otoczone są znacznikiem `<span>` dla łatwiejszego stylizowania przez CSS.
Ze względu na stylizację CSS oraz czytelność, nieco inny układ ma kod HTML pól typu `checkbox` i `radio` — kolejność etykiety i kontrolki jest odwrócona, kontrolka nie jest otoczona znacznikiem.

Elementy są renderowane w kolejności dodawania. Klasa nie generuje kodu znacznika `<form>` i przycisków.

Klasa implementuje metodę magiczną `__debugInfo()` dla debugowania przy użyciu funkcji `var_dump()`.

Jeśli nie wskazano inaczej, każda metoda zwraca `$this`, co umożliwia tworzenie łańcucha poleceń.

## `__construct($CSSClass = null)`

Konstruktor umożliwia przypisanie znacznikowi `<fieldset>` klasy CSS określonej w opcjonalnym argumencie `$CSSClass`.

## `__toString()`

Aby wygenerować kod HTML pól formularzy, należy instancję klasy rzutować na typ ciągu znaków.

## `text($label, $name, $value, array $HTMLAttributes = [])`

Dodaje pole `<input type="text">`.

Jako argument `$label` należy podać etykietę pola. Argument `$name` zawierać powinien nazwę pola (atrybut HTML `name`), która zostanie użyta przy odbiorze informacji z tablicy `$_POST` lub `$_GET`. W argumencie `$value` podać należy bieżącą wartość pola (atrybut HTML `value`). Z wartości pola usuwane są przełamania wierszy.

Opcjonalny argument `$HTMLAttributes` umożliwia określenie dodatkowych atrybutów kontrolki formularza w HTML (takich jak `disabled`, `readonly`, `tabindex`, `spellcheck`, `autofocus`, `accesskey` bądź jakichkolwiek innych). Należy podać go jako tablicę — jej klucze zostaną nazwami atrybutów, a wartości ich wartościami. Nie ma możliwości nadpisania atrybutów generowanych przez metodę (takich jak `id`, `value`, `name`).
Jeżeli atrybut jest typu logicznego i ma wartość prawda, zostanie wygenerowany w kodzie HTML bez żadnej wartości (np. `<input checked>`), jeżeli ma wartość fałsz — zostanie pominięty.

## `number($label, $name, $value, array $HTMLAttributes = [])`

Dodaje pole `<input type="number">`

Zobacz opis argumentów metody `text()`.

Wartość `$value` musi być liczbą, inaczej zostanie użyte zero.

## `password($label, $name, array $HTMLAttributes = [])`

Dodaje pole `<input type="password">`

Zobacz opis argumentów metody `text()`.

Nie ma możliwości podania wartości dla tego pola.

## `checkbox($label, $name, $currentValue, array $HTMLAttributes = [])`

Dodaje pole zaznaczenia `<input type="checkbox">`

Zobacz opis argumentów metody `text()`.

Jako `$currentValue` należy podać bieżącą wartość przełącznika. Zostanie ona rzutowana na typ logiczny. Jeśli wartość będzie prawdą – checkbox zostanie zaznaczony, jeśli fałszem — będzie odznaczony.

## `radio($label, $name, $fieldValue, $currentValue, array $HTMLAttributes = [])`

Dodaje pole wyboru `<input type="radio">`

Zobacz opis argumentów metody `text()`.

W argumencie `$fieldValue` należy wprowadzić wartość pola wyboru (atrybut HTML `value`). Jako `$currentValue` należy podać bieżącą wartość przełącznika. Jeżeli `$fieldValue` i `$currentValue` będą sobie równe, pole zostanie zaznaczone, w innym wypadku będzie odznaczone.

## `option(...)`

Alias dla metody `radio()`.

## `textarea($label, $name, $content, array $HTMLAttributes = [])`

Dodaje pole wieloliniowe `<textarea>`.

Zobacz opis argumentów metody `text()`.

Argument `$content` (nazwany inaczej dla rozróżnienia od atrybutu HTML `value`) przyjmuje jako wartość treść umieszczaną wewnątrz znacznika `<textarea>`. W przeciwieństwie to pola tekstowego można w niej użyć przełamania wierszy.

## `select($label, $name, $selected, array $valuesList, array $HTMLAttributes = [])`

Dodaje listę wyboru `<select>`.

Zobacz opis argumentów metody `text()`.

Argument `$valueList` jest tablicą zawierającą opcje możliwe do wybrania z listy. Klucze tablicy są wartościami opcji (`<option value="...">`), a wartości tablicy są etykietami opcji.

Argument `$selected` określa wartość aktualnie zaznaczonej opcji.

## `textWithHints($label, $name, $value, array $hints, array $HTMLAttributes = [])`

Działa dokładnie tak samo jak `text()` z tą różnicą, że umożliwia dodanie do pola tekstowego podpowiedzi (znacznik HTML `<datalist>`) określonych w argumencie `$hints`.

## `remove($name)`

Usuwa wszystkie pola o nazwie (atrybucie HTML `name`) określonym w argumencie `$name`.