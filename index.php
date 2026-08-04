<?php
require_once __DIR__ . '/config/config.php';

// Dane startowe — w prawdziwej aplikacji przyszłyby np. z bazy danych.
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

      <div>
        <div class="section-label">Zasięg</div>
        <div class="field">
          <div class="field-head">
            <label for="radius">Promień szukania</label>
            <span class="val" id="radiusVal">25 km</span>
          </div>
          <input type="range" id="radius" min="5" max="150" step="5" value="25">
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
          <input type="range" id="elevation" min="0" max="1500" step="50" value="150">
          <p class="hint">Wyżej = mniej mgły i poświaty przy horyzoncie.</p>
        </div>
      </div>

      <div>
        <div class="section-label">Zanieczyszczenie światłem</div>
        <div class="field">
          <div class="field-head">
            <label>Maks. na skali Bortle'a</label>
            <span class="val" id="bortleVal">4</span>
          </div>
          <div class="bortle" id="bortleGroup">
            <button data-v="2">1–2</button>
            <button data-v="4" class="active">3–4</button>
            <button data-v="6">5–6</button>
            <button data-v="9">7–9</button>
          </div>
          <p class="hint">1 = niebo idealnie ciemne, 9 = centrum dużego miasta.</p>
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

      <button class="apply-btn" id="applyBtn">Znajdź miejsca obserwacji</button>

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
