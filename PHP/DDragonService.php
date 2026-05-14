<?php
class DDragonService
{
    private static string $BASE = 'https://ddragon.leagueoflegends.com';
    private static string $LANG = 'es_ES';
    private static ?string $version = null;
    private static string $cache_dir = '';
    private static int $TTL = 86400; // 24h

    private static function init(): void
    {
        if (self::$cache_dir === '') {
            self::$cache_dir = __DIR__ . '/../cache/ddragon/';
            if (!is_dir(self::$cache_dir)) {
                mkdir(self::$cache_dir, 0755, true);
            }
        }
    }

    public static function getVersion(): string
    {
        if (self::$version) return self::$version;
        $data = self::fetch(self::$BASE . '/api/versions.json', 'versions');
        self::$version = $data[0] ?? '14.9.1';
        return self::$version;
    }

    // Devuelve todos los items del JSON de Riot
    public static function getAllItems(): array
    {
        $v   = self::getVersion();
        $url = self::$BASE . "/cdn/{$v}/data/" . self::$LANG . "/item.json";
        $raw = self::fetch($url, 'items_' . $v);
        return $raw['data'] ?? [];
    }

    // Datos de un item por su riot_id ("3031", "6692"…)
    public static function getItem(string $riotId): ?array
    {
        $items = self::getAllItems();
        return $items[$riotId] ?? null;
    }

    // URL del icono de un item
    public static function itemIcon(string $riotId): string
    {
        $v    = self::getVersion();
        $item = self::getItem($riotId);
        $img  = $item['image']['full'] ?? "{$riotId}.png";
        return self::$BASE . "/cdn/{$v}/img/item/{$img}";
    }

    // Árbol completo de runas
    public static function getRuneTree(): array
    {
        $v   = self::getVersion();
        $url = self::$BASE . "/cdn/{$v}/data/" . self::$LANG . "/runesReforged.json";
        return self::fetch($url, 'runes_' . $v);
    }

    // Un camino de runas por ID (ej: 8000 = Precisión)
    public static function getRunePath(int $pathId): ?array
    {
        foreach (self::getRuneTree() as $path) {
            if ((int)$path['id'] === $pathId) return $path;
        }
        return null;
    }

    // URL del icono de una runa/path
    public static function runeIcon(string $iconPath): string
    {
        return self::$BASE . '/cdn/img/' . ltrim($iconPath, '/');
    }

    // Cache + fetch
    private static function fetch(string $url, string $key): array
    {
        self::init();
        $file = self::$cache_dir . md5($key) . '.json';

        if (file_exists($file) && (time() - filemtime($file) < self::$TTL)) {
            return json_decode(file_get_contents($file), true) ?? [];
        }

        $ctx = stream_context_create(['http' => ['timeout' => 10]]);
        $raw = @file_get_contents($url, false, $ctx);

        if ($raw === false) {
            // Si falla la red, devuelve caché antigua si existe
            return file_exists($file) ? (json_decode(file_get_contents($file), true) ?? []) : [];
        }

        file_put_contents($file, $raw);
        return json_decode($raw, true) ?? [];
    }
}
?>
