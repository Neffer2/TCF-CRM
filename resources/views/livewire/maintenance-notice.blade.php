<div>
    @if($showNotice)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-60" style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(0,0,0,0.6); z-index: 99999; display: flex; align-items: center; justify-content: center;">
            <div style="background: white; padding: 30px; border-radius: 8px; max-width: 400px; width: 90%; text-align: center; box-shadow: 0 4px 15px rgba(0,0,0,0.3); font-family: 'Open Sans', sans-serif;">
                <h2 style="margin-top: 0; color: #333; font-size: 20px; font-weight: bold;">🛠️ Mantenimiento Programado</h2>
                <p style="color: #666; line-height: 1.5; font-size: 14px; margin-bottom: 20px;">
                    Estamos actualizando nuestros servidores para ofrecerte un mejor servicio. Estaremos de vuelta pronto.
                </p>
            </div>
        </div>
    @endif
</div>