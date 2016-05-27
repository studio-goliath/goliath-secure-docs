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
 * Fonction call on plugin activation
 *
 *  - create secure document folder
 */
function goliath_secure_documents_activate() {

    $secure_doc_folder_path = goliath_secure_documents_get_docs_folder();

    mkdir( $secure_doc_folder_path, 0600 );

}
register_activation_hook( __FILE__, 'goliath_secure_documents_activate' );



function goliath_secure_documents_get_docs_folder(){

    $secure_doc_folder_path = dirname( ABSPATH );

    return $secure_doc_folder_path . '/goliath-secure-documents';

}