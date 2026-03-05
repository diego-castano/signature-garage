 <!-- Content Start-->
    
 
<?php if( 'v1' == get_sub_field( 'style' ) ): ?>
	<?php if ( get_sub_field( 'default_space' ) == 1 ) : ?>
	<section class="content-area pt-0 pb-0">
	<?php else : ?>
	<section class="content-area pt_200 pb-0">
	<?php endif; ?>
    
    <div class="container">
        <?php if ( have_rows( 'content_list' ) ) : ?>
            <?php while ( have_rows( 'content_list' ) ) : the_row(); ?>
            <?php if( 'v1' == get_sub_field( 'image_position' ) ): ?>
                <div class="content-item">
                    <div class="text-center">
                        <?php 
                        //Heading
                        if( $heading = get_sub_field( 'heading' ) ):
                            echo '<h2 class="h2 color_white letter_space">'. $heading .'</h2>';
                        endif;
                        ?>
						<?php 
                        //Heading
                        if( $headingSub = get_sub_field( 'heading_sub' ) ):
                            echo '<h3 class="h4 color_white letter_space">'. $headingSub .'</h3>';
                        endif;
                        ?>
                    </div>
                    <div class="item-inner bg_white">
                        <?php 
                            $image = get_sub_field( 'image' ); 
                            if ( $image ) :
                            ?>
                            <div class="content-img" >
                                <img  class="item_bg left d-none d-xxl-block"  src="<?php echo esc_url( $image['url'] ); ?>" alt="<?php echo esc_attr( $image['alt'] ); ?>" class="img-left" />
                            </div>
                        <?php endif; ?>
                        <div class="row ">
                            <div class="col-sm-6">
                                <div class="content-img d-xxl-none">
                                <?php 
                                    $image = get_sub_field( 'image' ); 
                                    if ( $image ) :
                                    ?>
                                    <div class="content-img">
                                        <img  src="<?php echo esc_url( $image['url'] ); ?>" alt="<?php echo esc_attr( $image['alt'] ); ?>" class="img-left" />
                                    </div>
                                <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="content-text font_gillsans">
                                        <?php 
                                        //Description
                                        the_sub_field( 'description' );
                                        ?>

                                        <?php 
                                        //Quotes
                                        if( $quotes = get_sub_field( 'quotes' ) ):
                                            echo '<div class="quote font_oswald">'. $quotes .'</div>';
                                        endif;
                                        ?>
                                    <div class="group_btn">
                                        <?php 
                                        //Button
                                        $buttonOne = get_sub_field( 'button_one' ); 
                                        if( isset( $buttonOne ) && !empty( $buttonOne ) ):
                                            
                                            $targetone = ( isset( $buttonOne['target'] ) && !empty( $buttonOne['target'] ) )? 'target="'. $buttonOne['target'] .'"' : '';
                                            $urlone = ( isset( $buttonOne['url'] ) && !empty( $buttonOne['url'] ) )? 'href="'. $buttonOne['url'] .'"' : '';
                                            $titleone = ( isset( $buttonOne['title'] ) && !empty( $buttonOne['title'] ) )? $buttonOne['title'] : '';
                                            
                                            printf( '<a %s %s class="cta-btn btn_red">%s</a>', $urlone, $targetone, $titleone  );
                                            
                                        endif;

                                        //Button
                                        $buttonTwo = get_sub_field( 'button_two' ); 
                                        if( isset( $buttonTwo ) && !empty( $buttonTwo ) ):
                                            
                                            $targettwo = ( isset( $buttonTwo['target'] ) && !empty( $buttonTwo['target'] ) )? 'target="'. $buttonTwo['target'] .'"' : '';
                                            $urltwo = ( isset( $buttonTwo['url'] ) && !empty( $buttonTwo['url'] ) )? 'href="'. $buttonTwo['url'] .'"' : '';
                                            $titletwo = ( isset( $buttonTwo['title'] ) && !empty( $buttonTwo['title'] ) )? $buttonTwo['title'] : '';
                                            
                                            printf( '<a %s %s class="cta-btn btn_dark">%s</a>', $urltwo, $targettwo, $titletwo  );
                                            
                                        endif;
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php else: ?>
                <div class="content-item">
                    <div class="text-center">
                    <?php 
                        //Heading
                        if( $heading = get_sub_field( 'heading' ) ):
                            echo '<h3 class="h2 color_white letter_space">'. $heading .'</h3>';
                        endif;
                        ?>
						<?php 
                        //Heading
                        if( $headingSub = get_sub_field( 'heading_sub' ) ):
                            echo '<h3 class="h4 color_white letter_space">'. $headingSub .'</h3>';
                        endif;
                        ?>
                    </div>
                    <div class="item-inner bg_white">
                        <?php 
                            $image = get_sub_field( 'image' ); 
                            if ( $image ) :
                            ?>
                            <div class="content-img">
                                <img  class="item_bg right d-none d-xxl-block"  src="<?php echo esc_url( $image['url'] ); ?>" alt="<?php echo esc_attr( $image['alt'] ); ?>" class="img-left" />
                            </div>
                        <?php endif; ?>
                        <div class="row">
                            <div class="col-sm-6 order-sm-2 order-1">
                                <?php 
                                    $image = get_sub_field( 'image' ); 
                                    if ( $image ) :
                                    ?>
                                    <div class="content-img" >
                                        <img  src="<?php echo esc_url( $image['url'] ); ?>" alt="<?php echo esc_attr( $image['alt'] ); ?>" class="img-left" />
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="col-sm-6 order-sm-1 order-2">
                                <div class="content-text font_gillsans">
                                    <?php 
                                    //Description
                                    the_sub_field( 'description' );
                                    ?>
                                    <?php 
                                        //Quotes
                                        if( $quotes = get_sub_field( 'quotes' ) ):
                                            echo '<div class="quote font_oswald"><span class="quotes_top"></span>'. $quotes .'<span class="quotes_bottom"></span></div>';
                                        endif;
                                        ?>
                                    <div class="group_btn">
                                        <?php 
                                        //Button
                                        $buttonOne = get_sub_field( 'button_one' ); 
                                        if( isset( $buttonOne ) && !empty( $buttonOne ) ):
                                            
                                            $targetone = ( isset( $buttonOne['target'] ) && !empty( $buttonOne['target'] ) )? 'target="'. $buttonOne['target'] .'"' : '';
                                            $urlone = ( isset( $buttonOne['url'] ) && !empty( $buttonOne['url'] ) )? 'href="'. $buttonOne['url'] .'"' : '';
                                            $titleone = ( isset( $buttonOne['title'] ) && !empty( $buttonOne['title'] ) )? $buttonOne['title'] : '';
                                            
                                            printf( '<a %s %s class="cta-btn btn_red">%s</a>', $urlone, $targetone, $titleone  );
                                            
                                        endif;

                                        //Button
                                        $buttonTwo = get_sub_field( 'button_two' ); 
                                        if( isset( $buttonTwo ) && !empty( $buttonTwo ) ):
                                            
                                            $targettwo = ( isset( $buttonTwo['target'] ) && !empty( $buttonTwo['target'] ) )? 'target="'. $buttonTwo['target'] .'"' : '';
                                            $urltwo = ( isset( $buttonTwo['url'] ) && !empty( $buttonTwo['url'] ) )? 'href="'. $buttonTwo['url'] .'"' : '';
                                            $titletwo = ( isset( $buttonTwo['title'] ) && !empty( $buttonTwo['title'] ) )? $buttonTwo['title'] : '';
                                            
                                            printf( '<a %s %s class="cta-btn btn_dark">%s</a>', $urltwo, $targettwo, $titletwo  );
                                            
                                        endif;
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            <?php endwhile; ?>
        <?php endif; ?>

    
    </div>
