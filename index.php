<?php
require_once __DIR__ . '/config/config.php';

// Dane startowe - w prawdziwej aplikacji przyszłyby np. z bazy danych.
$skyStatus = [
    'label'  => 'dobra',
    'bortle' => 4,
];

$nearestSpot = [
    'distance_km' => 18,
    'elevation_m' => 210,
    'bortle'      => 3,
    'area_type'   => 'Pole',
];

/**
 * Ocena warunków obserwacyjnych na podstawie zachmurzenia.
 * W realnej wersji tę logikę można rozbudować o wilgotność, wiatr itd.
 */
function ratingForCloud(int $cloudPct): array
{
    if ($cloudPct <= 20) return ['label' => 'dobra', 'class' => 'good'];
    if ($cloudPct <= 55) return ['label' => 'średnia', 'class' => 'mid'];
    return ['label' => 'słaba', 'class' => 'bad'];
}

/**
 * Zwraca prognozę na najbliższe noce.
 * TODO: podmienić na prawdziwe zapytanie do API pogodowego przy użyciu WEATHER_API_KEY,
 * np. OpenWeatherMap: https://api.openweathermap.org/data/2.5/forecast?...
 */
function getWeatherForecast(): array
{
    return [
        ['date' => 'Dziś, 27 lip',    'temp' => 18, 'cloud' => 15, 'wind' => 8,  'humidity' => 55, 'moon' => 62],
        ['date' => 'Jutro, 28 lip',   'temp' => 17, 'cloud' => 40, 'wind' => 12, 'humidity' => 60, 'moon' => 70],
        ['date' => 'Śr, 29 lip',      'temp' => 19, 'cloud' => 70, 'wind' => 18, 'humidity' => 65, 'moon' => 78],
        ['date' => 'Czw, 30 lip',     'temp' => 16, 'cloud' => 25, 'wind' => 10, 'humidity' => 58, 'moon' => 85],
        ['date' => 'Pt, 31 lip',      'temp' => 15, 'cloud' => 10, 'wind' => 6,  'humidity' => 50, 'moon' => 91],
        ['date' => 'Sob, 1 sie',      'temp' => 17, 'cloud' => 30, 'wind' => 9,  'humidity' => 57, 'moon' => 96],
        ['date' => 'Ndz, 2 sie',      'temp' => 18, 'cloud' => 55, 'wind' => 14, 'humidity' => 62, 'moon' => 99],
    ];
}

$weatherForecast = getWeatherForecast();
?>
<!DOCTYPE html>
<html lang="pl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>SaySky</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,500;9..144,600&family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<canvas id="stars"></canvas>

