<?php
/**
 * Konfiguracja aplikacji "Ciemnia".
 *
 * Klucz Google Maps API najlepiej trzymać poza repozytorium
 * (np. jako zmienną środowiskową GOOGLE_MAPS_API_KEY na serwerze),
 * dlatego najpierw próbujemy odczytać ją z środowiska, a dopiero
 * potem korzystamy z wartości domyślnej poniżej.
 *
 * Jak zdobyć klucz: https://console.cloud.google.com/google/maps-apis
 */

define('GOOGLE_MAPS_API_KEY', getenv('GOOGLE_MAPS_API_KEY') ?: 'YOUR_API_KEY');

// Domyślny środek mapy, gdy użytkownik nie poda jeszcze swojej lokalizacji.
define('DEFAULT_MAP_CENTER_LAT', 52.2297); // Warszawa
define('DEFAULT_MAP_CENTER_LNG', 21.0122);
define('DEFAULT_MAP_ZOOM', 8);

/**
 * Klucz do API pogodowego (np. OpenWeatherMap, Visual Crossing).
 * Dopóki nie zostanie ustawiony, sekcja prognozy pokazuje dane przykładowe
 * zwracane przez getWeatherForecast() w index.php.
 */
define('WEATHER_API_KEY', getenv('WEATHER_API_KEY') ?: '');
