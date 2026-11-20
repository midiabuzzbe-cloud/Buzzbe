<?php
// Pincelada Anatômica · portal do cliente (modelo fixo + dados do cliente)
require __DIR__ . '/lib.php';
aplicar_atualizacoes();

$t = $_GET['t'] ?? '';
$slug = token_ok($t) ? cliente_por_token($t) : null;
header('X-Robots-Tag: noindex, nofollow');
header('Cache-Control: no-store');
if (!$slug) {
    http_response_code(404);
    echo '<!DOCTYPE html><html lang="pt-BR"><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Portal não encontrado</title><body style="font-family:Arial,sans-serif;padding:48px;background:#F6F3EE;color:#17181D"><h1>Link não encontrado</h1><p>Confira o link ou peça um novo ao seu mentor.</p></body></html>';
    exit;
}
$d = ler("clientes/$slug.json", []);
$r = ler("respostas/$slug.json", ['respostas' => []])['respostas'] ?? [];
$f = JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT;
$inj = '<script>window.DADOS=' . json_encode($d, $f) . ';window.RESPOSTAS=' . json_encode((object) $r, $f) . ';window.TOKEN=' . json_encode($t, $f) . ';window.MENTOR=' . (mentor_logado() ? 'true' : 'false') . ';</script>';
header('Content-Type: text/html; charset=utf-8');
echo str_replace('<!--DADOS-->', $inj, (string) file_get_contents(__DIR__ . '/portal.html'));