</section>
<?php 
if( $amtext = get_sub_field( 'amend_text' ) ): ?>
<div class="amend-text">
	<div class="container">
		<div class="row ">
			<div class="col-lg-10 mx-auto">
				<div class="text-center amend-inner">
					<?php 
                       echo $amtext;
                        ?>
				</div>
			</div>
		</div>
	</div>
</div>

<?php endif; ?>
		
<?php elseif( 'v3' == get_sub_field( 'style' ) ): ?>
	<?php if ( get_sub_field( 'default_space' ) == 1 ) : ?>
	<section class="content-area pt-0 pb-0">
	<?php else : ?>
	<section class="content-area pt_200 pb-0">
	<?php endif; ?>
    <div class="container">
        <?php if ( have_rows( 'content_list' ) ) : ?>
            <?php while ( have_rows( 'content_list' ) ) : the_row(); ?>
            <?php if( 'v1' == get_sub_field( 'image_position' ) ): ?>
                <div class="content-items">
                    <div class="text-center">
                        <?php 
                        //Heading
                        if( $heading = get_sub_field( 'heading' ) ):
                            echo '<h2 class="h2 color_white letter_space">'. $heading .'</h2>';
                        endif;
                        ?>
						<?php 
                        //Heading
                        if( $headingSub = get_sub_field( 'heading_sub' ) ):
                            echo '<h3 class="h4 color_white letter_space">'. $headingSub .'</h3>';
                        endif;
                        ?>
                    </div>
                    <div class="item-inner bg_white v3">
                        <div class="row align-items-lg-center">
                            <div class="col-sm-6">
                                <div class="content-img ">
                                <?php 
                                    $image = get_sub_field( 'image' ); 
                                    if ( $image ) :
                                    ?>
                                    <div class="content-img">
                                        <img  src="<?php echo esc_url( $image['url'] ); ?>" alt="<?php echo esc_attr( $image['alt'] ); ?>" class="img-left" />
                                    </div>
                                <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="content-text font_gillsans">
                                        <?php 
                                        //Description
                                        the_sub_field( 'description' );
                                        ?>

                                        <?php 
                                        //Quotes
                                        if( $quotes = get_sub_field( 'quotes' ) ):
                                            echo '<div class="quote font_oswald">'. $quotes .'</div>';
                                        endif;
                                        ?>
                                    <div class="group_btn">
                                        <?php 
                                        //Button
                                        $buttonOne = get_sub_field( 'button_one' ); 
                                        if( isset( $buttonOne ) && !empty( $buttonOne ) ):
                                            
                                            $targetone = ( isset( $buttonOne['target'] ) && !empty( $buttonOne['target'] ) )? 'target="'. $buttonOne['target'] .'"' : '';
                                            $urlone = ( isset( $buttonOne['url'] ) && !empty( $buttonOne['url'] ) )? 'href="'. $buttonOne['url'] .'"' : '';
                                            $titleone = ( isset( $buttonOne['title'] ) && !empty( $buttonOne['title'] ) )? $buttonOne['title'] : '';
                                            
                                            printf( '<a %s %s class="cta-btn btn_red">%s</a>', $urlone, $targetone, $titleone  );
                                            
                                        endif;

                                        //Button
                                        $buttonTwo = get_sub_field( 'button_two' ); 
                                        if( isset( $buttonTwo ) && !empty( $buttonTwo ) ):
                                            
                                            $targettwo = ( isset( $buttonTwo['target'] ) && !empty( $buttonTwo['target'] ) )? 'target="'. $buttonTwo['target'] .'"' : '';
                                            $urltwo = ( isset( $buttonTwo['url'] ) && !empty( $buttonTwo['url'] ) )? 'href="'. $buttonTwo['url'] .'"' : '';
                                            $titletwo = ( isset( $buttonTwo['title'] ) && !empty( $buttonTwo['title'] ) )? $buttonTwo['title'] : '';
                                            
                                            printf( '<a %s %s class="cta-btn btn_dark">%s</a>', $urltwo, $targettwo, $titletwo  );
                                            
                                        endif;
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php else: ?>
                <div class="content-item">
                    <div class="text-center">
                    <?php 
                        //Heading
                        if( $heading = get_sub_field( 'heading' ) ):
                            echo '<h3 class="h2 color_white letter_space">'. $heading .'</h3>';
                        endif;
                        ?>
                    </div>
                    <div class="item-inner bg_white">
                       
                        <div class="row align-items-lg-center">
                            <div class="col-sm-6 order-sm-2 order-1">
                                <?php 
                                    $image = get_sub_field( 'image' ); 
                                    if ( $image ) :
                                    ?>
                                    <div class="content-img" >
                                        <img  src="<?php echo esc_url( $image['url'] ); ?>" alt="<?php echo esc_attr( $image['alt'] ); ?>" class="img-left" />
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="col-sm-6 order-sm-1 order-2">
                                <div class="content-text font_gillsans">
                                    <?php 
                                    //Description
                                    the_sub_field( 'description' );
                                    ?>
                                    <?php 
                                        //Quotes
                                        if( $quotes = get_sub_field( 'quotes' ) ):
                                            echo '<div class="quote font_oswald"><span class="quotes_top"></span>'. $quotes .'<span class="quotes_bottom"></span></div>';
                                        endif;
                                        ?>
                                    <div class="group_btn">
                                        <?php 
                                        //Button
                                        $buttonOne = get_sub_field( 'button_one' ); 
                                        if( isset( $buttonOne ) && !empty( $buttonOne ) ):
                                            
                                            $targetone = ( isset( $buttonOne['target'] ) && !empty( $buttonOne['target'] ) )? 'target="'. $buttonOne['target'] .'"' : '';
                                            $urlone = ( isset( $buttonOne['url'] ) && !empty( $buttonOne['url'] ) )? 'href="'. $buttonOne['url'] .'"' : '';
                                            $titleone = ( isset( $buttonOne['title'] ) && !empty( $buttonOne['title'] ) )? $buttonOne['title'] : '';
                                            
                                            printf( '<a %s %s class="cta-btn btn_red">%s</a>', $urlone, $targetone, $titleone  );
                                            
                                        endif;

                                        //Button
                                        $buttonTwo = get_sub_field( 'button_two' ); 
                                        if( isset( $buttonTwo ) && !empty( $buttonTwo ) ):
                                            
                                            $targettwo = ( isset( $buttonTwo['target'] ) && !empty( $buttonTwo['target'] ) )? 'target="'. $buttonTwo['target'] .'"' : '';
                                            $urltwo = ( isset( $buttonTwo['url'] ) && !empty( $buttonTwo['url'] ) )? 'href="'. $buttonTwo['url'] .'"' : '';
                                            $titletwo = ( isset( $buttonTwo['title'] ) && !empty( $buttonTwo['title'] ) )? $buttonTwo['title'] : '';
                                            
                                            printf( '<a %s %s class="cta-btn btn_dark">%s</a>', $urltwo, $targettwo, $titletwo  );
                                            
                                        endif;
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            <?php endwhile; ?>
        <?php endif; ?>

    
    </div>
