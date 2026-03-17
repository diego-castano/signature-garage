<?php
if (!defined('ABSPATH')) exit;

class SGU_SMTP {

    const OPTION_KEY = 'sgu_smtp_settings';
    const LOG_OPTION = 'sgu_email_log';
    const MAX_LOG_ENTRIES = 100;

    private static $defaults = [
        'enabled'    => true,
        'host'       => 'smtp.titan.email',
        'port'       => 587,
        'encryption' => 'tls',
        'auth'       => true,
        'username'   => 'contacto@signature-garage.com',
        'password'   => 'S1ign35ature?Garage?',
        'from_email' => 'contacto@signature-garage.com',
        'from_name'  => 'Signature Garage',
    ];

    public static function init() {
        add_action('admin_init', function () {
            SGU_Admin::register_tab('email', 'Email / SMTP', SGU_PLUGIN_DIR . 'admin/views/tab-email.php');
        });

        // Hook into PHPMailer to configure SMTP
        add_action('phpmailer_init', [__CLASS__, 'configure_phpmailer'], 10, 1);

        // Log all emails sent through wp_mail
        add_action('wp_mail_succeeded', [__CLASS__, 'log_success']);
        add_action('wp_mail_failed', [__CLASS__, 'log_failure']);

        // AJAX handlers
        add_action('wp_ajax_sgu_send_test_email', [__CLASS__, 'ajax_send_test_email']);
        add_action('wp_ajax_sgu_save_smtp_settings', [__CLASS__, 'ajax_save_settings']);
        add_action('wp_ajax_sgu_clear_email_log', [__CLASS__, 'ajax_clear_log']);
    }

    public static function get_settings() {
        $saved = get_option(self::OPTION_KEY, []);
        return wp_parse_args($saved, self::$defaults);
    }

    public static function configure_phpmailer($phpmailer) {
        $settings = self::get_settings();

        if (empty($settings['enabled']) || empty($settings['host'])) {
            return;
        }

        $phpmailer->isSMTP();
        $phpmailer->Host       = $settings['host'];
        $phpmailer->Port       = (int) $settings['port'];
        $phpmailer->SMTPSecure = $settings['encryption'];
        $phpmailer->SMTPAuth   = (bool) $settings['auth'];

        if ($settings['auth'] && !empty($settings['username'])) {
            $phpmailer->Username = $settings['username'];
            $phpmailer->Password = $settings['password'];
        }

        if (!empty($settings['from_email'])) {
            $phpmailer->setFrom($settings['from_email'], $settings['from_name']);
        }

        SGU_Logger::debug('smtp', 'PHPMailer configured', [
            'host' => $settings['host'],
            'port' => $settings['port'],
            'enc'  => $settings['encryption'],
        ]);
    }

    // --- Email Logging ---

    public static function log_success($mail_data) {
        self::add_log_entry([
            'status'  => 'sent',
            'to'      => is_array($mail_data['to']) ? implode(', ', $mail_data['to']) : $mail_data['to'],
            'subject' => $mail_data['subject'] ?? '(no subject)',
            'time'    => current_time('Y-m-d H:i:s'),
        ]);

        SGU_Logger::info('smtp', 'Email sent successfully', [
            'to'      => $mail_data['to'],
            'subject' => $mail_data['subject'] ?? '',
        ]);
    }

    public static function log_failure($error) {
        $error_data = $error->get_error_data();

        self::add_log_entry([
            'status'  => 'failed',
            'to'      => $error_data['to'][0] ?? 'unknown',
            'subject' => $error_data['subject'] ?? '(no subject)',
            'error'   => $error->get_error_message(),
            'time'    => current_time('Y-m-d H:i:s'),
        ]);

        SGU_Logger::error('smtp', 'Email failed', [
            'to'    => $error_data['to'] ?? [],
            'error' => $error->get_error_message(),
        ]);
    }

    private static function add_log_entry($entry) {
        $log = get_option(self::LOG_OPTION, []);
        array_unshift($log, $entry);
        $log = array_slice($log, 0, self::MAX_LOG_ENTRIES);
        update_option(self::LOG_OPTION, $log, false);
    }

    public static function get_log() {
        return get_option(self::LOG_OPTION, []);
    }

    // --- AJAX Handlers ---

    public static function ajax_send_test_email() {
        check_ajax_referer('sgu_smtp_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error('Permission denied');
        }

        $to = sanitize_email($_POST['to'] ?? '');
        if (empty($to) || !is_email($to)) {
            wp_send_json_error('Invalid email address');
        }

        $subject = 'Signature Garage - Test Email';
        $body = "This is a test email from Signature Garage Upgrades plugin.\n\n";
        $body .= "If you receive this message, your SMTP configuration is working correctly.\n\n";
        $body .= "Sent at: " . current_time('Y-m-d H:i:s') . "\n";
        $body .= "Server: " . home_url() . "\n";

        $settings = self::get_settings();
        $body .= "\nSMTP Config:\n";
        $body .= "- Host: " . ($settings['enabled'] ? $settings['host'] : 'PHP mail (SMTP disabled)') . "\n";
        $body .= "- Port: " . $settings['port'] . "\n";
        $body .= "- Encryption: " . ($settings['encryption'] ?: 'none') . "\n";

        $result = wp_mail($to, $subject, $body);

        if ($result) {
            wp_send_json_success('Test email sent to ' . $to);
        } else {
            global $phpmailer;
            $error_msg = 'Failed to send test email.';
            if (isset($phpmailer) && !empty($phpmailer->ErrorInfo)) {
                $error_msg .= ' Error: ' . $phpmailer->ErrorInfo;
            }
            wp_send_json_error($error_msg);
        }
    }

    public static function ajax_save_settings() {
        check_ajax_referer('sgu_smtp_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error('Permission denied');
        }

        $settings = [
            'enabled'    => !empty($_POST['enabled']),
            'host'       => sanitize_text_field($_POST['host'] ?? ''),
            'port'       => absint($_POST['port'] ?? 587),
            'encryption' => sanitize_text_field($_POST['encryption'] ?? 'tls'),
            'auth'       => !empty($_POST['auth']),
            'username'   => sanitize_text_field($_POST['username'] ?? ''),
            'password'   => $_POST['password'] ?? '',
            'from_email' => sanitize_email($_POST['from_email'] ?? ''),
            'from_name'  => sanitize_text_field($_POST['from_name'] ?? 'Signature Garage'),
        ];

        // Don't overwrite password if placeholder was sent
        if ($settings['password'] === '••••••••') {
            $current = self::get_settings();
            $settings['password'] = $current['password'];
        }

        update_option(self::OPTION_KEY, $settings);

        SGU_Logger::info('smtp', 'SMTP settings updated', [
            'host'    => $settings['host'],
            'enabled' => $settings['enabled'],
        ]);

        wp_send_json_success('Settings saved');
    }

    public static function ajax_clear_log() {
        check_ajax_referer('sgu_smtp_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error('Permission denied');
        }

        delete_option(self::LOG_OPTION);
        wp_send_json_success('Log cleared');
    }
}
