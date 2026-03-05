<section class="content-area pt_200 ">
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
		<?php if ( have_rows( 'videos' ) ) : ?>
		<div class="row">
			<?php while ( have_rows( 'videos' ) ) : the_row(); ?>
				<div class="col-sm-4">
					<?php 
					if( $video_url = get_sub_field( 'video_url' ) ): ?>
					<a data-lity href="<?php echo $video_url; ?>" class="video-gallery">
					<?php else: ?>
					<div class="video-gallery">
					<?php endif;
					?>
				 	
						<?php $thumbnail = get_sub_field( 'thumbnail' ); ?>
						<?php if ( $thumbnail ) : ?>
						<img src="<?php echo esc_url( $thumbnail['url'] ); ?>" alt="<?php echo esc_attr( $thumbnail['alt'] ); ?>" />
						<?php endif; ?>
						<?php 
						if( $video_url ):
						echo '<span class="play_btn"><em class="fa fa-play"></em></span>';
						endif;
						?>
					<?php 
					if( $video_url = get_sub_field( 'video_url' ) ): ?>
					</a>
					<?php else: ?>
					</div>
					<?php endif;
					?>
				</div>
			<?php endwhile; ?>
		</div>
		<?php endif; ?>
	</div>
</section>