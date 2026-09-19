let allTickets = [];
let filteredTickets = [];
let userType = '';

async function checkSession() {
    try {
        const response = await fetch('/tickets/api/auth.php?action=check-session');
        const data = await response.json();
        if (data.authenticated) {
            userType = data.user_type;
            const userInfo = document.getElementById('user-info');
            if (userInfo) {
                userInfo.textContent = `${data.name} (${userType === 'admin' ? 'Administrador' : 'Colaborador'})`;
            }

            // Inyectar botón de reporte y mostrar countdown si es admin
            if (userType === 'admin') {
                console.log('Configurando vista de administrador...');

                // Inyectar cuadro de countdown
                const statsGrid = document.querySelector('.stats-grid');
                if (statsGrid && !document.getElementById('countdown-box')) {
                    const box = document.createElement('div');
                    box.className = 'stat-box highlight stat-countdown-card';
                    box.id = 'countdown-box';
                    box.innerHTML = `
                        <div class="stat-number" id="stat-countdown">0</div>
                        <div class="stat-text" title="Días restantes para el envío automático de reporte y limpieza de tickets.">Días para Reset</div>
                    `;
                    statsGrid.appendChild(box);
                    updateCountdown(); // Actualizar número inmediatamente
                }

                const actionsWrapper = document.querySelector('.dashboard-actions') || document.querySelector('.dashboard-header');
                if (actionsWrapper && !document.getElementById('btn-report')) {
                    const btn = document.createElement('a');
                    btn.href = '/tickets/api/tickets.php?action=export';
                    btn.id = 'btn-report';
                    btn.className = 'btn btn-secondary btn-report';
                    btn.innerHTML = `
                        <svg class="btn-icon-sm" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                            <polyline points="7 10 12 15 17 10"></polyline>
                            <line x1="12" y1="15" x2="12" y2="3"></line>
                        </svg>
                        <span>Generar Reporte</span>
                    `;
                    const primaryBtn = actionsWrapper.querySelector('.btn-primary');
                    if (primaryBtn) {
                        actionsWrapper.insertBefore(btn, primaryBtn);
                    } else {
                        actionsWrapper.appendChild(btn);
                    }
                    console.log('Botón inyectado con éxito.');
                }
            }
            return true;
        }
        return false;
    } catch (error) {
        console.error('Error checking session:', error);
        return false;
    }
}

async function loadTickets() {
    console.log('Iniciando carga de tickets...');
    try {
        const sessionActive = await checkSession();
        if (!sessionActive) {
            console.warn('Sesión no activa, redireccionando...');
            window.location.href = '/tickets/index.php?page=login';
            return;
        }

        console.log('Solicitando lista de tickets...');
        const response = await fetch('/tickets/api/tickets.php?action=list');
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();
        console.log('Datos recibidos:', data);

        if (data.success) {
            allTickets = data.tickets || [];
            populateDepartmentFilter();
            populateAssigneeFilter();
            console.log(`Cargados ${allTickets.length} tickets`);
            filterAndDisplay();
            updateStats();
        } else {
            console.error('Error de API:', data.error);
            showMessage('Error al cargar tickets: ' + (data.error || 'Desconocido'), 'error');
        }
    } catch (error) {
        console.error('Error en loadTickets:', error);
        showMessage('Error de conexión o de datos', 'error');
        const container = document.getElementById('tickets-container');
        if (container) {
            container.innerHTML = `<p class="error-msg">Error al cargar tickets: ${error.message}</p>`;
        }
    }
}

