<?php
/**
 * signaturecar functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package signaturecar
 */

if ( ! defined( '_S_VERSION' ) ) {
	// Replace the version number of the theme on each release.
	define( '_S_VERSION', '1.0.0' );
}

/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function signaturecar_setup() {
	/*
		* Make theme available for translation.
		* Translations can be filed in the /languages/ directory.
		* If you're building a theme based on signaturecar, use a find and replace
		* to change 'signaturecar' to the name of your theme in all the template files.
		*/
	load_theme_textdomain( 'signaturecar', get_template_directory() . '/languages' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	/*
		* Let WordPress manage the document title.
		* By adding theme support, we declare that this theme does not use a
		* hard-coded <title> tag in the document head, and expect WordPress to
		* provide it for us.
		*/
	add_theme_support( 'title-tag' );

	/*
		* Enable support for Post Thumbnails on posts and pages.
		*
		* @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		*/
	add_theme_support( 'post-thumbnails' );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus(
		array(
			'menu-1' => esc_html__( 'Primary', 'signaturecar' ),
		)
	);

	/*
		* Switch default core markup for search form, comment form, and comments
		* to output valid HTML5.
		*/
	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'style',
			'script',
		)
	);

	// Set up the WordPress core custom background feature.
	add_theme_support(
		'custom-background',
		apply_filters(
			'signaturecar_custom_background_args',
			array(
				'default-color' => 'ffffff',
				'default-image' => '',
			)
		)
	);

	// Add theme support for selective refresh for widgets.
	add_theme_support( 'customize-selective-refresh-widgets' );

	/**
	 * Add support for core custom logo.
	 *
	 * @link https://codex.wordpress.org/Theme_Logo
	 */
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 250,
			'width'       => 250,
			'flex-width'  => true,
			'flex-height' => true,
		)
	);
}
add_action( 'after_setup_theme', 'signaturecar_setup' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function signaturecar_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'signaturecar_content_width', 640 );
}
add_action( 'after_setup_theme', 'signaturecar_content_width', 0 );

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function signaturecar_widgets_init() {
	register_sidebar(
		array(
			'name'          => esc_html__( 'Sidebar', 'signaturecar' ),
			'id'            => 'sidebar-1',
			'description'   => esc_html__( 'Add widgets here.', 'signaturecar' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);
}
add_action( 'widgets_init', 'signaturecar_widgets_init' );

/**
 * Enqueue scripts and styles.
 */
function signaturecar_scripts() {
	wp_enqueue_style( 'signaturecar-style', get_stylesheet_uri(), array(), _S_VERSION );
	wp_style_add_data( 'signaturecar-style', 'rtl', 'replace' );

	wp_enqueue_style('bootstrap', get_template_directory_uri(). '/assets/vendors/bootstrap/css/bootstrap.min.css', false, false);
	wp_enqueue_style('fontawesome', get_template_directory_uri(). '/assets/vendors/font-awesome/css/all.min.css', false, false);
	wp_enqueue_style('magnific', 'https://cdnjs.cloudflare.com/ajax/libs/magnific-popup.js/1.1.0/magnific-popup.min.css', false, false);
	wp_enqueue_style('slick-theme', get_template_directory_uri(). '/assets/vendors/slick/slick-theme.css', false, false);
	wp_enqueue_style('slick', get_template_directory_uri(). '/assets/vendors/slick/slick.css', false, false);
	wp_enqueue_style('lity-css', get_template_directory_uri(). '/assets/vendors/lity/lity.min.css', false, false);
	wp_enqueue_style( 'fonts',  get_template_directory_uri() . '/assets/fonts/fonts.css', false, false );
    wp_enqueue_style( 'main-style',  get_template_directory_uri() . '/assets/css/style.css', false, false );

	//Js Files
    wp_enqueue_script( 'bootstrap-min', get_template_directory_uri() . '/assets/vendors/bootstrap/js/bootstrap.min.js', array('jquery'), false, true );
    wp_enqueue_script( 'slick-slider', get_template_directory_uri() . '/assets/vendors/slick/slick.min.js', array('jquery'), false, true );
    wp_enqueue_script( 'magnific', 'https://cdnjs.cloudflare.com/ajax/libs/magnific-popup.js/1.1.0/jquery.magnific-popup.min.js', array('jquery'), false, true );
	wp_enqueue_script( 'masonry', 'https://cdnjs.cloudflare.com/ajax/libs/masonry/4.2.2/masonry.pkgd.min.js', array('jquery'), false, true );
	wp_enqueue_script( 'lity-js', get_template_directory_uri() . '/assets/vendors/lity/lity.min.js', array('jquery'), false, true );
    wp_enqueue_script( 'main', get_template_directory_uri() . '/assets/js/main.js', array('jquery'), false, true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'signaturecar_scripts' );


add_filter('manage_post_posts_columns', 'misha_featured_image_column');
function misha_featured_image_column( $column_array ) {

    
    $column_array = array_slice( $column_array, 0, 1, true )
    + array('featured_image' => 'Featured Image') 
    + array_slice( $column_array, 1, NULL, true );

    return $column_array;
}


add_action('manage_posts_custom_column', 'misha_render_the_column', 10, 2);
function misha_render_the_column( $column_name, $post_id ) {

    if( $column_name == 'featured_image' ) {

        if( has_post_thumbnail( $post_id ) ) {

            $thumb_id = get_post_thumbnail_id( $post_id );
            echo '<img data-id="' . $thumb_id . '" src="' . wp_get_attachment_url( $thumb_id ) . '" />';

        } else {

            echo '<img data-id="-1" src="' . get_stylesheet_directory_uri() . '/placeholder.png" />';

        }

    }

}

add_action( 'admin_head', 'misha_custom_css' );
function misha_custom_css(){

    echo '<style>
        #featured_image{
            width:120px;
        }
        td.featured_image.column-featured_image img{
            max-width: 100%;
            height: auto;
        }

        /* some styles to make Quick Edit meny beautiful */
        #misha_featured_image .title{margin-top:10px;display:block;}
        #misha_featured_image a.misha_upload_featured_image{
            display:inline-block;
            margin:10px 0 0;
        }
        #misha_featured_image img{
            display:block;
            max-width:200px !important;
            height:auto;
        }
        #misha_featured_image .misha_remove_featured_image{
            display:none;
        }
    </style>';

}

