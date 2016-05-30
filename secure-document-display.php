<?php


// check if user is logged in
if ( !is_user_logged_in() ) {

    auth_redirect();

}


$secure_doc_name = get_query_var( 'secure_doc_name' );

$doc_path = goliath_secure_documents_get_docs_folder() . '/' . $secure_doc_name;

if( is_file( $doc_path ) ){

    $secure_doc = file_get_contents( $doc_path );


    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $file_mime_type = finfo_file($finfo, $secure_doc);

    header ("Content-Type: {$file_mime_type}");

    echo $secure_doc;

}
