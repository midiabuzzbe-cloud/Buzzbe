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

// ---------- canal de atualizações (o mentor pede, o Claude envia criptografado pelo repositório) ----------
function chave_par(): string {
    $p = DIR_DADOS . '/chave.json';
    if (is_file($p)) {
        $j = json_decode((string) file_get_contents($p), true);
        if (!empty($j['par'])) return base64_decode($j['par']);
    }
    $par = sodium_crypto_box_keypair();
    gravar('chave.json', ['par' => base64_encode($par)]);
    file_put_contents(DIR_DADOS . '/chave-publica.txt', base64_encode(sodium_crypto_box_publickey($par)));
    return $par;
}

function mesclar(array $base, array $novo): array {
    foreach ($novo as $k => $v) {
        $ehLista = is_array($v) && array_keys($v) === range(0, count($v) - 1);
        if (is_array($v) && !$ehLista && isset($base[$k]) && is_array($base[$k])) $base[$k] = mesclar($base[$k], $v);
        else $base[$k] = $v;
    }
    return $base;
}

function aplicar_atualizacoes(): void {
    static $feito = false;
    if ($feito) return;
    $feito = true;
    $par = chave_par();
    $arquivos = glob(__DIR__ . '/atualizacoes/*.txt') ?: [];
    if (!$arquivos) return;
    sort($arquivos);
    $aplicadas = ler('aplicadas.json', []);
    foreach ($arquivos as $arq) {
        $nome = basename($arq);
        if (isset($aplicadas[$nome])) continue;
        $claro = sodium_crypto_box_seal_open((string) base64_decode(trim((string) file_get_contents($arq))), $par);
        if ($claro === false) { $aplicadas[$nome] = 'ignorada: outra chave'; continue; }
        $u = json_decode($claro, true);
        if (!is_array($u)) { $aplicadas[$nome] = 'ignorada: inválida'; continue; }
        foreach (($u['acoes'] ?? []) as $a) {
            $tipo = $a['tipo'] ?? '';
            if ($tipo === 'criar_cliente') {
                $cs = clientes();
                $slug = slugify((string) ($a['empresa'] ?? $a['mentorado'] ?? 'cliente'));
                $base = $slug; $i = 2;
                while (isset($cs[$slug])) $slug = $base . '-' . $i++;
                $d = modelo();
                foreach (['mentorado', 'empresa'] as $k) if (!empty($a[$k])) $d['cliente'][$k] = $a[$k];
                if (($a['negocio'] ?? '') === 'servicos' || ($a['negocio'] ?? '') === 'produtos') $d['cliente']['tipo'] = $a['negocio'];
                if (!empty($a['dados']) && is_array($a['dados'])) $d = mesclar($d, $a['dados']);
                gravar("clientes/$slug.json", $d);
                $cs[$slug] = ['token' => $slug . '-' . bin2hex(random_bytes(12)), 'criado' => date('c')];
                gravar('clientes.json', $cs);
            } elseif ($tipo === 'mesclar' || $tipo === 'substituir') {
                $slug = (string) ($a['cliente'] ?? '');
                if (!slug_ok($slug) || !isset(clientes()[$slug]) || !is_array($a['dados'] ?? null)) continue;
                $atual = ler("clientes/$slug.json", []);
                gravar("clientes/$slug.json", $tipo === 'mesclar' ? mesclar($atual, $a['dados']) : $a['dados']);
            } elseif ($tipo === 'modelo' && is_array($a['dados'] ?? null)) {
                gravar('modelo-editado.json', mesclar(modelo(), $a['dados']));
            }
        }
        $aplicadas[$nome] = date('c');
    }
    gravar('aplicadas.json', $aplicadas);
}

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
