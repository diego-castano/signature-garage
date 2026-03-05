<?php
/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package signaturecar
 */

get_header();
?>

<section class="content-area pt_200">
    <div class="container">
        
        <?php 
            //Loop
            if ( have_posts() ) :
          
              echo '<div class="row">';  
                      
                      /* Start the Loop */
                      while ( have_posts() ) : the_post();
                
                        ?>
                        <article id="post-<?php the_ID(); ?>" <?php post_class('col-md-4 col-sm-6'); ?>>
    
						<div class="box box-style-1">
                            <span class="title text-center d-block font_gillsans"><?php the_title(); ?></span>
                            <a href="<?php the_permalink(); ?>"><?php echo get_the_post_thumbnail( $post->ID, 'full' ); ?></a>
                            <div class="details">
                                <?php
                                $informations = get_field('informations');
                                if( $informations ): ?>
                                    <div class="box-meta d-flex align-items-center justify-content-between">
                                        <div>
                                            <div class="year font_oswald">
                                                <span class="d-block">AÑO</span><?php echo $informations['year'] ?>
                                            </div>
                                        </div>
                                        <div>
                                            <div class="year font_oswald">
                                                <span class="d-block">kilómetro</span><?php echo $informations['kilometers'] ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="box-btns font_oswald d-flex align-items-center justify-content-between">
                                    <span class="price"><?php the_field('price') ?></span>
                                    <a href="<?php the_permalink(); ?>" class="cta-btn btn_red">Ver detalles</a>
                                </div>
								
								<?php 
								$chatW = get_field('whatsapp_link', 'option');
								$chatBTNText = get_field('button_text', 'option');
								if( $chatW ) :?>
                                <div class="box-bottom-meta  d-flex align-items-center justify-content-center">
                                        <a href="<?php the_field( 'whatsapp_link', 'option' ); ?>" class="save_action d-flex align-items-center"><img src="<?php echo get_template_directory_uri(); ?>/assets/img/chat.png" alt="icon" /><?php echo $chatBTNText; ?></a>
                                    </div>
								<?php endif; ?>
                                
                            </div>
                        </div>
                        
                        </article><!-- #post-## -->
                        <?php
                
                      endwhile;
              
                echo '</div>';
              
              
            else :
        
              get_template_part( 'template-parts/content', 'none' );
        
            endif;
        ?>
    </div>
  </section>

<?php
get_footer();
