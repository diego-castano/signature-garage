<?php 
//Bakground.
$bg = ( $background_image = get_sub_field( 'add_bg' ) )? 'style="background-image: linear-gradient(to left, transparent 50%, #303137 50%), url('. $background_image .') "' : '';
?>

<!-- content Start -->
<section class="content-area auto_bg" <?php echo $bg; ?>>
    <div class="container">
        <div class="row">
            <div class="col-sm-6">
                <div class="content-text color_white">
                    <?php 
                    //Heading
                    if( $heading = get_sub_field( 'heading' ) ):
                        echo '<h3 class="border_heading h2 letter_space">'. $heading .'</h3>';
                    endif;
                    
                    ?>
                    <div class="font_gillsans font_medium">
                        <?php the_sub_field( 'description' ); ?>
                    </div>
                    <?php 
                    //Button
                    $button = get_sub_field( 'button' ); 
                    if( isset( $button ) && !empty( $button ) ):
                        $target = ( isset( $button['target'] ) && !empty( $button['target'] ) )? 'target="'. $button['target'] .'"' : '';
                        $url = ( isset( $button['url'] ) && !empty( $button['url'] ) )? 'href="'. $button['url'] .'"' : '';
                        $title = ( isset( $button['title'] ) && !empty( $button['title'] ) )? $button['title'] : '';
                        
                        printf( '<a %s %s class="cta-btn btn_white_border">%s</a>', $url, $target, $title  );
                    
                    endif;
                    ?>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="content-text list color_white pl_50">
                    <?php 
                    if( $headings = get_sub_field( 'heading_service' ) ):
                        echo '<h3 class="border_heading h2 letter_space">'. $headings .'</h3>';
                    endif;
                    ?>
                    <?php if ( have_rows( 'lists' ) ) : ?>
                        <ul class="list_border_check font_gillsans">
                            <?php while ( have_rows( 'lists' ) ) : the_row(); ?>
                                <li><?php the_sub_field('list'); ?></li>
                            <?php endwhile; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- content End -->