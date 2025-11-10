const qs = (sel) => document.querySelector(sel);
const qsa = (sel) => [...document.querySelectorAll(sel)];
const show = (id) => {
  qsa('.view').forEach(v => v.classList.remove('active'));
  qs(`#view-${id}`).classList.add('active');
};
const TOKEN_KEY = 'cc_token';
const getToken = () => localStorage.getItem(TOKEN_KEY) || '';
const setToken = (t) => localStorage.setItem(TOKEN_KEY, t);
const clearToken = () => localStorage.removeItem(TOKEN_KEY);
const API_HEADERS = () => {
  const h = { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' };
  const t = getToken();
  if (t) h['Authorization'] = `Bearer ${t}`;
  return h;
};
const getCsrfToken = () => {
  const el = document.querySelector('meta[name="csrf-token"]');
  return el ? el.getAttribute('content') : '';
};
const getCookie = (name) => {
  const value = `; ${document.cookie}`;
  const parts = value.split(`; ${name}=`);
  if (parts.length === 2) return parts.pop().split(';').shift();
};

let currentUser = null;
let currentRole = null;

async function ensureCsrf() { return; }

const hasSession = () => Boolean(getToken());

async function fetchUser() {
  if (!hasSession()) return null;
  const res = await fetch('/api/user', { headers: API_HEADERS() });
  if (!res.ok) return null;
  return res.json().catch(() => null);
}

async function login(email, password) {
  await ensureCsrf();
  const form = new URLSearchParams();
  form.append('email', email);
  form.append('password', password);
  form.append('remember', 'true');

  const res = await fetch('/api/login', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8', 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
    body: form
  });
  if (!res.ok) {
    const err = await res.json().catch(() => ({ error: 'Échec' }));
    throw new Error(err.error || 'Échec de connexion');
  }
  const data = await res.json().catch(() => null);
  const apiToken = data?.token;
  if (apiToken) setToken(apiToken);
}

async function logout() {
  await fetch('/api/logout', { method: 'POST', headers: API_HEADERS() });
  currentUser = null; currentRole = null;
  clearToken();
  qs('#logoutBtn').hidden = true;
  show('login');
}

async function loadAdminPending() {
  const res = await fetch('/api/reservations/pending', { headers: API_HEADERS() });
  const data = await res.json().catch(() => ({ data: [] }));
  const cards = (data.data || []).map(r => `
    <div class="card-item">
      <div class="row"><strong>#${r.id}</strong><span class="meta">${(r.item_type || '').split('\\').pop()} • ${r.item_id}</span></div>
      <div class="row"><span>${new Date(r.date_debut).toLocaleString()}</span><span class="meta">→</span><span>${new Date(r.date_fin).toLocaleString()}</span></div>
      <div class="row">
        <button class="btn small success" data-approve="${r.id}">Approuver</button>
        <button class="btn small danger" data-reject="${r.id}">Rejeter</button>
      </div>
    </div>
  `).join('');
  qs('#adminPending').innerHTML = cards || '<div class="muted">Aucune demande</div>';

  qs('#adminPending').onclick = async (e) => {
    const approveId = e.target.getAttribute('data-approve');
    const rejectId = e.target.getAttribute('data-reject');
    if (approveId) {
      await fetch(`/api/reservations/${approveId}/approve`, { method:'POST', headers: API_HEADERS() });
      loadAdminPending();
    } else if (rejectId) {
      await fetch(`/api/reservations/${rejectId}/reject`, { method:'POST', headers: API_HEADERS() });
      loadAdminPending();
    }
  };
}

async function loadTeacherReservations() {
  const res = await fetch('/api/teacher/reservations', { headers: API_HEADERS() });
  const data = await res.json().catch(() => ({ data: [] }));
  qs('#teacherList').innerHTML = (data.data || []).map(r => `
    <div class="item">
      <div>
        <strong>#${r.id}</strong> • ${(r.item_type || '').split('\\').pop()} ${r.item_id}
        <div class="muted">${new Date(r.date_debut).toLocaleString()} → ${new Date(r.date_fin).toLocaleString()}</div>
      </div>
      <span class="badge ${r.statut === 'approved' ? 'ok' : (r.statut === 'rejected' ? 'nok' : '')}">${r.statut}</span>
    </div>
  `).join('') || '<div class="muted">Aucune réservation pour l’instant.</div>';
}

