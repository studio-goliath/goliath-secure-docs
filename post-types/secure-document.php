<?php
/**
 * "secure-document" custom post type
 *
 */


add_action( 'init', 'secure_document_init' );

/**
 *
 * "secure-document" custom post type registration
 *
 */
function secure_document_init()
{

	$secure_doc_label = apply_filters( 'goliath-secure-docs-cpt-secure-document-label', array(
		'name'                => __( 'Secure documents', 'goliath-secure-docs' ),
		'singular_name'       => __( 'Secure document', 'goliath-secure-docs' ),
		'all_items'           => __( 'All Secure documents', 'goliath-secure-docs' ),
		'new_item'            => __( 'New Secure document', 'goliath-secure-docs' ),
		'add_new'             => __( 'Add New', 'goliath-secure-docs' ),
		'add_new_item'        => __( 'Add New Secure document', 'goliath-secure-docs' ),
		'edit_item'           => __( 'Edit Secure document', 'goliath-secure-docs' ),
		'view_item'           => __( 'View Secure document', 'goliath-secure-docs' ),
		'search_items'        => __( 'Search Secure documents', 'goliath-secure-docs' ),
		'not_found'           => __( 'No Secure documents found', 'goliath-secure-docs' ),
		'not_found_in_trash'  => __( 'No Secure documents found in trash', 'goliath-secure-docs' ),
		'parent_item_colon'   => __( 'Parent Secure document', 'goliath-secure-docs' ),
		'menu_name'           => __( 'Secure docs', 'goliath-secure-docs' ),
	) );

	$secure_doc_args = apply_filters( 'goliath-secure-docs-cpt-secure-document-params', array(
		'labels'                => $secure_doc_label,
		'public'                => false,
		'hierarchical'          => false,
		'show_ui'               => true,
		'show_in_nav_menus'     => false,
		'supports'              => array( 'title', 'editor' ),
		'has_archive'           => false,
		'rewrite'               => false,
		'query_var'             => false,
		'register_meta_box_cb'  => 'secure_document_register_file_meta_box',
		'menu_icon'             => 'dashicons-lock',
	) );

	register_post_type( 'secure-document', $secure_doc_args );

}