</section>
<?php else: ?>
    <?php 
//Bakground.
$bg = ( $background_image = get_sub_field( 'background_image' ) )? 'style="background: url('. $background_image .')  no-repeat scroll 0 0/cover"' : '';
?>
    <section class="content-area bg_dark" <?php echo $bg; ?>>
    <div class="container">
        <?php if ( have_rows( 'content_list' ) ) : ?>
            <?php while ( have_rows( 'content_list' ) ) : the_row(); ?>
            <?php if( 'v1' == get_sub_field( 'image_position' ) ): ?>
                <div class="content-items">
                    <div class="text-center">
                        <?php 
                        //Heading
                        if( $heading = get_sub_field( 'heading' ) ):
                            echo '<h2 class="h2 color_white letter_space">'. $heading .'</h2>';
                        endif;
                        ?>
						<?php 
                        //Heading
                        if( $headingSub = get_sub_field( 'heading_sub' ) ):
                            echo '<h3 class="h4 color_white letter_space">'. $headingSub .'</h3>';
                        endif;
                        ?>
                    </div>
                    <div class="item-inner bg_white">
                        <?php 
                            $image = get_sub_field( 'image' ); 
                            if ( $image ) :
                            ?>
                            <div class="content-img" >
                                <img  class="item_bg left d-none d-xxl-block"  src="<?php echo esc_url( $image['url'] ); ?>" alt="<?php echo esc_attr( $image['alt'] ); ?>" class="img-left" />
                            </div>
                        <?php endif; ?>
                        <div class="row ">
                            <div class="col-sm-6">
                                <div class="content-img d-xxl-none">
                                <?php 
                                    $image = get_sub_field( 'image' ); 
                                    if ( $image ) :
                                    ?>
                                    <div class="content-img">
                                        <img  src="<?php echo esc_url( $image['url'] ); ?>" alt="<?php echo esc_attr( $image['alt'] ); ?>" class="img-left" />
                                    </div>
                                <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="content-text font_gillsans">
                                        <?php 
                                        //Description
                                        the_sub_field( 'description' );
                                        ?>

                                        <?php 
                                        //Quotes
                                        if( $quotes = get_sub_field( 'quotes' ) ):
                                            echo '<div class="quote font_oswald">'. $quotes .'</div>';
                                        endif;
                                        ?>
                                    <div class="group_btn">
                                        <?php 
                                        //Button
                                        $buttonOne = get_sub_field( 'button_one' ); 
                                        if( isset( $buttonOne ) && !empty( $buttonOne ) ):
                                            
                                            $targetone = ( isset( $buttonOne['target'] ) && !empty( $buttonOne['target'] ) )? 'target="'. $buttonOne['target'] .'"' : '';
                                            $urlone = ( isset( $buttonOne['url'] ) && !empty( $buttonOne['url'] ) )? 'href="'. $buttonOne['url'] .'"' : '';
                                            $titleone = ( isset( $buttonOne['title'] ) && !empty( $buttonOne['title'] ) )? $buttonOne['title'] : '';
                                            
                                            printf( '<a %s %s class="cta-btn btn_red">%s</a>', $urlone, $targetone, $titleone  );
                                            
                                        endif;

                                        //Button
                                        $buttonTwo = get_sub_field( 'button_two' ); 
                                        if( isset( $buttonTwo ) && !empty( $buttonTwo ) ):
                                            
                                            $targettwo = ( isset( $buttonTwo['target'] ) && !empty( $buttonTwo['target'] ) )? 'target="'. $buttonTwo['target'] .'"' : '';
                                            $urltwo = ( isset( $buttonTwo['url'] ) && !empty( $buttonTwo['url'] ) )? 'href="'. $buttonTwo['url'] .'"' : '';
                                            $titletwo = ( isset( $buttonTwo['title'] ) && !empty( $buttonTwo['title'] ) )? $buttonTwo['title'] : '';
                                            
                                            printf( '<a %s %s class="cta-btn btn_dark">%s</a>', $urltwo, $targettwo, $titletwo  );
                                            
                                        endif;
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php else: ?>
                <div class="content-item">
                    <div class="text-center">
                    <?php 
                        //Heading
                        if( $heading = get_sub_field( 'heading' ) ):
                            echo '<h3 class="h2 color_white letter_space">'. $heading .'</h3>';
                        endif;
                        ?>
                    </div>
                    <div class="item-inner bg_white">
                        <?php 
                            $image = get_sub_field( 'image' ); 
                            if ( $image ) :
                            ?>
                            <div class="content-img">
                                <img  class="item_bg right d-none d-xxl-block"  src="<?php echo esc_url( $image['url'] ); ?>" alt="<?php echo esc_attr( $image['alt'] ); ?>" class="img-left" />
                            </div>
                        <?php endif; ?>
                        <div class="row">
                            <div class="col-sm-6 order-sm-2 order-1">
                                <?php 
                                    $image = get_sub_field( 'image' ); 
                                    if ( $image ) :
                                    ?>
                                    <div class="content-img" >
                                        <img  src="<?php echo esc_url( $image['url'] ); ?>" alt="<?php echo esc_attr( $image['alt'] ); ?>" class="img-left" />
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="col-sm-6 order-sm-1 order-2">
                                <div class="content-text font_gillsans">
                                    <?php 
                                    //Description
                                    the_sub_field( 'description' );
                                    ?>
                                    <?php 
                                        //Quotes
                                        if( $quotes = get_sub_field( 'quotes' ) ):
                                            echo '<div class="quote font_oswald"><span class="quotes_top"></span>'. $quotes .'<span class="quotes_bottom"></span></div>';
                                        endif;
                                        ?>
                                    <div class="group_btn">
                                        <?php 
                                        //Button
                                        $buttonOne = get_sub_field( 'button_one' ); 
                                        if( isset( $buttonOne ) && !empty( $buttonOne ) ):
                                            
                                            $targetone = ( isset( $buttonOne['target'] ) && !empty( $buttonOne['target'] ) )? 'target="'. $buttonOne['target'] .'"' : '';
                                            $urlone = ( isset( $buttonOne['url'] ) && !empty( $buttonOne['url'] ) )? 'href="'. $buttonOne['url'] .'"' : '';
                                            $titleone = ( isset( $buttonOne['title'] ) && !empty( $buttonOne['title'] ) )? $buttonOne['title'] : '';
                                            
                                            printf( '<a %s %s class="cta-btn btn_red">%s</a>', $urlone, $targetone, $titleone  );
                                            
                                        endif;

                                        //Button
                                        $buttonTwo = get_sub_field( 'button_two' ); 
                                        if( isset( $buttonTwo ) && !empty( $buttonTwo ) ):
                                            
                                            $targettwo = ( isset( $buttonTwo['target'] ) && !empty( $buttonTwo['target'] ) )? 'target="'. $buttonTwo['target'] .'"' : '';
                                            $urltwo = ( isset( $buttonTwo['url'] ) && !empty( $buttonTwo['url'] ) )? 'href="'. $buttonTwo['url'] .'"' : '';
                                            $titletwo = ( isset( $buttonTwo['title'] ) && !empty( $buttonTwo['title'] ) )? $buttonTwo['title'] : '';
                                            
                                            printf( '<a %s %s class="cta-btn btn_dark">%s</a>', $urltwo, $targettwo, $titletwo  );
                                            
                                        endif;
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            <?php endwhile; ?>
        <?php endif; ?>

    
    </div>
</section>
<?php endif; ?>
 
 
 
<!-- Content End-->