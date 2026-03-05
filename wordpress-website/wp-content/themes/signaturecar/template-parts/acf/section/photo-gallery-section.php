<?php if( 'v2' == get_sub_field( 'style' ) ): ?>
<section class="photo-gallery-area pt-0">
<div class="container">
	<div class="text-center">
			<?php 
			//Heading
			if( $heading = get_sub_field( 'heading' ) ):
			echo '<h2 class="h2 color_white letter_space">'. $heading .'</h2>';
			endif;
			?>
			<?php 
			//Heading
			if( $headingSub = get_sub_field( 'sub_heading' ) ):
			echo '<h3 class="h4 color_white letter_space">'. $headingSub .'</h3>';
			endif;
			?>
		</div>
	<?php $images_images = get_sub_field( 'images' ); ?>
			<?php if ( $images_images ) :  ?>
							<div class="details-photos gallerym">
									<div class="gallery-slider">
						
	<?php foreach ( $images_images as $images_image ): ?>
												<div class="slide">
													<a href="<?php echo esc_url( $images_image['url'] ); ?>" class="image-box ">
													<div class="img-box-inner" style="background: url(<?php echo esc_url( $images_image['sizes']['large'] ); ?>) no-repeat center / cover;"><i class="fas fa-plus"></i></div>
													</a>
										</div>
	<?php endforeach; ?>
										
									</div>
								</div>										
<?php endif; ?>	
	</div>
</section>
<?php else: ?>
<section class="photo-gallery-area pt-0">
<div class="container">
	<div class="text-center">
			<?php 
			//Heading
			if( $heading = get_sub_field( 'heading' ) ):
			echo '<h2 class="h2 color_white letter_space">'. $heading .'</h2>';
			endif;
			?>
			<?php 
			//Heading
			if( $headingSub = get_sub_field( 'sub_heading' ) ):
			echo '<h3 class="h4 color_white letter_space">'. $headingSub .'</h3>';
			endif;
			?>
		</div>
	<?php $images_images = get_sub_field( 'images' ); ?>
			<?php if ( $images_images ) :  ?>
							<div class="details-photos gallerym">
									<div class="row">
						
	<?php foreach ( $images_images as $images_image ): ?>
												<a href="<?php echo esc_url( $images_image['url'] ); ?>" class="image-box col-sm-4">
													<div class="img-box-inner" style="background: url(<?php echo esc_url( $images_image['sizes']['large'] ); ?>) no-repeat center / cover;"><i class="fas fa-plus"></i></div>
													</a>
	<?php endforeach; ?>
										
									</div>
								</div>										
<?php endif; ?>	
	</div>
</section>
<?php endif; ?>

