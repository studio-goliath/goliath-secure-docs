<?php

class Goliath_Secure_Documents_Test extends WP_UnitTestCase {


	/**
	 * Test if the post type "secure-document" exist
	 */
	function test_custom_post_type_creation()
	{
		$this->assertTrue( post_type_exists( 'secure-document' ) );
	}

	function test_goliath_secure_documents_folder_existe()
	{

		$secure_doc_folder_path = goliath_secure_documents_get_docs_folder();

		$this->assertTrue( is_dir( $secure_doc_folder_path ) );
	}
}

