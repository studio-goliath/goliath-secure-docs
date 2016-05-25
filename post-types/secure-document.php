<?php

function secure_document_init() {

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
		'menu_name'           => __( 'Secure documents', 'goliath-secure-docs' ),
	) );

	$secure_doc_args = apply_filters( 'goliath-secure-docs-cpt-secure-document-params', array(
		'labels'            => $secure_doc_label,
		'public'            => false,
		'hierarchical'      => false,
		'show_ui'           => true,
		'show_in_nav_menus' => false,
		'supports'          => array( 'title', 'editor' ),
		'has_archive'       => false,
		'rewrite'           => false,
		'query_var'         => false,
		'menu_icon'         => 'dashicons-lock',
	) );

	register_post_type( 'secure-document', $secure_doc_args );

}
add_action( 'init', 'secure_document_init' );

function secure_document_updated_messages( $messages ) {
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
		date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( $permalink ) ),
		10 => sprintf( __('Secure document draft updated. <a target="_blank" href="%s">Preview Secure document</a>', 'goliath-secure-docs'), esc_url( add_query_arg( 'preview', 'true', $permalink ) ) ),
	);

	return $messages;
}
add_filter( 'post_updated_messages', 'secure_document_updated_messages' );
