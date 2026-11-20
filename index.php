<?php
// Método TA · Painel do Mentor
header('Content-Type: text/html; charset=utf-8');
header('X-Robots-Tag: noindex, nofollow');
header('Cache-Control: no-store');
?><!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Método TA — Painel do Mentor</title>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@600;700&family=DM+Sans:wght@400;500;700&display=swap">
<style>
  :root { --escuro:#17181D; --claro:#F6F3EE; --claro2:#ECE6DC; --destaque:#B8894A; --dt:#835C28; --texto:#4A4A52; --linha:#D9D1C3; --branco:#FCFBF8; }
  * { box-sizing:border-box; }
  body { margin:0; background:var(--claro); color:var(--escuro); font-family:'DM Sans', Arial, sans-serif; font-size:15px; line-height:1.5; }
  header { background:var(--escuro); color:var(--claro); padding:22px 24px; }
  .in { max-width:1080px; margin:0 auto; }
  header .in { display:flex; justify-content:space-between; align-items:center; gap:12px; flex-wrap:wrap; }
  .eyebrow { font-size:12px; font-weight:700; letter-spacing:.14em; text-transform:uppercase; color:var(--destaque); }
  h1, h2 { font-family:'Cormorant Garamond', Georgia, serif; font-weight:600; margin:0; line-height:1.1; }
  header h1 { font-size:30px; margin-top:4px; }
  main { padding:32px 24px 80px; }
  .btn { font:inherit; font-weight:700; font-size:14px; border-radius:999px; padding:10px 18px; cursor:pointer; border:1px solid var(--escuro); background:var(--escuro); color:var(--claro); text-decoration:none; display:inline-block; }
  .btn.sec { background:transparent; color:var(--escuro); }
  header .btn.sec { color:var(--claro); border-color:#4A4B53; }
  .btn.ouro { background:var(--destaque); border-color:var(--destaque); color:var(--escuro); }
  .grid { display:grid; grid-template-columns:repeat(auto-fill, minmax(240px, 1fr)); gap:16px; margin-top:20px; }
  .card { background:var(--branco); border:1px solid var(--linha); border-radius:16px; overflow:hidden; cursor:pointer; display:flex; flex-direction:column; text-align:left; font:inherit; color:inherit; padding:0; }
  .card:hover { border-color:var(--destaque); }
  .faixa { height:10px; }
  .card .c { padding:18px; display:flex; flex-direction:column; gap:6px; }
  .card h3 { margin:0; font-size:18px; }
  .card p { margin:0; color:var(--texto); font-size:13.5px; }
  .tag { display:inline-block; font-size:11.5px; font-weight:700; border-radius:999px; padding:2px 10px; background:var(--claro2); }
  .tag.ok { background:var(--destaque); }
  .modelo { background:var(--escuro); color:var(--claro); }
  .modelo p { color:#CFCBC3; }
  .barra { display:flex; justify-content:space-between; align-items:center; gap:12px; flex-wrap:wrap; }
  .caixa { background:var(--branco); border:1px solid var(--linha); border-radius:16px; padding:22px; margin-top:18px; }
  label.l { display:block; font-weight:700; font-size:13.5px; margin:12px 0 6px; }
  input[type=text], input[type=password], textarea, select { width:100%; font:inherit; font-size:14.5px; color:var(--escuro); background:var(--claro); border:1px solid var(--linha); border-radius:10px; padding:9px 11px; }
  textarea { min-height:70px; resize:vertical; }
  input:focus, textarea:focus, select:focus { outline:2px solid var(--destaque); outline-offset:1px; }
  details.sec { background:var(--branco); border:1px solid var(--linha); border-radius:14px; margin-top:12px; }
  details.sec > summary { cursor:pointer; padding:14px 18px; font-family:'Cormorant Garamond', Georgia, serif; font-size:22px; font-weight:600; }
  details.sec > .corpo { padding:0 18px 18px; }
  fieldset { border:1px solid var(--linha); border-radius:12px; padding:10px 14px 14px; margin:12px 0 0; }
  legend { font-weight:700; font-size:13.5px; padding:0 6px; color:var(--dt); }
  .item { display:flex; gap:8px; align-items:flex-start; margin-top:8px; }
  .item > :first-child { flex:1; }
  .mini { font-size:12.5px; padding:6px 12px; }
  .link { display:flex; gap:8px; align-items:center; flex-wrap:wrap; background:var(--claro2); border-radius:12px; padding:12px 14px; margin-top:12px; }
  .link code { font-size:13px; word-break:break-all; flex:1; min-width:200px; }
  .fixo { position:sticky; bottom:0; background:var(--claro); padding:14px 0; display:flex; gap:10px; align-items:center; border-top:1px solid var(--linha); margin-top:20px; }
  .msg { font-size:13.5px; color:var(--texto); }
  .erro { color:#A12A2A; font-weight:700; }
  .login { max-width:420px; margin:60px auto; }
  .chk { display:flex; gap:8px; align-items:center; margin-top:10px; font-weight:700; }
</style>
</head>
<body>
<header><div class="in">
  <div><div class="eyebrow">Método TA · Painel do Mentor</div><h1 id="tit">Seus clientes</h1></div>
  <div id="topo-acoes"></div>
</div></header>
<main><div class="in" id="app"></div></main>

<script>
(function () {
  const app = document.getElementById('app');
  const el = (t, a, ...k) => { const n = document.createElement(t); for (const [x, v] of Object.entries(a || {})) { if (x === 'class') n.className = v; else if (x.startsWith('on')) n.addEventListener(x.slice(2), v); else if (v != null && v !== false) n.setAttribute(x, v); } k.flat().forEach(c => { if (c != null && c !== '') n.append(c.nodeType ? c : document.createTextNode(c)); }); return n; };
  async function api(a, corpo, extra) {
    const r = await fetch('api.php?a=' + a + (extra || ''), corpo ? { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-Painel': '1' }, body: JSON.stringify(corpo) } : {});
    const j = await r.json().catch(() => ({}));
    if (!r.ok) throw new Error(j.erro || 'Erro ' + r.status);
    return j;
  }
  const linkDe = token => location.origin + '/c/' + token;
  const titulo = t => { document.getElementById('tit').textContent = t; };
  const topo = (...b) => { const n = document.getElementById('topo-acoes'); n.replaceChildren(...b); };

  /* ---------- entrada ---------- */
  async function inicio() {
    let e; try { e = await api('estado'); } catch (x) { app.replaceChildren(el('p', { class: 'erro' }, 'Não foi possível conectar ao servidor.')); return; }
    if (!e.configurado) return telaSenha(true);
    if (!e.logado) return telaSenha(false);
    lista();
  }
  function telaSenha(primeira) {
    titulo(primeira ? 'Crie a sua senha' : 'Entrar'); topo();
    const s = el('input', { type: 'password', autocomplete: primeira ? 'new-password' : 'current-password', 'aria-label': 'Senha' });
    const s2 = primeira ? el('input', { type: 'password', autocomplete: 'new-password', 'aria-label': 'Repita a senha' }) : null;
    const msg = el('p', { class: 'msg' });
    const ir = async () => {
      if (primeira && s.value !== s2.value) { msg.className = 'erro'; msg.textContent = 'As senhas não são iguais.'; return; }
      try { await api(primeira ? 'setup' : 'login', { senha: s.value }); lista(); } catch (x) { msg.className = 'erro'; msg.textContent = x.message; }
    };
    [s, s2].forEach(i => i && i.addEventListener('keydown', ev => { if (ev.key === 'Enter') ir(); }));
    app.replaceChildren(el('div', { class: 'caixa login' },
      el('h2', {}, primeira ? 'Primeiro acesso' : 'Painel do Mentor'),
      el('p', { class: 'msg' }, primeira ? 'Crie a senha que só você vai usar para entrar neste painel. Use pelo menos 8 caracteres.' : 'Digite a sua senha.'),
      el('label', { class: 'l' }, 'Senha'), s, primeira ? [el('label', { class: 'l' }, 'Repita a senha'), s2] : '',
      el('p', {}, el('button', { class: 'btn ouro', type: 'button', onclick: ir }, primeira ? 'Criar senha e entrar' : 'Entrar')), msg));
    s.focus();
  }

  /* ---------- lista de clientes ---------- */
  async function lista() {
    titulo('Seus clientes');
    topo(el('button', { class: 'btn sec', type: 'button', onclick: async () => { await api('logout', {}); telaSenha(false); } }, 'Sair'));
    let cs = []; try { cs = await api('lista'); } catch (x) { return telaSenha(false); }
    const novo = el('button', { class: 'btn ouro', type: 'button', onclick: telaNovo }, '+ Novo cliente');
    app.replaceChildren(
      el('div', { class: 'barra' }, el('p', { class: 'msg' }, cs.length + (cs.length === 1 ? ' cliente' : ' clientes')), novo),
      el('div', { class: 'grid' },
        el('button', { class: 'card modelo', type: 'button', onclick: telaModelo }, el('div', { class: 'faixa', style: 'background:var(--destaque)' }),
          el('div', { class: 'c' }, el('h3', {}, 'Modelo'), el('p', {}, 'O ponto de partida de todo cliente novo. Mudanças aqui não alteram os clientes que já existem.'))),
        cs.map(c => el('button', { class: 'card', type: 'button', onclick: () => telaCliente(c.slug) },
          el('div', { class: 'faixa', style: 'background:' + ((c.cores && c.cores.destaque) || '#B8894A') }),
          el('div', { class: 'c' }, el('h3', {}, c.empresa || c.mentorado), el('p', {}, c.mentorado),
            el('p', {}, el('span', { class: 'tag' + (c.respostas ? ' ok' : '') }, c.respostas + ' respostas'), ' ', el('span', { class: 'tag' + (c.liberada ? ' ok' : '') }, c.liberada ? 'Apresentação liberada' : 'Apresentação em preparação')),
            el('p', {}, c.atualizado ? 'Última resposta: ' + new Date(c.atualizado).toLocaleString('pt-BR') : 'Ainda sem respostas'))))));
  }

  function telaNovo() {
    titulo('Novo cliente');
    topo(el('button', { class: 'btn sec', type: 'button', onclick: lista }, '← Clientes'));
    const n = el('input', { type: 'text', 'aria-label': 'Nome do mentorado' }), e = el('input', { type: 'text', 'aria-label': 'Empresa' });
    const t = el('select', { 'aria-label': 'Tipo' }, el('option', { value: 'produtos' }, 'Vende produtos'), el('option', { value: 'servicos' }, 'Vende serviços'));
    const msg = el('p', { class: 'msg' });
    app.replaceChildren(el('div', { class: 'caixa' }, el('h2', {}, 'Criar portal a partir do modelo'),
      el('label', { class: 'l' }, 'Nome do mentorado'), n, el('label', { class: 'l' }, 'Empresa'), e, el('label', { class: 'l' }, 'Tipo de negócio'), t,
      el('p', {}, el('button', { class: 'btn ouro', type: 'button', onclick: async () => { try { const r = await api('criar', { mentorado: n.value, empresa: e.value, tipo: t.value }); telaCliente(r.slug); } catch (x) { msg.className = 'erro'; msg.textContent = x.message; } } }, 'Criar portal')), msg));
    n.focus();
  }

  /* ---------- editor de dados ---------- */
  const ROT = { cliente: 'Cliente', mentorado: 'Nome do mentorado', empresa: 'Empresa', tipo: 'Tipo de negócio', primeiroEncontro: 'Primeiro encontro', cores: 'Cores da marca', escuro: 'Escuro', claro: 'Claro', claro2: 'Claro 2', destaque: 'Destaque', destaqueTexto: 'Destaque para textos', briefing: 'Briefing', extras: 'Perguntas a mais deste cliente', remover: 'Números das perguntas-padrão a tirar', apresentacao: 'Apresentação', liberada: 'Liberar a apresentação para o cliente', retrato: 'Retrato de Essência', tese: 'Tese de Valor', lacunas: 'As 3 lacunas', direcao: 'Direção estratégica', encontros: 'Encontros (ex.: 8 encontros semanais de 1 hora)', horas: 'Dedicação (ex.: 3 horas por semana)', aplicacao: 'Aplicação', caminhos: 'Caminhos da Etapa 5', formatos: 'Formatos de copy', modulos: 'Orientação no topo de cada módulo', texto: 'Texto', porque: 'Por que', raiox: 'Raio-X do negócio (2.2)', concorrentes: 'Concorrentes (3.1)', auditoria: 'Auditoria (4.1)', funil: 'Funil e gargalo (4.2)', mercado: 'Dados de mercado (5.1)', casos: 'Casos de referência (5.2)', resultados: 'Leitura de resultados (7.1)', propostas: 'Propostas para o cliente aprovar', circulo: 'Porquê, como e o quê', valor: 'Proposta de valor', posicionamento: 'Posicionamento', territorios: 'Territórios de conteúdo', editorial: 'Linha editorial e calendário', trafego: 'Estratégia de tráfego', plano90: 'Plano de 90 dias', copyPronta: 'Copy pronta pela colinha', formato: 'Formato', tema: 'Tema', blocos: 'Os 7 blocos', pecas: 'Peça pronta por formato', anuncios: 'Anúncios de exemplo', topo: 'Topo · valor', meio: 'Meio · prova', fundo: 'Fundo · direto', guerrilha: 'Ações de guerrilha', titulo: 'Título', acao: 'Ação', reels: 'Como vira Reels' };
  const BLOCOS = ['1 · Chamada', '2 · Desenvolvimento', '3 · Educação', '4 · Reestruturação', '5 · Solução prática', '6 · Convite e autoridade', '7 · Fechamento'];
  const SECOES = { cliente: 'Cliente', briefing: '1 · Briefing', apresentacao: '2 · Apresentação', aplicacao: '3 · Aplicação', mentor: 'O que você prepara para o cliente' };
  const rot = (k, pai) => (pai === 'cliente' && k === 'mentor') ? 'Nome do mentor' : (ROT[k] || k);
  const vazio = v => Array.isArray(v) ? [] : (v && typeof v === 'object') ? Object.fromEntries(Object.keys(v).map(k => [k, vazio(v[k])])) : (typeof v === 'boolean' ? false : '');

  function campo(obj, k, pai) {
    const v = obj[k];
    if (typeof v === 'boolean') { const c = el('input', { type: 'checkbox' }); c.checked = v; c.addEventListener('change', () => { obj[k] = c.checked; }); return el('label', { class: 'chk' }, c, rot(k, pai)); }
    if (k === 'tipo' && pai === 'cliente') { const s = el('select', {}, el('option', { value: 'produtos' }, 'Vende produtos'), el('option', { value: 'servicos' }, 'Vende serviços')); s.value = v; s.addEventListener('change', () => { obj[k] = s.value; }); return [el('label', { class: 'l' }, rot(k, pai)), s]; }
    if (k === 'remover') { const i = el('input', { type: 'text', placeholder: 'Ex.: 12, 15' }); i.value = (v || []).join(', '); i.addEventListener('input', () => { obj[k] = i.value.split(/[^0-9]+/).filter(Boolean).map(Number); }); return [el('label', { class: 'l' }, rot(k, pai)), i]; }
    if (Array.isArray(v)) {
      const box = el('div');
      const objs = v.length && typeof v[0] === 'object';
      const desenha = () => {
        box.replaceChildren(...v.map((it, i) => {
          const rem = el('button', { class: 'btn sec mini', type: 'button', onclick: () => { v.splice(i, 1); desenha(); } }, 'Remover');
          if (objs) return el('fieldset', {}, el('legend', {}, rot(k, pai) + ' ' + (i + 1)), Object.keys(it).map(kk => campo(it, kk, k)), el('p', {}, rem));
          const t = el('textarea', { rows: '2', 'aria-label': rot(k, pai) + ' ' + (i + 1) }); t.value = it; t.addEventListener('input', () => { v[i] = t.value; });
          return el('div', {}, k === 'blocos' ? el('label', { class: 'l' }, BLOCOS[i] || 'Bloco ' + (i + 1)) : '', el('div', { class: 'item' }, t, k === 'blocos' ? '' : rem));
        }), k === 'blocos' ? '' : el('p', {}, el('button', { class: 'btn sec mini', type: 'button', onclick: () => { v.push(objs ? vazio(v[0] || {}) : ''); desenha(); } }, '+ Adicionar')));
      };
      desenha();
      return el('fieldset', {}, el('legend', {}, rot(k, pai)), box);
    }
    if (v && typeof v === 'object') return el('fieldset', {}, el('legend', {}, rot(k, pai)), Object.keys(v).map(kk => campo(v, kk, k)));
    const longo = !['mentorado', 'empresa', 'mentor', 'primeiroEncontro', 'escuro', 'claro', 'claro2', 'destaque', 'destaqueTexto', 'formato', 'tema', 'titulo', 'encontros', 'horas'].includes(k);
    const i = el(longo ? 'textarea' : 'input', longo ? { rows: '3' } : { type: 'text' }); i.value = v == null ? '' : v; i.setAttribute('aria-label', rot(k, pai));
    i.addEventListener('input', () => { obj[k] = i.value; });
    return [el('label', { class: 'l' }, rot(k, pai)), i];
  }
  function editor(dados) {
    return Object.keys(dados).filter(k => k !== 'briefing').map((k, i) => el('details', { class: 'sec', open: i === 0 ? '' : null }, el('summary', {}, SECOES[k] || rot(k)),
      el('div', { class: 'corpo' }, (dados[k] && typeof dados[k] === 'object' && !Array.isArray(dados[k])) ? Object.keys(dados[k]).map(kk => campo(dados[k], kk, k)) : campo(dados, k))));
  }


  /* ---------- editor de briefing ---------- */
  const BRIEFING_PADRAO = [["Sobre você", ["Conte sua história em poucas linhas: de onde veio, como chegou até aqui e em que fase da vida sente que está hoje.", "O que as pessoas mais elogiam em você, e em que situações costumam te procurar?", "O que você faz com facilidade e que os outros acham difícil?", "Quando pensa em crescer, cobrar mais ou aparecer, o que passa pela sua cabeça? O que você evita fazer, mesmo sabendo que deveria?", "Qual foi a maior dificuldade que você já superou, e o que ela te ensinou?", "Daqui a 3 anos, como seria a sua vida ideal, pessoal e profissional?"]], ["O seu negócio", ["Nome da empresa, cidade e bairro, há quanto tempo existe e por que você a abriu.", {"produtos": "Seus principais produtos, com o preço de cada um. Qual vende mais, qual dá mais lucro e quantos itens você tem no total?", "servicos": "Seus principais serviços, como cada um é entregue, o preço e o tempo que leva. Qual é o mais procurado e qual dá mais lucro?"}, {"produtos": "Faturamento mensal médio (aproximado), valor médio por venda e meta para os próximos 12 meses.", "servicos": "Faturamento mensal médio (aproximado), quantos atendimentos você consegue fazer por mês e meta para os próximos 12 meses."}, "Quem trabalha com você, e quem cuida do marketing hoje?", "Por que os clientes compram de você e não do concorrente? Que provas você tem (depoimentos, avaliações, antes e depois)?", "Quais são os meses mais fortes e os mais fracos do ano?"]], ["Clientes e vendas", ["Descreva o seu melhor cliente e o problema ou desejo que leva ele até você.", "Quais dúvidas e objeções você mais escuta, e por que alguns desistem de comprar?", {"produtos": "Como os clientes chegam até você hoje (loja, site, WhatsApp, marketplace)? Quantos contatos recebe por mês, e quantos compram?", "servicos": "Como os clientes chegam até você hoje? Quantos pedem orçamento ou agendam por mês, e quantos fecham?"}]], ["Marketing e mercado", ["Quais canais você usa hoje (Instagram, Google, site, WhatsApp, outros)? Informe @ e links. Você aparece nos conteúdos?", "Já fez anúncios pagos? Quanto investiu, quem gerenciou e qual foi o resultado?", "Quem são os seus 3 principais concorrentes (@ ou site), e o que eles fazem bem ou mal?"]], ["Compromisso", ["Quantas horas por semana você consegue dedicar à mentoria, e quanto pode investir por mês em anúncios?", "Qual resultado, ao fim dos 2 meses, faria você dizer que a mentoria valeu a pena?"]]];
  function briefingPadrao(tipo) {
    let o = 0;
    return BRIEFING_PADRAO.map(([parte, qs]) => ({ parte, itens: qs.map(q => { o++; return { id: 'b' + o, q: typeof q === 'string' ? q : (q[tipo] || q.produtos) }; }) }));
  }
  const novoId = () => 'n' + Date.now().toString(36) + Math.random().toString(36).slice(2, 6);
  function editorBriefing(d) {
    d.briefing = d.briefing || {};
    if (!Array.isArray(d.briefing.perguntas) || !d.briefing.perguntas.length) {
      const base = briefingPadrao(d.cliente.tipo);
      const rem = (d.briefing.remover || []).map(Number);
      base.forEach(p => { p.itens = p.itens.filter(x => !rem.includes(Number(x.id.slice(1)))); });
      const ex = (d.briefing.extras || []).filter(q => (q || '').trim());
      if (ex.length) base.push({ parte: 'Perguntas do seu negócio', itens: ex.map(q => ({ id: novoId(), q })) });
      d.briefing.perguntas = base;
    }
    const P = d.briefing.perguntas;
    const caixa = el('div', { class: 'caixa' });
    const conta = el('p', { class: 'msg' });
    const total = () => P.reduce((t, p) => t + p.itens.length, 0);
    function desenha() {
      const n = total();
      conta.className = n > 20 ? 'erro' : 'msg';
      conta.textContent = n + ' perguntas' + (n > 20 ? ' (o ideal é no máximo 20)' : '');
      let num = 0;
      caixa.replaceChildren(
        el('h2', {}, 'Briefing deste cliente'),
        el('p', { class: 'msg' }, 'Reescreva, apague ou acrescente perguntas. As mudanças valem só para este cliente. Depois clique em "Salvar alterações".'),
        conta,
        P.map((p, pi) => el('fieldset', {},
          el('legend', {}, 'Parte ' + (pi + 1)),
          (() => { const t = el('input', { type: 'text', 'aria-label': 'Nome da parte' }); t.value = p.parte; t.addEventListener('input', () => { p.parte = t.value; }); return [el('label', { class: 'l' }, 'Nome da parte'), t]; })(),
          p.itens.map((it, ii) => { num++; const t = el('textarea', { rows: '2', 'aria-label': 'Pergunta ' + num }); t.value = it.q; t.addEventListener('input', () => { it.q = t.value; });
            return el('div', {}, el('label', { class: 'l' }, 'Pergunta ' + num), el('div', { class: 'item' }, t,
              el('div', { style: 'display:flex;flex-direction:column;gap:4px' },
                el('button', { class: 'btn sec mini', type: 'button', 'aria-label': 'Subir pergunta', onclick: () => { if (ii > 0) { [p.itens[ii - 1], p.itens[ii]] = [p.itens[ii], p.itens[ii - 1]]; desenha(); } } }, '↑'),
                el('button', { class: 'btn sec mini', type: 'button', 'aria-label': 'Descer pergunta', onclick: () => { if (ii < p.itens.length - 1) { [p.itens[ii + 1], p.itens[ii]] = [p.itens[ii], p.itens[ii + 1]]; desenha(); } } }, '↓'),
                el('button', { class: 'btn sec mini', type: 'button', onclick: () => { p.itens.splice(ii, 1); desenha(); } }, 'Apagar')))); }),
          el('p', {}, el('button', { class: 'btn sec mini', type: 'button', onclick: () => { p.itens.push({ id: novoId(), q: '' }); desenha(); } }, '+ Pergunta nesta parte'), ' ',
            p.itens.length ? '' : el('button', { class: 'btn sec mini', type: 'button', onclick: () => { P.splice(pi, 1); desenha(); } }, 'Apagar parte')))),
        el('p', {}, el('button', { class: 'btn sec mini', type: 'button', onclick: () => { P.push({ parte: 'Nova parte', itens: [{ id: novoId(), q: '' }] }); desenha(); } }, '+ Nova parte'), ' ',
          el('button', { class: 'btn sec mini', type: 'button', onclick: () => { P.splice(0, P.length, ...briefingPadrao(d.cliente.tipo)); d.briefing.extras = []; d.briefing.remover = []; desenha(); } }, 'Voltar ao briefing padrão')));
    }
    desenha();
    return caixa;
  }
  async function telaCliente(slug) {
    let r; try { r = await api('ver', null, '&slug=' + encodeURIComponent(slug)); } catch (x) { return lista(); }
    const d = r.dados; titulo(d.cliente.empresa || d.cliente.mentorado);
    topo(el('button', { class: 'btn sec', type: 'button', onclick: lista }, '← Clientes'));
    const link = linkDe(r.token);
    const msg = el('span', { class: 'msg' });
    const copiar = el('button', { class: 'btn sec mini', type: 'button', onclick: async () => { try { await navigator.clipboard.writeText(link); copiar.textContent = 'Copiado'; } catch (e) { copiar.textContent = 'Copie manualmente'; } } }, 'Copiar link');
    const nresp = Object.values(r.respostas.respostas || {}).filter(v => Array.isArray(v) ? v.length : String(v).trim() !== '').length;
    app.replaceChildren(
      el('div', { class: 'caixa' }, el('h2', {}, d.cliente.mentorado),
        el('p', { class: 'msg' }, nresp + ' respostas preenchidas' + (r.respostas.atualizado ? ' · última em ' + new Date(r.respostas.atualizado).toLocaleString('pt-BR') : '')),
        el('div', { class: 'link' }, el('code', {}, link), copiar, el('a', { class: 'btn mini', href: link, target: '_blank', rel: 'noopener' }, 'Abrir portal'), el('a', { class: 'btn ouro mini', href: link + '#resumo', target: '_blank', rel: 'noopener' }, 'Ver respostas')),
        el('p', { class: 'msg' }, 'Envie este link para o cliente. Ele é único e dá acesso só ao portal dele.')),
      editorBriefing(d),
      el('h2', { style: 'margin-top:28px' }, 'Conteúdo deste cliente'),
      el('p', { class: 'msg' }, 'O que você escrever aqui aparece só no portal deste cliente.'),
      ...editor(d),
      el('div', { class: 'fixo' }, el('button', { class: 'btn ouro', type: 'button', onclick: async () => { msg.textContent = 'Salvando…'; try { await api('salvar_dados', { dados: d }, '&slug=' + encodeURIComponent(slug)); msg.textContent = 'Salvo. O cliente já vê as mudanças.'; } catch (x) { msg.className = 'erro'; msg.textContent = x.message; } } }, 'Salvar alterações'), msg));
  }

  async function telaModelo() {
    const d = await api('modelo'); titulo('Modelo');
    topo(el('button', { class: 'btn sec', type: 'button', onclick: lista }, '← Clientes'));
    const msg = el('span', { class: 'msg' });
    app.replaceChildren(el('p', { class: 'msg' }, 'Estes valores entram em todo cliente novo. Os clientes que já existem não mudam.'), ...editor(d),
      el('div', { class: 'fixo' }, el('button', { class: 'btn ouro', type: 'button', onclick: async () => { msg.textContent = 'Salvando…'; try { await api('salvar_modelo', { dados: d }); msg.textContent = 'Modelo salvo.'; } catch (x) { msg.className = 'erro'; msg.textContent = x.message; } } }, 'Salvar modelo'), msg));
  }

  inicio();
})();
</script>
</body>
</html>
