<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package signaturecar
 */

get_header();
?>

	<main id="primary" class="site-main">

		<?php
		while ( have_posts() ) :
			the_post(); ?>
			<?php $informations = get_field('informations');?>
		<?php 
//Bakground.
$bg = ( $background_image = get_field( 'background_image' ) )? 'style="background: url('. $background_image .')   no-repeat fixed center center/cover"' : '';
?>
		<section class="content-area singleCaar pt_200"  <?php echo $bg; ?>>
            <div class="container">
                <div class="single-details-header">
                    <div class="content-text">
                        <a class="return_action font_gillsans" href="<?php echo home_url( '/inventario' ); ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="15" viewBox="0 0 18 15">
                                <path id="ic_reply_24px" d="M10,9V5L3,12l7,7V14.9c5,0,8.5,1.6,11,5.1C20,15,17,10,10,9Z" transform="translate(-3 -5)" fill="#ac1d28"></path>
                            </svg>
                            VOLVER AL INVENTARIO
                        </a>
                        <div class="breadcrumb-navigation">
<?php
if ( function_exists('yoast_breadcrumb') ) {
  yoast_breadcrumb( '<div id="breadcrumbs">','</div>' );
}
?>	
                        </div>
                        <h1 class="title h2 font_gillsans color_white font_medium"><?php the_title(); ?></h1>
                        <ul class="single-meta font_oswald">
							
                            <li>AÑO <span><?php echo $informations['year'] ?></span></li>
                            <li>KILÓMETROS <span><?php echo $informations['kilometers'] ?></span></li>
                        </ul>
                    </div>
                    <div class="content-text text-md-end text-center">
						<?php if( 'v1' == get_field( 'sold' ) ): ?>
							<h2 class="status  h2 font_gillsans color_white font_medium">VENDIDO</h2>
						<?php else: ?>
							<h2 class="status  h2 font_gillsans color_white font_medium"><?php the_field('price') ?></h2>
						<?php endif; ?>
                        
                        <ul class="single-action font_gillsans">
                            
							 <li>
										<div class="send-whatsapp">
											<a target="_blank" href="https://wa.me/598094300100?text=Quiero%20m%C3%A1s%20informaci%C3%B3n%20sobre%3A%20
<?php the_permalink(); ?>" class="save_action d-flex align-items-center"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20">
													<path id="ic_textsms_24px" d="M20,2H4A2,2,0,0,0,2.01,4L2,22l4-4H20a2.006,2.006,0,0,0,2-2V4A2.006,2.006,0,0,0,20,2ZM9,11H7V9H9Zm4,0H11V9h2Zm4,0H15V9h2Z" transform="translate(-2 -2)" fill="#fff"></path>
												</svg>
												<span>Contacto</span>
											</a>
										</div>
									</li>
                            <!--<li>
                                <div class="share">
                                    <a href="javascript:;" class="save_action d-flex align-items-center"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 13.216 10.573">
                                            <path id="ic_email_24px" d="M13.894,4H3.322A1.32,1.32,0,0,0,2.007,5.322L2,13.251a1.325,1.325,0,0,0,1.322,1.322H13.894a1.325,1.325,0,0,0,1.322-1.322V5.322A1.325,1.325,0,0,0,13.894,4Zm0,2.643-5.286,3.3-5.286-3.3V5.322l5.286,3.3,5.286-3.3Z" transform="translate(-2 -4)" fill="#fff"></path>
                                        </svg>
                                        <span>Cuota</span>
                                    </a>
                                </div>
                            </li>-->
                        </ul>
                    </div>
                </div>
				
				<?php $images_images = get_field( 'images' ); ?>
<?php if ( $images_images ) :  ?>
							<div class="details-photos gallerym single-gallery">
								<div class="gallery-slider">

									<?php foreach ( $images_images as $images_image ): ?>
									<div class="slide">
										<a href="<?php echo esc_url( $images_image['url'] ); ?>" class="image-box">
										<div class="img-box-inner" style="background: url(<?php echo esc_url( $images_image['sizes']['large'] ); ?>) no-repeat center / cover;"><i class="fas fa-plus"></i></div>
									</a>
									</div>
									<?php endforeach; ?>

								</div>
				</div>									
