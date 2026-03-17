<!-- Hero Start -->
<?php
// Check if SGU Slider plugin has slides configured
$sgu_slides = [];
$sgu_settings = [];
if (class_exists('SGU_Slider')) {
    $sgu_slides = SGU_Slider::get_slides();
    $sgu_settings = SGU_Slider::get_slider_data();
}
?>

<?php if (!empty($sgu_slides)) : ?>
<style>
.sgu-hero {
    position: relative;
    width: 100%;
    overflow: hidden;
    background: #0a0a0a;
}
.sgu-hero .slick-list,
.sgu-hero .slick-track {
    height: 100%;
}
.sgu-hero-slide {
    position: relative;
    height: 85vh;
    min-height: 500px;
    max-height: 800px;
    overflow: hidden;
}
.sgu-hero-slide-bg {
    position: absolute;
    inset: 0;
    background-size: cover;
    background-position: center;
    transition: transform 6s ease-out;
}
.slick-active .sgu-hero-slide-bg {
    transform: scale(1.05);
}
.sgu-hero-slide-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(
        to bottom,
        rgba(0,0,0,0.1) 0%,
        rgba(0,0,0,0.15) 50%,
        rgba(0,0,0,0.7) 100%
    );
}
.sgu-hero-slide-content {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    padding: 0 60px 60px;
    z-index: 2;
    opacity: 0;
    transform: translateY(20px);
    transition: opacity 0.6s ease, transform 0.6s ease;
}
.slick-active .sgu-hero-slide-content {
    opacity: 1;
    transform: translateY(0);
    transition-delay: 0.3s;
}
.sgu-hero-slide-make {
    font-family: "Oswald", sans-serif;
    font-size: 13px;
    letter-spacing: 4px;
    text-transform: uppercase;
    color: rgba(255,255,255,0.6);
    margin-bottom: 6px;
}
.sgu-hero-slide-title {
    font-family: "Oswald", sans-serif;
    font-size: 42px;
    font-weight: 600;
    color: #fff;
    line-height: 1.1;
    margin-bottom: 12px;
    text-shadow: 0 2px 20px rgba(0,0,0,0.3);
}
.sgu-hero-slide-meta {
    display: flex;
    align-items: center;
    gap: 20px;
    margin-bottom: 20px;
}
.sgu-hero-slide-year,
.sgu-hero-slide-price {
    font-family: "Oswald", sans-serif;
    font-size: 16px;
    color: rgba(255,255,255,0.85);
    letter-spacing: 1px;
}
.sgu-hero-slide-price {
    color: #fff;
    font-weight: 600;
    font-size: 20px;
}
.sgu-hero-slide-divider {
    width: 1px;
    height: 18px;
    background: rgba(255,255,255,0.3);
}
.sgu-hero-slide-actions {
    display: flex;
    gap: 12px;
}
.sgu-hero-btn {
    display: inline-block;
    font-family: "Oswald", sans-serif;
    font-size: 13px;
    letter-spacing: 2px;
    text-transform: uppercase;
    padding: 12px 28px;
    border-radius: 4px;
    text-decoration: none;
    transition: all 0.25s;
    cursor: pointer;
}
.sgu-hero-btn-primary {
    background: #ac1d28;
    color: #fff;
    border: 1px solid #ac1d28;
}
.sgu-hero-btn-primary:hover {
    background: #c62533;
    border-color: #c62533;
    color: #fff;
}
.sgu-hero-btn-outline {
    background: transparent;
    color: #fff;
    border: 1px solid rgba(255,255,255,0.4);
}
.sgu-hero-btn-outline:hover {
    border-color: #fff;
    background: rgba(255,255,255,0.1);
    color: #fff;
}
.sgu-hero-nores {
    display: inline-block;
    background: linear-gradient(135deg, #1e3a5f 0%, #1e40af 100%);
    color: #fff;
    font-size: 11px;
    font-weight: 700;
    padding: 4px 10px;
    border-radius: 4px;
    letter-spacing: 1px;
    margin-left: 12px;
    vertical-align: middle;
}

/* Navigation */
.sgu-hero .slick-dots {
    position: absolute;
    bottom: 24px;
    right: 60px;
    display: flex !important;
    gap: 8px;
    list-style: none;
    padding: 0;
    margin: 0;
    z-index: 3;
}
.sgu-hero .slick-dots li {
    margin: 0;
}
.sgu-hero .slick-dots li button {
    width: 32px;
    height: 3px;
    border-radius: 2px;
    background: rgba(255,255,255,0.3);
    border: none;
    padding: 0;
    font-size: 0;
    cursor: pointer;
    transition: all 0.3s;
}
.sgu-hero .slick-dots li.slick-active button {
    width: 48px;
    background: #ac1d28;
}
.sgu-hero .slick-arrow {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    z-index: 3;
    width: 48px;
    height: 48px;
    border-radius: 50%;
    background: rgba(0,0,0,0.3);
    backdrop-filter: blur(4px);
    border: 1px solid rgba(255,255,255,0.15);
    color: #fff;
    font-size: 0;
    cursor: pointer;
    transition: all 0.25s;
    display: flex !important;
    align-items: center;
    justify-content: center;
}
.sgu-hero .slick-arrow:hover {
    background: rgba(172,29,40,0.7);
    border-color: rgba(172,29,40,0.7);
}
.sgu-hero .slick-arrow::after {
    font-size: 18px;
    font-family: dashicons;
    display: block;
}
.sgu-hero .slick-prev {
    left: 24px;
}
.sgu-hero .slick-prev::after {
    content: "\f341";
}
.sgu-hero .slick-next {
    right: 24px;
}
.sgu-hero .slick-next::after {
    content: "\f345";
}

/* Counter */
.sgu-hero-counter {
    position: absolute;
    bottom: 24px;
    left: 60px;
    font-family: "Oswald", sans-serif;
    font-size: 13px;
    letter-spacing: 2px;
    color: rgba(255,255,255,0.5);
    z-index: 3;
}
.sgu-hero-counter-current {
    color: #fff;
    font-size: 18px;
    font-weight: 600;
}

/* Responsive */
@media (max-width: 991px) {
    .sgu-hero-slide {
        height: 70vh;
        min-height: 400px;
    }
    .sgu-hero-slide-content {
        padding: 0 40px 50px;
    }
    .sgu-hero-slide-title {
        font-size: 32px;
    }
    .sgu-hero .slick-dots {
        right: 40px;
    }
    .sgu-hero-counter {
        left: 40px;
    }
}
@media (max-width: 767px) {
    .sgu-hero-slide {
        height: 60vh;
        min-height: 350px;
        max-height: 550px;
    }
    .sgu-hero-slide-content {
        padding: 0 20px 90px;
        text-align: center;
    }
    .sgu-hero-slide-meta {
        justify-content: center;
    }
    .sgu-hero-slide-actions {
        justify-content: center;
    }
    .sgu-hero-slide-title {
        font-size: 26px;
    }
    .sgu-hero-slide-meta {
        gap: 12px;
        flex-wrap: wrap;
    }
    .sgu-hero-slide-price {
        font-size: 17px;
    }
    .sgu-hero-btn {
        padding: 10px 20px;
        font-size: 12px;
    }
    .sgu-hero .slick-arrow {
        width: 38px;
        height: 38px;
    }
    .sgu-hero .slick-prev { left: 12px; }
    .sgu-hero .slick-next { right: 12px; }
    .sgu-hero .slick-dots {
        left: 50%;
        right: auto;
        transform: translateX(-50%);
        bottom: 20px;
    }
    .sgu-hero-counter {
        left: 50%;
        transform: translateX(-50%);
        bottom: 48px;
    }
}
</style>

<section class="hero-area py-0">
    <div class="sgu-hero">
        <div class="hero-slider sgu-hero-slider">
            <?php foreach ($sgu_slides as $i => $slide) : ?>
            <div class="sgu-hero-slide">
                <div class="sgu-hero-slide-bg" style="background-image: url('<?php echo esc_url($slide['image']); ?>')"></div>
                <div class="sgu-hero-slide-overlay"></div>
                <?php if (!empty($sgu_settings['show_info'])) : ?>
                <div class="sgu-hero-slide-content">
                    <div class="sgu-hero-slide-make"><?php echo esc_html($slide['make']); ?></div>
                    <div class="sgu-hero-slide-title">
                        <?php echo esc_html($slide['title']); ?>
                        <?php if ($slide['no_res']) : ?>
                            <span class="sgu-hero-nores">NO RESIDENTES</span>
                        <?php endif; ?>
                    </div>
                    <div class="sgu-hero-slide-meta">
                        <?php if ($slide['year']) : ?>
                            <span class="sgu-hero-slide-year"><?php echo esc_html($slide['year']); ?></span>
                            <span class="sgu-hero-slide-divider"></span>
                        <?php endif; ?>
                        <?php if ($slide['sold']) : ?>
                            <span class="sgu-hero-slide-price" style="color:#ac1d28;">VENDIDO</span>
                        <?php elseif ($slide['price']) : ?>
                            <span class="sgu-hero-slide-price"><?php echo esc_html($slide['price']); ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="sgu-hero-slide-actions">
                        <a href="<?php echo esc_url($slide['permalink']); ?>" class="sgu-hero-btn sgu-hero-btn-primary">Ver Detalles</a>
                        <a href="https://wa.me/598094300100?text=<?php echo rawurlencode('Quiero más información sobre éste vehículo de Signature Garage: ' . $slide['permalink']); ?>" target="_blank" class="sgu-hero-btn sgu-hero-btn-outline"><em class="fab fa-whatsapp" style="margin-right:6px;"></em>WhatsApp</a>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="sgu-hero-counter">
            <span class="sgu-hero-counter-current">01</span> / <?php echo str_pad(count($sgu_slides), 2, '0', STR_PAD_LEFT); ?>
        </div>
    </div>
</section>

<script>
jQuery(document).ready(function($) {
    var total = <?php echo count($sgu_slides); ?>;
    $('.sgu-hero-slider').slick({
        dots: <?php echo $sgu_settings['show_dots'] ? 'true' : 'false'; ?>,
        arrows: <?php echo $sgu_settings['show_arrows'] ? 'true' : 'false'; ?>,
        infinite: true,
        speed: 600,
        slidesToShow: 1,
        slidesToScroll: 1,
        fade: <?php echo $sgu_settings['transition'] === 'fade' ? 'true' : 'false'; ?>,
        autoplay: <?php echo $sgu_settings['autoplay'] ? 'true' : 'false'; ?>,
        autoplaySpeed: <?php echo intval($sgu_settings['autoplay_speed']); ?>,
        cssEase: 'cubic-bezier(0.7, 0, 0.3, 1)',
        pauseOnHover: true,
    });
    // Update counter
    $('.sgu-hero-slider').on('afterChange', function(e, slick, currentSlide) {
        $('.sgu-hero-counter-current').text(String(currentSlide + 1).padStart(2, '0'));
    });
});
</script>

<?php else : ?>
<!-- Fallback: Original ACF image slider -->
<section class="hero-area py-0">
<?php if ( have_rows( 'images' ) ) : ?>
    <div class="hero-slider">
        <?php while ( have_rows( 'images' ) ) : the_row(); ?>
            <?php $image = get_sub_field( 'image' ); ?>
            <?php if ( $image ) : ?>
                <div class="slide">
                    <div class="hero-item">
                        <a href="javascript:;">
                            <img src="<?php echo esc_url( $image['url'] ); ?>" alt="<?php echo esc_attr( $image['alt'] ); ?>" />
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        <?php endwhile; ?>
    </div>
<?php endif; ?>
</section>
<?php endif; ?>
<!-- Hero End -->
