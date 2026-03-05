<?php 
if ( have_rows( 'page_builder' ) ):

    while ( have_rows( 'page_builder' ) ) : the_row();
        
        //Hero Section
        if( get_row_layout() == 'hero_section' ):
            get_template_part( 'template-parts/acf/section/hero', 'section' );
        endif;

        //Inventory Slider Section
        if( get_row_layout() == 'inventory_section' ):
            get_template_part( 'template-parts/acf/section/inventory', 'section' );
        endif;

        //Sell Car Section
        if( get_row_layout() == 'sell_your_car_section' ):
            get_template_part( 'template-parts/acf/section/sell-your-car', 'section' );
        endif;

         //Sell Car Section
         if( get_row_layout() == 'service_content_section' ):
            get_template_part( 'template-parts/acf/section/service-content', 'section' );
        endif;

         //Promo Section
         if( get_row_layout() == 'promo_section' ):
            get_template_part( 'template-parts/acf/section/promo', 'section' );
        endif;

        //Image text Section
        if( get_row_layout() == 'page_banner_section' ):
            get_template_part( 'template-parts/acf/section/page-banner', 'section' );
        endif;
        //Image text Section
        if( get_row_layout() == 'image_text_section' ):
            get_template_part( 'template-parts/acf/section/image-text', 'section' );
        endif;

         //Team Section
         if( get_row_layout() == 'team_section' ):
            get_template_part( 'template-parts/acf/section/team', 'section' );
        endif;

          //Inventory Page Section
          if( get_row_layout() == 'inventory_page_section' ):
            get_template_part( 'template-parts/acf/section/inventory-page', 'section' );
        endif;
        
          //Sell Car Page Section
          if( get_row_layout() == 'sell_car_page_section' ):
            get_template_part( 'template-parts/acf/section/sell-car-page', 'section' );
        endif;


           //Sell Car Page Section
           if( get_row_layout() == 'instagram_section' ):
            get_template_part( 'template-parts/acf/section/instagram', 'section' );
        endif;

//Sell Car Page Section
           if( get_row_layout() == 'rich_text_section' ):
            get_template_part( 'template-parts/acf/section/rich-text', 'section' );
        endif;

//Sell Car Page Section
           if( get_row_layout() == 'video_gallery_section' ):
            get_template_part( 'template-parts/acf/section/video-gallery', 'section' );
        endif;

//Sell Car Page Section
           if( get_row_layout() == 'photo_gallery_section' ):
            get_template_part( 'template-parts/acf/section/photo-gallery', 'section' );
        endif;
        
        
    endwhile;
    
endif;