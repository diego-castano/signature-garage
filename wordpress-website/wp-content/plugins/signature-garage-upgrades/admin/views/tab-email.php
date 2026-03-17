<?php
if (!defined('ABSPATH')) exit;

$settings = SGU_SMTP::get_settings();
$log = SGU_SMTP::get_log();
$password_display = !empty($settings['password']) ? '••••••••' : '';
?>

<!-- SMTP Configuration -->
<div class="sgu-section">
    <h2>SMTP Configuration</h2>
    <p>Configure SMTP to ensure form emails are delivered reliably. Without SMTP, WordPress uses PHP <code>mail()</code> which often fails on shared hosting.</p>

    <form id="sgu-smtp-form">
        <table class="form-table">
            <tr>
                <th><label for="sgu-smtp-enabled">Enable SMTP</label></th>
                <td>
                    <label>
                        <input type="checkbox" id="sgu-smtp-enabled" name="enabled"
                               value="1" <?php checked($settings['enabled']); ?>>
                        Route all emails through SMTP server
                    </label>
                </td>
            </tr>
            <tr>
                <th><label for="sgu-smtp-host">SMTP Host</label></th>
                <td>
                    <input type="text" id="sgu-smtp-host" name="host"
                           value="<?php echo esc_attr($settings['host']); ?>"
                           class="regular-text" placeholder="smtp.gmail.com">
                    <p class="description">
                        Common: <code>smtp.gmail.com</code>, <code>smtp.sendgrid.net</code>,
                        <code>email-smtp.us-east-1.amazonaws.com</code> (SES)
                    </p>
                </td>
            </tr>
            <tr>
                <th><label for="sgu-smtp-port">Port</label></th>
                <td>
                    <select id="sgu-smtp-port" name="port">
                        <option value="587" <?php selected($settings['port'], 587); ?>>587 (TLS - recommended)</option>
                        <option value="465" <?php selected($settings['port'], 465); ?>>465 (SSL)</option>
                        <option value="25" <?php selected($settings['port'], 25); ?>>25 (No encryption)</option>
                        <option value="2525" <?php selected($settings['port'], 2525); ?>>2525 (Alternative)</option>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="sgu-smtp-encryption">Encryption</label></th>
                <td>
                    <select id="sgu-smtp-encryption" name="encryption">
                        <option value="tls" <?php selected($settings['encryption'], 'tls'); ?>>TLS</option>
                        <option value="ssl" <?php selected($settings['encryption'], 'ssl'); ?>>SSL</option>
                        <option value="" <?php selected($settings['encryption'], ''); ?>>None</option>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="sgu-smtp-auth">Authentication</label></th>
                <td>
                    <label>
                        <input type="checkbox" id="sgu-smtp-auth" name="auth"
                               value="1" <?php checked($settings['auth']); ?>>
                        Use SMTP authentication
                    </label>
                </td>
            </tr>
            <tr>
                <th><label for="sgu-smtp-username">Username</label></th>
                <td>
                    <input type="text" id="sgu-smtp-username" name="username"
                           value="<?php echo esc_attr($settings['username']); ?>"
                           class="regular-text" placeholder="user@domain.com" autocomplete="off">
                </td>
            </tr>
            <tr>
                <th><label for="sgu-smtp-password">Password</label></th>
                <td>
                    <input type="password" id="sgu-smtp-password" name="password"
                           value="<?php echo esc_attr($password_display); ?>"
                           class="regular-text" autocomplete="new-password">
                    <p class="description">For Gmail, use an <strong>App Password</strong> (not your regular password).</p>
                </td>
            </tr>
            <tr>
                <th><label for="sgu-smtp-from-email">From Email</label></th>
                <td>
                    <input type="email" id="sgu-smtp-from-email" name="from_email"
                           value="<?php echo esc_attr($settings['from_email']); ?>"
                           class="regular-text" placeholder="info@signature-garage.com">
                    <p class="description">Must match your SMTP account or an authorized sender.</p>
                </td>
            </tr>
            <tr>
                <th><label for="sgu-smtp-from-name">From Name</label></th>
                <td>
                    <input type="text" id="sgu-smtp-from-name" name="from_name"
                           value="<?php echo esc_attr($settings['from_name']); ?>"
                           class="regular-text" placeholder="Signature Garage">
                </td>
            </tr>
        </table>
        <p>
            <button type="submit" class="button button-primary">Save SMTP Settings</button>
            <span id="sgu-smtp-saved" class="sgu-inline-message" style="display:none;">Settings saved!</span>
        </p>
    </form>
