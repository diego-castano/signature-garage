<?php 
//Bakground.
$bg = ( $background_image = get_sub_field( 'background_image' ) )? 'style="background: url('. $background_image .') no-repeat scroll 0 0/cover;"' : '';
?>

<!-- Contetn Start -->
<section class="content-area selling_form_area" <?php echo $bg; ?>>
            <div class="container smalls">
                <div class="heading">
                    <div class="row align-items-end">
                        <div class="col-sm-5">
                            <?php 
                            //Heading
                            if( $heading = get_sub_field( 'heading' ) ):
                                echo '<h2 class="h1 color_white">'. $heading .'</h2>';
                            endif;
                            ?>
                        </div>

                        <div class="col-sm-7">
                        <?php $images = get_sub_field( 'images' ); ?>
                            <?php if ( $images ) : ?>
                                <img src="<?php echo esc_url( $images['url'] ); ?>" class="max_width"  alt="<?php echo esc_attr( $images['alt'] ); ?>" />
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="row justify-content-between">
                    <div class="col-md-6 col-sm-6">
                        <?php if ( have_rows( 'process' ) ) : ?>
                            <div class="content-text">
                                <ul class="number_list_icon">
                                    <?php while ( have_rows( 'process' ) ) : the_row(); ?>
                                        <li class="d-flex align-items-start">
                                        <?php $image = get_sub_field( 'number' ); ?>
                                            <?php if ( $image ) : ?>
                                                <div class="icon">
                                                    <span class="num"><?php echo $image; ?></span>
                                                </div>
                                            <?php endif; ?>
                                            
                                            <div class="text color_white">
                                                <?php 
                                                //Heading
                                                if( $title = get_sub_field( 'title' ) ):
                                                    echo '<h3 class="h3">'. $title .'</h3>';
                                                endif;

                                                if( $description = get_sub_field( 'description' ) ):
                                                    echo '<p>'. $description .'</p>';
                                                endif;
                                                ?>
                                            </div>
                                        </li>
                                    <?php endwhile; ?>
                                </ul>
                            </div>
                            
                        <?php endif; ?>
                        
                    </div>
                    <div class="col-md-5 col-sm-6">
                        <div class="selling_form">
                            <?php 
                             //Heading
                             if( $titles = get_sub_field( 'foirm_title' ) ):
                                echo '<span class="title d-block">'. $titles .'</span>';
                            endif;
                            ?>
                            <?php 
                                //Gravity Form
                                $form_object = get_sub_field( 'select_form' );
                                if( !empty( $form_object['id'] ) ):
                                    gravity_form_enqueue_scripts($form_object['id'], true);
                                    gravity_form($form_object['id'], false, false, false, '', true, 1);
                                endif;
                                ?>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- Contetn End -->