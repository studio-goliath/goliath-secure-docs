<?php
/**
 * Plugin Name: Goliath secure docs
 * Version: 0.1-alpha
 * Description: goliath-secure-docs
 * Author: Studio Goliath
 * Author URI: http://wwww.studio-goliath.com
 *
 * Text Domain: goliath-secure-docs
 * Domain Path: /languages
 *
 * @package Goliath secure docs
 */



// Add custom post types
require_once plugin_dir_path( __FILE__ ) . 'post-types/secure-document.php';


/**
 * Function call on plugin activation
 *
 *  - create secure document folder
 */
function goliath_secure_documents_activate() {

    $secure_doc_folder_path = goliath_secure_documents_get_docs_folder();

    if( ! is_dir( $secure_doc_folder_path ) ){
        mkdir( $secure_doc_folder_path, 0644 );
    }

}
register_activation_hook( __FILE__, 'goliath_secure_documents_activate' );


/**
 * function to get the secure documents folder
 *
 * @return string
 */
function goliath_secure_documents_get_docs_folder(){

    $secure_doc_folder_path = dirname( ABSPATH );

    return $secure_doc_folder_path . '/goliath-secure-documents';

}


/**
 * function to get the slug of the secure documents url
 *
 * @return string
 */
function goliath_secure_documents_get_doc_slug()
{

    return apply_filters( 'goliath_secure_documents_get_doc_slug', 'secure-doc');
}


/**
 * @param $secure_file_name
 *
 * @return string
 */
function goliath_secure_documents_get_doc_url( $secure_file_name )
{
    return site_url( goliath_secure_documents_get_doc_slug() . '/' . $secure_file_name );
}


add_action('init', 'goliath_secure_docs_rewrite_rules' );

/**
 * Add new rewrite rules for the secure documents
 */
function goliath_secure_docs_rewrite_rules() {

    $secure_doc_slug = goliath_secure_documents_get_doc_slug();

    add_rewrite_rule("^{$secure_doc_slug}/(.+?)/?$", 'index.php?secure_doc_name=$matches[1]', 'top');

}


add_filter('query_vars', 'add_goliath_secure_docs_query_var' );

function add_goliath_secure_docs_query_var($public_query_vars) {
    $public_query_vars[] = 'secure_doc_name';
    return $public_query_vars;
}


add_filter( 'template_include', 'goliath_secure_docs_redirect_template', 99 );

function goliath_secure_docs_redirect_template( $template ) {

    $secure_doc_name = get_query_var( 'secure_doc_name' );

    if ( $secure_doc_name ) {

        $new_template = plugin_dir_path( __FILE__ ) . 'secure-document-display.php';

        return $new_template ;


    }

    return $template;
}