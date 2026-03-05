<?php
/**
 * Functions which enhance the theme by hooking into WordPress
 *
 * @package signaturecar
 */

/**
 * Adds custom classes to the array of body classes.
 *
 * @param array $classes Classes for the body element.
 * @return array
 */
function signaturecar_body_classes( $classes ) {
	// Adds a class of hfeed to non-singular pages.
	if ( ! is_singular() ) {
		$classes[] = 'hfeed';
	}

	// Adds a class of no-sidebar when there is no sidebar present.
	if ( ! is_active_sidebar( 'sidebar-1' ) ) {
		$classes[] = 'no-sidebar';
	}

	return $classes;
}
add_filter( 'body_class', 'signaturecar_body_classes' );

/**
 * Add a pingback url auto-discovery header for single posts, pages, or attachments.
 */
function signaturecar_pingback_header() {
	if ( is_singular() && pings_open() ) {
		printf( '<link rel="pingback" href="%s">', esc_url( get_bloginfo( 'pingback_url' ) ) );
	}
}
add_action( 'wp_head', 'signaturecar_pingback_header' );



/**
 * Add Header Html Content.
 */
add_action( 'wp_body_open', 'signaturecar_header' );
function signaturecar_header(){
    ?>
    <!-- ======== MAIN WRAPPER START ======= -->
    <div class="main-wrapper">
        <!-- Header Start -->
        <header class="header-area">
			<?php if ( have_rows( 'contact_information', 'options' ) ) : ?>
				<div class="header-top-area d-none d-lg-block">
					<div class="container-fluid">
						<div class="top-actions">
							<ul>
								<?php while ( have_rows( 'contact_information', 'options' ) ) : the_row(); ?>
									
									<li>
										<?php if ( get_sub_field( 'target_blank' ) == 1 ) : ?>
											<a target="_blank" href="<?php the_sub_field('link')  ?>"><img src="<?php the_sub_field( 'icon' ) ?>" alt="icon" /><span class="d-none d-sm-block"><?php the_sub_field('text')  ?></span></a>
										<?php else : ?>
											<a href="<?php the_sub_field('link')  ?>"><img src="<?php the_sub_field( 'icon' ) ?>" alt="icon" /><span class="d-none d-sm-block"><?php the_sub_field('text')  ?></span></a>
										<?php endif; ?>
										
								</li>
									
								<?php endwhile; ?>
							</ul>
						</div>
					</div>
				</div>
                    
			<?php endif; ?>
            
            <div class="header">
                <div class="container-fluid">
                    <div class="header-inn d-flex justify-content-between">
                        <div class="site-logo">
							<a href="<?php echo home_url( '/' ); ?>">
								<?php 
									//Logo Image.
									$header_logo = get_field( 'logo', 'options' ); 
									if ( $header_logo ) { 
										echo '<img class="logo_blue" src="'. esc_url( $header_logo['url'] ) .'" alt="'. esc_attr( $header_logo['alt'] ) .'"/>';     
									}else{
										echo get_bloginfo( 'name' );
									}
			
								?>
								
								<?php 
									//Logo Image.
									$dark_logo = get_field( 'logo_dark', 'options' ); 
									if ( $dark_logo ) { 
										echo '<img class="darkLogo" src="'. esc_url( $dark_logo['url'] ) .'" alt="'. esc_attr( $dark_logo['alt'] ) .'"/>';     
									}
			
								?>
							</a>
                        </div>
                        <div class="header-navigation d-flex align-items-center justify-content-between">
                        <?php
                            if( has_nav_menu( 'menu-1' ) ):
                                echo '<div class="mainmenu">';
                                    //Menu Arguments    
                                    wp_nav_menu( array(
                                        'container'       => 'nav',
										'container_id'    => 'menu',
                                        'menu_id'         => '',
                                        'menu_class'         => 'dropdown',
                                        'theme_location'  => 'menu-1'
                                    ));
                                echo '</div>';
                            
                            endif;
                         ?>    

                            <div class="top-actions d-lg-none">
                                <ul>
                                <?php while ( have_rows( 'contact_information', 'options' ) ) : the_row(); ?>
									
									<li>
										<?php if ( get_sub_field( 'target_blank' ) == 1 ) : ?>
											<a target="_blank" href="<?php the_sub_field('link')  ?>"><img src="<?php the_sub_field( 'icon' ) ?>" alt="icon" /></a>
										<?php else : ?>
											<a href="<?php the_sub_field('link')  ?>"><img src="<?php the_sub_field( 'icon' ) ?>" alt="icon" /></a>
										<?php endif; ?>
										
									</li>
									
								<?php endwhile; ?>
                                </ul>
                            </div>
                            <div class="header-toggle">
                                <div class="menu-toggle-inn">
                                    <div id="menu-toggle">
                                        <span></span><span></span><span></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>
        <!-- Header End -->
    <?php
}