function secure_document_updated_messages( $messages )
{
	global $post;

	$permalink = get_permalink( $post );

	$messages['secure-document'] = array(
		0 => '', // Unused. Messages start at index 1.
		1 => sprintf( __('Secure document updated. <a target="_blank" href="%s">View Secure document</a>', 'goliath-secure-docs'), esc_url( $permalink ) ),
		2 => __('Custom field updated.', 'goliath-secure-docs'),
		3 => __('Custom field deleted.', 'goliath-secure-docs'),
		4 => __('Secure document updated.', 'goliath-secure-docs'),
		/* translators: %s: date and time of the revision */
		5 => isset($_GET['revision']) ? sprintf( __('Secure document restored to revision from %s', 'goliath-secure-docs'), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
		6 => sprintf( __('Secure document published. <a href="%s">View Secure document</a>', 'goliath-secure-docs'), esc_url( $permalink ) ),
		7 => __('Secure document saved.', 'goliath-secure-docs'),
		8 => sprintf( __('Secure document submitted. <a target="_blank" href="%s">Preview Secure document</a>', 'goliath-secure-docs'), esc_url( add_query_arg( 'preview', 'true', $permalink ) ) ),
		9 => sprintf( __('Secure document scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview Secure document</a>', 'goliath-secure-docs'),
		// translators: Publish box date format, see http://php.net/date
		date_i18n( __( 'M j, Y @ G:i', 'goliath-secure-docs' ), strtotime( $post->post_date ) ), esc_url( $permalink ) ),
		10 => sprintf( __('Secure document draft updated. <a target="_blank" href="%s">Preview Secure document</a>', 'goliath-secure-docs'), esc_url( add_query_arg( 'preview', 'true', $permalink ) ) ),
	);

	return $messages;
}
add_filter( 'post_updated_messages', 'secure_document_updated_messages' );


/**
 * Register the upload file metabox
 *
 */
function secure_document_register_file_meta_box()
{
	add_meta_box( 'secure_document_file_meta_box', __( 'Secure file', 'goliath-secure-docs' ) , 'secure_document_file_meta_box_content', 'secure-document' );
}


/**
 * Display the upload file metabox
 *
 */
function secure_document_file_meta_box_content( $post )
{
	// Display current file
	$secure_doc_path_meta = get_post_meta( $post->ID, '_secure_doc_path', true );
	$secure_doc_mime_meta = get_post_meta( $post->ID, '_secure_doc_mime_type', true );

	$required_option = get_option( '_secure_doc_field_required', true );

	$required_attribut = $required_option && ! $secure_doc_path_meta ? 'required="required"' : '';

	if( 'application/zip' == $secure_doc_mime_meta ){
		$dashicons = 'dashicons-media-archive';

	} else if ( strpos( $secure_doc_mime_meta, 'text') === 0 ||
	            strpos( $secure_doc_mime_meta, 'application' ) === 0 ){
		$dashicons = 'dashicons-media-document';

	} else if ( strpos( $secure_doc_mime_meta, 'image') === 0 ){
		$dashicons = 'dashicons-media-interactive';

	} else {
		$dashicons = 'dashicons-media-default';

	}

	wp_nonce_field( 'secure_document_nonce', 'secure_document_nonce_name');
	?>
	<p class="dashicons-before <?php echo $dashicons; ?>">
		<a href="<?php echo goliath_secure_documents_get_doc_url( $secure_doc_path_meta ); ?>"><?php echo $secure_doc_path_meta; ?></a>
	</p>

	<input type="file" name="goliath_secure_doc_file" <?php echo $required_attribut ?>/>
	<?php
}


add_action( 'save_post_secure-document', 'goliath_secure_documents_save_file_and_meta');

function goliath_secure_documents_save_file_and_meta( $post_id )
{
	if( isset( $_FILES['goliath_secure_doc_file'] ) && check_admin_referer( 'secure_document_nonce', 'secure_document_nonce_name' ) ){

		$secure_doc_file = $_FILES['goliath_secure_doc_file'];

		// Check if wee have a file and if it is send via HTTP POST
		if (is_uploaded_file( $secure_doc_file['tmp_name'] ) ) {

			// The file new path
			$secure_doc_path = goliath_secure_documents_get_docs_folder() . '/' . $secure_doc_file['name'];

			// check if there is no file with the same name already
			if( is_file( $secure_doc_path ) ){

				// get extension
				preg_match('/\.[^\.]+$/i', $secure_doc_path, $ext) ;

				// rename the file - NAME w/out .ext+time()+.ext
				$secure_doc_path = substr( $secure_doc_path,0 ,-(strlen($ext[0]))).'_'.time().$ext[0];
			}

			// Save the file in the write place
			$file_is_move = move_uploaded_file( $secure_doc_file['tmp_name'], $secure_doc_path );

			// Update post meta
			if( $file_is_move ){

				update_post_meta( $post_id, '_secure_doc_path', basename( $secure_doc_path ) );
				update_post_meta( $post_id, '_secure_doc_mime_type', $secure_doc_file['type'] );
			}
		}
	}
}


add_action( 'post_edit_form_tag' , 'goliath_secure_doc_post_edit_form_tag' );

/**
 *
 * @param WP_Post $post
 */
function goliath_secure_doc_post_edit_form_tag( $post )
{
	if( 'secure-document' == $post->post_type ){

		echo ' enctype="multipart/form-data"';
	}
}


add_filter('manage_secure-document_posts_columns', 'goliath_secure_doc_column');

/**
 * Add the "File" column on the "Secure documents" admin page
 *
 * @param array $defaults
 *
 * @return array
 */
function goliath_secure_doc_column( $defaults ) {
	$defaults['secure_doc_link']  = 'File';
	return $defaults;
}


add_action( 'manage_secure-document_posts_custom_column', 'goliath_secure_doc_column_content', 10, 2 );

/**
 * Dis play the link to secure documents in the "File" column on the "Secure documents" admin page
 *
 * @param $column_name
 * @param $post_id
 */
function goliath_secure_doc_column_content( $column_name, $post_id ) {

	if ($column_name == 'secure_doc_link') {
		$secure_doc_path_meta = get_post_meta( $post_id, '_secure_doc_path', true );
		echo '<a href="'. goliath_secure_documents_get_doc_url( $secure_doc_path_meta ) .'">'. $secure_doc_path_meta . '</a>';
	}

}
