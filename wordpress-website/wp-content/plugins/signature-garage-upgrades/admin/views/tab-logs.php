<?php
if (!defined('ABSPATH')) exit;

$token = get_option('sgu_api_token', '');
if (empty($token)) {
    $token = wp_generate_password(40, false);
    update_option('sgu_api_token', $token);
}

$dates = SGU_Logger::get_available_dates();
$selected_date = isset($_GET['log_date']) ? sanitize_text_field($_GET['log_date']) : '';
$selected_level = isset($_GET['log_level']) ? sanitize_text_field($_GET['log_level']) : '';
$logs = SGU_Logger::get_logs($selected_date ?: null, 300, $selected_level ?: null);
$site_url = site_url();
?>

<!-- API Access Info -->
<div class="sgu-section">
    <h2>Remote API Access</h2>
    <p>Use these endpoints to monitor the plugin remotely (via Playwright, curl, etc.):</p>

    <table class="widefat" style="max-width:700px;">
        <tr>
            <th>Health Check</th>
            <td><code>GET <?php echo esc_html($site_url); ?>/wp-json/sgu/v1/health</code></td>
        </tr>
        <tr>
            <th>Read Logs</th>
            <td><code>GET <?php echo esc_html($site_url); ?>/wp-json/sgu/v1/logs?lines=100&level=ERROR</code></td>
        </tr>
        <tr>
            <th>Log Dates</th>
            <td><code>GET <?php echo esc_html($site_url); ?>/wp-json/sgu/v1/logs/dates</code></td>
        </tr>
        <tr>
            <th>Auth Header</th>
            <td><code>X-SGU-Token: <?php echo esc_html($token); ?></code></td>
        </tr>
    </table>

    <p class="description" style="margin-top:10px;">
        Example: <code>curl -H "X-SGU-Token: <?php echo esc_html($token); ?>" "<?php echo esc_html($site_url); ?>/wp-json/sgu/v1/health"</code>
    </p>
</div>

<!-- Log Viewer -->
<div class="sgu-section">
    <h2>Log Viewer</h2>

    <form method="get" style="margin-bottom:16px;">
        <input type="hidden" name="page" value="sgu-dashboard">
        <input type="hidden" name="tab" value="logs">

        <label>Date:
            <select name="log_date">
                <option value="">Today</option>
                <?php foreach (array_reverse($dates) as $d): ?>
                    <option value="<?php echo esc_attr($d); ?>" <?php selected($selected_date, $d); ?>>
                        <?php echo esc_html($d); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>

        <label style="margin-left:12px;">Level:
            <select name="log_level">
                <option value="">All</option>
                <?php foreach (['INFO', 'WARNING', 'ERROR', 'DEBUG'] as $lvl): ?>
                    <option value="<?php echo esc_attr($lvl); ?>" <?php selected($selected_level, $lvl); ?>>
                        <?php echo esc_html($lvl); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>

        <button type="submit" class="button">Filter</button>
    </form>

    <?php if (empty($logs)): ?>
        <p>No log entries found for the selected criteria.</p>
    <?php else: ?>
        <div class="sgu-log" style="display:block;">
            <?php foreach ($logs as $line): ?>
                <?php
                $class = 'sgu-log-info';
                if (strpos($line, '[ERROR]') !== false) $class = 'sgu-log-error';
                elseif (strpos($line, '[WARNING]') !== false) $class = 'sgu-log-warning';
                elseif (strpos($line, '[DEBUG]') !== false) $class = 'sgu-log-info';
                ?>
                <div class="sgu-log-entry <?php echo $class; ?>"><?php echo esc_html($line); ?></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