/**
 * Add Footer Html Content.
 */
add_action( 'wp_footer', 'digitalhenka_footer' );
function digitalhenka_footer(){ ?>

<!-- Content Start -->
<section class="newsletter-area">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <div class="content-text text-md-start text-center">
                            <h3 class="h2 color_white letter_space">BOLETIN INFORMATIVO</h3>
                            <div class="font_gillsans font_medium color_white">
                                <p>Suscríbase a nuestro boletín para mantenerse actualizado sobre los autos nuevos que agregamos a nuestro inventario, eventos y más.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="newsletter-form">
							<div id="mc_embed_shell">
        <link href="//cdn-images.mailchimp.com/embedcode/classic-061523.css" rel="stylesheet" type="text/css">
        <style>
            .mc-field-group {
                display: flex !important;
                align-items: center;
                justify-content: flex-end;
                gap: 10px;
            }
			.newsletter-form input {
    flex: 1;
    height: 51px;
    background: #fff;
    border: 1px solid #fff !important;
    padding: 10px 20px !important;
    font-size: 16px;
    color: #202020 !important;
    font-family: 'Oswald', sans-serif;
    border-radius: 0 !important;
}
			#mc_embed_signup .mc-field-group{
				width:100% !important;
				padding:0 !important
			}
			.newsletter-form input[type="submit"] {
    margin: 0 !important;
    width: 152px !important;
    display: inline-block !important;
    min-width: 152px;
    max-width: 152px;
    flex: 1;
    text-indent: inherit;
    background: #AC1D28;
    border-color: #AC1D28 !important;
    color: #fff !important;
    font-family: 'Oswald', sans-serif !important;
				transition:all 0.4s
}
			.newsletter-form input[type="submit"]:hover{
				background:#fff !important;
				color:#AC1D28 !important;
				border-color:#fff !important
			}
			#mc_embed_signup div.response {
				margin: 0 !important;
				padding: 0 !important;
				font-size: 16px !important;
			}
			#mce-responses{
				margin-left:0 !important
			}
        </style>
        <div id="mc_embed_signup">
            <form action="https://signature-garage.us18.list-manage.com/subscribe/post?u=a603fcae0407ede20d70b1087&amp;id=7a42b5eb38&amp;f_id=008ae7e1f0" method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" class="validate m-0" target="_blank">
                <div id="mc_embed_signup_scroll">
                    <div class="mc-field-group">
                        <input type="email" name="EMAIL" class="required email" placeholder="Introduce tu correo electrónico*" id="mce-EMAIL" required="" value="">
                        <input type="submit" name="subscribe" id="mc-embedded-subscribe" class="cta-btn btn_white_border" value="REGÍSTRATE AHORA">
                    </div>
                    <div id="mce-responses" class="clear foot">
                        <div class="response" id="mce-error-response" style="display: none;"></div>
                        <div class="response" id="mce-success-response" style="display: none;"></div>
                    </div>
                </div>
            </form>
        </div>
        <script type="text/javascript" src="//s3.amazonaws.com/downloads.mailchimp.com/js/mc-validate.js"></script>
        <script type="text/javascript">
            (function($) {
                window.fnames = new Array();
                window.ftypes = new Array();
                fnames[0] = 'EMAIL';
                ftypes[0] = 'email';
                fnames[1] = 'FNAME';
                ftypes[1] = 'text';
                fnames[2] = 'LNAME';
                ftypes[2] = 'text';
                fnames[3] = 'ADDRESS';
                ftypes[3] = 'address';
                fnames[4] = 'PHONE';
                ftypes[4] = 'phone';
                fnames[5] = 'BIRTHDAY';
                ftypes[5] = 'birthday';
            }(jQuery));
            var $mcj = jQuery.noConflict(true);

			
			// select the target node
