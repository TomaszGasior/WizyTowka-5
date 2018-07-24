UploadedFilesHandler
===

Klasa operująca na plikach przesłanych przez użytkownika za pomocą formularza HTML. Przenosi nowo wysłane pliki do katalogu przesłanych plików systemu. Oferuje też dodatkowe metody pozwalające na uzyskanie informacji o funkcji przesyłania plików w PHP.

Aby skorzystać z klasy, należy stworzyć formularz HTML zawierający następującą konstrukcję:

	<form method="post" enctype="multipart/form-data">
		<input name="sendingFiles[]" type="file" multiple>
		<button>Wyślij</button>
	</form>

Przy odbieraniu danych należy przekazać zawartość zmiennej `$_FILES['sendingFiles']` metodzie `handleSentFiles()`. Wymagane jest użycie atrybutu `multiple` — klasa oczekuje możliwości przesłania wielu plików na raz.

## `__construct(int $maxFileSizeBytes = 0, bool $lowercaseFileNames = true)`

Argument `$maxFileSizeBytes` określa maksymalną wielkość jednego przesłanego pliku w bajtach. Jeżeli będzie równy zero, wielkość pliku nie będzie ograniczona przez skrypt. Należy jednakże pamiętać, że maksymalną wielkość przesłanych danych określa też konfiguracja interpretera PHP — zobacz metodę `getMaxFileSize()`.

Ustawienie drugiego argumentu `$lowercaseFileNames` na prawdę sprawia, że wielkość liter w nazwach wysłanych plików zostanie zamieniona na małe.

## `handleSentFiles(array $_FILESField) : void`

Metoda podejmuje próbę przeniesienia wszystkich wysłanych plików do systemowego katalogu. Jako argument `$_FILESField` należy podać element tablicy `$_FILES` zgodnie z przykładem powyżej. Jeżeli przenoszenie jednego z plików nie powiedzie się, metoda zachowuje informacje o błędzie i kontynuuje próby na kolejnych plikach.

Zostanie rzucony wyjątek `UploadedFilesHandlerException` #1, jeśli struktura elementu tablicy `$_FILES` będzie nieprawidłowa (niezgodna z zachowaniem PHP). Nie należy wywoływać tej metody więcej niż jeden raz, inaczej zostanie rzucony wyjątek `UploadedFilesHandlerException` #2.

## `countMoved() : ?int`

Zwraca liczbę wysłanych plików pomyślnie przeniesionych do katalogu systemowego. Zwraca `null`, jeśli nie wywołano jeszcze `handleSentFiles()`.

## `countErrors() : ?int`

Zwraca liczbę wysłanych plików, których nie udało się pomyślnie przenieść do katalogu systemowego. Zwraca `null`, jeśli nie wywołano jeszcze `handleSentFiles()`.

## `getErrors() : ?array`

Zwraca tablicę z listą plików, których nie udało się pomyślnie przenieść do katalogu systemowego. Zwraca `null`, jeśli nie wywołano jeszcze `handleSentFiles()`.

Klucze tablicy zawierają nazwy plików, wartości elementów tablicy zawierają wartości stałych. Jeśli błąd został zgłoszony przez interpreter PHP, będą to wartości stałych określonych w [dokumentacji PHP nt. wysyłania plików](http://php.net/manual/en/features.file-upload.errors.php), natomiast jeżeli błąd zostanie wychwycony wewnątrz klasy, będą to wartości następujących stałych klasowych:

* `ERROR_MOVE_UPLOADED_FILE` — nie udało się przenieść pliku do katalogu systemowego, wykonanie funkcji `move_uploaded_file()` było niepomyślne;
* `ERROR_FILE_STILL_EXISTS` — mimo próby uniknięcia kolizji nazw plików, plik o używanej nazwie istnieje w już wysłanych plikach (jeżeli plik o danej nazwie już istnieje, dopisywany jest do niej aktualny uniksowy znacznik czasu — ten błąd występuje jedynie, gdy nawet to nie rozwiązuje konfliktu);
* `ERROR_FILE_TOO_BIG` — plik jest zbyt duży (ten błąd występuje jedynie, jeśli najbardziej rygorystycznym ograniczeniem była wartość w bajtach określona w konstruktorze — nie jest uwzględniania konfiguracja PHP).

## `getMaxFileSize() : ?int`

Zwraca maksymalną wielość jednego przesłanego pliku w bajtach. Uwzględniana jest wartość określona w konstruktorze klasy oraz konfiguracja PHP (ustawienia `php.ini`: `post_max_size`, `upload_max_filesize`) — zwracana jest zawsze najbardziej rygorystyczna wartość. Zwraca `null`, jeśli nie udało się ustalić wartości.

## `getMaxFilesNumber() : ?int`

Zwraca maksymalną możliwą liczbę przesyłanych jednocześnie plików. Zwraca `null`, jeśli nie udało się ustalić wartości.

## `isSendingFilesEnabled() : bool`

Zwraca prawdę, jeśli przesyłanie plików jest włączone w konfiguracji PHP, inaczej — fałsz.