async function loadNotifications() {
    try {
        const r = await fetch('/tickets/api/tickets.php?action=notifications');
        const d = await r.json();
        const list = d.notifications || [];
        const countEl = document.getElementById('notifications-count');
        if (countEl) {
            countEl.textContent = list.length;
            countEl.style.display = list.length > 0 ? 'inline-flex' : 'none';
        }

        const panel = document.getElementById('notifications-panel');
        if (!panel) return;

        if (list.length === 0) {
            panel.innerHTML = `
                <div class="notif-header">
                    <div class="notif-header-info">
                        <svg class="notif-header-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>
                        <strong>Centro de Notificaciones</strong>
                    </div>
                </div>
                <div class="notif-empty-state">
                    <div class="notif-empty-icon-wrap">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/></svg>
                    </div>
                    <p class="notif-empty-title">Sin notificaciones pendientes</p>
                    <p class="notif-empty-desc">No hay novedades registradas recientemente.</p>
                </div>
            `;
        } else {
            panel.innerHTML = `
                <div class="notif-header">
                    <div class="notif-header-info">
                        <span class="notif-header-pulse"></span>
                        <strong>Centro de Notificaciones</strong>
                        <span class="notif-header-badge">${list.length}</span>
                    </div>
                    <button type="button" class="notif-close-btn" id="notif-close-btn" title="Cerrar panel">✕</button>
                </div>
                <div class="notif-list-container">
                    ${list.slice(0, 15).map(n => {
                        const isUpdate = (n.message || '').toLowerCase().includes('actualizado');
                        const matchFolio = (n.message || '').match(/(TK-\d{4}-\d+|#\d+)/i);
                        const folio = matchFolio ? matchFolio[0] : '';
                        let cleanMsg = n.message || '';
                        if (folio) {
                            cleanMsg = cleanMsg.replace(folio, '').replace(/^[:\s·-]+/, '').trim();
                        }
                        return `
                            <div class="notif-card ${isUpdate ? 'is-update' : 'is-created'}">
                                <div class="notif-badge-icon">
                                    ${isUpdate 
                                        ? '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21.5 2v6h-6M21.34 15.57a10 10 0 1 1-.57-8.38l5.67-5.67"/></svg>' 
                                        : '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>'}
                                </div>
                                <div class="notif-body">
                                    <div class="notif-topline">
                                        ${folio ? `<span class="notif-folio-tag">${folio}</span>` : ''}
                                        <span class="notif-action-tag ${isUpdate ? 'tag-update' : 'tag-create'}">
                                            ${isUpdate ? 'Actualización' : 'Nuevo Ticket'}
                                        </span>
                                    </div>
                                    <p class="notif-message-text">${cleanMsg || n.message}</p>
                                    <div class="notif-timestamp">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                        <span>${n.at || 'Reciente'}</span>
                                    </div>
                                </div>
                            </div>
                        `;
                    }).join('')}
                </div>
            `;
            const closeBtn = document.getElementById('notif-close-btn');
            if (closeBtn) {
                closeBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    panel.style.display = 'none';
                });
            }
        }
    } catch(e) {
        console.error('Error cargando notificaciones:', e);
    }
}

function filterAndDisplay() {
    const search = document.getElementById('search').value.toLowerCase();
    const status = document.getElementById('status-filter').value;
    const priority = document.getElementById('priority-filter').value;
    const department = document.getElementById('department-filter').value;
    const assignee = document.getElementById('assignee-filter').value;
    const from = document.getElementById('date-from').value;
    const to = document.getElementById('date-to').value;

    filteredTickets = allTickets.filter(ticket => {
        const matchSearch = (ticket.title && ticket.title.toLowerCase().includes(search)) ||
            ticket.id.toString().includes(search) || (ticket.folio || '').toLowerCase().includes(search);
        const matchStatus = !status || ticket.status === status;
        const matchPriority = !priority || ticket.priority === priority;
        const matchDepartment = !department || ticket.department === department;
        const matchAssignee = !assignee || (ticket.assigned_to || '') === assignee;
        const created = (ticket.created_at || '').slice(0, 10);
        const matchFrom = !from || created >= from;
        const matchTo = !to || created <= to;

        return matchSearch && matchStatus && matchPriority && matchDepartment && matchAssignee && matchFrom && matchTo;
    });

    displayTickets();
}

function populateDepartmentFilter() { const select=document.getElementById('department-filter'); const current=select.value; const departments=[...new Set(allTickets.map(t=>t.department).filter(Boolean))].sort(); select.innerHTML='<option value="">Todos los departamentos</option>'+departments.map(d=>`<option>${d}</option>`).join(''); select.value=current; }
function populateAssigneeFilter() { const select=document.getElementById('assignee-filter'); const current=select.value; const people=[...new Set(allTickets.map(t=>t.assigned_to).filter(Boolean))].sort(); select.innerHTML='<option value="">Todos los responsables</option>'+people.map(p=>`<option>${p}</option>`).join(''); select.value=current; }

