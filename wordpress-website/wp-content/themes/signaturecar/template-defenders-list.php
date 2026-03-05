<?php
/*
Template Name: Defenders List
*/

get_header();
?>

<main id="main-content">
    <header class="page-header">
        <h1 class="page-title">Our Defenders</h1>
    </header>

    <?php
    // Custom query to fetch all 'defenders' posts
    $args = array(
        'post_type'      => 'defenders',
        'posts_per_page' => -1, // Show all defenders
        'orderby'        => 'date',
        'order'          => 'DESC',
    );
    $defenders_query = new WP_Query($args);

    if ($defenders_query->have_posts()) :
    ?>
        <div class="defenders-list">
            <?php
            while ($defenders_query->have_posts()) : $defenders_query->the_post();
            ?>
                <article id="post-<?php the_ID(); ?>" <?php post_class('defender-item'); ?>>
                    <h2 class="defender-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>

                    <?php if (has_post_thumbnail()) : ?>
                        <div class="defender-thumbnail">
                            <a href="<?php the_permalink(); ?>">
                                <?php the_post_thumbnail('medium'); ?>
                            </a>
                        </div>
                    <?php endif; ?>

                    <div class="defender-excerpt">
                        <?php the_excerpt(); ?>
                    </div>

                    <a href="<?php the_permalink(); ?>" class="read-more">Read More</a>
                </article>
            <?php
            endwhile;
            ?>
        </div>

    <?php
    else :
        echo '<p>No defenders found.</p>';
    endif;

    // Reset post data
    wp_reset_postdata();
    ?>
</main>

<?php get_footer(); ?>