<div class="app">

  <header>
    <div class="brand">
      <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M12 3C12 3 9.5 6 9.5 10C9.5 14.5 12 21 12 21C12 21 14.5 14.5 14.5 10C14.5 6 12 3 12 3Z" stroke="#6FE9D6" stroke-width="1.2"/>
        <circle cx="12" cy="10" r="1.6" fill="#F3B968"/>
        <circle cx="19" cy="5" r="0.9" fill="#E9EAF7"/>
        <circle cx="4.5" cy="7" r="0.7" fill="#E9EAF7"/>
      </svg>
      <div class="brand-text">
        <h1>SaySky</h1>
        <span>znajdź swoje miejsce</span>
      </div>
    </div>
    <div class="sky-status">
      <span class="dot"></span>
      Widoczność dziś: <b><?= htmlspecialchars($skyStatus['label']) ?></b> · Bortle <?= (int)$skyStatus['bortle'] ?>
    </div>
  </header>

  <div class="layout">

    <aside>
      <form action="" method="get" id="filtersForm">
        <div>
          <div class="section-label">Zasięg</div>
          <div class="field">
            <div class="field-head">
              <label for="radius">Promień szukania</label>
              <span class="val" id="radiusVal">25 km</span>
            </div>
            <input type="range" name="radius" id="radius" min="5" max="150" step="5" value="25">
            <p class="hint">Miejsca w tej odległości od Twojej lokalizacji.</p>
          </div>
        </div>

        <div>
          <div class="section-label">Teren</div>
          <div class="field">
            <div class="field-head">
              <label for="elevation">Minimalna wysokość n.p.m.</label>
              <span class="val" id="elevationVal">150 m</span>
            </div>
            <input type="range" name="elevation" id="elevation" min="0" max="1500" step="50" value="150">
            <p class="hint">Wyżej = mniej mgły i poświaty przy horyzoncie.</p>
          </div>
        </div>

        <div>
          <div class="section-label">Zanieczyszczenie światłem</div>
          <div class="field">
            <div class="field-head">
              <label for="bortle">Maks. na skali Bortle'a</label>
              <span class="val" id="bortleVal">4</span>
            </div>
            <input type="range" name="bortle" id="bortle" class="bortle-slider" min="1" max="9" step="1" value="4">
            <div class="bortle-ticks">
              <span>1</span><span>2</span><span>3</span><span>4</span><span>5</span><span>6</span><span>7</span><span>8</span><span>9</span>
            </div>
            <p class="hint" id="bortleHint">Niebo wiejskie - wyraźnie widoczna Droga Mleczna.</p>
          </div>
        </div>

        <div>
          <div class="section-label">Typ obszaru</div>
          <div class="area-grid" id="areaGroup">
            <div class="area-card active" data-v="pole">
              <svg viewBox="0 0 24 24" fill="none" stroke-width="1.4" stroke-linecap="round"><path d="M3 19h18M4 19c0-4 2-7 3-9M9 19c0-5 1-9 3-11M14 19c1-4 2-7 3-9M19 19c-1-4-2-7-1-11"/></svg>
              <span>Pole</span>
            </div>
            <div class="area-card" data-v="las">
              <svg viewBox="0 0 24 24" fill="none" stroke-width="1.4" stroke-linecap="round"><path d="M12 2 7 10h3l-4 6h5v6h2v-6h5l-4-6h3z"/></svg>
              <span>Las</span>
            </div>
            <div class="area-card" data-v="woda">
              <svg viewBox="0 0 24 24" fill="none" stroke-width="1.4" stroke-linecap="round"><path d="M3 16c1.5-1.5 3-1.5 4.5 0s3 1.5 4.5 0 3-1.5 4.5 0 3 1.5 4.5 0M3 20c1.5-1.5 3-1.5 4.5 0s3 1.5 4.5 0 3-1.5 4.5 0 3 1.5 4.5 0"/><circle cx="12" cy="7" r="3"/></svg>
              <span>Zbiornik wodny</span>
            </div>
          </div>
        </div>

        <input type="hidden" name="lat" id="lat" value="">
        <input type="hidden" name="lng" id="lng" value="">

        <button class="apply-btn" id="applyBtn">Znajdź miejsca obserwacji</button>
      </form>
    </aside>

    <main>
      <div class="map-toolbar">
        <div class="search-box">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/></svg>
          <input type="text" placeholder="Wpisz miejscowość lub adres…">
        </div>
        <div class="legend">
          <span><i style="background:#6FE9D6"></i>Rekomendowane</span>
          <span><i style="background:#F3B968"></i>Twoja lokalizacja</span>
        </div>
      </div>

      <div id="map-wrap">
        <div id="map"></div>
        <!-- Widoczne dopóki nie zostanie podłączony prawdziwy Google Maps JavaScript API -->
        <div class="map-fallback" id="mapFallback">
          <div class="radius-ring"><div class="pin"></div></div>
          <div class="fallback-note">
            Podgląd mapy. Ustaw klucz <code>GOOGLE_MAPS_API_KEY</code> w <code>config/config.php</code>
            (lub jako zmienną środowiskową), aby załadować prawdziwą mapę.
          </div>
        </div>

        <div class="result-card">
          <h3>Najbliższe miejsce</h3>
          <div class="result-row"><span>Odległość</span><b><?= (int)$nearestSpot['distance_km'] ?> km</b></div>
          <div class="result-row"><span>Wysokość</span><b><?= (int)$nearestSpot['elevation_m'] ?> m n.p.m.</b></div>
          <div class="result-row"><span>Bortle</span><b><?= (int)$nearestSpot['bortle'] ?></b></div>
          <div class="result-row"><span>Teren</span><b><?= htmlspecialchars($nearestSpot['area_type']) ?></b></div>
        </div>
      </div>
    </main>

  </div>

  <section class="weather">
    <div class="weather-head">
      <div class="section-label" style="margin-bottom:0;">Prognoza dla obserwacji</div>
      <span class="weather-sub">Zachmurzenie ma największe znaczenie - im niższe, tym lepiej.</span>
    </div>

    <div class="weather-row">
      <?php foreach ($weatherForecast as $day): ?>
        <?php $rating = ratingForCloud($day['cloud']); ?>
        <div class="weather-card">
          <div class="weather-card-head">
            <span class="weather-date"><?= htmlspecialchars($day['date']) ?></span>
            <span class="weather-badge weather-badge--<?= $rating['class'] ?>"><?= htmlspecialchars($rating['label']) ?></span>
          </div>

          <div class="weather-temp"><?= (int)$day['temp'] ?>°</div>

          <div class="weather-metric">
            <svg viewBox="0 0 24 24" fill="none" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"><path d="M7 18a4 4 0 1 1 .7-7.94A5.5 5.5 0 0 1 18 12.5 3.5 3.5 0 0 1 17.5 18H7Z"/></svg>
            <span>Zachmurzenie</span>
            <b><?= (int)$day['cloud'] ?>%</b>
          </div>
          <div class="weather-metric">
            <svg viewBox="0 0 24 24" fill="none" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"><path d="M3 8h11a3 3 0 1 0-3-3M3 16h13a3 3 0 1 1-3 3M3 12h16a2.5 2.5 0 1 0-2.5-2.5"/></svg>
            <span>Wiatr</span>
            <b><?= (int)$day['wind'] ?> km/h</b>
          </div>
          <div class="weather-metric">
            <svg viewBox="0 0 24 24" fill="none" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3s6 6.5 6 11a6 6 0 0 1-12 0c0-4.5 6-11 6-11Z"/></svg>
            <span>Wilgotność</span>
            <b><?= (int)$day['humidity'] ?>%</b>
          </div>

          <div class="weather-moon">
            <svg viewBox="0 0 24 24" fill="none" stroke-width="1.4"><path d="M20 14.5A8.5 8.5 0 1 1 9.5 4a7 7 0 0 0 10.5 10.5Z"/></svg>
            <span>Faza księżyca</span>
            <div class="moon-bar"><i style="width:<?= (int)$day['moon'] ?>%"></i></div>
            <b><?= (int)$day['moon'] ?>%</b>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

    <?php if (WEATHER_API_KEY === ''): ?>
      <p class="weather-note">
        Dane przykładowe. Ustaw <code>WEATHER_API_KEY</code> w <code>config/config.php</code>
        i podepnij prawdziwe API (np. OpenWeatherMap) w <code>getWeatherForecast()</code> w <code>index.php</code>.
      </p>
    <?php endif; ?>
  </section>

</div>

<script>
  // Dane przekazane z PHP do JS (np. klucz API, domyślny środek mapy).
  window.APP_CONFIG = {
    googleMapsApiKey: <?= json_encode(GOOGLE_MAPS_API_KEY) ?>,
    defaultCenter: {
      lat: <?= json_encode(DEFAULT_MAP_CENTER_LAT) ?>,
      lng: <?= json_encode(DEFAULT_MAP_CENTER_LNG) ?>
    },
    defaultZoom: <?= json_encode(DEFAULT_MAP_ZOOM) ?>
  };
</script>
<script src="assets/js/stars.js"></script>
<script src="assets/js/filters.js"></script>
<script src="assets/js/map.js"></script>
<?php if (GOOGLE_MAPS_API_KEY !== 'YOUR_API_KEY'): ?>
<script src="https://maps.googleapis.com/maps/api/js?key=<?= urlencode(GOOGLE_MAPS_API_KEY) ?>&callback=initMap" async defer></script>
<?php endif; ?>
</body>
</html>
