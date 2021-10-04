<?php
/**
 * The template for displaying meta box in page/post
 *
 * This adds Select Sidebar, Header Featured Image Options, Single Page/Post Image
 * This is only for the design purpose and not used to save any content
 *
 * @package Top Blog
 */

/**
 * Register meta box(es).
 */
function top_blog_register_meta_boxes() {
    add_meta_box( 'top-blog-sidebar-options', esc_html__( 'Select Sidebar', 'top-blog' ), 'top_blog_display_sidebar_options', array( 'post', 'page' ), 'side' );

    add_meta_box( 'top-blog-header-image-options', esc_html__( 'Header Image', 'top-blog' ), 'top_blog_display_header_image_options', array( 'post', 'page' ), 'side' );

    add_meta_box( 'top-blog-featured-image-options', esc_html__( 'Featured Image', 'top-blog' ), 'top_blog_display_featured_image_options', array( 'post', 'page' ), 'side' );

    add_meta_box( 'top-blog-hide-content-options', esc_html__( 'Hide Content', 'top-blog' ), 'top_blog_display_hide_content_options', array( 'page' ), 'side' );
}
add_action( 'add_meta_boxes', 'top_blog_register_meta_boxes' );
 
/**
 * Meta box display callback.
 *
 * @param WP_Post $post Current post object.
 */
function top_blog_display_sidebar_options( $post ) {
	wp_nonce_field( basename( __FILE__ ), 'top_blog_custom_meta_box_nonce' );

	$sidebar_options = array(
		'default-sidebar'        => esc_html__( 'Default Sidebar', 'top-blog' ),
		'optional-sidebar-one'   => esc_html__( 'Optional Sidebar One', 'top-blog' ),
		'optional-sidebar-two'   => esc_html__( 'Optional Sidebar Two', 'top-blog' ),
		'optional-sidebar-three' => esc_html__( 'Optional Sidebar three', 'top-blog' ),
	);

	$meta_option = get_post_meta( $post->ID, 'top-blog-sidebar-option', true );

	if ( empty( $meta_option ) ){
		$meta_option = 'default-sidebar';
	}
	
	?>
	<select name="top-blog-sidebar-option"> 
		<?php
		foreach ( $sidebar_options as $field => $label ) {
		?>
			<option value="<?php echo esc_attr( $field ); ?>" <?php selected( $field, $meta_option ); ?>><?php echo esc_html( $label ); ?></option>
		<?php
		} // endforeach.
		?>
	</select>
	<?php
}

/**
 * Meta box display callback.
 *
 * @param WP_Post $post Current post object.
 */
function top_blog_display_header_image_options( $post ) {
	wp_nonce_field( basename( __FILE__ ), 'top_blog_custom_meta_box_nonce' );

	$header_image_options = array(
		'default' => esc_html__( 'Default', 'top-blog' ),
		'enable'  => esc_html__( 'Enable', 'top-blog' ),
		'disable' => esc_html__( 'Disable', 'top-blog' ),
	);

	$meta_option = get_post_meta( $post->ID, 'top-blog-header-image', true );

	if ( empty( $meta_option ) ){
		$meta_option = 'default';
	}	
	?>
	<p class="description"><?php esc_html_e( 'Will override Option from Appearance=> Custommize=> Header Media for this particular Page/Post if anything other than default is selected.', 'top-blog' ); ?></p>
	<?php
	foreach ( $header_image_options as $field => $label ) {
	?>
		<label>
			<input type="radio" name="top-blog-header-image" value="<?php echo esc_attr( $field ); ?>"<?php checked( $field === $meta_option ); ?> />
			<?php echo esc_html( $label ); ?>
		</label>
	<?php
	} // endforeach.
}

/**
 * Meta box display callback.
 *
 * @param WP_Post $post Current post object.
 */
function top_blog_display_featured_image_options( $post ) {
	wp_nonce_field( basename( __FILE__ ), 'top_blog_custom_meta_box_nonce' );

	$featured_image_options = array(
		'default'        => esc_html__( 'Default', 'top-blog' ),
		'disable'        => esc_html__( 'Disable', 'top-blog' ),
		'post-thumbnail' => esc_html__( 'Post Thumbnail (470x470)', 'top-blog' ),
		'top-blog-slider'   => esc_html__( 'Slider Image (1920x954)', 'top-blog' ),
		'full'           => esc_html__( 'Original Image Size', 'top-blog' ),
	);

	$meta_option = get_post_meta( $post->ID, 'top-blog-featured-image', true );

	if ( empty( $meta_option ) ){
		$meta_option = 'default';
	}
	
	?>
	<select name="top-blog-featured-image"> 
		<?php
		foreach ( $featured_image_options as $field => $label ) {
		?>
			<option value="<?php echo esc_attr( $field ); ?>" <?php selected( $field, $meta_option ); ?>><?php echo esc_html( $label ); ?></option>
		<?php
		} // endforeach.
		?>
	</select>
	<?php
}

/**
 * Meta box display callback.
 *
 * @param WP_Post $post Current post object.
 */
function top_blog_display_hide_content_options( $post ) {
	wp_nonce_field( basename( __FILE__ ), 'top_blog_custom_meta_box_nonce' );

	$meta_option = get_post_meta( $post->ID, 'top-blog-hide-content', true );
	?>
	<label>
	<input type="checkbox" value='1' name="top-blog-hide-content" <?php checked( 1, $meta_option ); ?>><?php esc_html_e( 'Check to hide current page\'s content', 'top-blog' ); ?>
	<?php
}
 
/**
 * Save meta box content.
 *
 * @param int $post_id Post ID
 */
function top_blog_save_meta_box( $post_id ) {
	global $post_type;

	$post_type_object = get_post_type_object( $post_type );

	if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )                      // Check Autosave
	|| ( ! isset( $_POST['post_ID'] ) || $post_id != $_POST['post_ID'] )        // Check Revision
	|| ( ! in_array( $post_type, array( 'page', 'post' ) ) )                  // Check if current post type is supported.
	|| ( ! check_admin_referer( basename( __FILE__ ), 'top_blog_custom_meta_box_nonce') )    // Check nonce - Security
	|| ( ! current_user_can( $post_type_object->cap->edit_post, $post_id ) ) )  // Check permission
	{
	  return $post_id;
	}

	$fields = array(
		'top-blog-header-image',
		'top-blog-sidebar-option',
		'top-blog-featured-image',
		'top-blog-hide-content',
	);

	foreach ( $fields as $field ) {
		$new = $_POST[ $field ];

		delete_post_meta( $post_id, $field );

		if ( '' == $new || array() == $new ) {
			return;
		} else {
			if ( ! update_post_meta ( $post_id, $field, sanitize_text_field( sanitize_key( $new ) ) ) ) {
				add_post_meta( $post_id, $field, sanitize_text_field( sanitize_key( $new ) ), true );
			}
		}
	} // end foreach
}
add_action( 'save_post', 'top_blog_save_meta_box' );