function displayTickets() {
    const container = document.getElementById('tickets-container');

    if (filteredTickets.length === 0) {
        container.innerHTML = `
            <div class="no-tickets">
                <svg class="no-tickets-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><circle cx="12" cy="12" r="10"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
                <h3>No hay tickets para mostrar</h3>
                <p>Intenta ajustar los filtros de búsqueda o crea un nuevo ticket.</p>
            </div>
        `;
        return;
    }

    container.innerHTML = filteredTickets.map(ticket => {
        const isOverdue = ticket.due_date && ticket.due_date < new Date().toISOString().slice(0,10) && ticket.status !== 'cerrado';
        const formattedDate = ticket.created_at ? new Date(ticket.created_at).toLocaleDateString('es-ES', { day: '2-digit', month: 'short', year: 'numeric' }) : '';
        const folioStr = ticket.folio || ('#' + ticket.id);

        return `
            <div class="ticket-card ${isOverdue ? 'is-card-overdue' : ''}" onclick="window.location.href='/tickets/index.php?page=ticket&id=${ticket.id}'">
                <div class="ticket-header">
                    <div class="ticket-header-title-box">
                        <span class="ticket-folio-tag">${folioStr}</span>
                        <h3 class="ticket-title-text">${ticket.title}</h3>
                    </div>
                    <div class="header-actions">
                        <span class="badge badge-${ticket.status}">
                            <span class="badge-status-dot"></span>
                            ${ticket.status.replace('_', ' ')}
                        </span>
                        ${userType === 'admin' ? `<button class="btn-delete" onclick="deleteTicket(event, ${ticket.id})" title="Eliminar ticket">Eliminar</button>` : ''}
                    </div>
                </div>
                <p class="ticket-desc">${ticket.description ? ticket.description.substring(0, 120) : 'Sin descripción adicional'}${ticket.description && ticket.description.length > 120 ? '...' : ''}</p>
                <div class="ticket-footer">
                    <div class="footer-left-tags">
                        <span class="ticket-dept">
                            <svg class="footer-tag-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                            ${ticket.department || 'General'}
                        </span>
                        <span class="ticket-creator">
                            <svg class="footer-tag-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                            ${ticket.creator || 'Anónimo'}
                        </span>
                    </div>
                    <div class="footer-right-tags">
                        <span class="ticket-priority priority-${ticket.priority}">${ticket.priority}</span>
                        <span class="ticket-date ${isOverdue ? 'date-overdue' : ''}">
                            <svg class="footer-tag-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                            ${isOverdue ? 'VENCIDO · ' : ''}${formattedDate}
                        </span>
                    </div>
                </div>
            </div>
        `;
    }).join('');
}

async function deleteTicket(event, id) {
    event.stopPropagation();
    if (!confirm('¿Estás seguro de que deseas eliminar este ticket?')) return;

    try {
        const response = await fetch('/tickets/api/tickets.php?action=delete', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id })
        });
        const data = await response.json();

        if (data.success) {
            loadTickets();
        } else {
            alert(data.error || 'Error al eliminar el ticket');
        }
    } catch (error) {
        alert('Error de conexión');
    }
}

function updateStats() {
    document.getElementById('stat-total').textContent = allTickets.length;
    document.getElementById('stat-open').textContent = allTickets.filter(t => t.status === 'abierto').length;
    document.getElementById('stat-process').textContent = allTickets.filter(t => t.status === 'en_proceso').length;
    document.getElementById('stat-closed').textContent = allTickets.filter(t => t.status === 'cerrado').length;
    document.getElementById('stat-overdue').textContent = allTickets.filter(t => t.due_date && t.due_date < new Date().toISOString().slice(0,10) && t.status !== 'cerrado').length;
    document.getElementById('stat-urgent').textContent = allTickets.filter(t => t.priority === 'urgente' && t.status !== 'cerrado').length;
    const closed=allTickets.filter(t=>t.status==='cerrado'&&t.created_at&&t.updated_at); const avg=closed.length?closed.reduce((sum,t)=>sum+((new Date(t.updated_at)-new Date(t.created_at))/3600000),0)/closed.length:0; document.getElementById('stat-average').textContent=avg.toFixed(1)+'h';
    updateCountdown();
}

function updateCountdown() {
    const now = new Date();
    // Primer día del próximo mes a las 00:00:00
    const nextMonth = new Date(now.getFullYear(), now.getMonth() + 1, 1);

    const diffTime = nextMonth - now;
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

    const countdownEl = document.getElementById('stat-countdown');
    if (countdownEl) {
        countdownEl.textContent = diffDays;

        // Agregar un título al stat-text para explicar qué es el reset
        const statText = countdownEl.nextElementSibling;
        if (statText) {
            statText.title = "Días restantes para el envío automático de reporte y limpieza de tickets.";
        }
    }
}

function showMessage(msg, type = 'info') {
    console.log(`[${type}] ${msg}`);
}

document.getElementById('search').addEventListener('input', filterAndDisplay);
document.getElementById('status-filter').addEventListener('change', filterAndDisplay);
document.getElementById('priority-filter').addEventListener('change', filterAndDisplay);
document.getElementById('department-filter').addEventListener('change', filterAndDisplay);
document.getElementById('assignee-filter').addEventListener('change', filterAndDisplay);
document.getElementById('date-from').addEventListener('change', filterAndDisplay);
document.getElementById('date-to').addEventListener('change', filterAndDisplay);

// Control de apertura y cierre de notificaciones
const notifBtn = document.getElementById('notifications-button');
if (notifBtn) {
    notifBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        const p = document.getElementById('notifications-panel');
        if (!p) return;
        const isHidden = p.style.display === 'none' || !p.style.display;
        p.style.display = isHidden ? 'block' : 'none';
    });
}

// Cerrar dropdown al hacer click fuera
document.addEventListener('click', (e) => {
    const p = document.getElementById('notifications-panel');
    const b = document.getElementById('notifications-button');
    if (p && p.style.display === 'block') {
        if (!p.contains(e.target) && !b.contains(e.target)) {
            p.style.display = 'none';
        }
    }
});

// Inicialización
updateCountdown();
loadTickets();
loadNotifications();
setInterval(loadTickets, 30000);