add_action( 'admin_enqueue_scripts', 'misha_include_myuploadscript' );
function misha_include_myuploadscript() {
    if ( ! did_action( 'wp_enqueue_media' ) ) {
        wp_enqueue_media();
    }
}

add_action('quick_edit_custom_box',  'misha_add_featured_image_quick_edit', 10, 2);
function misha_add_featured_image_quick_edit( $column_name, $post_type ) {

    if ($column_name != 'featured_image') return;

    // we add #misha_featured_image to use it in JavaScript in CSS
    echo '<fieldset id="misha_featured_image" class="inline-edit-col-left">
        <div class="inline-edit-col">
            <span class="title">Featured Image</span>
            <div>
                <a href="#" class="misha_upload_featured_image">Set featured image</a>
                <input type="hidden" name="_thumbnail_id" value="" />
                <a href="#" class="misha_remove_featured_image">Remove Featured Image</a>
            </div>
        </div></fieldset>';


}

add_action('admin_footer', 'misha_quick_edit_js_update');
function misha_quick_edit_js_update() {

    global $current_screen;

    if (($current_screen->id != 'edit-post') || ($current_screen->post_type != 'post'))
        return;

        ?><script>
        jQuery(function($){

            $('body').on('click', '.misha_upload_featured_image', function(e){
                e.preventDefault();
                var button = $(this),
                 custom_uploader = wp.media({
                    title: 'Set featured image',
                    library : { type : 'image' },
                    button: { text: 'Set featured image' },
                }).on('select', function() {
                    var attachment = custom_uploader.state().get('selection').first().toJSON();
                    $(button).html('<img src="' + attachment.url + '" />').next().val(attachment.id).parent().next().show();
                }).open();
            });

            $('body').on('click', '.misha_remove_featured_image', function(){
                $(this).hide().prev().val('-1').prev().html('Set featured Image');
                return false;
            });

            var $wp_inline_edit = inlineEditPost.edit;
            inlineEditPost.edit = function( id ) {
                $wp_inline_edit.apply( this, arguments );
                var $post_id = 0;
                if ( typeof( id ) == 'object' ) { 
                    $post_id = parseInt( this.getId( id ) );
                }

                if ( $post_id > 0 ) {
                    var $edit_row = $( '#edit-' + $post_id ),
                            $post_row = $( '#post-' + $post_id ),
                            $featured_image = $( '.column-featured_image', $post_row ).html(),
                            $featured_image_id = $( '.column-featured_image', $post_row ).find('img').attr('data-id');


                    if( $featured_image_id != -1 ) {

                        $( ':input[name="_thumbnail_id"]', $edit_row ).val( $featured_image_id ); // ID
                        $( '.misha_upload_featured_image', $edit_row ).html( $featured_image ); // image HTML
                        $( '.misha_remove_featured_image', $edit_row ).show(); // the remove link

                    }
                }
        }
    });
    </script>
<?php
}

/**
 * Implement the Custom Header feature.
 */
require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Functions which enhance the theme by hooking into WordPress.
 */
require get_template_directory() . '/inc/template-functions.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
if ( defined( 'JETPACK__VERSION' ) ) {
	require get_template_directory() . '/inc/jetpack.php';
}

