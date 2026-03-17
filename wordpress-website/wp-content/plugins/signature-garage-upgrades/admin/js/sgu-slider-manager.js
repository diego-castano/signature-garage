(function () {
    'use strict';

    var slides = [];
    var searchTimer;

    // ── DOM refs ──
    var $results, $slides, $count, $empty, $status, $search;

    document.addEventListener('DOMContentLoaded', function () {
        $results = document.getElementById('sguSmResults');
        $slides = document.getElementById('sguSmSlides');
        $count = document.getElementById('sguSmCount');
        $empty = document.getElementById('sguSmEmpty');
        $status = document.getElementById('sguSmStatus');
        $search = document.getElementById('sguSmSearch');

        if (!$results) return;

        // Load current slider
        loadSlider();

        // Search handler
        $search.addEventListener('input', function () {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(function () { searchVehicles($search.value); }, 300);
        });

        // Save
        document.getElementById('sguSmSave').addEventListener('click', saveSlider);

        // Initial search (load all)
        searchVehicles('');
    });

    // ── Load current slider ──
    function loadSlider() {
        ajax('sgu_slider_get', {}, function (data) {
            // Load vehicles
            slides = [];
            data.vehicles.forEach(function (v) { addSlide(v); });

            // Load settings
            var s = data.settings;
            document.getElementById('sguSmAutoplay').checked = s.autoplay;
            document.getElementById('sguSmSpeed').value = s.autoplay_speed;
            document.getElementById('sguSmTransition').value = s.transition;
            document.getElementById('sguSmDots').checked = s.show_dots;
            document.getElementById('sguSmArrows').checked = s.show_arrows;
            document.getElementById('sguSmInfo').checked = s.show_info;

            updateCount();
        });
    }

    // ── Search vehicles ──
    function searchVehicles(term) {
        var excludeIds = slides.map(function (s) { return s.id; });

        ajax('sgu_slider_search', { term: term, exclude: excludeIds }, function (data) {
            $results.innerHTML = '';
            if (!data.length) {
                $results.innerHTML = '<p class="sgu-sm-hint">No se encontraron vehiculos.</p>';
                return;
            }

            data.forEach(function (v) {
                var el = document.createElement('div');
                el.className = 'sgu-sm-vehicle';
                el.innerHTML =
                    (v.thumb ? '<img class="sgu-sm-vehicle-thumb" src="' + esc(v.thumb) + '" alt="" />' :
                        '<div class="sgu-sm-vehicle-thumb"></div>') +
                    '<div class="sgu-sm-vehicle-info">' +
                        '<div class="sgu-sm-vehicle-title">' + esc(v.title) + '</div>' +
                        '<div class="sgu-sm-vehicle-meta">' + esc(v.make) + ' &middot; ' + esc(v.year) + '</div>' +
                    '</div>' +
                    '<span class="sgu-sm-vehicle-price' + (v.sold ? ' sgu-sm-vehicle-sold' : '') + '">' +
                        (v.sold ? 'VENDIDO' : esc(v.price)) +
                    '</span>' +
                    '<button class="sgu-sm-vehicle-add" title="Agregar al slider">+</button>';

                el.querySelector('.sgu-sm-vehicle-add').addEventListener('click', function (e) {
                    e.stopPropagation();
                    addSlide(v);
                    el.remove();
                    updateCount();
                });

                $results.appendChild(el);
            });
        });
    }

    // ── Add slide ──
    function addSlide(v) {
        slides.push(v);
        renderSlide(v, slides.length);
        updateCount();
    }

    // ── Render slide item ──
    function renderSlide(v, num) {
        $empty.style.display = 'none';
        var el = document.createElement('div');
        el.className = 'sgu-sm-slide';
        el.dataset.id = v.id;
        el.draggable = true;

        el.innerHTML =
            '<span class="sgu-sm-slide-handle dashicons dashicons-menu"></span>' +
            '<span class="sgu-sm-slide-num">' + num + '</span>' +
            (v.thumb ? '<img class="sgu-sm-slide-thumb" src="' + esc(v.thumb) + '" alt="" />' :
                '<div class="sgu-sm-slide-thumb"></div>') +
            '<div class="sgu-sm-slide-info">' +
                '<div class="sgu-sm-slide-title">' + esc(v.title) + '</div>' +
                '<div class="sgu-sm-slide-meta">' + esc(v.make) + ' &middot; ' + esc(v.year) +
                    (v.price ? ' &middot; ' + esc(v.price) : '') + '</div>' +
            '</div>' +
            '<button class="sgu-sm-slide-remove" title="Quitar">&times;</button>';

        // Remove handler
        el.querySelector('.sgu-sm-slide-remove').addEventListener('click', function () {
            var idx = slides.findIndex(function (s) { return s.id === v.id; });
            if (idx > -1) slides.splice(idx, 1);
            el.remove();
            renumberSlides();
            updateCount();
            // Refresh search to show removed vehicle
            searchVehicles($search.value);
        });

        // Drag handlers
        el.addEventListener('dragstart', function (e) {
            el.classList.add('dragging');
            e.dataTransfer.effectAllowed = 'move';
            e.dataTransfer.setData('text/plain', v.id);
        });
        el.addEventListener('dragend', function () {
            el.classList.remove('dragging');
            document.querySelectorAll('.sgu-sm-slide.drag-over').forEach(function (s) {
                s.classList.remove('drag-over');
            });
        });
        el.addEventListener('dragover', function (e) {
            e.preventDefault();
            e.dataTransfer.dropEffect = 'move';
            var dragging = $slides.querySelector('.dragging');
            if (dragging && dragging !== el) {
                el.classList.add('drag-over');
            }
        });
        el.addEventListener('dragleave', function () {
            el.classList.remove('drag-over');
        });
        el.addEventListener('drop', function (e) {
            e.preventDefault();
            el.classList.remove('drag-over');
            var dragging = $slides.querySelector('.dragging');
            if (!dragging || dragging === el) return;

            // Reorder in DOM
            var rect = el.getBoundingClientRect();
            var mid = rect.top + rect.height / 2;
            if (e.clientY < mid) {
                $slides.insertBefore(dragging, el);
            } else {
                $slides.insertBefore(dragging, el.nextSibling);
            }

            // Reorder in array
            reorderFromDOM();
            renumberSlides();
        });

        $slides.appendChild(el);
    }

    // ── Reorder slides array from DOM order ──
    function reorderFromDOM() {
        var newOrder = [];
        $slides.querySelectorAll('.sgu-sm-slide').forEach(function (el) {
            var id = parseInt(el.dataset.id);
            var s = slides.find(function (sl) { return sl.id === id; });
            if (s) newOrder.push(s);
        });
        slides = newOrder;
    }

    // ── Renumber slides ──
    function renumberSlides() {
        $slides.querySelectorAll('.sgu-sm-slide-num').forEach(function (el, i) {
            el.textContent = i + 1;
        });
        if (!slides.length) $empty.style.display = '';
    }

    // ── Update count ──
    function updateCount() {
        $count.textContent = slides.length;
        $empty.style.display = slides.length ? 'none' : '';
    }

    // ── Save slider ──
    function saveSlider() {
        reorderFromDOM();
        var ids = slides.map(function (s) { return s.id; });

        ajax('sgu_slider_save', {
            vehicle_ids: JSON.stringify(ids),
            autoplay: document.getElementById('sguSmAutoplay').checked ? '1' : '',
            autoplay_speed: document.getElementById('sguSmSpeed').value,
            transition: document.getElementById('sguSmTransition').value,
            show_dots: document.getElementById('sguSmDots').checked ? '1' : '',
            show_arrows: document.getElementById('sguSmArrows').checked ? '1' : '',
            show_info: document.getElementById('sguSmInfo').checked ? '1' : '',
        }, function () {
            $status.textContent = 'Guardado';
            $status.classList.add('saved');
            setTimeout(function () {
                $status.textContent = '';
                $status.classList.remove('saved');
            }, 3000);
        });
    }

    // ── AJAX helper ──
    function ajax(action, data, cb) {
        var fd = new FormData();
        fd.append('action', action);
        fd.append('nonce', sguSlider.nonce);
        for (var k in data) {
            if (Array.isArray(data[k])) {
                data[k].forEach(function (v) { fd.append(k + '[]', v); });
            } else {
                fd.append(k, data[k]);
            }
        }
        fetch(sguSlider.ajaxurl, { method: 'POST', body: fd })
            .then(function (r) { return r.json(); })
            .then(function (r) { if (r.success && cb) cb(r.data); });
    }

    // ── Escape HTML ──
    function esc(str) {
        if (!str) return '';
        var d = document.createElement('div');
        d.textContent = str;
        return d.innerHTML;
    }
})();
