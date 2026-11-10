<div>
    @if($showNotice)
        <div style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(0,0,0,0.6); z-index: 99999; display: flex; align-items: center; justify-content: center;">
            <div style="background: white; padding: 30px; border-radius: 8px; max-width: 400px; width: 90%; text-align: center; box-shadow: 0 4px 15px rgba(0,0,0,0.3); font-family: 'Open Sans', sans-serif;">
                <h2 style="margin-top: 0; color: #333; font-size: 20px; font-weight: bold;">🛠️ Mantenimiento Programado</h2>
                <p style="color: #666; line-height: 1.5; font-size: 14px; margin: 0 0 15px;">
                    Estamos actualizando nuestros servidores para ofrecerte un mejor servicio. Estaremos de vuelta pronto.
                </p>

                @if($endsAt)
                    <div wire:ignore
                         id="maintenance-countdown"
                         data-end="{{ $endsAt }}"
                         style="font-size: 32px; font-weight: 700; color: #5e72e4; font-variant-numeric: tabular-nums;">
                        --:--:--
                    </div>

                    <script>
                        (function () {
                            var el = document.getElementById('maintenance-countdown');
                            if (!el) return;
                            var end = parseInt(el.dataset.end, 10);

                            function pad(n) { return String(n).padStart(2, '0'); }

                            function tick() {
                                var diff = end - Date.now();
                                if (diff <= 0) { window.location.reload(); return; }
                                var h = Math.floor(diff / 3600000);
                                var m = Math.floor((diff % 3600000) / 60000);
                                var s = Math.floor((diff % 60000) / 1000);
                                el.textContent = pad(h) + ':' + pad(m) + ':' + pad(s);
                            }

                            tick();
                            setInterval(tick, 1000);
                        })();
                    </script>
                @endif
            </div>
        </div>
    @endif
</div>