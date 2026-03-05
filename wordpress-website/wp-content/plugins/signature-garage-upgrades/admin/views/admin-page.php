<?php
if (!defined('ABSPATH')) exit;

$tabs = SGU_Admin::get_tabs();
$current_tab = SGU_Admin::get_current_tab();
?>
<div class="wrap sgu-wrap">
    <h1>Signature Garage Upgrades</h1>

    <?php if (count($tabs) > 1): ?>
    <nav class="nav-tab-wrapper sgu-tabs">
        <?php foreach ($tabs as $slug => $tab): ?>
            <a href="<?php echo esc_url(admin_url('admin.php?page=sgu-dashboard&tab=' . $slug)); ?>"
               class="nav-tab <?php echo $current_tab === $slug ? 'nav-tab-active' : ''; ?>">
                <?php echo esc_html($tab['label']); ?>
            </a>
        <?php endforeach; ?>
    </nav>
    <?php endif; ?>

    <div class="sgu-tab-content">
        <?php
        if (isset($tabs[$current_tab]['view']) && file_exists($tabs[$current_tab]['view'])) {
            include $tabs[$current_tab]['view'];
        }
        ?>
    </div>
</div>
