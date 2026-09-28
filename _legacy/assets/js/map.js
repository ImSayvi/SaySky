/**
 * Inicjalizacja Google Maps.
 *
 * initMap() jest wywoływana automatycznie jako callback skryptu Google Maps,
 * który index.php dołącza tylko wtedy, gdy w config/config.php ustawiono
 * prawdziwy GOOGLE_MAPS_API_KEY. Do tego czasu widoczny jest #mapFallback
 * zdefiniowany w index.php / style.css.
 */
function initMap() {
  const fallback = document.getElementById('mapFallback');
  if (fallback) fallback.style.display = 'none';

  const center = (window.APP_CONFIG && window.APP_CONFIG.defaultCenter)
    || { lat: 52.2297, lng: 21.0122 }; // domyślnie: Warszawa
  const zoom = (window.APP_CONFIG && window.APP_CONFIG.defaultZoom) || 8;

  const map = new google.maps.Map(document.getElementById('map'), {
    center,
    zoom,
    disableDefaultUI: true,
    zoomControl: true,
    styles: [
      { elementType: 'geometry', stylers: [{ color: '#0d1130' }] },
      { elementType: 'labels.text.fill', stylers: [{ color: '#8b90bc' }] },
      { elementType: 'labels.text.stroke', stylers: [{ color: '#080b1f' }] },
      { featureType: 'water', elementType: 'geometry', stylers: [{ color: '#0a1330' }] },
      { featureType: 'road', elementType: 'geometry', stylers: [{ color: '#161b3d' }] },
      { featureType: 'poi', stylers: [{ visibility: 'off' }] }
    ]
  });

  new google.maps.Marker({ position: center, map, title: 'Twoja lokalizacja' });
}

window.initMap = initMap;
