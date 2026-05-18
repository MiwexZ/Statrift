<?php
/**
 * Security — clase estática que centraliza CSRF, sanitización,
 * rate limiting de login y configuración segura de sesión.
 *
 * No depende de PDO, Composer ni librerías externas: solo PHP nativo + mysqli.
 */
class Security
{
    /** Flag interno para hacer configurarSesion() idempotente. */
    private static bool $sesionConfigurada = false;

    // ============================================================
    // 1) CSRF TOKENS
    // ============================================================

    /** Genera un token CSRF aleatorio, lo guarda en $_SESSION['csrf_token'] y lo devuelve. */
    public static function generarTokenCsrf(): string
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $token = bin2hex(random_bytes(32));
        $_SESSION['csrf_token'] = $token;
        return $token;
    }

    /** Valida un token contra $_SESSION['csrf_token'] usando hash_equals; loguea si falla. */
    public static function validarTokenCsrf(string $token): bool
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $almacenado = $_SESSION['csrf_token'] ?? '';
        if ($almacenado === '' || !hash_equals($almacenado, $token)) {
            error_log('[Security] CSRF token inválido o ausente.');
            return false;
        }
        return true;
    }

    /** Devuelve un <input type="hidden"> con el token CSRF listo para embeber en un formulario. */
    public static function campoHiddenCsrf(): string
    {
        $token = self::generarTokenCsrf();
        return '<input type="hidden" name="csrf_token" value="'
             . htmlspecialchars($token, ENT_QUOTES | ENT_HTML5, 'UTF-8')
             . '">';
    }

    // ============================================================
    // 2) SANITIZACIÓN
    // ============================================================

    /** Limpia un string: trim + htmlspecialchars con ENT_QUOTES | ENT_HTML5 en UTF-8. */
    public static function limpiarString(string $input): string
    {
        return htmlspecialchars(trim($input), ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    /** Valida y devuelve un int; 0 si la entrada no es un entero válido. */
    public static function limpiarInt(mixed $input): int
    {
        $valor = filter_var($input, FILTER_VALIDATE_INT);
        return $valor === false ? 0 : (int) $valor;
    }

    /** Devuelve la URL solo si es válida y empieza por https://; en caso contrario, ''. */
    public static function limpiarUrl(string $url): string
    {
        $url = trim($url);
        if ($url === '') return '';
        if (!filter_var($url, FILTER_VALIDATE_URL)) return '';
        if (stripos($url, 'https://') !== 0) return '';
        return $url;
    }

    /** Sanitiza HTML de Quill: whitelist de etiquetas + valida que href en <a> sea http(s). */
    public static function sanitizarHtmlQuill(string $html): string
    {
        $permitidas = '<p><br><strong><em><u><s><ol><ul><li><blockquote><h1><h2><h3><a><span>';
        $limpio = strip_tags($html, $permitidas);

        // Saneamos las etiquetas <a>: si href no es http(s), lo reemplazamos por "#".
        $limpio = preg_replace_callback(
            '/<a\b([^>]*)>/i',
            static function (array $m): string {
                $attrs = $m[1];
                if (preg_match('/\bhref\s*=\s*(["\'])(.*?)\1/i', $attrs, $href)) {
                    $valor = trim($href[2]);
                    if (!preg_match('#^https?://#i', $valor)) {
                        $attrs = preg_replace(
                            '/\s*\bhref\s*=\s*(["\']).*?\1/i',
                            ' href="#"',
                            $attrs
                        );
                    }
                } else {
                    $attrs .= ' href="#"';
                }
                return '<a' . $attrs . '>';
            },
            $limpio
        );

        return $limpio ?? '';
    }

    // ============================================================
    // 3) RATE LIMITING DE LOGIN (en sesión, ventana de 15 min)
    // ============================================================

    /** Incrementa el contador de intentos fallidos en $_SESSION['login_intentos'][$nick]. */
    public static function registrarIntentoFallido(string $nick): void
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $datos = $_SESSION['login_intentos'][$nick] ?? null;
        // Si no existe o la ventana de 15 min caducó, abrimos ventana nueva.
        if ($datos === null || (time() - ($datos['first'] ?? 0)) > 900) {
            $_SESSION['login_intentos'][$nick] = [
                'count' => 1,
                'first' => time(),
                'last'  => time(),
            ];
            return;
        }
        $_SESSION['login_intentos'][$nick]['count'] = ($datos['count'] ?? 0) + 1;
        $_SESSION['login_intentos'][$nick]['last']  = time();
    }

    /** Devuelve true si el nick acumula 5+ intentos fallidos en los últimos 15 minutos. */
    public static function estaBloqueado(string $nick): bool
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $datos = $_SESSION['login_intentos'][$nick] ?? null;
        if ($datos === null) return false;
        if ((time() - ($datos['first'] ?? 0)) > 900) {
            unset($_SESSION['login_intentos'][$nick]);
            return false;
        }
        return ($datos['count'] ?? 0) >= 5;
    }

    /** Borra el contador de intentos fallidos del nick (tras login exitoso). */
    public static function limpiarIntentos(string $nick): void
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        unset($_SESSION['login_intentos'][$nick]);
    }

    // ============================================================
    // 4) CONFIGURACIÓN DE SESIÓN SEGURA
    // ============================================================

    /** Configura cookies y flags de sesión; idempotente y debe llamarse ANTES de session_start(). */
    public static function configurarSesion(): void
    {
        if (self::$sesionConfigurada) return;
        // Si la sesión ya arrancó, las llamadas a session_set_cookie_params/ini_set no surten efecto.
        if (session_status() === PHP_SESSION_ACTIVE) {
            self::$sesionConfigurada = true;
            return;
        }
        session_set_cookie_params([
            'lifetime' => 0,
            'path'     => '/',
            'secure'   => false,
            'httponly' => true,
            'samesite' => 'Strict',
        ]);
        ini_set('session.use_strict_mode', '1');
        ini_set('session.use_only_cookies', '1');
        self::$sesionConfigurada = true;
    }

    /** Regenera el ID de sesión (llamar tras login exitoso para mitigar fixation). */
    public static function regenerarSesion(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_regenerate_id(true);
        }
    }
}