</div>

<!-- Test Email -->
<div class="sgu-section">
    <h2>Send Test Email</h2>
    <p>Verify your SMTP configuration by sending a test email.</p>

    <div class="sgu-actions">
        <input type="email" id="sgu-test-email-to" class="regular-text"
               placeholder="recipient@example.com"
               value="<?php echo esc_attr(wp_get_current_user()->user_email); ?>">
        <button type="button" class="button button-primary" id="sgu-send-test-btn">
            Send Test Email
        </button>
    </div>
    <div id="sgu-test-result" style="margin-top: 10px;"></div>
</div>

<!-- Email Log -->
<div class="sgu-section">
    <h2>
        Email Log
        <?php if (!empty($log)): ?>
            <button type="button" class="button button-small" id="sgu-clear-log-btn" style="margin-left: 10px; vertical-align: middle;">
                Clear Log
            </button>
        <?php endif; ?>
    </h2>

    <?php if (empty($log)): ?>
        <p style="color: #646970;">No emails logged yet. Emails will appear here after they are sent.</p>
    <?php else: ?>
        <table class="widefat striped" style="margin-top: 10px;">
            <thead>
                <tr>
                    <th style="width: 50px;">Status</th>
                    <th>To</th>
                    <th>Subject</th>
                    <th>Time</th>
                    <th>Details</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($log as $entry): ?>
                <tr>
                    <td>
                        <?php if ($entry['status'] === 'sent'): ?>
                            <span style="color: #00a32a; font-weight: 600;">&#10003;</span>
                        <?php else: ?>
                            <span style="color: #d63638; font-weight: 600;">&#10007;</span>
                        <?php endif; ?>
                    </td>
                    <td><?php echo esc_html($entry['to']); ?></td>
                    <td><?php echo esc_html($entry['subject']); ?></td>
                    <td><?php echo esc_html($entry['time']); ?></td>
                    <td>
                        <?php if (!empty($entry['error'])): ?>
                            <span style="color: #d63638;"><?php echo esc_html($entry['error']); ?></span>
                        <?php else: ?>
                            <span style="color: #646970;">-</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<!-- Quick Setup Guides -->
<div class="sgu-section">
    <h2>Quick Setup Guides</h2>

    <details style="margin-bottom: 12px;">
        <summary style="cursor: pointer; font-weight: 600;">Gmail SMTP</summary>
        <div style="padding: 10px 0 0 20px;">
            <ol>
                <li>Go to <strong>Google Account > Security > 2-Step Verification</strong> (must be enabled)</li>
                <li>Go to <strong>App Passwords</strong> and create one for "Mail"</li>
                <li>Use these settings:
                    <ul>
                        <li>Host: <code>smtp.gmail.com</code></li>
                        <li>Port: <code>587</code> / Encryption: <code>TLS</code></li>
                        <li>Username: your full Gmail address</li>
                        <li>Password: the 16-char App Password</li>
                    </ul>
                </li>
            </ol>
            <p><strong>Limit:</strong> 500 emails/day (fine for form notifications).</p>
        </div>
    </details>

    <details style="margin-bottom: 12px;">
        <summary style="cursor: pointer; font-weight: 600;">SendGrid (recommended for production)</summary>
        <div style="padding: 10px 0 0 20px;">
            <ol>
                <li>Create a free SendGrid account (100 emails/day free)</li>
                <li>Go to <strong>Settings > API Keys</strong>, create a key with "Mail Send" permission</li>
                <li>Use these settings:
                    <ul>
                        <li>Host: <code>smtp.sendgrid.net</code></li>
                        <li>Port: <code>587</code> / Encryption: <code>TLS</code></li>
                        <li>Username: <code>apikey</code> (literal text)</li>
                        <li>Password: your API key</li>
                    </ul>
                </li>
            </ol>
        </div>
    </details>

    <details style="margin-bottom: 12px;">
        <summary style="cursor: pointer; font-weight: 600;">Hosting SMTP (cPanel / Plesk)</summary>
        <div style="padding: 10px 0 0 20px;">
            <p>If your hosting provides email accounts (e.g., info@signature-garage.com):</p>
            <ul>
                <li>Host: <code>mail.signature-garage.com</code> (or check cPanel Email Accounts)</li>
                <li>Port: <code>587</code> / Encryption: <code>TLS</code></li>
                <li>Username: the full email address</li>
                <li>Password: the email account password</li>
            </ul>
            <p>This is the simplest option if you already have hosting email set up.</p>
        </div>
    </details>
