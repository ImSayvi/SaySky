/**
 * Animowane, delikatnie migoczące gwiazdy w tle + rzadkie spadające gwiazdy.
 */
(function () {
  const canvas = document.getElementById('stars');
  const ctx = canvas.getContext('2d');
  let stars = [];
  let shootingStar = null;

  function resize() {
    canvas.width = window.innerWidth;
    canvas.height = window.innerHeight;
    const count = Math.floor((canvas.width * canvas.height) / 4000);
    stars = Array.from({ length: count }, () => ({
      x: Math.random() * canvas.width,
      y: Math.random() * canvas.height,
      r: Math.random() * 1.1 + 0.2,
      baseAlpha: Math.random() * 0.6 + 0.2,
      twinkleSpeed: Math.random() * 0.02 + 0.005,
      phase: Math.random() * Math.PI * 2
    }));
  }
  window.addEventListener('resize', resize);
  resize();

  function maybeSpawnShootingStar() {
    if (!shootingStar && Math.random() < 0.0025) {
      const startX = Math.random() * canvas.width * 0.6;
      shootingStar = {
        x: startX, y: -10,
        vx: 6 + Math.random() * 3, vy: 3 + Math.random() * 2,
        life: 1
      };
    }
  }

  function draw(t) {
    ctx.clearRect(0, 0, canvas.width, canvas.height);

    for (const s of stars) {
      const a = s.baseAlpha + Math.sin(t * s.twinkleSpeed + s.phase) * 0.25;
      ctx.beginPath();
      ctx.fillStyle = `rgba(233,234,247,${Math.max(0, a)})`;
      ctx.arc(s.x, s.y, s.r, 0, Math.PI * 2);
      ctx.fill();
    }

    maybeSpawnShootingStar();
    if (shootingStar) {
      const s = shootingStar;
      ctx.strokeStyle = `rgba(111,233,214,${s.life})`;
      ctx.lineWidth = 1.4;
      ctx.beginPath();
      ctx.moveTo(s.x, s.y);
      ctx.lineTo(s.x - s.vx * 8, s.y - s.vy * 8);
      ctx.stroke();
      s.x += s.vx; s.y += s.vy; s.life -= 0.012;
      if (s.life <= 0 || s.x > canvas.width || s.y > canvas.height) shootingStar = null;
    }

    requestAnimationFrame(draw);
  }
  requestAnimationFrame(draw);
})();
