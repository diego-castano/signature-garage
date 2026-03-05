<?php 
//Bakground.
$bg = ( $background_image = get_sub_field( 'background_image' ) )? 'style="background: url('. $background_image .')  no-repeat scroll 0 0/cover"' : '';
?>
<!-- Content Start -->
<section class="content-area promo-area"  <?php echo $bg; ?>>
    <div class="container">
        <?php if ( have_rows( 'promo_list' ) ) : ?>
            <div class="row">
            <?php while ( have_rows( 'promo_list' ) ) : the_row(); ?>
                <div class="col-sm-6">
                <a href="<?php echo esc_url(get_sub_field('link_to') ); ?>" class="row align-items-md-center">
                        <div class="col-lg-8">
                            <div class="content-text color_white">
                                <?php 
                                if( $heading = get_sub_field( 'heading' ) ):
                                    echo '<h3 class="border_heading h2 letter_space">'. $heading .'</h3>';
                                endif;
                                ?>
                                <div class="font_gillsans font_medium">
                                    <?php the_sub_field( 'description' ); ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <?php $image = get_sub_field( 'image' ); ?>
                            <?php if ( $image ) : ?>
                                <div class="content-img d-none d-lg-block">
                                <img src="<?php echo esc_url( $image['url'] ); ?>" alt="<?php echo esc_attr( $image['alt'] ); ?>" />
                                </div>
                            <?php endif; ?>
                        </div>
                    </a>
                    
                </div>
            <?php endwhile; ?>
            </div>
        <?php endif; ?>
    
    </div>
</section>
<!-- Content End -->