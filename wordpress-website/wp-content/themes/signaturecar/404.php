<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 *
 * @package signaturecar
 */

get_header();
?>

	<main id="primary" class="site-main">

		<section class="error-404 not-found text-center">
			<div class="container">
				<img src="https://wpxstudios.dev/signature/wp-content/uploads/2022/11/sellcars.png" alt="" />
				
				<h1 class="page-title"><?php esc_html_e( 'Oops! That page can&rsquo;t be found.', 'signaturecar' ); ?></h1>
				<p><?php esc_html_e( 'It looks like nothing was found at this location.', 'signaturecar' ); ?></p>
				<a href="/" class="cta-btn btn_red">Back to Homepage</a>
			</div>
		</section><!-- .error-404 -->

	</main><!-- #main -->

<?php
get_footer();
