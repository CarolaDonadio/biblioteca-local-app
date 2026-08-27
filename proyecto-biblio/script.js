/* =================== DATOS EN MEMORIA =================== */
let books = [
  {id:1, isbn:'978-950-04-1', titulo:'Rayuela', autor:'Julio Cortázar', editorial:'Sudamericana', anio:1963, categoria:'Ficción', cantidad:3, disponibles:2, sinopsis:'Novela experimental que puede leerse en distinto orden.'},
  {id:2, isbn:'978-950-04-2', titulo:'El Aleph', autor:'Jorge Luis Borges', editorial:'Emecé', anio:1949, categoria:'Ficción', cantidad:2, disponibles:0, sinopsis:'Cuentos breves sobre el infinito y la memoria.'},
  {id:3, isbn:'978-950-04-3', titulo:'Historia de Chascomús', autor:'Comisión Local', editorial:'Municipal', anio:2005, categoria:'Historia', cantidad:1, disponibles:1, sinopsis:'Crónica documental de la ciudad.'},
  {id:4, isbn:'978-950-04-4', titulo:'Manual de Ciencias', autor:'Equipo Docente', editorial:'Kapelusz', anio:2018, categoria:'Ciencia', cantidad:4, disponibles:4, sinopsis:'Guía escolar de ciencias.'},
];
let nextBookId = 5;

let members = [
  {id:1, nombre:'Lucía Fernández', dni:'34.221.001', email:'lucia@mail.com', tel:'2241-123456', domicilio:'San Martín 450', tipo:'Estudiante', estado:'activo'},
  {id:2, nombre:'Marcos Ibáñez', dni:'28.554.221', email:'marcos@mail.com', tel:'2241-654321', domicilio:'Belgrano 120', tipo:'Docente', estado:'activo'},
  {id:3, nombre:'Rafael Sosa', dni:'40.112.334', email:'rafael@mail.com', tel:'2241-998877', domicilio:'Rivadavia 88', tipo:'General', estado:'activo'},
];
let nextMemberId = 4;
const TIPOS = {General:{dias:7, max:2}, Estudiante:{dias:14, max:3}, Docente:{dias:21, max:5}};
const MULTA_DIA = 50;

let loans = [
  {id:1, bookId:2, memberId:1, fechaPrestamo: daysAgo(20), fechaDevolucion: daysAgo(6), devuelto:false, renovado:false},
];
let nextLoanId = 2;

let reservations = []; // {id, bookId, memberId, fecha, estado:'en cola'|'lista para retirar'}
let nextReservationId = 1;

let history = { 1: [{bookId:1, fecha: daysAgo(60)}] }; // memberId -> préstamos ya devueltos

let bajas = []; // ejemplares perdidos/dañados: {bookId, motivo, fecha}

function daysAgo(n){ const d=new Date(); d.setDate(d.getDate()-n); return d; }
function addDays(date,n){ const d=new Date(date); d.setDate(d.getDate()+n); return d; }
function fmt(d){ return d.toLocaleDateString('es-AR'); }

/* =================== NAVEGACIÓN APP =================== */
function setMode(mode){
  document.getElementById('btn-admin').classList.toggle('active', mode==='admin');
  document.getElementById('btn-comunidad').classList.toggle('active', mode==='comunidad');
  document.getElementById('tabbar-admin').style.display = mode==='admin' ? 'flex':'none';
  document.getElementById('tabbar-comunidad').style.display = mode==='comunidad' ? 'flex':'none';
  setTab(mode, mode==='admin' ? 'libros' : 'buscar');
}
function setTab(mode, tab){
  document.querySelectorAll('.panel').forEach(p=>p.classList.remove('active'));
  document.getElementById(mode+'-'+tab).classList.add('active');
  const bar = document.getElementById('tabbar-'+mode);
  [...bar.children].forEach(b=>b.classList.remove('active'));
  const idx = mode==='admin' ? ['libros','socios','prestamos','reportes'].indexOf(tab) : ['buscar','cuenta'].indexOf(tab);
  bar.children[idx].classList.add('active');
  refreshAll();
}
function toast(msg){
  const t=document.getElementById('toast'); t.textContent=msg; t.classList.add('show');
  clearTimeout(window._toastTimer); window._toastTimer=setTimeout(()=>t.classList.remove('show'),2200);
}

