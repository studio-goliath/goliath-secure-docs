<?php
/**
 * Show secure doc
 *
 */

global $wpdb;

// check if user is logged in
if ( !is_user_logged_in() ) {

    auth_redirect();

}


$secure_doc_name = get_query_var( 'secure_doc_name' );

$doc_path = goliath_secure_documents_get_docs_folder() . '/' . $secure_doc_name;

if( is_file( $doc_path ) ){

    $secure_doc = file_get_contents( $doc_path );

    $file_mime_type = $wpdb->get_var(
        "
        SELECT meta_value 
        FROM $wpdb->postmeta 
        WHERE  meta_key = '_secure_doc_mime_type'
        AND post_id = ( SELECT post_id FROM $wpdb->postmeta WHERE meta_value = '$secure_doc_name' )
        "
    );

    header ("Content-Type: {$file_mime_type}");
    header('Content-Disposition: inline; filename="'. $secure_doc_name .'"');

    echo $secure_doc;

}
