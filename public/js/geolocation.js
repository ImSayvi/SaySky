(function() {
    const latInput = document.getElementById('lat');
    const lngInput = document.getElementById('lng');

    const DEFAULT_CENTER = { lat: 52.2297, lng: 21.0122 }; // Warszawa

    // Brak inputów albo lokalizacja już przyszła w URL po wysłaniu formularza.
    if (!latInput || !lngInput || latInput.value) return;

    function updateLocationInputs(position) {
        latInput.value = position.coords.latitude.toFixed(6);
        lngInput.value = position.coords.longitude.toFixed(6);
    }

    function useDefaultLocation() {
        latInput.value = DEFAULT_CENTER.lat.toFixed(6);
        lngInput.value = DEFAULT_CENTER.lng.toFixed(6);
    }

    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            updateLocationInputs,
            useDefaultLocation
        );
    } else {
        useDefaultLocation();
    }
})();