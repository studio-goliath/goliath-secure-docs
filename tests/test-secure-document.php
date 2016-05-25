<?php

class Goliath_Secure_Documents_Test extends WP_UnitTestCase {


	/**
	 * Test if the post type "secure-document" exist
	 */
	function test_custom_post_type_creation()
	{
		$this->assertTrue( post_type_exists( 'secure-document' ) );
	}
}

