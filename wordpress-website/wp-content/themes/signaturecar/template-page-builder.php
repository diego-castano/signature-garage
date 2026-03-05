<?php
/**
 * Template Name: Page Builder ACF Template
 * 
 * The template for displaying pages with page builder acf field group
 */
 get_header(); 
    
 while ( have_posts() ) : the_post();

 get_template_part( 'template-parts/acf/acf', 'content' );

endwhile; // End of the loop.
        
 get_footer();