</div>

<script>
(function() {
    var ajaxurl = '<?php echo esc_js(admin_url('admin-ajax.php')); ?>';
    var nonce = '<?php echo wp_create_nonce('sgu_smtp_nonce'); ?>';

    // Save settings
    document.getElementById('sgu-smtp-form').addEventListener('submit', function(e) {
        e.preventDefault();
        var form = this;
        var data = new FormData(form);
        data.append('action', 'sgu_save_smtp_settings');
        data.append('nonce', nonce);

        // Checkboxes not included if unchecked
        if (!form.querySelector('[name="enabled"]').checked) data.set('enabled', '');
        if (!form.querySelector('[name="auth"]').checked) data.set('auth', '');

        fetch(ajaxurl, { method: 'POST', body: data })
            .then(function(r) { return r.json(); })
            .then(function(res) {
                var msg = document.getElementById('sgu-smtp-saved');
                msg.textContent = res.success ? 'Settings saved!' : ('Error: ' + res.data);
                msg.style.color = res.success ? '#00a32a' : '#d63638';
                msg.style.display = 'inline';
                setTimeout(function() { msg.style.display = 'none'; }, 3000);
            });
    });

    // Send test email
    document.getElementById('sgu-send-test-btn').addEventListener('click', function() {
        var btn = this;
        var to = document.getElementById('sgu-test-email-to').value;
        var result = document.getElementById('sgu-test-result');

        if (!to) {
            result.innerHTML = '<span style="color:#d63638;">Please enter an email address.</span>';
            return;
        }

        btn.disabled = true;
        btn.textContent = 'Sending...';
        result.innerHTML = '<span style="color:#646970;">Sending test email...</span>';

        var data = new FormData();
        data.append('action', 'sgu_send_test_email');
        data.append('nonce', nonce);
        data.append('to', to);

        fetch(ajaxurl, { method: 'POST', body: data })
            .then(function(r) { return r.json(); })
            .then(function(res) {
                if (res.success) {
                    result.innerHTML = '<span style="color:#00a32a;font-weight:600;">' + res.data + '</span><br><small style="color:#646970;">Check inbox (and spam folder). Refresh this page to see the log entry.</small>';
                } else {
                    result.innerHTML = '<span style="color:#d63638;font-weight:600;">' + res.data + '</span>';
                }
            })
            .catch(function() {
                result.innerHTML = '<span style="color:#d63638;">Request failed. Check console for details.</span>';
            })
            .finally(function() {
                btn.disabled = false;
                btn.textContent = 'Send Test Email';
            });
    });

    // Clear log
    var clearBtn = document.getElementById('sgu-clear-log-btn');
    if (clearBtn) {
        clearBtn.addEventListener('click', function() {
            if (!confirm('Clear all email log entries?')) return;

            var data = new FormData();
            data.append('action', 'sgu_clear_email_log');
            data.append('nonce', nonce);

            fetch(ajaxurl, { method: 'POST', body: data })
                .then(function(r) { return r.json(); })
                .then(function(res) {
                    if (res.success) location.reload();
                });
        });
    }

    // Auto-update port/encryption pairing
    document.getElementById('sgu-smtp-port').addEventListener('change', function() {
        var enc = document.getElementById('sgu-smtp-encryption');
        if (this.value === '465') enc.value = 'ssl';
        else if (this.value === '587') enc.value = 'tls';
        else if (this.value === '25') enc.value = '';
    });
})();
</script>
