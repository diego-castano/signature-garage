<!-- Instagram Start -->
<section class="instagram-area bg_white">
            <div class="container">
                <?php 
                $image = get_sub_field( 'instagram_logo' ); 
                if ( $image ) :
                ?>
                <div class="insta-logo text-center">
                    <img src="<?php echo esc_url( $image['url'] ); ?>" alt="<?php echo esc_attr( $image['alt'] ); ?>" />
                </div>
                <?php endif; ?>
				<?php if ( get_sub_field('instagram_shortcode') ) {
				echo do_shortcode( get_sub_field('instagram_shortcode') );
				} ?>
	</div>
        </section>
        <!-- Instagram End -->