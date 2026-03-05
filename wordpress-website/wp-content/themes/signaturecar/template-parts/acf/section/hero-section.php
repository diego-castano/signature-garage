<!-- Hero Start -->
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
<!-- Hero End -->


