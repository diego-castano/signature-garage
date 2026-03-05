 
<!-- Banner Start -->
<?php if( 'v1' == get_sub_field( 'style' ) ): ?>
        <?php 
        $image = get_sub_field( 'banner_image' ); 
        if ( $image ) :
        ?>
        <div class="banner-area section">
                <img src="<?php echo esc_url( $image['url'] ); ?>" alt="<?php echo esc_attr( $image['alt'] ); ?>" />
        </div>
        <?php endif; ?>
<?php else: ?>
        <?php 
        $image = get_sub_field( 'banner_image' ); 
        if ( $image ) :
        ?>
        <div class="banner-area-alt ">
                <img src="<?php echo esc_url( $image['url'] ); ?>" alt="<?php echo esc_attr( $image['alt'] ); ?>" />
        </div>
        <?php endif; ?>
<?php endif; ?>
 


<!-- Banner End -->