/* =================== LIBROS (ADMIN) =================== */
function openBookForm(){ document.getElementById('book-form-card').style.display='block'; }
function closeBookForm(){
  document.getElementById('book-form-card').style.display='none';
  ['f-isbn','f-titulo','f-autor','f-editorial','f-anio','f-cantidad','f-sinopsis'].forEach(id=>document.getElementById(id).value='');
}
function saveBook(){
  const titulo=document.getElementById('f-titulo').value.trim();
  if(!titulo){ toast('Falta el título'); return; }
  const cantidad = parseInt(document.getElementById('f-cantidad').value)||1;
  books.push({
    id: nextBookId++, isbn: document.getElementById('f-isbn').value || '—', titulo,
    autor: document.getElementById('f-autor').value || 'Sin datos',
    editorial: document.getElementById('f-editorial').value || 'Sin datos',
    anio: parseInt(document.getElementById('f-anio').value) || null,
    categoria: document.getElementById('f-categoria').value,
    cantidad, disponibles: cantidad, sinopsis: document.getElementById('f-sinopsis').value,
  });
  closeBookForm(); toast('Libro guardado'); refreshAll();
}
function deleteBook(id){
  if(loans.some(l=>l.bookId===id && !l.devuelto)){ toast('No se puede dar de baja: tiene préstamos activos'); return; }
  books = books.filter(b=>b.id!==id);
  toast('Libro dado de baja'); refreshAll();
}
function markLost(id){
  const b=books.find(x=>x.id===id);
  if(!b || b.cantidad<=0){ toast('No hay ejemplares para marcar'); return; }
  b.cantidad--; if(b.disponibles>0) b.disponibles--;
  bajas.push({bookId:id, motivo:'perdido/dañado', fecha:new Date()});
  toast('Ejemplar registrado como perdido/dañado'); refreshAll();
}
function renderAdminBooks(){
  const q=(document.getElementById('admin-book-search')?.value||'').toLowerCase();
  const tbody=document.getElementById('admin-books-tbody');
  const filtered = books.filter(b => !q || b.titulo.toLowerCase().includes(q) || b.autor.toLowerCase().includes(q) || (b.isbn||'').toLowerCase().includes(q));
  tbody.innerHTML = filtered.map(b=>`
    <tr>
      <td><strong>${b.titulo}</strong><br><span class="mono muted-text" style="font-size:11px;">${b.isbn}</span></td>
      <td>${b.autor}</td><td>${b.categoria}</td><td>${b.anio||'—'}</td><td>${b.cantidad}</td>
      <td>${b.disponibles>0 ? `<span class="pill ok">${b.disponibles}</span>` : `<span class="pill warn">agotado</span>`}</td>
      <td>
        <button class="icon-btn" onclick="markLost(${b.id})" title="Marcar ejemplar perdido/dañado">⚠</button>
        <button class="icon-btn" onclick="deleteBook(${b.id})" title="Dar de baja">✕</button>
      </td>
    </tr>`).join('') || `<tr><td colspan="7"><div class="empty">No se encontraron libros.</div></td></tr>`;
  const autores=[...new Set(books.map(b=>b.autor))], editoriales=[...new Set(books.map(b=>b.editorial))];
  document.getElementById('autores-list').innerHTML = autores.map(a=>`<option value="${a}">`).join('');
  document.getElementById('editoriales-list').innerHTML = editoriales.map(e=>`<option value="${e}">`).join('');
}

/* =================== SOCIOS (ADMIN) =================== */
function openMemberForm(){ document.getElementById('member-form-card').style.display='block'; }
function closeMemberForm(){
  document.getElementById('member-form-card').style.display='none';
  ['m-nombre','m-dni','m-email','m-tel','m-domicilio'].forEach(id=>document.getElementById(id).value='');
}
function saveMember(){
  const nombre=document.getElementById('m-nombre').value.trim();
  if(!nombre){ toast('Falta el nombre'); return; }
  members.push({
    id: nextMemberId++, nombre, dni: document.getElementById('m-dni').value, email: document.getElementById('m-email').value,
    tel: document.getElementById('m-tel').value, domicilio: document.getElementById('m-domicilio').value,
    tipo: document.getElementById('m-tipo').value, estado:'activo'
  });
  closeMemberForm(); toast('Socio dado de alta'); refreshAll();
}
function toggleMemberEstado(id){
  const m=members.find(x=>x.id===id);
  m.estado = m.estado==='activo' ? 'suspendido' : 'activo';
  refreshAll();
}
function renderAdminMembers(){
  document.getElementById('admin-members-tbody').innerHTML = members.map(m=>`
    <tr>
      <td><strong>${m.nombre}</strong></td><td>${m.dni||'—'}</td>
      <td>${m.tipo} <span class="muted-text">(${TIPOS[m.tipo].dias}d · ${TIPOS[m.tipo].max})</span></td>
      <td>${m.estado==='activo' ? '<span class="pill ok">activo</span>' : '<span class="pill warn">suspendido</span>'}</td>
      <td class="muted-text">${m.email||'—'}</td>
      <td><button class="icon-btn" onclick="toggleMemberEstado(${m.id})" title="Cambiar estado">⇄</button></td>
    </tr>`).join('');
}

