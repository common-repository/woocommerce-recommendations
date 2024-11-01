<?php
/* This software is distributed under the terms of GNU GENERAL PUBLIC LICENSE Version 3, 29 June 2007 */
/**
 * WooCommerce Account setting manager
 *
 * Table of content
 * nt_render_ntoklo_js_link()
 * nt_render_event_posting_key()
 * nt_event_posting_key()
 *
 */
 class Event_Post{

 	/**
	 * Constructor
	 */
	public function __construct(){}

	/**
	 * Load the class
	 */
	public function load() {
		add_action( 'init', array( $this, 'load_hooks' ) );
	}

	/**
	 * Load the hooks
	 */
	public function load_hooks() {
		add_action(	'wp_footer', array($this, 'nt_render_ntoklo_js_link'),30);
	}

	public function nt_render_ntoklo_js_link(){

		$post_key = (array) get_option( 'general_settings' );
		$nt_key = json_decode($post_key['key_secret']);

		echo '<script type="text/javascript">var _ntoklo_key = "' . $nt_key->key . '"</script>';
		?>
			<script type="text/javascript" src="https://console.ntoklo.com/static/js/ntoklo.js"></script>
		<?php
	}
}//End of class
?>