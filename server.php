<?php

/**
 * Laravel - A PHP Framework For Web Artisans
 *
 * @package  Laravel
 * @author   Taylor Otwell <taylor@laravel.com>
 */

$uri = urldecode(
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? ''
);

// This file allows us to emulate Apache's "mod_rewrite" functionality from the
// built-in PHP web server. This provides a convenient way to test a Laravel
// application without having installed a "real" web server software here.
// /storage pasa por Laravel (exige sesión), igual que en producción con public/.htaccess.
// Como el archivo sí existe bajo public/ (enlace simbólico), el servidor de PHP pone SCRIPT_NAME apuntando al
// archivo y Laravel perdería la ruta: se fija el front controller a mano.
if ($uri !== '/' && strpos($uri, '/storage/') === 0) {
    $_SERVER['SCRIPT_NAME'] = '/index.php';
    $_SERVER['SCRIPT_FILENAME'] = __DIR__.'/public/index.php';
    $_SERVER['PHP_SELF'] = '/index.php';
} elseif ($uri !== '/' && file_exists(__DIR__.'/public'.$uri)) {
    return false;
}

require_once __DIR__.'/public/index.php';
