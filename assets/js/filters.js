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

  const bortle = document.getElementById('bortle');
  const bortleVal = document.getElementById('bortleVal');
  const bortleHint = document.getElementById('bortleHint');
  const BORTLE_HINTS = {
    1: 'Niebo idealnie ciemne - Droga Mleczna rzuca cień.',
    2: 'Typowe ciemne niebo wiejskie.',
    3: 'Niebo wiejskie - wyraźnie widoczna Droga Mleczna.',
    4: 'Niebo wiejskie/podmiejskie - lekka poświata przy horyzoncie.',
    5: 'Niebo podmiejskie - poświata widoczna w kilku kierunkach.',
    6: 'Jasne niebo podmiejskie.',
    7: 'Przejście miasto/przedmieścia.',
    8: 'Niebo miejskie - widoczne tylko najjaśniejsze obiekty.',
    9: 'Centrum dużego miasta - obserwacje mocno ograniczone.'
  };
  // Te same kolory co przystanki gradientu w style.css (.bortle-slider),
  // żeby obrys kciuka zawsze pasował do miejsca, w którym aktualnie jest.
  const BORTLE_COLORS = ['#2C6E63', '#4FA98F', '#6FE9D6', '#9FD98A', '#D9DD6E', '#F3D968', '#F3B968', '#E8794F', '#D95C5C'];
  function updateBortle() {
    const v = Number(bortle.value);
    bortleVal.textContent = v;
    bortleHint.textContent = BORTLE_HINTS[v] || '';
    const color = BORTLE_COLORS[v - 1];
    bortle.style.setProperty('--thumb-color', color);
    bortle.style.setProperty('--thumb-glow', color + '40'); // lekka poświata w kolorze strefy
  }
  bortle.addEventListener('input', updateBortle);
  updateBortle();

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
      maxBortle: Number(bortle.value),
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
