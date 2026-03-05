<?php
if (!defined('ABSPATH')) exit;

$settings = SGU_Image_Optimizer::get_settings();
$has_gd = SGU_Image_Optimizer::has_gd();
$has_webp = SGU_Image_Optimizer::has_webp_support();
?>

<?php if (!$has_gd): ?>
<div class="notice notice-error">
    <p><strong>GD Library not available.</strong> Image optimization requires PHP GD extension. Contact your hosting provider to enable it.</p>
</div>
<?php endif; ?>

<?php if ($has_gd && !$has_webp): ?>
<div class="notice notice-warning">
    <p><strong>WebP not supported.</strong> Your GD library doesn't support WebP. Images will be optimized but WebP versions won't be generated.</p>
</div>
<?php endif; ?>

<!-- Stats Dashboard -->
<div class="sgu-stats-grid" id="sgu-stats">
    <div class="sgu-stat-card">
        <span class="sgu-stat-number" id="stat-total">--</span>
        <span class="sgu-stat-label">Total Images</span>
    </div>
    <div class="sgu-stat-card">
        <span class="sgu-stat-number" id="stat-optimized">--</span>
        <span class="sgu-stat-label">Optimized</span>
    </div>
    <div class="sgu-stat-card">
        <span class="sgu-stat-number" id="stat-savings">--</span>
        <span class="sgu-stat-label">Space Saved</span>
    </div>
    <div class="sgu-stat-card">
        <span class="sgu-stat-number" id="stat-webp">--</span>
        <span class="sgu-stat-label">WebP Created</span>
    </div>
</div>

<div class="sgu-progress-bar-wrap" id="sgu-overall-progress" style="display:none;">
    <div class="sgu-progress-bar">
        <div class="sgu-progress-fill" id="sgu-overall-fill" style="width:0%"></div>
    </div>
    <span class="sgu-progress-text" id="sgu-overall-text">0%</span>
</div>

<!-- Bulk Optimizer -->
<div class="sgu-section">
    <h2>Bulk Image Optimizer</h2>
    <p>Scan your media library for unoptimized images and optimize them in bulk.</p>

    <div class="sgu-actions">
        <button type="button" class="button button-primary" id="sgu-scan-btn" <?php echo !$has_gd ? 'disabled' : ''; ?>>
            Scan Unoptimized Images
        </button>
        <button type="button" class="button button-primary" id="sgu-optimize-btn" style="display:none;">
            Optimize All
        </button>
        <button type="button" class="button" id="sgu-stop-btn" style="display:none;">
            Stop
        </button>
        <span id="sgu-scan-result" class="sgu-inline-message"></span>
    </div>

    <div class="sgu-progress-bar-wrap" id="sgu-bulk-progress" style="display:none;">
        <div class="sgu-progress-bar">
            <div class="sgu-progress-fill" id="sgu-progress-fill" style="width:0%"></div>
        </div>
        <span class="sgu-progress-text" id="sgu-progress-text">0 / 0</span>
    </div>

    <div id="sgu-log" class="sgu-log" style="display:none;"></div>
</div>

<!-- Settings -->
<div class="sgu-section">
    <h2>Optimization Settings</h2>
    <form id="sgu-settings-form">
        <table class="form-table">
            <tr>
                <th><label for="sgu-max-width">Max Width (px)</label></th>
                <td>
                    <input type="number" id="sgu-max-width" name="max_width"
                           value="<?php echo esc_attr($settings['max_width']); ?>"
                           min="800" max="4096" step="1" class="small-text">
                    <p class="description">Images wider than this will be resized. Default: 1920</p>
                </td>
            </tr>
            <tr>
                <th><label for="sgu-jpeg-quality">JPEG Quality</label></th>
                <td>
                    <input type="range" id="sgu-jpeg-quality" name="jpeg_quality"
                           value="<?php echo esc_attr($settings['jpeg_quality']); ?>"
                           min="50" max="100" step="1">
                    <span id="sgu-jpeg-quality-val"><?php echo esc_html($settings['jpeg_quality']); ?></span>%
                    <p class="description">Lower = smaller file, less quality. Default: 82</p>
                </td>
            </tr>
            <tr>
                <th><label for="sgu-webp-quality">WebP Quality</label></th>
                <td>
                    <input type="range" id="sgu-webp-quality" name="webp_quality"
                           value="<?php echo esc_attr($settings['webp_quality']); ?>"
                           min="50" max="100" step="1">
                    <span id="sgu-webp-quality-val"><?php echo esc_html($settings['webp_quality']); ?></span>%
                    <p class="description">Quality for WebP conversion. Default: 82</p>
                </td>
            </tr>
            <tr>
                <th><label for="sgu-auto-optimize">Auto-Optimize on Upload</label></th>
                <td>
                    <label>
                        <input type="checkbox" id="sgu-auto-optimize" name="auto_optimize"
                               value="1" <?php checked($settings['auto_optimize']); ?>>
                        Automatically optimize images when uploaded
                    </label>
                </td>
            </tr>
        </table>
        <p>
            <button type="submit" class="button button-primary">Save Settings</button>
            <span id="sgu-settings-saved" class="sgu-inline-message" style="display:none;">Settings saved!</span>
        </p>
    </form>
</div>