<?php endif; ?>		
				
				
                <div class="details-content">
                    <h3 class="h2 title"><?php the_title(); ?></h3>
                    <div class="tabs-name">
                        <ul class="nav nav-nav mb-3" id="nav-tab" role="tablist">
							<?php if( $informations ): ?>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="nav-home-tab" data-bs-toggle="pill" data-bs-target="#nav-home" type="button" role="tab" aria-controls="nav-home" aria-selected="true">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 21.333">
                                        <path id="ic_directions_car_24px" d="M24.227,6.347A1.992,1.992,0,0,0,22.333,5H7.667A2,2,0,0,0,5.773,6.347L3,14.333V25a1.337,1.337,0,0,0,1.333,1.333H5.667A1.337,1.337,0,0,0,7,25V23.667H23V25a1.337,1.337,0,0,0,1.333,1.333h1.333A1.337,1.337,0,0,0,27,25V14.333ZM7.667,19.667a2,2,0,1,1,2-2A2,2,0,0,1,7.667,19.667Zm14.667,0a2,2,0,1,1,2-2A2,2,0,0,1,22.333,19.667ZM5.667,13l2-6H22.333l2,6Z" transform="translate(-3 -5)" fill="#ac1d28"></path>
                                    </svg>
                                    <span>Detalles</span>
                                </button>
                            </li>
							<?php endif; ?>
							
<?php $images_images = get_field( 'images' ); ?>
<?php if ( $images_images ) :  ?>
								<li class="nav-item" role="presentation">
									<button class="nav-link" id="nav-profile-tab" data-bs-toggle="pill" data-bs-target="#nav-profile" type="button" role="tab" aria-controls="nav-profile" aria-selected="false">
										<svg xmlns="http://www.w3.org/2000/svg" width="20" height="18" viewBox="0 0 20 18">
											<path id="ic_camera_enhance_24px" d="M9,3,7.17,5H4A2.006,2.006,0,0,0,2,7V19a2.006,2.006,0,0,0,2,2H20a2.006,2.006,0,0,0,2-2V7a2.006,2.006,0,0,0-2-2H16.83L15,3Zm3,15a5,5,0,1,1,5-5A5,5,0,0,1,12,18Zm0-1,1.25-2.75L16,13l-2.75-1.25L12,9l-1.25,2.75L8,13l2.75,1.25Z" transform="translate(-2 -3)" fill="#ac1d28"></path>
										</svg>
										<span>Todas las fotos</span>
									</button>
								</li>							
<?php endif; ?>

                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="nav-request-tab" data-bs-toggle="pill" data-bs-target="#nav-request" type="button" role="tab" aria-controls="nav-request" aria-selected="false">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="19.324" height="19.324" viewBox="0 0 19.324 19.324">
                                        <path id="ic_search_24px" d="M16.811,15.153h-.873l-.309-.3a7.192,7.192,0,1,0-.773.773l.3.309v.873l5.524,5.513,1.646-1.646Zm-6.629,0a4.972,4.972,0,1,1,4.972-4.972A4.965,4.965,0,0,1,10.182,15.153Z" transform="translate(-3 -3)" fill="#ac1d28"></path>
                                    </svg>
                                    <span>Contacto</span>
                                </button>
                            </li>
                        </ul>
                    </div>
                    <div class="tab-content" id="nav-tabContent">
					<?php if( $informations ): ?>
                        <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
                            <div class="details-table">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <table>
                                            <tbody>
                                                <tr>
                                                    <td>AÑO:</td>
                                                    <td><strong><?php echo $informations['year'] ?></strong></td>
                                                </tr>
                                                <tr>
                                                    <td>MARCA:</td>
                                                    <td><strong><?php echo $informations['make'] ?></strong></td>
                                                </tr>
                                                <tr>
                                                    <td>MODELO:</td>
                                                    <td><strong><?php echo $informations['model'] ?></strong></td>
                                                </tr>
                                                
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="col-sm-4">
                                        <table>
                                            <tbody>
                                                <tr>
                                                    <td>KILÓMETRO:</td>
                                                    <td><strong><?php echo $informations['kilometers'] ?></strong></td>
                                                </tr>
                                                <tr>
                                                    <td>MOTOR:</td>
                                                    <td><strong><?php echo $informations['engine'] ?></strong></td>
                                                </tr>
                                                <tr>
                                                    <td>TRANS:</td>
                                                    <td><strong><?php echo $informations['trans'] ?></strong></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
									<div class="col-sm-4">
                                        <table>
                                            <tbody>
                                                <tr>
                                                    <td>PATENTE:</td>
                                                    <td><strong><?php echo $informations['patente'] ?></strong></td>
                                                </tr>
                                                <tr>
                                                    <td>SEGURO:</td>
                                                    <td><strong><?php echo $informations['seguro'] ?></strong></td>
                                                </tr>
                                               <tr>
                                                    <td>COLOR:</td>
                                                    <td><strong><?php echo $informations['body'] ?></strong></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    
                                </div>
								<div>
									<h3 class=" h3 title mb-0">Observaciones</h3>
								</div>
								<div class="content-text ">
									<?php $observaciones = get_field( 'observaciones' ); ?>
							<?php echo $observaciones; ?>
                            
						</div>
                            </div>
                        </div>
						<?php endif; ?>