/* =================== PRESTAMOS Y RESERVAS =================== */
function fillLoanSelectors(){
  document.getElementById('p-socio').innerHTML = members.filter(m=>m.estado==='activo').map(m=>`<option value="${m.id}">${m.nombre} (${m.tipo})</option>`).join('');
  document.getElementById('p-libro').innerHTML = books.map(b=>`<option value="${b.id}">${b.titulo} — ${b.disponibles} disp.</option>`).join('');
  updateLoanPreview();
}
function updateLoanPreview(){
  const m=members.find(x=>x.id===parseInt(document.getElementById('p-socio').value));
  const prev=document.getElementById('p-preview');
  if(m){
    const activos = loans.filter(l=>l.memberId===m.id && !l.devuelto).length;
    prev.textContent = `${m.nombre}: ${activos}/${TIPOS[m.tipo].max} préstamos activos · ${TIPOS[m.tipo].dias} días de plazo.`;
  } else prev.textContent='';
}
function doLoan(){
  const memberId=parseInt(document.getElementById('p-socio').value);
  const bookId=parseInt(document.getElementById('p-libro').value);
  if(!memberId||!bookId){ toast('Elegí socio y libro'); return; }
  const m=members.find(x=>x.id===memberId), book=books.find(b=>b.id===bookId);
  const activos = loans.filter(l=>l.memberId===memberId && !l.devuelto).length;
  if(activos>=TIPOS[m.tipo].max){ toast(`Llegó al máximo de préstamos (${TIPOS[m.tipo].max})`); return; }
  if(book.disponibles<=0){ toast('No hay copias disponibles — se puede reservar desde el catálogo'); return; }
  book.disponibles--;
  loans.push({id:nextLoanId++, bookId, memberId, fechaPrestamo:new Date(), fechaDevolucion: addDays(new Date(), TIPOS[m.tipo].dias), devuelto:false, renovado:false});
  toast('Préstamo registrado'); refreshAll();
}
function returnLoan(loanId){
  const l=loans.find(x=>x.id===loanId);
  l.devuelto=true;
  const book=books.find(b=>b.id===l.bookId);
  const atraso = Math.max(0, Math.ceil((new Date() - l.fechaDevolucion)/86400000));
  history[l.memberId] = history[l.memberId]||[];
  history[l.memberId].push({bookId:l.bookId, fecha:new Date()});
  const pendiente = reservations.find(r=>r.bookId===l.bookId && r.estado==='en cola');
  if(pendiente){
    pendiente.estado='lista para retirar';
    toast(atraso>0 ? `Devuelto con ${atraso}d de atraso ($${atraso*MULTA_DIA}) — hay una reserva esperando este libro` : 'Devuelto — hay una reserva esperando este libro');
  } else {
    book.disponibles++;
    toast(atraso>0 ? `Devuelto con ${atraso}d de atraso — multa $${atraso*MULTA_DIA}` : 'Devuelto en término');
  }
  refreshAll();
}
function renewLoan(loanId){
  const l=loans.find(x=>x.id===loanId);
  if(l.renovado){ toast('Ya usó su renovación para este libro'); return; }
  if(reservations.some(r=>r.bookId===l.bookId && r.estado==='en cola')){ toast('No se puede renovar: hay una reserva en espera'); return; }
  l.renovado=true; l.fechaDevolucion = addDays(l.fechaDevolucion, 7);
  toast('Renovado 7 días más'); refreshAll();
}
function reserveBook(bookId, memberId){
  if(!memberId) return;
  if(reservations.some(r=>r.bookId===bookId && r.memberId===memberId && r.estado!=='retirada')){ toast('Ya tenés una reserva de este libro'); return; }
  reservations.push({id:nextReservationId++, bookId, memberId, fecha:new Date(), estado:'en cola'});
  toast('Reserva registrada'); refreshAll();
}
function cancelReservation(id){
  reservations = reservations.filter(r=>r.id!==id);
  toast('Reserva cancelada'); refreshAll();
}
function renderLoans(){
  const activos = loans.filter(l=>!l.devuelto);
  document.getElementById('loans-list').innerHTML = activos.map(l=>{
    const book=books.find(b=>b.id===l.bookId), m=members.find(x=>x.id===l.memberId);
    const vencido = new Date() > l.fechaDevolucion;
    return `<div class="row-item flex-between">
      <div><strong>${book?.titulo}</strong> · <span class="muted-text">${m?.nombre}</span><br>
        <span class="muted-text">Devolución: ${fmt(l.fechaDevolucion)}</span> ${vencido?'<span class="pill warn">vencido</span>':'<span class="pill ok">en término</span>'}</div>
      <div style="display:flex;gap:6px;">
        ${!l.renovado ? `<button class="btn ghost small" onclick="renewLoan(${l.id})">Renovar</button>`:''}
        <button class="btn small" onclick="returnLoan(${l.id})">Devolver</button>
      </div>
    </div>`;
  }).join('') || `<div class="empty">No hay préstamos activos.</div>`;
}
function renderAdminReservations(){
  document.getElementById('admin-reservations-list').innerHTML = reservations.map(r=>{
    const book=books.find(b=>b.id===r.bookId), m=members.find(x=>x.id===r.memberId);
    return `<div class="row-item flex-between">
      <div><strong>${book?.titulo}</strong> · <span class="muted-text">${m?.nombre}</span></div>
      <div>${r.estado==='lista para retirar' ? '<span class="pill ok">lista para retirar</span>' : '<span class="pill muted">en cola</span>'}
        <button class="icon-btn" onclick="cancelReservation(${r.id})" title="Cancelar">✕</button></div>
    </div>`;
  }).join('') || `<div class="empty">No hay reservas activas.</div>`;
}

