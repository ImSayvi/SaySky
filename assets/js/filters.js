/**
 * Interakcje panelu filtrów: promień, wysokość, skala Bortle'a, typ obszaru.
 */
(function () {
  const radius = document.getElementById('radius');
  const radiusVal = document.getElementById('radiusVal');
  function updateRadiusFill() {
    const pct = (radius.value - radius.min) / (radius.max - radius.min) * 100;
    radius.style.setProperty('--fill', pct + '%');
    radiusVal.textContent = radius.value + ' km';
  }
  radius.addEventListener('input', updateRadiusFill);
  updateRadiusFill();

  const elevation = document.getElementById('elevation');
  const elevationVal = document.getElementById('elevationVal');
  function updateElevationFill() {
    const pct = (elevation.value - elevation.min) / (elevation.max - elevation.min) * 100;
    elevation.style.setProperty('--fill', pct + '%');
    elevationVal.textContent = elevation.value + ' m';
  }
  elevation.addEventListener('input', updateElevationFill);
  updateElevationFill();

  const bortleGroup = document.getElementById('bortleGroup');
  const bortleVal = document.getElementById('bortleVal');
  bortleGroup.addEventListener('click', (e) => {
    const btn = e.target.closest('button');
    if (!btn) return;
    bortleGroup.querySelectorAll('button').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    bortleVal.textContent = btn.textContent;
  });

  const areaGroup = document.getElementById('areaGroup');
  areaGroup.addEventListener('click', (e) => {
    const card = e.target.closest('.area-card');
    if (!card) return;
    areaGroup.querySelectorAll('.area-card').forEach(c => c.classList.remove('active'));
    card.classList.add('active');
  });

  document.getElementById('applyBtn').addEventListener('click', () => {
    const filters = {
      radiusKm: Number(radius.value),
      minElevationM: Number(elevation.value),
      maxBortle: Number(bortleGroup.querySelector('.active').dataset.v),
      areaType: areaGroup.querySelector('.active').dataset.v
    };

    // Tutaj podłącz właściwe zapytanie, np. do własnego API:
    // fetch('api/szukaj.php', {
    //   method: 'POST',
    //   headers: { 'Content-Type': 'application/json' },
    //   body: JSON.stringify(filters)
    // }).then(r => r.json()).then(renderujWyniki);

    console.log('Filtry obserwacji:', filters);
  });
})();