async function submitTeacherReservation(formEl) {
  const fd = new FormData(formEl);
  const raw = Object.fromEntries(fd.entries());
  const toIso = (v) => { try { return new Date(v).toISOString(); } catch { return v; } };
  const payload = { ...raw, date_debut: toIso(raw.date_debut), date_fin: toIso(raw.date_fin) };
  const res = await fetch('/api/teacher/reservations', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', ...API_HEADERS() },
    body: JSON.stringify(payload),
  });
  if (res.ok) {
    formEl.reset();
    loadTeacherReservations();
    qs('#teacherCreateError').hidden = true;
  } else {
    const err = await res.json().catch(() => ({ error: 'Erreur' }));
    qs('#teacherCreateError').textContent = err.error || 'Erreur de validation.';
    qs('#teacherCreateError').hidden = false;
  }
}

async function loadStudentData() {
  const resS = await fetch('/api/student/salles', { headers: API_HEADERS() });
  const resM = await fetch('/api/student/materiels', { headers: API_HEADERS() });
  const parseArray = async (r) => {
    if (!r.ok) return [];
    try { const d = await r.json(); return Array.isArray(d) ? d : (Array.isArray(d?.data) ? d.data : []); } catch { return []; }
  };
  const salles = await parseArray(resS);
  const materiels = await parseArray(resM);

  qs('#studentSalles').innerHTML = salles.map(s => `
    <div class="item">
      <div><strong>${s.nom_salle}</strong> • ${s.capacite} places • ${s.localisation || '—'}</div>
  <span class="badge ${isAvailable(s.disponible) ? 'ok' : 'nok'}">${isAvailable(s.disponible) ? 'Libre' : 'Occupé'}</span>
    </div>
  `).join('');

  qs('#studentMateriels').innerHTML = materiels.map(m => `
    <div class="item">
      <div><strong>${m.nom_materiel}</strong></div>
  <span class="badge ${isAvailable(m.disponible) ? 'ok' : 'nok'}">${isAvailable(m.disponible) ? 'Libre' : 'Occupé'}</span>
    </div>
  `).join('');
}

async function submitStudentReservation(formEl) {
  const fd = new FormData(formEl);
  const raw = Object.fromEntries(fd.entries());
  const toIso = (v) => { try { return new Date(v).toISOString(); } catch { return v; } };
  const payload = { ...raw, date_debut: toIso(raw.date_debut), date_fin: toIso(raw.date_fin) };
  const res = await fetch('/api/student/reservations', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', ...API_HEADERS() },
    body: JSON.stringify(payload),
  });
  if (res.ok) {
    formEl.reset();
    qs('#studentCreateError').hidden = true;
    alert('Demande envoyée !');
  } else {
    const err = await res.json().catch(() => ({ error: 'Erreur' }));
    qs('#studentCreateError').textContent = err.error || 'Erreur de validation.';
    qs('#studentCreateError').hidden = false;
  }
}

async function init() {
  qs('#loginForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const email = e.target.email.value.trim();
    const password = e.target.password.value;
    qs('#loginError').hidden = true;
    try {
      await login(email, password);
      currentUser = await fetchUser();
      currentRole = currentUser?.role?.name;
      qs('#logoutBtn').hidden = false;
      if (!currentUser) {
        qs('#loginError').textContent = 'Identifiants invalides';
        qs('#loginError').hidden = false;
        return;
      }
      if (currentRole === 'Administrateur') {
        show('admin');
        loadAdminPending();
      } else if (currentRole === 'Enseignant') {
        show('teacher');
        loadTeacherReservations();
      } else if (currentRole === 'Étudiant') {
        show('student');
        loadStudentData();
      } else {
        show('login');
        qs('#loginError').textContent = 'Rôle inconnu';
        qs('#loginError').hidden = false;
      }
    } catch (err) {
      qs('#loginError').textContent = err.message || 'Échec de connexion';
      qs('#loginError').hidden = false;
    }
  });

  qs('#logoutBtn').addEventListener('click', logout);
  qs('#teacherCreateForm').addEventListener('submit', async (e) => { e.preventDefault(); submitTeacherReservation(e.target); });
  qs('#studentCreateForm').addEventListener('submit', async (e) => { e.preventDefault(); submitStudentReservation(e.target); });
  const adminAddSalleForm = qs('#adminAddSalleForm');
  const adminAddMaterielForm = qs('#adminAddMaterielForm');
  if (adminAddSalleForm) adminAddSalleForm.addEventListener('submit', async (e) => { e.preventDefault(); await submitAdminAddSalle(e.target); });
  if (adminAddMaterielForm) adminAddMaterielForm.addEventListener('submit', async (e) => { e.preventDefault(); await submitAdminAddMateriel(e.target); });

  const teacherType = qs('#teacherCreateForm select[name="item_type"]');
  const teacherItem = qs('#teacherItemSelect');
  teacherType.addEventListener('change', async () => {
    const items = await fetchItems(teacherType.value);
    populateItemSelect(teacherItem, items);
  });

  const studentType = qs('#studentCreateForm select[name="item_type"]');
  const studentItem = qs('#studentItemSelect');
  studentType.addEventListener('change', async () => {
    const items = await fetchItems(studentType.value);
    populateItemSelect(studentItem, items);
  });

  try {
    currentUser = await fetchUser();
    if (currentUser) {
      currentRole = currentUser?.role?.name;
      qs('#logoutBtn').hidden = false;
      if (currentRole === 'Administrateur') { 
        show('admin'); 
        loadAdminPending(); 
        loadAdminItems();
      }
      else if (currentRole === 'Enseignant') { 
        show('teacher'); 
        loadTeacherReservations(); 
        try { 
          const items = await fetchItems(teacherType.value); 
          populateItemSelect(teacherItem, items);
        } catch {}
      }
  else if (currentRole === 'Étudiant') { 
    show('student'); 
    loadStudentData(); 
    startStudentAutoRefresh();
    try { 
      const items = await fetchItems(studentType.value); 
      populateItemSelect(studentItem, items);
    } catch {}
  }
      else { show('login'); }
    } else {
      show('login');
    }
  } catch {}
}