/* =================== REPORTES (ADMIN) =================== */
function renderReportes(){
  document.getElementById('rep-activos').textContent = loans.filter(l=>!l.devuelto).length;
  document.getElementById('rep-vencidos').textContent = loans.filter(l=>!l.devuelto && new Date()>l.fechaDevolucion).length;
  document.getElementById('rep-bajas').textContent = bajas.length;

  const counts={};
  loans.forEach(l=>counts[l.bookId]=(counts[l.bookId]||0)+1);
  document.getElementById('rep-top').innerHTML = Object.entries(counts).sort((a,b)=>b[1]-a[1]).slice(0,5)
    .map(([id,c])=>`<li>${books.find(b=>b.id==id)?.titulo||'—'} — ${c} préstamo(s)</li>`).join('') || '<li class="muted-text">Sin datos todavía.</li>';

  const vencidos = loans.filter(l=>!l.devuelto && new Date()>l.fechaDevolucion);
  document.getElementById('rep-alertas').innerHTML = vencidos.map(l=>{
    const book=books.find(b=>b.id===l.bookId), m=members.find(x=>x.id===l.memberId);
    const atraso = Math.ceil((new Date()-l.fechaDevolucion)/86400000);
    return `<div class="row-item flex-between"><span>${book?.titulo} — ${m?.nombre}</span><span class="pill warn">${atraso}d · multa $${atraso*MULTA_DIA}</span></div>`;
  }).join('') || `<p class="muted-text">No hay devoluciones vencidas.</p>`;
}

/* =================== COMUNIDAD: BUSCAR =================== */
function renderPublicSearch(){
  const q=(document.getElementById('s-texto').value||'').toLowerCase();
  const cat=document.getElementById('s-categoria').value;
  const results = books.filter(b => (!q || b.titulo.toLowerCase().includes(q) || b.autor.toLowerCase().includes(q)) && (!cat || b.categoria===cat));
  document.getElementById('public-results').innerHTML = results.map(b=>`
    <div class="row-item">
      <div class="flex-between">
        <div><strong>${b.titulo}</strong><div class="muted-text">${b.autor} · ${b.editorial} · ${b.anio||'s/f'} · ${b.categoria}</div></div>
        ${b.disponibles>0 ? `<span class="pill ok">${b.disponibles} disponibles de ${b.cantidad}</span>` : `<button class="btn small" onclick="reserveBook(${b.id}, currentUserId)">Reservar</button>`}
      </div>
      <p style="font-size:13px;margin:8px 0 0;color:var(--text);">${b.sinopsis||'Sin sinopsis cargada.'}</p>
    </div>`).join('') || `<div class="empty">No hay libros que coincidan.</div>`;
}

