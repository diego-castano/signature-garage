<?php
get_header();
?>

<main id="main-content" class=" defenders-listing">

    <?php if (have_posts()) : ?>
        <div class="defenders-archive">
            <?php while (have_posts()) : the_post(); ?>
                <article id="post-<?php the_ID(); ?>" <?php post_class('defender-item'); ?>>
                    <a href="<?php the_permalink(); ?>" class="defender-permalink"> </a>
                    <?php if (has_post_thumbnail()) : ?>
                        <div class="defender-thumbnail" style="background: url('<?php echo esc_url(get_the_post_thumbnail_url()); ?>') no-repeat scroll center center/cover;">
							<div class="container wide">
								<span class="defender-title h1"><?php the_title(); ?></span>
							</div>
						</div>

                    <?php endif; ?>
                </article>
            <?php endwhile; ?>
        </div>

        <?php
        // Pagination
        the_posts_pagination();
        ?>
    <?php else : ?>
        <p>No defenders found.</p>
    <?php endif; ?>
</main>

<?php get_footer(); ?>
