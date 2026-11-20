<?php
// Pincelada Anatômica · funções comuns do portal
const DIR_DADOS = __DIR__ . '/dados';

function ler(string $arquivo, $padrao = null) {
    $p = DIR_DADOS . '/' . $arquivo;
    if (!is_file($p)) return $padrao;
    $j = json_decode((string) file_get_contents($p), true);
    return $j === null ? $padrao : $j;
}

function gravar(string $arquivo, $valor): void {
    $p = DIR_DADOS . '/' . $arquivo;
    $dir = dirname($p);
    if (!is_dir($dir)) mkdir($dir, 0750, true);
    $tmp = $p . '.tmp' . bin2hex(random_bytes(4));
    file_put_contents($tmp, json_encode($valor, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT), LOCK_EX);
    rename($tmp, $p);
}

function slug_ok($s): bool { return is_string($s) && preg_match('/^[a-z0-9-]{1,60}$/', $s) === 1; }
function token_ok($t): bool { return is_string($t) && preg_match('/^[a-z0-9-]{10,120}$/', $t) === 1; }

function slugify(string $s): string {
    $t = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $s);
    if ($t === false) $t = $s;
    $t = strtolower($t);
    $t = preg_replace('/[^a-z0-9]+/', '-', $t);
    $t = trim(substr($t, 0, 40), '-');
    return $t !== '' ? $t : 'cliente';
}

function clientes(): array {
    if (!is_file(DIR_DADOS . '/clientes.json')) {
        // primeira execução: cria os clientes iniciais de seed.json, cada um com um link novo e secreto
        $cs = [];
        foreach (ler('seed.json', []) as $s) {
            $slug = slugify((string) ($s['empresa'] ?? $s['mentorado'] ?? 'cliente'));
            $d = modelo();
            foreach (['mentorado', 'empresa', 'tipo'] as $k) if (!empty($s[$k])) $d['cliente'][$k] = $s[$k];
            gravar("clientes/$slug.json", $d);
            $cs[$slug] = ['token' => $slug . '-' . bin2hex(random_bytes(12)), 'criado' => date('c')];
        }
        gravar('clientes.json', $cs);
        return $cs;
    }
    return ler('clientes.json', []);
}

function cliente_por_token(string $t): ?string {
    foreach (clientes() as $slug => $c) {
        if (isset($c['token']) && hash_equals($c['token'], $t)) return $slug;
    }
    return null;
}

function modelo(): array {
    $m = ler('modelo-editado.json');
    return is_array($m) ? $m : ler('modelo.json', []);
}

function sessao(): void {
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_set_cookie_params(['lifetime' => 0, 'path' => '/', 'httponly' => true, 'secure' => true, 'samesite' => 'Lax']);
        session_name('pincelada');
        session_start();
    }
}

function mentor_logado(): bool { sessao(); return !empty($_SESSION['mentor']); }

function json_out($v, int $codigo = 200): void {
    http_response_code($codigo);
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-store');
    echo json_encode($v, JSON_UNESCAPED_UNICODE);
    exit;
}

function corpo(): array {
    $j = json_decode((string) file_get_contents('php://input'), true);
    return is_array($j) ? $j : [];
}