/**
 * Load ACF new fields.
 */
require get_template_directory() . '/inc/acf-fields-extends/gravity-forms-acf-field/acf-gravity_forms.php';

add_filter( 'gform_submit_button', 'form_submit_button', 10, 2 );
function form_submit_button( $button, $form ) {
    return "<div class='text-sm-end text-center' style='width:100%'><button class='cta-btn btn_red' id='gform_submit_button_{$form['id']}'>{$form['button']['text']}</button></div>";
}

add_action( 'init', 'cp_change_post_object' );
// Change dashboard Posts to News
function cp_change_post_object() {
    $get_post_type = get_post_type_object('post');
    $labels = $get_post_type->labels;
        $labels->name = 'Autos';
        $labels->singular_name = 'Autos';
        $labels->add_new = 'Add Autos';
        $labels->add_new_item = 'Add Autos';
        $labels->edit_item = 'Edit Autos';
        $labels->new_item = 'Autos';
        $labels->view_item = 'View Autos';
        $labels->search_items = 'Search Autos';
        $labels->not_found = 'No News found';
        $labels->not_found_in_trash = 'No Autos found in Trash';
        $labels->all_items = 'All Autos';
        $labels->menu_name = 'Autos';
        $labels->name_admin_bar = 'Autos';
}


function my_custom_login_stylesheet() {
	wp_enqueue_style( 'custom-login', get_stylesheet_directory_uri() . '/assets/css/login.css' );
}

add_action( 'login_enqueue_scripts', 'my_custom_login_stylesheet' );

add_filter( 'login_headerurl', 'codecanal_loginlogo_url' );
function codecanal_loginlogo_url($url)
{
  return 'http://singature-garage.com/';
}


add_action( 'gform_after_submission', 'send_whatsapp_message_specific_form', 10, 2 );
function send_whatsapp_message_specific_form( $entry, $form ) {
    // Define the ID of the specific form you want to target
    $target_form_id = 1; // Change 123 to the ID of your specific form

    // Check if the submitted form matches the target form
    if ( $form['id'] != $target_form_id ) {
        return;
    }

    // WhatsApp number
    $phone_number = "598095142020";

    // Retrieve form field values excluding the field with ID input_1_6
    $field_values = array();
    foreach ( $form['fields'] as $field ) {
        // Exclude the field with ID input_1_6
        if ( $field['id'] != 6 ) {
            $value = rgar( $entry, $field['id'] );
            if ( ! empty( $value ) ) {
                $field_values[] = urlencode( $field['label'] . ": " . $value );
            }
        }
    }

    // Construct WhatsApp message
    $message = "Quiero que Signature Garage me ayude a vender mi vehículo. Información:";
    if ( ! empty( $field_values ) ) {
        $message .= "%0A" . implode( "%0A", $field_values );
    }

    // Construct WhatsApp URL
    $whatsapp_url = "https://wa.me/{$phone_number}?text={$message}";

    // Open WhatsApp with the pre-filled message
    echo "<script>window.open('{$whatsapp_url}', '_blank');</script>";
}

add_filter( 'acf/admin/prevent_escaped_html_notice', '__return_true' );


function disable_post_types_order_on_random_page($ignore, $query) {
    // Check if we're on a specific page (use the page ID or slug)
    if ( is_page('inventario') || is_page(123) ) {
        // Disable Post Types Order plugin
        return true;
    }
    return $ignore;
}
add_filter('pto/posts_orderby/ignore', 'disable_post_types_order_on_random_page', 10, 2);




