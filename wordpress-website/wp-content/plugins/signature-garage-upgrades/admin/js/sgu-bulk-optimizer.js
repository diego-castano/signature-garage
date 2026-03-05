(function () {
    'use strict';

    var state = {
        ids: [],
        current: 0,
        running: false,
    };

    // Elements
    var scanBtn, optimizeBtn, stopBtn, scanResult;
    var progressWrap, progressFill, progressText;
    var logEl;

    document.addEventListener('DOMContentLoaded', function () {
        scanBtn = document.getElementById('sgu-scan-btn');
        optimizeBtn = document.getElementById('sgu-optimize-btn');
        stopBtn = document.getElementById('sgu-stop-btn');
        scanResult = document.getElementById('sgu-scan-result');
        progressWrap = document.getElementById('sgu-bulk-progress');
        progressFill = document.getElementById('sgu-progress-fill');
        progressText = document.getElementById('sgu-progress-text');
        logEl = document.getElementById('sgu-log');

        if (scanBtn) scanBtn.addEventListener('click', scan);
        if (optimizeBtn) optimizeBtn.addEventListener('click', startOptimize);
        if (stopBtn) stopBtn.addEventListener('click', stopOptimize);

        // Range slider labels
        bindRange('sgu-jpeg-quality', 'sgu-jpeg-quality-val');
        bindRange('sgu-webp-quality', 'sgu-webp-quality-val');

        // Settings form
        var form = document.getElementById('sgu-settings-form');
        if (form) form.addEventListener('submit', saveSettings);

        // Load stats on page load
        loadStats();
    });

    function ajax(action, data, callback) {
        var formData = new FormData();
        formData.append('action', action);
        formData.append('nonce', sguOptimizer.nonce);
        if (data) {
            for (var key in data) {
                formData.append(key, data[key]);
            }
        }
        var xhr = new XMLHttpRequest();
        xhr.open('POST', sguOptimizer.ajaxurl);
        xhr.onload = function () {
            var resp;
            try { resp = JSON.parse(xhr.responseText); } catch (e) { resp = { success: false }; }
            callback(resp);
        };
        xhr.onerror = function () { callback({ success: false }); };
        xhr.send(formData);
    }

    function loadStats() {
        ajax('sgu_get_stats', null, function (resp) {
            if (!resp.success) return;
            var d = resp.data;
            setText('stat-total', d.total_images);
            setText('stat-optimized', d.total_optimized);
            setText('stat-savings', d.total_savings);
            setText('stat-webp', d.total_webp);

            // Overall progress
            if (d.total_images > 0) {
                var pct = Math.round((d.total_optimized / d.total_images) * 100);
                var overall = document.getElementById('sgu-overall-progress');
                var overallFill = document.getElementById('sgu-overall-fill');
                var overallText = document.getElementById('sgu-overall-text');
                if (overall) {
                    overall.style.display = 'flex';
                    overallFill.style.width = pct + '%';
                    overallText.textContent = d.total_optimized + ' / ' + d.total_images + ' (' + pct + '%)';
                }
            }
        });
    }

    function scan() {
        scanBtn.disabled = true;
        scanBtn.textContent = 'Scanning...';
        scanResult.textContent = '';

        ajax('sgu_scan_unoptimized', null, function (resp) {
            scanBtn.disabled = false;
            scanBtn.textContent = 'Scan Unoptimized Images';

            if (!resp.success) {
                scanResult.textContent = 'Scan failed.';
                return;
            }

            state.ids = resp.data.ids;
            state.current = 0;

            if (resp.data.total === 0) {
                scanResult.textContent = 'All images are already optimized!';
                optimizeBtn.style.display = 'none';
            } else {
                scanResult.textContent = resp.data.total + ' unoptimized images found.';
                optimizeBtn.style.display = '';
            }
        });
    }

    function startOptimize() {
        if (state.ids.length === 0) return;

        state.running = true;
        state.current = 0;
        optimizeBtn.style.display = 'none';
        scanBtn.style.display = 'none';
        stopBtn.style.display = '';
        progressWrap.style.display = 'flex';
        logEl.style.display = '';
        logEl.innerHTML = '';

        updateProgress();
        processNext();
    }

    function stopOptimize() {
        state.running = false;
        stopBtn.style.display = 'none';
        scanBtn.style.display = '';
        logEntry('Stopped by user.', 'info');
    }

    function processNext() {
        if (!state.running || state.current >= state.ids.length) {
            state.running = false;
            stopBtn.style.display = 'none';
            scanBtn.style.display = '';
            if (state.current >= state.ids.length) {
                logEntry('All done! ' + state.ids.length + ' images optimized.', 'success');
            }
            loadStats();
            return;
        }

        var id = state.ids[state.current];
        ajax('sgu_optimize_single', { attachment_id: id }, function (resp) {
            state.current++;
            updateProgress();

            if (resp.success) {
                var d = resp.data;
                var msg = d.filename + ' — ' + d.original_size + ' → ' + d.optimized_size +
                    ' (' + d.savings_pct + '% saved)';
                if (d.was_resized) msg += ' [resized to ' + d.new_dimensions + ']';
                if (d.webp_created) msg += ' [WebP]';
                logEntry(msg, 'success');
            } else {
                var errMsg = resp.data || 'Unknown error';
                logEntry('Error: ' + errMsg, 'error');
            }

            processNext();
        });
    }

    function updateProgress() {
        var total = state.ids.length;
        var pct = total > 0 ? Math.round((state.current / total) * 100) : 0;
        progressFill.style.width = pct + '%';
        progressText.textContent = state.current + ' / ' + total + ' (' + pct + '%)';
    }

    function logEntry(message, type) {
        var div = document.createElement('div');
        div.className = 'sgu-log-entry sgu-log-' + (type || 'info');
        div.textContent = message;
        logEl.appendChild(div);
        logEl.scrollTop = logEl.scrollHeight;
    }

    function saveSettings(e) {
        e.preventDefault();
        var data = {
            max_width: document.getElementById('sgu-max-width').value,
            jpeg_quality: document.getElementById('sgu-jpeg-quality').value,
            webp_quality: document.getElementById('sgu-webp-quality').value,
            auto_optimize: document.getElementById('sgu-auto-optimize').checked ? '1' : '',
        };

        ajax('sgu_save_settings', data, function (resp) {
            var msg = document.getElementById('sgu-settings-saved');
            if (resp.success) {
                msg.style.display = '';
                msg.textContent = 'Settings saved!';
                setTimeout(function () { msg.style.display = 'none'; }, 3000);
            } else {
                msg.style.display = '';
                msg.textContent = 'Error saving settings.';
                msg.style.color = '#d63638';
            }
        });
    }

    function bindRange(inputId, labelId) {
        var input = document.getElementById(inputId);
        var label = document.getElementById(labelId);
        if (input && label) {
            input.addEventListener('input', function () {
                label.textContent = this.value;
            });
        }
    }

    function setText(id, value) {
        var el = document.getElementById(id);
        if (el) el.textContent = value;
    }
})();
