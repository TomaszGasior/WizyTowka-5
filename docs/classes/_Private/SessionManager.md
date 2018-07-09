SessionManager
===

**Ta klasa znajduje się w zagnieżdżonej przestrzeni nazw `_Private` — nie należy ręcznie tworzyć jej instancji. Aby uzyskać dostęp do obiektu tej klasy, należy użyć składni `WT()->session`.**

Menadżer sesji użytkownika. Zarządza sesją, umożliwia zalogowanie i wylogowanie użytkownika.

Rozpoczęcie sesji powoduje utworzenie po stronie klienta ciastka HTTP z identyfikatorem sesji. Dane sesji przypisane do jej identyfikatora (identyfikator użytkownika, data ważności sesji, WAI) są przechowywane w pliku konfiguracyjnym po stronie serwera. Identyfikator sesji jest, dla zwiększenia bezpieczeństwa, automatycznie zmieniany co kilka minut. Przy wylogowaniu ciastko HTTP i dane z pliku konfiguracyjnego są usuwane.

WAI („where am I?”) — to ciąg znaków używany do zidentyfikowania środowiska (przeglądarki internetowej) używanego przy zalogowaniu. Jest to hasz sha512 z ciągu znaków złożonego z informacji o przeglądarce i użytkowniku (m.in. user agent i adres IP).


## `__construct(string $cookieName, ConfigurationFile $config)`

Inicjuje menadżera sesji użytkownika. Jeśli sesja straciła ważność lub WAI jest niewłaściwy, sesja jest niszczona.

Argument `$cookieName` określa nazwę ciasteczka HTTP używanego do przechowywana identyfikatora sesji — nazwa ciasteczka musi być unikalna. Jako argument `$config` należy przekazać instancję klasy `ConfigurationFile` z plikiem konfiguracyjnym sesji użytkowników.

## `isUserLoggedIn() : bool`

Zwraca prawdę, jeśli użytkownik jest zalogowany bądź fałsz, jeśli nie jest.

## `getUserId() : ?int`

Zwraca identyfikator zalogowanego użytkownika, bądź `null`, jeśli użytkownik nie jest zalogowany.

## `logIn(int $userId, string $sessionDuration) : void`

Dokonuje zalogowania użytkownika o identyfikatorze określonym w argumencie `$userId`. Argument `$sessionDuration` określa czas trwania sesji użytkownika w sekundach.

Uwaga: użytkownik zostanie zalogowany dopiero przy następnym żądaniu HTTP! W bieżącym żądaniu menadżer sesji będzie zachowywać się, jakby użytkownik nie był zalogowany. Zalogowanie nie nastąpi, jeśli klient ma wyłączoną funkcję ciasteczek w przeglądarce.

Jeżeli użytkownik już jest zalogowany, zostanie rzucony wyjątek `SessionManagerException` #2.

## `logOut() : void`

Wylogowuje aktualnie zalogowanego użytkownika oraz usuwa z pliku konfiguracyjnego wszystkie sesje, których ważność upłynęła.

Jeżeli żaden użytkownik nie jest zalogowany, zostanie rzucony wyjątek `SessionManagerException` #1.

## `closeOtherSessions() : bool`

Wylogowuje wszystkie pozostałe sesje aktualnie zalogowanego użytkownika. Zwraca prawdę, jeśli istniała choć jedna inna sesja aktualnie zalogowanego użytkownika, która została usunięta; w innym przypadku — zwraca fałsz.

Jeżeli żaden użytkownik nie jest zalogowany, zostanie rzucony wyjątek `SessionManagerException` #1.

## `setExtraData(string $name, $value) : void`

Zapisuje w bieżącej sesji dodatkowe dane o wartości `$value` możliwe do późniejszego odczytania pod nazwą `$name`.
Wartość `$value` musi być wartością skalarną lub tablicą, inaczej rzucony zostanie wyjątek `SessionManagerException` #3. Aby usunąć klucz z dodatkowych danych, należy jako wartość `$value` podać `null`.

Jeżeli żaden użytkownik nie jest zalogowany, zostanie rzucony wyjątek `SessionManagerException` #1.

## `getExtraData(string $name)`

Zwraca dodatkowe dane zapisane pod nazwą `$name` przy użyciu metody `setExtraData()`. Jeżeli pod nazwą `$name` nie zostało nic zapisane, zwraca `null`.

Jeżeli żaden użytkownik nie jest zalogowany, zostanie rzucony wyjątek `SessionManagerException` #1.