<?php $images_images = get_field( 'images' ); ?>
<?php if ( $images_images ) :  ?>
							<div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">
								<div class="details-photos gallerym">
									<div class="row">
						
	<?php foreach ( $images_images as $images_image ): ?>
												<a href="<?php echo esc_url( $images_image['url'] ); ?>" class="image-box col-sm-4">
													<div class="img-box-inner" style="background: url(<?php echo esc_url( $images_image['sizes']['large'] ); ?>) no-repeat center / cover;"><i class="fas fa-plus"></i></div>
													</a>
	<?php endforeach; ?>
										
									</div>
								</div>
							</div>										
<?php endif; ?>						

						
                        
                        <div class="tab-pane fade" id="nav-request" role="tabpanel" aria-labelledby="nav-request-tab">
<?php if ( get_field( 'disable', 'option' ) == 1 ) : ?>
<?php else : ?>
							<div class="contact_info">
				<div class="">
					<div class="row">
						<div class="col-sm-6 col-md-4">
							<div class="item">
<?php $email = get_field( 'c_email', 'option' ); ?>
<?php if ( $email ) : ?>
	<i class="fas fa-envelope"></i> <a href="<?php echo esc_url( $email['url'] ); ?>" target="<?php echo esc_attr( $email['target'] ); ?>"><?php echo esc_html( $email['title'] ); ?></a>
								<?php endif; ?></div>
						</div>
						<div class="col-sm-6 col-md-4">
<div class="item"><?php $phone_number = get_field( 'c_phone_number', 'option' ); ?>
<?php if ( $phone_number ) : ?>
	<i class="fas fa-phone-alt"></i> <div>
	<a class="d-block" href="<?php echo esc_url( $phone_number['url'] ); ?>" target="<?php echo esc_attr( $phone_number['target'] ); ?>"><?php echo esc_html( $phone_number['title'] ); ?></a>
	<?php $phone_number_alt = get_field( 'c_phone_number_alt', 'option' ); ?>
	<?php if ( $phone_number ) : ?>
	<a class="d-block"  href="<?php echo esc_url( $phone_number_alt['url'] ); ?>" target="<?php echo esc_attr( $phone_number_alt['target'] ); ?>"><?php echo esc_html( $phone_number_alt['title'] ); ?></a>
	<?php endif; ?>
	</div>
	<?php endif; ?>			</div>				
						</div>
						<div class="col-md-4">
<div class="item"><?php $address = get_field( 'c_address', 'option' ); ?>
<?php if ( $address ) : ?>
	<i class="fas fa-map-marker-alt"></i> <a href="<?php echo esc_url( $address['url'] ); ?>" target="<?php echo esc_attr( $address['target'] ); ?>"><?php echo esc_html( $address['title'] ); ?></a>
	<?php endif; ?>	</div>							
						</div>
					</div>
								</div>								
								
							</div>							
<?php endif; ?>							

							
							
                            <div class="c_form">
                                <?php echo do_shortcode("[gravityform id='3' title='true']"); ?>
                            </div>
                        </div>
                        
                    </div>

                </div>
            </div>
        </section>
        <!-- Content End -->
		<?php 

		endwhile; // End of the loop.
		?>

	</main><!-- #main -->

<?php
get_footer();