document.addEventListener('DOMContentLoaded', init);
document.addEventListener('DOMContentLoaded', () => {
  const btn = qs('#studentRefreshBtn');
  if (btn) btn.addEventListener('click', () => {
    const studentViewActive = qs('#view-student').classList.contains('active');
    if (studentViewActive) loadStudentData();
  });
});
async function fetchItems(type) {
  const parseResponse = async (r) => {
    if (!r.ok) {
      console.warn('fetchItems: requête échouée', { status: r.status, type });
      return [];
    }
    try {
      const data = await r.json();
      if (Array.isArray(data)) return data;
      if (Array.isArray(data?.data)) return data.data; // fallback for paginated/resource structures
      return [];
    } catch {
      console.warn('fetchItems: JSON invalide');
      return [];
    }
  };

  if (type === 'salle') {
    const r = await fetch('/api/items/salles', { headers: API_HEADERS() });
    return parseResponse(r);
  }
  if (type === 'materiel') {
    const r = await fetch('/api/items/materiels', { headers: API_HEADERS() });
    return parseResponse(r);
  }
  return [];
}

function populateItemSelect(selectEl, items) {
  const safeItems = Array.isArray(items) ? items : [];
  if (safeItems.length === 0) {
    selectEl.innerHTML = '<option value="" disabled selected>Aucun élément disponible</option>';
    return;
  }
  const opts = ['<option value="" disabled selected>Choisir</option>']
    .concat(safeItems.map(it => `<option value="${it.id}">${it.nom_salle || it.nom_materiel}${isAvailable(it.disponible) ? '' : ' (Occupé)'}</option>`));
  selectEl.innerHTML = opts.join('');
  try { console.log('populateItemSelect: options chargées', { count: safeItems.length }); } catch {}
}

function isAvailable(val) {
  return val === true || val === 1 || val === '1';
}

async function loadAdminItems() {
  try {
    const [rs, rm] = await Promise.all([
      fetch('/api/admin/items/salles', { headers: API_HEADERS() }),
      fetch('/api/admin/items/materiels', { headers: API_HEADERS() }),
    ]);
    if (!rs.ok) {
      qs('#adminSallesList').innerHTML = `<div class="error">Erreur chargement salles (code ${rs.status}). Vérifiez vos droits Admin.</div>`;
    }
    if (!rm.ok) {
      qs('#adminMaterielsList').innerHTML = `<div class="error">Erreur chargement matériels (code ${rm.status}). Vérifiez vos droits Admin.</div>`;
    }
    const parseArray = async (r) => {
      if (!r.ok) return [];
      try { const d = await r.json(); return Array.isArray(d) ? d : (Array.isArray(d?.data) ? d.data : []); } catch { return []; }
    };
    const salles = await parseArray(rs);
    const materiels = await parseArray(rm);
    qs('#adminSallesList').innerHTML = (salles || []).map(s => `
      <div class="item">
        <div><strong>${s.nom_salle}</strong> • ${s.capacite || '—'} • ${s.localisation || '—'}</div>
        <button class="btn danger small" data-del-salle-id="${s.id}">Supprimer</button>
      </div>
    `).join('') || '<div class="muted">Aucune salle.</div>';
    qs('#adminMaterielsList').innerHTML = (materiels || []).map(m => `
      <div class="item">
        <div><strong>${m.nom_materiel}</strong></div>
        <button class="btn danger small" data-del-materiel-id="${m.id}">Supprimer</button>
      </div>
    `).join('') || '<div class="muted">Aucun matériel.</div>';
    qsa('[data-del-salle-id]').forEach(btn => btn.addEventListener('click', () => deleteSalle(btn.getAttribute('data-del-salle-id'))));
    qsa('[data-del-materiel-id]').forEach(btn => btn.addEventListener('click', () => deleteMateriel(btn.getAttribute('data-del-materiel-id'))));
  } catch {}
}