var target = document.getElementById('mce-success-response');

// create an observer instance
var observer = new MutationObserver(function(mutations) {
  mutations.forEach(function(mutation) {
    if (target.innerHTML === "Thank you for subscribing!") {
      target.innerHTML = "Gracias por Suscribirse";
    }
  });
});

// configuration of the observer:
var config = { attributes: true, childList: true, characterData: true };

// pass in the target node, as well as the observer options
observer.observe(target, config);
        </script>
    </div>
                            <form class="d-none">
                                <label for="n_email" hidden>Correo electrónico</label>
                                <input type="email" id="n_email" placeholder="Introduce tu correo electrónico*" />
                                <button type="submit" class="cta-btn btn_white_border">REGÍSTRATE AHORA</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- Content End -->
		<!-- Footer Start -->
		<?php 
		//Bakground.
		$bg = ( $background_image = get_field( 'map_image', 'options' ) )? 'style="background: url('. $background_image .')  no-repeat scroll center/cover"' : '';
		?>
		<footer class="footer-area">
            <div class="footer-top-area">
                <div class="map_bg d-none d-lg-block" <?php echo $bg; ?>></div>
                <div class="container">
                    <div class="row align-items-end">
                        <div class="col-lg-4 order-md-1 order-2">
                            <div class="footer-directions  d-none d-lg-block">
								<?php 
								$button_text = get_field( 'map_button_text', 'options' );
								$button_link = get_field( 'map_link', 'options' );
                                if( $button_text && $button_link ):
                                    echo '<a target="_blank" href="'.$button_link.'" class="cta-btn btn_dark">'. $button_text .'</a>';
                                endif;
                                ?>
                            </div>
                        </div>
                        <div class="col-lg-8 order-md-2 order-1">
                            <div class="row">
                                <div class="col-md-5">
                                    <div class="footer-widget">
									<?php 
										if( $heading = get_field( 'heading', 'options' ) ):
											echo '<h4 class="footer-title">'. $heading .'</h4>';
										endif;
										?>
                                        <p><?php the_field('address', 'options'); ?><br>
										<?php
										if( $phone = get_field( 'phone', 'options' ) ):
											echo 'Teléfono: <a href="tel:'.$phone.'">'.$phone.'</a>';
										endif;
										?></p>
                                        <div class="footer-hours">
											<?php 
											if( $heading_office = get_field( 'heading_office', 'options' ) ):
												echo '<span class="hours_title d-block">'. $heading_office .'</span>';
											endif;
											?>
                                            <p><?php the_field('hours', 'options'); ?></p>
                                        </div>
										<?php if ( have_rows( 'footer_social', 'options' ) ) : ?>
											<ul class="footer-social">
												<?php while ( have_rows( 'footer_social', 'options' ) ) : the_row(); ?>
												<li><a target="_blank" href="<?php the_sub_field( 'link' ); ?>"><?php the_sub_field( 'icon' ); ?><span class="d-none">icon </span></a></li>
												<?php endwhile; ?>
											</ul>
										<?php endif; ?>
                                    </div>
                                    <div class="footer-directions  d-lg-none">
                                    <?php 
                                    $map_image = get_field( 'map_image', 'options'  ); 
                                    if ( $map_image ) :
                                    ?>
                                        <img src="<?php echo $map_image ?>"  />
                                        <?php 
                                            $button_text = get_field( 'map_button_text', 'options' );
                                            $button_link = get_field( 'map_link', 'options' );
                                            if( $button_text && $button_link ):
                                                echo '<a target="_blank" href="'.$button_link.'" class="cta-btn btn_dark">'. $button_text .'</a>';
                                            endif;
                                            ?>
                                    <?php endif; ?>
                                        
                                    </div>
                                </div>
                                <div class="col-md-7 order-md-3 order-3">
                                    <div class="footer-widget">
                                        <div class="footer_reviews">
											<?php 
											if( $r_heading = get_field( 'r_heading', 'options' ) ):
												echo '<h4 class="footer-title letter_space">'. $r_heading .'</h4>';
											endif;
											?>
											<div class="fslider">
												<?php the_field('description', 'options'); ?>
											</div>
											<?php 
											//Button
											$button = get_field( 'button', 'options' ); 
											if( isset( $button ) && !empty( $button ) ):
												$target = ( isset( $button['target'] ) && !empty( $button['target'] ) )? 'target="'. $button['target'] .'"' : '';
												$url = ( isset( $button['url'] ) && !empty( $button['url'] ) )? 'href="'. $button['url'] .'"' : '';
												$title = ( isset( $button['title'] ) && !empty( $button['title'] ) )? $button['title'] : '';
												
												printf( '<a %s %s class="cta-btn btn_dark ">%s</a>', $url, $target, $title  );
											
											endif;
											?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="copyright-area">
                <div class="container">
                    <div class="row">
                        <div class="col-sm-8">
                            <div class="text-sm-start text-center">
                            <?php the_field('copyright', 'options'); ?>
                                
                            </div>
                        </div>
                        
                    </div>
                </div>
            </div>
        </footer>


		
        <!-- Footer End -->
        <?php 
        if( $whatsApp = get_field( 'whatsapp_link', 'options' ) ):
            echo '<a href="'.$whatsApp.'" class="float-wa"><em class="fab fa-whatsapp"></em><span class="d-none">icon</span></a>';
        endif;
        ?>
        
         
    </div>
    <!-- ======== MAIN WRAPPER END ======= -->

