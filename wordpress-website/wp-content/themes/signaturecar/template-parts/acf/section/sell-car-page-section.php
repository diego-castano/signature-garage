<!-- Content Start -->
<section class="content-area pt_200">
            <div class="container">
                <div class="sell_box">
                    <?php 
                        //Gravity Form
                        $form_object = get_sub_field( 'sell_page_form' );
                        if( !empty( $form_object['id'] ) ):
                            gravity_form_enqueue_scripts($form_object['id'], true);
                            gravity_form($form_object['id'], false, false, false, '', true, 1);
                        endif;
                        ?>
                    <div class="row">
                        <div class="col-md-9 mx-auto">
                            <div class="sell_inner">
                                <?php 
                                //Heading
                                if( $headingone = get_sub_field( 'heading_one' ) ):
                                    echo '<span class="title d-block font_oswald text-center">'. $headingone .'</span>';
                                endif;
                                ?>
                                <div class="content-text">
                                <?php 
                                //Heading
                                if( $headingtwo = get_sub_field( 'heading_two' ) ):
                                    echo '<h3 class="title d-block font_oswald">'. $headingtwo .'</h3>';
                                endif;

                                if( $subheading = get_sub_field( 'sub_heading' ) ):
                                    echo '<h3 class="h4 title d-block font_oswald ">'. $subheading .'</h3>';
                                endif;
                                ?>
                                     <div class="font_gillsans">
                                     <?php 
                                        //Description
                                        the_sub_field( 'content' );
                                        ?>
                                     </div>
                                </div>
                            </div>
                            <?php 
                                //Heading
                                if( $video = get_sub_field( 'video' ) ):
                                    echo '<div class="embed-container">'. $video .'</div>';
                                endif;
                                ?>
                        </div>
                    </div>
                </div>

            </div>
        </section>
        <!-- Content End -->
