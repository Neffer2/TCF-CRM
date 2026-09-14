/* ==========================================================================
   BULLCRM · animación del dashboard de gerencia
   - Conteo animado de cifras ([data-count]), barras ([data-width]) y anillo ([data-ring])
   - Entrada escalonada de las tarjetas (.crm-kpi)
   - Gráfica de tendencia (Chart.js, #crmTendencia)
   Se ejecuta al cargar y después de cada actualización de Livewire (cambio de filtros).
   ========================================================================== */
(function () {
  var reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  // Señal para el CSS: solo con esta clase se parte del estado oculto (entrada animada)
  if (!reduce) document.documentElement.classList.add('crm-js');
  var easeOut = function (t) { return 1 - Math.pow(1 - t, 4); };
  var fmt = function (n, decimals) {
    return Number(n).toLocaleString('en-US', { minimumFractionDigits: decimals, maximumFractionDigits: decimals });
  };

  // Último valor mostrado por clave: al cambiar filtros la cifra cuenta desde
  // el valor anterior hacia el nuevo, en vez de arrancar de cero.
  var previos = {};
  function contar(el) {
    var target = parseFloat(el.getAttribute('data-count'));
    if (isNaN(target)) return;
    var decimals = parseInt(el.getAttribute('data-decimals') || '0', 10);
    var key = el.getAttribute('data-key');
    var from = key && previos[key] !== undefined ? previos[key] : 0;
    if (key) previos[key] = target;
    if (reduce) { el.textContent = fmt(target, decimals); el.setAttribute('data-from', target); return; }
    var dur = 950, start = null;
    function step(ts) {
      if (!start) start = ts;
      var p = Math.min(1, (ts - start) / dur);
      el.textContent = fmt(from + (target - from) * easeOut(p), decimals);
      if (p < 1) requestAnimationFrame(step); else el.setAttribute('data-from', target);
    }
    requestAnimationFrame(step);
  }

  function barras() {
    document.querySelectorAll('.crm-bar > span[data-width]').forEach(function (b) {
      var w = Math.max(0, Math.min(100, parseFloat(b.getAttribute('data-width')) || 0));
      b.style.width = '0%';
      requestAnimationFrame(function () { requestAnimationFrame(function () { b.style.width = w + '%'; }); });
    });
  }

  function anillos() {
    document.querySelectorAll('.crm-ring[data-ring]').forEach(function (r) {
      var target = Math.max(0, Math.min(100, parseFloat(r.getAttribute('data-ring')) || 0));
      if (reduce) { r.style.setProperty('--pct', target); return; }
      var dur = 1100, start = null;
      function step(ts) {
        if (!start) start = ts;
        var p = Math.min(1, (ts - start) / dur);
        r.style.setProperty('--pct', (target * easeOut(p)).toFixed(2));
        if (p < 1) requestAnimationFrame(step);
      }
      requestAnimationFrame(step);
    });
  }

  function entrada() {
    // Tarjetas del dashboard + cualquier tarjeta de contenido de las demás pantallas
    var tarjetas = document.querySelectorAll('.crm-kpi, .crm-chart, .crm-panel, .main-content .card');
    var orden = 0;
    tarjetas.forEach(function (el) {
      if (!el.style.getPropertyValue('--i') && !el.closest('.card:not(.crm-kpi) .card')) el.style.setProperty('--i', orden++);
      el.classList.remove('is-in');
      void el.offsetWidth; // reinicia la animación
      el.classList.add('is-in');
    });
    // Filas de las tablas de Argon: numeradas para la entrada escalonada (máx. 40 filas animadas)
    document.querySelectorAll('.main-content .table:not(.crm-table)').forEach(function (t) {
      Array.prototype.forEach.call(t.tBodies, function (tb) {
        Array.prototype.forEach.call(tb.rows, function (tr, i) { tr.style.setProperty('--i', Math.min(i, 40)); });
      });
      t.classList.remove('is-in');
      void t.offsetWidth;
      t.classList.add('is-in');
    });
  }

  var chart = null;
  function grafica() {
    var canvas = document.getElementById('crmTendencia');
    if (!canvas || typeof Chart === 'undefined') return;
    var labels = JSON.parse(canvas.getAttribute('data-labels') || '[]');
    var venta = JSON.parse(canvas.getAttribute('data-venta') || '[]');
    var meta = JSON.parse(canvas.getAttribute('data-presupuesto') || '[]');
    if (chart) { chart.destroy(); chart = null; }
    var ctx = canvas.getContext('2d');
    var grad = ctx.createLinearGradient(0, 0, 0, 260);
    grad.addColorStop(0, 'rgba(239, 95, 23, .34)');
    grad.addColorStop(1, 'rgba(239, 95, 23, 0)');
    var css = getComputedStyle(document.documentElement);
    var ink3 = css.getPropertyValue('--crm-ink-3').trim() || '#9A9088';
    var line = css.getPropertyValue('--crm-line').trim() || '#E9E2DA';
    var money = function (v) {
      if (Math.abs(v) >= 1e9) return '$' + (v / 1e9).toFixed(1) + ' mil M';
      if (Math.abs(v) >= 1e6) return '$' + (v / 1e6).toFixed(0) + ' M';
      return '$' + fmt(v, 0);
    };
    chart = new Chart(ctx, {
      type: 'bar',
      data: {
        labels: labels,
        datasets: [
          {
            type: 'line', label: 'Venta facturada', data: venta, order: 1,
            borderColor: '#EF5F17', borderWidth: 2.5, tension: .42, fill: true, backgroundColor: grad,
            pointRadius: 4, pointHoverRadius: 6, pointBackgroundColor: '#fff', pointBorderColor: '#EF5F17', pointBorderWidth: 2
          },
          {
            type: 'bar', label: 'Presupuesto', data: meta, order: 2,
            backgroundColor: 'rgba(154, 144, 136, .22)', hoverBackgroundColor: 'rgba(154, 144, 136, .38)',
            borderRadius: 6, borderSkipped: false, maxBarThickness: 34
          }
        ]
      },
      options: {
        responsive: true, maintainAspectRatio: false,
        animation: reduce ? false : { duration: 1100, easing: 'easeOutQuart' },
        interaction: { mode: 'index', intersect: false },
        plugins: {
          legend: { display: false },
          tooltip: {
            backgroundColor: '#1E1915', titleColor: '#fff', bodyColor: '#EAE4DD', padding: 12, cornerRadius: 10, displayColors: true,
            callbacks: {
              label: function (c) { return ' ' + c.dataset.label + ': $' + fmt(c.parsed.y, 0); },
              afterBody: function (items) {
                var v = items.find(function (i) { return i.dataset.label === 'Venta facturada'; });
                var m = items.find(function (i) { return i.dataset.label === 'Presupuesto'; });
                if (v && m && m.parsed.y > 0) return ' Cumplimiento: ' + (v.parsed.y / m.parsed.y * 100).toFixed(1) + ' %';
                return '';
              }
            }
          }
        },
        scales: {
          x: { grid: { display: false }, ticks: { color: ink3, font: { family: 'Manrope', size: 11, weight: '600' } }, border: { display: false } },
          y: { grid: { color: line, drawBorder: false }, border: { display: false }, ticks: { color: ink3, font: { family: 'Manrope', size: 11 }, maxTicksLimit: 5, callback: money } }
        }
      }
    });
  }

  function animarTodo() {
    entrada();
    document.querySelectorAll('[data-count]').forEach(contar);
    barras();
    anillos();
    grafica();
  }

  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', animarTodo); else animarTodo();

  // Re-animar tras cada actualización de Livewire (cambio de año / mes / comercial)
  document.addEventListener('livewire:load', function () {
    if (!window.Livewire || !window.Livewire.hook) return;
    var pendiente = null;
    window.Livewire.hook('message.processed', function () {
      clearTimeout(pendiente);
      pendiente = setTimeout(animarTodo, 40);
    });
  });
})();