<?php }


/**
 * Add ACF option page( signaturecar Theme Options ).
 */
add_action( 'init', 'signaturecar_acf_page_init' );
function signaturecar_acf_page_init (){
    if( function_exists('acf_add_options_page') ) {
   	    acf_add_options_page(array(
    		'page_title' 	=> 'General Options',
    		'menu_title'	=> 'Theme Options',
    		'menu_slug' 	=> 'signaturecar-general-option',
    		'capability'	=> 'edit_posts',
    		'redirect'		=> false
    	));
        acf_add_options_page(array(
    		'page_title' 	=> 'Header Options',
    		'menu_title'	=> 'Header Settings',
    		'parent_slug' 	=> 'signaturecar-general-option',
    		'capability'	=> 'edit_posts',
    		'redirect'		=> false
    	));	
        acf_add_options_page(array(
    		'page_title' 	=> 'Footer Options',
    		'menu_title'	=> 'Footer Settings',
    		'parent_slug' 	=> 'signaturecar-general-option',
    		'capability'	=> 'edit_posts',
    		'redirect'		=> false
    	));
    }
}

/**
 * Allow SVG upload
 */
function signaturecar_mime_types($mimes) {
	$mimes['svg'] = 'image/svg+xml';
	return $mimes;
  }
  add_filter('upload_mimes', 'signaturecar_mime_types'); 