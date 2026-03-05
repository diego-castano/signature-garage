<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

get_header();
?>

<main id="main-content"  class=" defenders-listing">
    <?php
    if (have_posts()) :
        while (have_posts()) : the_post();
    ?>
        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			
			<?php if (has_post_thumbnail()) : ?>
                         <div class="defender-thumbnail" style="background: url('<?php echo esc_url(get_the_post_thumbnail_url()); ?>') no-repeat scroll center center/cover;">
							<div class="container wide">
								<span class="defender-title h1"><?php the_title(); ?></span>
							</div>
						</div>
                    <?php endif; ?>
			

            <div class="defender-content">
               <div class="container wide">
				    <?php
                // Display the content
                the_content();
                ?>
				</div>
            </div>
			
			<div class="defender-gallery">
				<?php if (have_rows('defender_gallery')) : ?>
					

					<div class="gallery-thumbnail">
						<div class="container wide">
							<?php $index = 0; ?>
							<?php while (have_rows('defender_gallery')) : the_row(); ?>
								<?php $gallery_thumbnail = get_sub_field('gallery_thumbnail'); ?>
								<?php if ($gallery_thumbnail) : ?>
									<div class="tab-thumbnail" data-index="<?php echo $index; ?>" style="display: none;">
										<img src="<?php echo esc_url($gallery_thumbnail['url']); ?>" alt="<?php echo esc_attr($gallery_thumbnail['alt']); ?>" />
									</div>
								<?php endif; ?>
								<?php $index++; ?>
							<?php endwhile; ?>
						</div>
					</div>
				
				<div class="gallery-tabs-title">
						<div class="container wide">
							<?php $index = 0; ?>
							<?php while (have_rows('defender_gallery')) : the_row(); ?>
								<?php $title = get_sub_field('title'); ?>
								<div class="tab-title" data-index="<?php echo $index; ?>">
									<?php echo esc_html($title); ?>
								</div>
								<?php $index++; ?>
							<?php endwhile; ?>
						</div>
					</div>

					<div class="gallery-tabs-gallery">
						<div class="container wide">
							<?php $index = 0; ?>
							<?php while (have_rows('defender_gallery')) : the_row(); ?>
								<?php $gallery_images = get_sub_field('gallery'); ?>
								<?php if ($gallery_images) : ?>
									<div class="tab-gallery" data-index="<?php echo $index; ?>" style="display: none;">
										<div class="row masonry">
											<?php foreach ($gallery_images as $gallery_image) : ?>
												<div class="col-md-4 col-sm-6 col-6 well">
													<a class="tabs-g-box image-box" href="<?php echo esc_url($gallery_image['url']); ?>">
														<img src="<?php echo esc_url($gallery_image['url']); ?>" alt="<?php echo esc_attr($gallery_image['alt']); ?>" />
														<i class="fas fa-plus"></i>
													</a>
												</div>
											<?php endforeach; ?>
										</div>
									</div>
								<?php endif; ?>
								<?php $index++; ?>
							<?php endwhile; ?>
						</div>
					</div>
				<?php endif; ?>
			</div>


        </article>


    <?php
        endwhile;
    else :
        echo '<p>No defenders found.</p>';
    endif;
    ?>
</main>



<?php get_footer(); ?>
