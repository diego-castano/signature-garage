(function () {
    'use strict';

    // ── State ──
    var state = {
        page: 1,
        search: '',
        type: '',
        status: '',
        sold: '',
        loading: false,
        totalPages: 1
    };

    var debounceTimer;

    function init() {
        bindDraftButtons();
        bindNoResToggle();
        bindContactActions();
        bindVehiclesTable();
        initChart();
    }

    // ═══════════ DRAFT BUTTONS (Recent Vehicles) ═══════════

    function bindDraftButtons() {
        document.querySelectorAll('.sgu-draft-btn').forEach(function (btn) {
            btn.addEventListener('click', function () { setDraft(btn); });
        });
    }

    function setDraft(btn) {
        if (btn.disabled) return;
        var postId = btn.getAttribute('data-id');
        btn.disabled = true;
        btn.innerHTML = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="sgu-spin"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg>';

        ajax('sgu_vehicle_set_draft', { post_id: postId }, function (res) {
            if (res.success) {
                btn.innerHTML = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>';
                btn.classList.add('sgu-done');
                // Fade the row
                var row = btn.closest('.sgu-vehicle-row');
                if (row) {
                    row.classList.add('sgu-fade-out');
                    setTimeout(function () { row.remove(); }, 300);
                }
                loadVehicles();
            } else {
                btn.innerHTML = 'Error';
                btn.disabled = false;
            }
        });
    }

    // ═══════════ NO RESIDENTES TOGGLE ═══════════

    function bindNoResToggle() {
        document.querySelectorAll('.sgu-nores-toggle').forEach(function (btn) {
            btn.addEventListener('click', function () { toggleNoRes(btn); });
        });
    }

    function toggleNoRes(btn) {
        if (btn.disabled) return;
        var postId = btn.getAttribute('data-id');
        btn.disabled = true;

        ajax('sgu_toggle_no_residentes', { post_id: postId }, function (res) {
            btn.disabled = false;
            if (!res.success) return;
            var isActive = res.data.no_residentes;
            var row = btn.closest('.sgu-vehicle-row');

            if (isActive) {
                btn.classList.add('sgu-nores-active');
                btn.classList.remove('sgu-btn-ghost');
                if (row) row.classList.add('sgu-row-nores');
                // Add tag if not present
                var sub = row ? row.querySelector('.sgu-vehicle-sub') : null;
                if (sub && !sub.querySelector('.sgu-nores-tag')) {
                    var tag = document.createElement('span');
                    tag.className = 'sgu-nores-tag';
                    tag.innerHTML = '\uD83C\uDDFA\uD83C\uDDF8 NO RESIDENTES';
                    sub.appendChild(tag);
                }
            } else {
                btn.classList.remove('sgu-nores-active');
                btn.classList.add('sgu-btn-ghost');
                if (row) row.classList.remove('sgu-row-nores');
                if (row) {
                    var tag = row.querySelector('.sgu-nores-tag');
                    if (tag) tag.remove();
                }
            }
        });
    }

    // ═══════════ CONTACT ACTIONS ═══════════

    function bindContactActions() {
        // Replied toggle
        document.querySelectorAll('.sgu-btn-replied').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var item = btn.closest('.sgu-contact-item');
                var entryId = btn.getAttribute('data-id');

                ajax('sgu_toggle_replied', { entry_id: entryId }, function (res) {
                    if (!res.success) return;
                    var isReplied = res.data.replied;
                    if (isReplied) {
                        item.classList.add('sgu-replied');
                        item.classList.remove('sgu-unread');
                        btn.classList.add('sgu-btn-replied-active');
                        btn.classList.remove('sgu-btn-ghost');
                        // Add tag if not present
                        var top = item.querySelector('.sgu-contact-top');
                        if (top && !top.querySelector('.sgu-tag-replied')) {
                            var tag = document.createElement('span');
                            tag.className = 'sgu-tag sgu-tag-replied';
                            tag.textContent = 'Respondido';
                            top.querySelector('.sgu-contact-name').after(tag);
                        }
                    } else {
                        item.classList.remove('sgu-replied');
                        btn.classList.remove('sgu-btn-replied-active');
                        btn.classList.add('sgu-btn-ghost');
                        var tag = item.querySelector('.sgu-tag-replied');
                        if (tag) tag.remove();
                    }
                });
            });
        });

        // Dismiss
        document.querySelectorAll('.sgu-btn-dismiss').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var item = btn.closest('.sgu-contact-item');
                var entryId = item.getAttribute('data-entry-id');
                item.classList.add('sgu-fade-out');
                setTimeout(function () { item.remove(); }, 300);
                ajax('sgu_dismiss_entry', { entry_id: entryId });
            });
        });

        // Spam
        document.querySelectorAll('.sgu-btn-spam').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var item = btn.closest('.sgu-contact-item');
                var entryId = btn.getAttribute('data-id');
                item.classList.add('sgu-fade-out');
                setTimeout(function () { item.remove(); }, 300);
                ajax('sgu_mark_spam', { entry_id: entryId });
            });
        });
    }

    // ═══════════ VEHICLES TABLE ═══════════

    function bindVehiclesTable() {
        var tbody = document.getElementById('sgu-vehicles-tbody');
        if (!tbody) return;

        loadVehicles();

        // Search
        var searchInput = document.getElementById('sgu-filter-search');
        if (searchInput) {
            searchInput.addEventListener('input', function () {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(function () {
                    state.search = searchInput.value;
                    state.page = 1;
                    loadVehicles();
                }, 350);
            });
        }

        // Selects
        ['sgu-filter-type', 'sgu-filter-status', 'sgu-filter-sold'].forEach(function (id) {
            var el = document.getElementById(id);
            if (el) el.addEventListener('change', function () {
                state.type = document.getElementById('sgu-filter-type').value;
                state.status = document.getElementById('sgu-filter-status').value;
                state.sold = document.getElementById('sgu-filter-sold').value;
                state.page = 1;
                loadVehicles();
            });
        });

        // Pagination
        var prev = document.getElementById('sgu-prev-page');
        var next = document.getElementById('sgu-next-page');
        if (prev) prev.addEventListener('click', function () {
            if (state.page > 1) { state.page--; loadVehicles(); }
        });
        if (next) next.addEventListener('click', function () {
            if (state.page < state.totalPages) { state.page++; loadVehicles(); }
        });
    }

    function loadVehicles() {
        if (state.loading) return;
        state.loading = true;

        var tbody = document.getElementById('sgu-vehicles-tbody');
        // Show skeleton
        var skeletonHtml = '';
        for (var i = 0; i < 5; i++) {
            skeletonHtml += '<tr class="sgu-skeleton-row"><td colspan="7"><div class="sgu-skeleton"></div></td></tr>';
        }
        tbody.innerHTML = skeletonHtml;

        ajax('sgu_get_vehicles', {
            page: state.page,
            search: state.search,
            type: state.type,
            status: state.status,
            sold: state.sold
        }, function (res) {
            state.loading = false;

            if (!res.success) {
                tbody.innerHTML = '<tr><td colspan="7" style="text-align:center;padding:24px;color:var(--sgu-danger);">Error al cargar datos</td></tr>';
                return;
            }

            var data = res.data;
            state.totalPages = data.total_pages;

            // Update counter
            var counter = document.getElementById('sgu-total-count');
            if (counter) counter.textContent = data.total + ' vehículos';

            if (data.vehicles.length === 0) {
                tbody.innerHTML = '<tr><td colspan="7" style="text-align:center;padding:32px;color:var(--sgu-text-muted);">No se encontraron vehículos</td></tr>';
            } else {
                tbody.innerHTML = data.vehicles.map(function (v) {
                    var thumb = v.thumb
                        ? '<img src="' + v.thumb + '" class="sgu-tbl-thumb" alt="" loading="lazy">'
                        : '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#cbd5e1" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="M21 15l-5-5L5 21"/></svg>';

                    var statusClass = v.status === 'publish' ? 'sgu-status-publish' : 'sgu-status-draft';
                    var statusDot = v.status === 'publish' ? '&#9679; ' : '&#9675; ';
                    var statusLabel = v.status === 'publish' ? 'Publicado' : 'Borrador';
                    var soldBadge = v.sold ? ' <span class="sgu-sold-tag">VENDIDO</span>' : '';
                    var noresBadge = v.no_residentes ? ' <span class="sgu-nores-tag">\uD83C\uDDFA\uD83C\uDDF8 NO RES.</span>' : '';

                    var noresBtn = '<button class="sgu-btn sgu-btn-sm sgu-table-nores' + (v.no_residentes ? ' sgu-nores-active' : ' sgu-btn-ghost') + '" data-id="' + v.id + '" title="Toggle No Residentes">\uD83C\uDDFA\uD83C\uDDF8</button>';
                    var draftBtn = v.status === 'publish'
                        ? '<button class="sgu-btn sgu-btn-danger-ghost sgu-btn-sm sgu-table-draft" data-id="' + v.id + '" title="Borrador"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg></button>'
                        : '';

                    return '<tr' + (v.no_residentes ? ' class="sgu-row-nores"' : '') + '>' +
                        '<td>' + thumb + '</td>' +
                        '<td class="sgu-tbl-title">' + esc(v.title) + '</td>' +
                        '<td>' + esc(v.year) + '</td>' +
                        '<td><span class="sgu-type-tag sgu-type-' + v.type_raw + '">' + esc(v.type) + '</span></td>' +
                        '<td style="font-weight:500;">' + esc(v.price) + '</td>' +
                        '<td><span class="sgu-status-pill ' + statusClass + '">' + statusDot + statusLabel + '</span>' + soldBadge + noresBadge + '</td>' +
                        '<td><div class="sgu-tbl-actions">' +
                            '<a href="' + v.edit_url + '" class="sgu-btn sgu-btn-ghost sgu-btn-sm" title="Editar"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg></a>' +
                            noresBtn +
                            draftBtn +
                        '</div></td>' +
                        '</tr>';
                }).join('');

                // Rebind draft buttons
                tbody.querySelectorAll('.sgu-table-draft').forEach(function (btn) {
                    btn.addEventListener('click', function () { setDraft(btn); });
                });

                // Rebind no-res toggle buttons
                tbody.querySelectorAll('.sgu-table-nores').forEach(function (btn) {
                    btn.addEventListener('click', function () {
                        toggleNoResTable(btn);
                    });
                });
            }

            // Pagination
            var pageInfo = document.getElementById('sgu-page-info');
            var prev = document.getElementById('sgu-prev-page');
            var next = document.getElementById('sgu-next-page');
            if (pageInfo) pageInfo.textContent = 'Página ' + data.page + ' de ' + data.total_pages;
            if (prev) prev.disabled = data.page <= 1;
            if (next) next.disabled = data.page >= data.total_pages;
        });
    }

    function toggleNoResTable(btn) {
        if (btn.disabled) return;
        var postId = btn.getAttribute('data-id');
        btn.disabled = true;

        ajax('sgu_toggle_no_residentes', { post_id: postId }, function (res) {
            btn.disabled = false;
            if (!res.success) return;
            var isActive = res.data.no_residentes;
            var row = btn.closest('tr');
            var statusCell = row ? row.querySelectorAll('td')[5] : null;

            if (isActive) {
                btn.classList.add('sgu-nores-active');
                btn.classList.remove('sgu-btn-ghost');
                if (row) row.classList.add('sgu-row-nores');
                if (statusCell && !statusCell.querySelector('.sgu-nores-tag')) {
                    var tag = document.createElement('span');
                    tag.className = 'sgu-nores-tag';
                    tag.innerHTML = ' \uD83C\uDDFA\uD83C\uDDF8 NO RES.';
                    statusCell.appendChild(tag);
                }
            } else {
                btn.classList.remove('sgu-nores-active');
                btn.classList.add('sgu-btn-ghost');
                if (row) row.classList.remove('sgu-row-nores');
                if (statusCell) {
                    var tag = statusCell.querySelector('.sgu-nores-tag');
                    if (tag) tag.remove();
                }
            }
        });
    }

    // ═══════════ ANALYTICS CHART ═══════════

    function initChart() {
        var canvas = document.getElementById('sgu-views-chart');
        var dataEl = document.getElementById('sgu-chart-data');
        if (!canvas || !dataEl || typeof Chart === 'undefined') return;

        var chartData;
        try { chartData = JSON.parse(dataEl.textContent); } catch (e) { return; }

        var ctx = canvas.getContext('2d');

        // Gradient fill
        var gradient = ctx.createLinearGradient(0, 0, 0, 220);
        gradient.addColorStop(0, 'rgba(59, 130, 246, 0.15)');
        gradient.addColorStop(1, 'rgba(59, 130, 246, 0.01)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: chartData.labels,
                datasets: [{
                    label: 'Visitas',
                    data: chartData.data,
                    borderColor: '#3b82f6',
                    backgroundColor: gradient,
                    borderWidth: 2.5,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#3b82f6',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    pointHoverBackgroundColor: '#2563eb',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    intersect: false,
                    mode: 'index'
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        titleFont: { family: 'Inter', size: 12, weight: '600' },
                        bodyFont: { family: 'Inter', size: 12 },
                        padding: 10,
                        cornerRadius: 8,
                        displayColors: false,
                        callbacks: {
                            label: function (context) {
                                return context.parsed.y + ' visitas';
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: {
                            font: { family: 'Inter', size: 11 },
                            color: '#94a3b8'
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: '#f1f5f9' },
                        ticks: {
                            font: { family: 'Inter', size: 11 },
                            color: '#94a3b8',
                            stepSize: 1
                        }
                    }
                }
            }
        });
    }

    // ═══════════ HELPERS ═══════════

    function ajax(action, data, callback) {
        var fd = new FormData();
        fd.append('action', action);
        fd.append('nonce', sguDashboard.nonce);
        Object.keys(data).forEach(function (k) {
            fd.append(k, data[k]);
        });

        fetch(sguDashboard.ajaxurl, { method: 'POST', body: fd })
            .then(function (r) { return r.json(); })
            .then(function (res) { if (callback) callback(res); })
            .catch(function () { if (callback) callback({ success: false }); });
    }

    function esc(str) {
        if (!str) return '';
        var d = document.createElement('div');
        d.textContent = str;
        return d.innerHTML;
    }

    // ── Init ──
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
