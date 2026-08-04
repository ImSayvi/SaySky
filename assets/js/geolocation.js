(function() {
    const latInput = document.getElementById('lat');
    const lngInput = document.getElementById('lng');

    if (!latInput || !lngInput) return;

    function updateLocationInputs(position) {
        latInput.value = position.coords.latitude.toFixed(6);
        lngInput.value = position.coords.longitude.toFixed(6);
    }

    function useDefaultLocation() {
        latInput.value = window.APP_CONFIG.defaultCenter.lat.toFixed(6);
        lngInput.value = window.APP_CONFIG.defaultCenter.lng.toFixed(6);
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