async function deleteSalle(id) {
  if (!confirm('Supprimer cette salle ?')) return;
  const r = await fetch(`/api/admin/items/salles/${id}`, { method: 'DELETE', headers: API_HEADERS() });
  const data = await r.json().catch(() => ({}));
  if (!r.ok || data?.ok === false) { alert(data?.error || 'Suppression impossible'); return; }
  await loadAdminItems();
  await refreshDropdownsIfVisible();
}

async function deleteMateriel(id) {
  if (!confirm('Supprimer ce matériel ?')) return;
  const r = await fetch(`/api/admin/items/materiels/${id}`, { method: 'DELETE', headers: API_HEADERS() });
  const data = await r.json().catch(() => ({}));
  if (!r.ok || data?.ok === false) { alert(data?.error || 'Suppression impossible'); return; }
  await loadAdminItems();
  await refreshDropdownsIfVisible();
}

async function refreshDropdownsIfVisible() {
  const teacherViewActive = qs('#view-teacher').classList.contains('active');
  const studentViewActive = qs('#view-student').classList.contains('active');
  try {
    if (teacherViewActive) {
      const items = await fetchItems(teacherType.value);
      populateItemSelect(teacherItem, items);
    }
    if (studentViewActive) {
      const items = await fetchItems(studentType.value);
      populateItemSelect(studentItem, items);
    }
  } catch {}
}

let studentRefreshTimer = null;
function startStudentAutoRefresh() {
  if (studentRefreshTimer) clearInterval(studentRefreshTimer);
  studentRefreshTimer = setInterval(() => {
    const studentViewActive = qs('#view-student').classList.contains('active');
    if (studentViewActive) {
      loadStudentData();
    }
  }, 5000);
}

async function submitAdminAddSalle(formEl) {
  const errorEl = qs('#adminAddSalleError');
  if (errorEl) { errorEl.hidden = true; errorEl.textContent = ''; }
  const formData = new FormData(formEl);
  const payload = {
    nom_salle: formData.get('nom_salle')?.toString().trim(),
    capacite: formData.get('capacite') ? Number(formData.get('capacite')) : null,
    localisation: formData.get('localisation')?.toString().trim() || null,
  };
  try {
    const r = await fetch('/api/admin/items/salles', {
      method: 'POST',
      headers: { ...API_HEADERS(), 'Content-Type': 'application/json' },
      body: JSON.stringify(payload),
    });
    const data = await r.json().catch(() => ({}));
    if (!r.ok || data?.ok === false) {
      throw new Error(data?.message || data?.error || 'Échec de création de la salle');
    }
    formEl.reset();
    alert('Salle créée avec succès');
    await loadAdminItems();
    await refreshDropdownsIfVisible();
  } catch (e) {
    if (errorEl) { errorEl.textContent = e.message || 'Erreur'; errorEl.hidden = false; }
  }
}

async function submitAdminAddMateriel(formEl) {
  const errorEl = qs('#adminAddMaterielError');
  if (errorEl) { errorEl.hidden = true; errorEl.textContent = ''; }
  const formData = new FormData(formEl);
  const payload = {
    nom_materiel: formData.get('nom_materiel')?.toString().trim(),
  };
  try {
    const r = await fetch('/api/admin/items/materiels', {
      method: 'POST',
      headers: { ...API_HEADERS(), 'Content-Type': 'application/json' },
      body: JSON.stringify(payload),
    });
    const data = await r.json().catch(() => ({}));
    if (!r.ok || data?.ok === false) {
      throw new Error(data?.message || data?.error || 'Échec de création du matériel');
    }
    formEl.reset();
    alert('Matériel créé avec succès');
    await loadAdminItems();
    await refreshDropdownsIfVisible();
  } catch (e) {
    if (errorEl) { errorEl.textContent = e.message || 'Erreur'; errorEl.hidden = false; }
  }
}