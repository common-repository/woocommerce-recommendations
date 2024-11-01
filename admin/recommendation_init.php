<?php
	//Add Style
	function nt_add_widget_css () {
		wp_enqueue_style("style_sheets", WOORMD_CSS_URL . "/style.css" );
		wp_enqueue_style("widget_sheets", WOORMD_CSS_URL . "/widget.css" );
	}
	add_action("admin_enqueue_scripts", "nt_add_widget_css");
	
	function woorec_activate_plugin(){
		add_option( 'nt_woorec_just_installed', true );
	}

	function woorec_deactivate_plugin(){
		delete_option('general_settings');
	}
	
	//if plugin is activated redirect to admin woo recommendation page
	function woorec_init() {
		if ( get_option( 'nt_woorec_just_installed', false ) ) {
			delete_option( 'nt_woorec_just_installed' );
			wp_redirect( admin_url( 'admin.php?page=ntoklo_options', 'relative' ) );
			exit;
		}
	}
	add_action('init', 'woorec_init');

	// Add Admin Menu
	//add_action('admin_menu', 'woo_recommendation_add_menu_item_e_commerce', 10);
	
	function woo_recommendation_add_menu_item_e_commerce() {
		$woo_page = 'woocommerce';
		add_submenu_page( $woo_page , 
			__( 'nToklo Recommendation'), 
			__( 'nToklo Recommendation'), 
			'manage_options', 
			'woo-recommendation-settings', 
			'woo_recommendation_dashboard' );
	}
	
	function woo_recommendation_dashboard() {
	
		echo '<div class="wrap"><div id="icon-tools" class="icon32 icon32-posts-shop_order"></div>';
			echo '<h2>nToklo Recommendation</h2>';
		echo '</div>';
		
		WC_Recommendation_Settings::panel_manager();
	}
?>