/* =================== COMUNIDAD: CUENTA =================== */
function renderReaderAccount(){
  const id = currentUserId;
  if(!id) return;
  const mine = loans.filter(l=>l.memberId===id && !l.devuelto);
  document.getElementById('reader-loans').innerHTML = mine.map(l=>{
    const book=books.find(b=>b.id===l.bookId);
    return `<div class="row-item">
      <strong>${book?.titulo}</strong><div class="muted-text">Devolución: ${fmt(l.fechaDevolucion)}</div>
      <div style="margin-top:6px;">${!l.renovado?`<button class="btn ghost small" onclick="renewLoan(${l.id})">Pedir renovación</button>`:'<span class="pill muted">ya renovado</span>'}</div>
    </div>`;
  }).join('') || `<div class="empty">No tenés préstamos activos.</div>`;

  const mineRes = reservations.filter(r=>r.memberId===id);
  document.getElementById('reader-reservations').innerHTML = mineRes.map(r=>{
    const book=books.find(b=>b.id===r.bookId);
    return `<div class="row-item flex-between"><span>${book?.titulo}</span>${r.estado==='lista para retirar'?'<span class="pill ok">lista para retirar</span>':'<span class="pill muted">en cola</span>'}</div>`;
  }).join('') || `<p class="muted-text">Sin reservas activas.</p>`;

  const h = history[id]||[];
  document.getElementById('reader-history').innerHTML = h.length
    ? h.map(x=>`${books.find(b=>b.id===x.bookId)?.titulo} — ${fmt(x.fecha)}`).join('<br>')
    : 'Todavía no hay historial de préstamos.';
}

/* =================== REFRESH GLOBAL =================== */
function refreshAll(){
  renderAdminBooks(); renderAdminMembers(); fillLoanSelectors(); renderLoans(); renderAdminReservations();
  renderReportes(); renderPublicSearch(); renderReaderAccount();
}

/* =====================================================================
   PANTALLA 1 — selección de usuario (estilo simple tipo "perfiles")
   ===================================================================== */
let currentUserId = null;
let loginSelectedCardMember = null;
const LG_COLORS = {General:'#c58b1f', Estudiante:'#2f6fed', Docente:'#2c9a63'};

function initials(nombre){
  return nombre.split(' ').filter(Boolean).slice(0,2).map(p=>p[0].toUpperCase()).join('');
}
function renderLoginUsers(){
  const wrap = document.getElementById('lg-users');
  wrap.innerHTML = members.map(m=>`
    <div class="lg-card" data-id="${m.id}" tabindex="0" onclick="selectLoginUser(${m.id})">
      <div class="lg-avatar" style="background:${LG_COLORS[m.tipo]||'#767a82'}">${initials(m.nombre)}</div>
      <div class="lg-name">${m.nombre}</div>
      <div class="lg-tipo">${m.tipo}</div>
    </div>`).join('') + `
    <div class="lg-card lg-new-card" onclick="toggleNewUserForm(true)">
      <div class="plus">+</div><div class="lg-tipo">Nuevo usuario</div>
    </div>`;
}
function selectLoginUser(id){
  document.querySelectorAll('.lg-card').forEach(c=>c.classList.remove('selected'));
  const card = document.querySelector(`.lg-card[data-id="${id}"]`);
  if(card) card.classList.add('selected');
  loginSelectedCardMember = id;
  document.getElementById('lg-enterBtn').disabled = false;
}
function enterLibrary(){
  if(!loginSelectedCardMember) return;
  currentUserId = loginSelectedCardMember;
  const login = document.getElementById('login-screen');
  login.classList.add('fading');
  setTimeout(()=>{
    login.style.display='none';
    document.getElementById('app-screen').style.display='block';
    refreshAll();
    setMode('comunidad');
    setTab('comunidad','cuenta');
  }, 250);
}
function backToLogin(){
  document.getElementById('app-screen').style.display='none';
  const login = document.getElementById('login-screen');
  login.style.display='flex';
  login.classList.remove('fading');
  currentUserId = null; loginSelectedCardMember = null;
  document.getElementById('lg-enterBtn').disabled = true;
  renderLoginUsers();
}
function toggleNewUserForm(show){
  document.getElementById('lg-form').classList.toggle('show', show);
  if(show) document.getElementById('lg-nu-nombre').focus();
}
function createLoginUser(){
  const nombre = document.getElementById('lg-nu-nombre').value.trim();
  if(!nombre) { document.getElementById('lg-nu-nombre').focus(); return; }
  const tipo = document.getElementById('lg-nu-tipo').value;
  members.push({id:nextMemberId++, nombre, dni:'', email:'', tel:'', domicilio:'', tipo, estado:'activo'});
  document.getElementById('lg-nu-nombre').value='';
  toggleNewUserForm(false);
  renderLoginUsers();
}

/* =================== INICIALIZACIÓN =================== */
document.addEventListener('DOMContentLoaded', ()=>{
  document.getElementById('p-socio').addEventListener('change', updateLoanPreview);
  renderLoginUsers();
  refreshAll();
});