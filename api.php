<?php
// Pincelada Anatômica · API do portal e do painel
require __DIR__ . '/lib.php';
aplicar_atualizacoes();

$a = $_GET['a'] ?? '';
$post = $_SERVER['REQUEST_METHOD'] === 'POST';

// ---------- rotas do cliente ----------
if ($a === 'salvar') {
    if (!$post) json_out(['erro' => 'método'], 405);
    $t = $_GET['t'] ?? '';
    if (!token_ok($t)) json_out(['erro' => 'link inválido'], 400);
    $slug = cliente_por_token($t);
    if (!$slug) json_out(['erro' => 'cliente não encontrado'], 404);
    $r = corpo()['respostas'] ?? null;
    if (!is_array($r)) json_out(['erro' => 'respostas inválidas'], 400);
    $limpo = [];
    foreach ($r as $k => $v) {
        if (!is_string($k) || strlen($k) > 200) continue;
        if (is_array($v)) { $limpo[$k] = array_values(array_map('strval', array_filter($v, 'is_scalar'))); }
        elseif (is_scalar($v)) { $limpo[$k] = mb_substr((string) $v, 0, 20000); }
    }
    if (strlen(json_encode($limpo)) > 2000000) json_out(['erro' => 'conteúdo grande demais'], 413);
    gravar("respostas/$slug.json", ['atualizado' => date('c'), 'respostas' => (object) $limpo]);
    json_out(['ok' => true]);
}

// ---------- acesso do mentor ----------
if ($a === 'estado') { json_out(['configurado' => is_file(DIR_DADOS . '/mentor.json'), 'logado' => mentor_logado()]); }

if ($a === 'setup') {
    if (!$post) json_out(['erro' => 'método'], 405);
    sessao();
    if (is_file(DIR_DADOS . '/mentor.json')) json_out(['erro' => 'A senha já foi criada.'], 403);
    $s = (string) (corpo()['senha'] ?? '');
    if (mb_strlen($s) < 8) json_out(['erro' => 'A senha precisa ter pelo menos 8 caracteres.'], 400);
    gravar('mentor.json', ['hash' => password_hash($s, PASSWORD_DEFAULT), 'criado' => date('c')]);
    session_regenerate_id(true);
    $_SESSION['mentor'] = 1;
    json_out(['ok' => true]);
}

if ($a === 'login') {
    if (!$post) json_out(['erro' => 'método'], 405);
    sessao();
    $m = ler('mentor.json');
    $s = (string) (corpo()['senha'] ?? '');
    if (!$m || !password_verify($s, $m['hash'])) { sleep(1); json_out(['erro' => 'Senha incorreta.'], 401); }
    session_regenerate_id(true);
    $_SESSION['mentor'] = 1;
    json_out(['ok' => true]);
}

if (!mentor_logado()) json_out(['erro' => 'Faça login.'], 401);
if ($post && ($_SERVER['HTTP_X_PAINEL'] ?? '') !== '1') json_out(['erro' => 'origem'], 403);

switch ($a) {
    case 'logout':
        $_SESSION = [];
        session_destroy();
        json_out(['ok' => true]);

    case 'lista':
        $out = [];
        foreach (clientes() as $slug => $c) {
            $d = ler("clientes/$slug.json", []);
            $r = ler("respostas/$slug.json", ['respostas' => []]);
            $n = 0;
            foreach (($r['respostas'] ?? []) as $v) { if (is_array($v) ? count($v) : trim((string) $v) !== '') $n++; }
            $out[] = [
                'slug' => $slug, 'token' => $c['token'], 'criado' => $c['criado'] ?? null,
                'mentorado' => $d['cliente']['mentorado'] ?? '', 'empresa' => $d['cliente']['empresa'] ?? '',
                'cores' => $d['cliente']['cores'] ?? null, 'liberada' => !empty($d['apresentacao']['liberada']),
                'respostas' => $n, 'atualizado' => $r['atualizado'] ?? null,
            ];
        }
        usort($out, fn($x, $y) => strcmp((string) $y['criado'], (string) $x['criado']));
        json_out($out);

    case 'criar':
        if (!$post) json_out(['erro' => 'método'], 405);
        $b = corpo();
        $nome = trim((string) ($b['mentorado'] ?? ''));
        $emp = trim((string) ($b['empresa'] ?? ''));
        $tipo = ($b['tipo'] ?? '') === 'servicos' ? 'servicos' : 'produtos';
        if ($nome === '') json_out(['erro' => 'Informe o nome do mentorado.'], 400);
        $cs = clientes();
        $base = slugify($emp !== '' ? $emp : $nome);
        $slug = $base; $i = 2;
        while (isset($cs[$slug])) $slug = $base . '-' . $i++;
        $d = modelo();
        $d['cliente']['mentorado'] = $nome;
        $d['cliente']['empresa'] = $emp !== '' ? $emp : $nome;
        $d['cliente']['tipo'] = $tipo;
        gravar("clientes/$slug.json", $d);
        $cs[$slug] = ['token' => $slug . '-' . bin2hex(random_bytes(12)), 'criado' => date('c')];
        gravar('clientes.json', $cs);
        json_out(['ok' => true, 'slug' => $slug]);

    case 'ver':
        $slug = $_GET['slug'] ?? '';
        $cs = clientes();
        if (!slug_ok($slug) || !isset($cs[$slug])) json_out(['erro' => 'Cliente não encontrado.'], 404);
        json_out(['dados' => ler("clientes/$slug.json", []), 'token' => $cs[$slug]['token'], 'respostas' => ler("respostas/$slug.json", ['respostas' => (object) []])]);

    case 'salvar_dados':
        if (!$post) json_out(['erro' => 'método'], 405);
        $slug = $_GET['slug'] ?? '';
        if (!slug_ok($slug) || !isset(clientes()[$slug])) json_out(['erro' => 'Cliente não encontrado.'], 404);
        $d = corpo()['dados'] ?? null;
        if (!is_array($d)) json_out(['erro' => 'Dados inválidos.'], 400);
        gravar("clientes/$slug.json", $d);
        json_out(['ok' => true]);

    case 'modelo':
        json_out(modelo());

    case 'salvar_modelo':
        if (!$post) json_out(['erro' => 'método'], 405);
        $d = corpo()['dados'] ?? null;
        if (!is_array($d)) json_out(['erro' => 'Dados inválidos.'], 400);
        gravar('modelo-editado.json', $d);
        json_out(['ok' => true]);
}
json_out(['erro' => 'ação desconhecida'], 404);