// Register Custom Post Type: Defenders
function defenders_custom_post_type() {
    $labels = array(
        'name'                  => _x('Defenders', 'Post Type General Name', 'text_domain'),
        'singular_name'         => _x('Defender', 'Post Type Singular Name', 'text_domain'),
        'menu_name'             => __('Defenders', 'text_domain'),
        'name_admin_bar'        => __('Defender', 'text_domain'),
        'archives'              => __('Defender Archives', 'text_domain'),
        'attributes'            => __('Defender Attributes', 'text_domain'),
        'parent_item_colon'     => __('Parent Defender:', 'text_domain'),
        'all_items'             => __('All Defenders', 'text_domain'),
        'add_new_item'          => __('Add New Defender', 'text_domain'),
        'add_new'               => __('Add New', 'text_domain'),
        'new_item'              => __('New Defender', 'text_domain'),
        'edit_item'             => __('Edit Defender', 'text_domain'),
        'update_item'           => __('Update Defender', 'text_domain'),
        'view_item'             => __('View Defender', 'text_domain'),
        'view_items'            => __('View Defenders', 'text_domain'),
        'search_items'          => __('Search Defender', 'text_domain'),
        'not_found'             => __('Not found', 'text_domain'),
        'not_found_in_trash'    => __('Not found in Trash', 'text_domain'),
        'featured_image'        => __('Featured Image', 'text_domain'),
        'set_featured_image'    => __('Set featured image', 'text_domain'),
        'remove_featured_image' => __('Remove featured image', 'text_domain'),
        'use_featured_image'    => __('Use as featured image', 'text_domain'),
        'insert_into_item'      => __('Insert into defender', 'text_domain'),
        'uploaded_to_this_item' => __('Uploaded to this defender', 'text_domain'),
        'items_list'            => __('Defenders list', 'text_domain'),
        'items_list_navigation' => __('Defenders list navigation', 'text_domain'),
        'filter_items_list'     => __('Filter defenders list', 'text_domain'),
    );

    $args = array(
        'label'                 => __('Defender', 'text_domain'),
        'description'           => __('Post Type for Defenders', 'text_domain'),
        'labels'                => $labels,
        'supports'              => array('title', 'editor', 'excerpt', 'thumbnail', 'revisions', 'custom-fields'),
        'taxonomies'            => array('category', 'post_tag'),
        'hierarchical'          => false,
        'public'                => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 5,
        'menu_icon'             => 'dashicons-shield', // Choose a dashicon for the menu icon
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => true,
        'can_export'            => true,
        'has_archive'           => true,
        'exclude_from_search'   => false,
        'publicly_queryable'    => true,
        'capability_type'       => 'post',
        'show_in_rest'          => true, // Enable Gutenberg support
    );

    register_post_type('defenders', $args);
}
add_action('init', 'defenders_custom_post_type', 0);



add_action('wp_ajax_filter_posts', 'filter_posts');
add_action('wp_ajax_nopriv_filter_posts', 'filter_posts');

function filter_posts() {
    $search_term = isset($_POST['search_term']) ? sanitize_text_field($_POST['search_term']) : '';

    $args = array(
        'post_type' => 'post',
        's' => $search_term,
        'posts_per_page' => -1,
    );

    $query = new WP_Query($args);
    if ($query->have_posts()) :
        while ($query->have_posts()) : $query->the_post();
            $informations = get_field('informations');
            ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class('col-md-4 col-sm-6 ssCar'); ?>
                data-year="<?php echo esc_attr($informations['year']); ?>"
                data-make="<?php echo esc_attr($informations['make']); ?>"
                data-model="<?php echo esc_attr($informations['model']); ?>"
                data-sold="<?php echo esc_attr(get_field('sold')); ?>">
                <div class="box box-style-1">
                    <span class="title text-center d-block font_gillsans"><?php the_title(); ?></span>
                    <?php if ( has_post_thumbnail() ) : ?>
                        <a href="<?php the_permalink(); ?>" class="post-thumbnail-link alt" style="background-image: url('<?php echo esc_url( get_the_post_thumbnail_url( $post->ID, 'full' ) ); ?>');">
                            <?php if ( get_field('no_residentes') ) : ?>
                                <span class="sgu-nores-badge">&#x1F1FA;&#x1F1F8; NO RESIDENTES</span>
                            <?php endif; ?>
                        </a>
                    <?php endif; ?>
                    <div class="details">
                        <?php if( $informations ) : ?>
                            <div class="box-meta d-flex align-items-center justify-content-between">
                                <div><div class="year font_oswald"><span class="d-block">AÑO</span><?php echo esc_html( $informations['year'] ); ?></div></div>
                                <div><div class="year font_oswald"><span class="d-block">kilómetros</span><?php echo esc_html( $informations['kilometers'] ); ?></div></div>
                            </div>
                        <?php endif; ?>
                        <div class="box-btns font_oswald d-flex align-items-center justify-content-between">
                            <?php if( 'v1' == get_field( 'sold' ) ) : ?>
                                <span class="price">VENDIDO</span>
                            <?php else : ?>
                                <span class="price"><?php the_field( 'price' ); ?></span>
                            <?php endif; ?>
                            <a href="<?php the_permalink(); ?>" class="cta-btn btn_red">Ver detalles</a>
                        </div>
                        <div class="box-bottom-meta d-flex align-items-center justify-content-center">
                            <a target="_blank" href="https://wa.me/598094300100?text=Quiero%20m%C3%A1s%20informaci%C3%B3n%20sobre%20%C3%A9ste%20veh%C3%ADculo%20de%20Signature%20Garage%3A<?php the_permalink(); ?>" class="save_action d-flex align-items-center"><em class="fab fa-whatsapp"></em>Enviar a WhatsApp</a>
                        </div>
                    </div>
                </div>
            </article>
            <?php
        endwhile;
        wp_reset_postdata();
    else :
        echo '<p class="no_cars">No results found.</p>';
    endif;

    wp_die();
}

