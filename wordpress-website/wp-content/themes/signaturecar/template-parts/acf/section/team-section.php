<section class="content-area pt-0">
    <div class="container">
    <div class="text-start">
        <?php 
        //Heading
        if( $heading = get_sub_field( 'heading' ) ):
            echo '<h3 class="h2 color_white letter_space">'. $heading .'</h3>';
        endif;
        ?>
    </div>
    <?php if ( have_rows( 'team_members' ) ) : ?>
    <div class="item-inner bg_white">
        <div class="team_box_inner">
            <div class="row">
                <?php while ( have_rows( 'team_members' ) ) : the_row(); ?>
                <div class="col-lg-3 col-md-4 col-sm-6">
                        <div class="team-box">
                        <?php 
                            $image = get_sub_field( 'image' ); 
                            if ( $image ) :
                            ?>
                            <div class="team-img">
                                <img  src="<?php echo esc_url( $image['url'] ); ?>" alt="<?php echo esc_attr( $image['alt'] ); ?>" class="img-left" />
                            </div>
                        <?php endif; ?>
                        <div class="team-info">
                            <?php 
                                //Name
                                if( $name = get_sub_field( 'name' ) ):
                                    echo '<span class="title d-block font_oswald letter_space">'. $name .'</span>';
                                endif;

                                //designation
                                if( $designation = get_sub_field( 'designation' ) ):
                                    echo '<p class="font_gillsans">'. $designation .'</p>';
                                endif;
                                ?>
                                <?php 
                                $phone = get_sub_field( 'phone' ); 
                                $email = get_sub_field( 'email' ); 
                                if( $phone or  $email): ?>
                                    <ul class="team-meta">
                                        <?php 
                                        if( $phone ):
                                            echo '<li class="phn"><a href="tel:'.$phone.'">'. $phone .'</a></li>';
                                        endif;

                                        if( $email ):
                                            echo '<li class="mail"><a href="mailto:'.$email.'">'. $email .'</a></li>';
                                        endif;
                                        ?>
                                     </ul>
                                <?php  endif; ?>
                                
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>
    
<?php endif; ?>
    </div>
</section>


