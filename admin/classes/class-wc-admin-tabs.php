<?php
/*
 * Plugin Name: Settings API Tabs Demo
 * Plugin URI: http://theme.fm/?p=
 * Description: This is a demo showing off usage of tabs with the WordPress Settings API
 * Version: 1.0
 * Author: kovshenin
 * Author URI: http://theme.fm
 * License: GPL2
 */

/*
 * The main plugin class, holds everything our plugin does,
 * initialized right after declaration
 */
class Settings_API_Tabs_Plugin {
	
	/*
	 * For easier overriding we declared the keys
	 * here as well as our tabs array which is populated
	 * when registering settings
	 */
	private $general_settings_key = 'general_settings';
	private $advanced_settings_key = 'widget_settings';
	private $analytics_settings_key = 'analytics_settings';
	private $plugin_options_key = 'ntoklo_options';
	private $plugin_settings_tabs = array();
	
	
	/*
	 * Fired during plugins_loaded (very very early),
	 * so don't miss-use this, only actions and filters,
	 * current ones speak for themselves.
	 */
	function __construct() {
		add_action( 'init', array( &$this, 'load_settings' ) );
		add_action( 'admin_init', array( &$this, 'general_settings' ) );
		add_action( 'admin_init', array( &$this, 'widget_settings' ) );
		add_action( 'admin_init', array( &$this, 'analytics_settings' ) );
		add_action( 'admin_menu', array( &$this, 'add_admin_menus' ) );
	}
	
	/*
	 * Loads both the general and advanced settings from
	 * the database into their respective arrays. Uses
	 * array_merge to merge with default values if they're
	 * missing.
	 */
	function load_settings() {
		$this->general_settings = (array) get_option( $this->general_settings_key );
		$this->advanced_settings = (array) get_option( $this->advanced_settings_key );
		//$this->advanced_settings = (array) get_option( $this->advanced_settings_key );

		// Merge with defaults
		$this->general_settings = array_merge( array(
			'key' => 'Key'
		), $this->general_settings );
		
		$this->advanced_settings = array_merge( array(
			'advanced_option' => 'Advanced value'
		), $this->advanced_settings );
	}
	
	/*
	 * Registers the general settings via the Settings API,
	 * appends the setting to the tabs array of the object.
	 */
	function general_settings() {
	
    	if(is_object(json_decode($this->general_settings['key_secret'])) && $_GET['tab'] == 'general_settings' && $_GET['settings-updated'] == true){ 
    		$this->redirect_to_widget_options();
    	}elseif(is_object(json_decode($this->general_settings['key_secret'])) && $_GET['settings-updated'] == true && $_GET['page'] == 'ntoklo_options'){
    		$this->redirect_to_widget_options();

    	}

		$this->plugin_settings_tabs[$this->general_settings_key] = 'Settings';
		register_setting( $this->general_settings_key, $this->general_settings_key );
		add_settings_section( 'error_msg_setting_page', '', array( &$this, 'error_msg_setting_page' ), $this->general_settings_key );	

		add_settings_section( 'section_general', '', array( &$this, 'ntoklo_account_form' ), $this->general_settings_key );	
		//add_settings_field( 'key', 'Key', array( &$this, 'key_option' ), $this->general_settings_key, 'section_general' );
		//add_settings_field( 'secret', 'Secret', array( &$this, 'secret_option' ), $this->general_settings_key, 'section_general' );
		add_settings_field( 'key_secret_textarea', 'Paste your key and secret here:', array( &$this, 'key_secret_textarea' ), $this->general_settings_key, 'section_general' );
	}
	
	/*
	 * Registers the widget settings and appends the
	 * key to the plugin settings tabs array.
	 */
	function widget_settings() {
		if($_GET['saved_key_secret'] == true ){
		?>
 			<div class="updated">
       			 <p><?php _e( 'Your setting is saved', 'my-text-domain' ); ?></p>
    		</div>		
    	<?php 
    	}
		$this->plugin_settings_tabs[$this->advanced_settings_key] = 'Widget Options';	
		register_setting( $this->advanced_settings_key, $this->advanced_settings_key );	
		add_settings_section( 'section_advanced', 'How to place widgets on your page', array( &$this, 'how_to_place_widgets' ), $this->advanced_settings_key );
	}


	/*
	 * Registers the analytics settings and appends the
	 * key to the plugin settings tabs array.
	 */
	function analytics_settings() {
		$this->plugin_settings_tabs[$this->analytics_settings_key] = 'Analytics';
		register_setting( $this->analytics_settings_key, $this->analytics_settings_key );
		add_settings_section( 'section_chart', '', array( &$this, 'nt_render_no_chart_data_to_display' ), $this->analytics_settings_key );
		add_settings_section( 'section_advanced', '', array( &$this, 'nt_render_console_link' ), $this->analytics_settings_key );
	}
	
	/*
	 * The following methods provide descriptions
	 * for their respective sections, used as callbacks
	 * with add_settings_section
	 */
	function section_general_desc() { echo 'We need to link your store to an nToklo account to provide you with recommendations. Please choose from the following options to get yourself set up.'; }
	function section_advanced_desc() { echo 'Advanced section description goes here and fu goes here too.'; }
	function section_advanced2_desc() { echo 'Advanced section description goes here.'; }

	
	/*
	 * General Option field callback, renders a
	 * text input, note the name and value.
	 */
	function key_option() {
		?>
		<input type="text" name="<?php echo $this->general_settings_key; ?>[key_option]" value="<?php echo esc_attr( trim($this->general_settings['key_option']) ); ?>" />
		<?php
	}

	function secret_option() {
		?>
		<input type="text" name="<?php echo $this->general_settings_key; ?>[secret_option]" value="<?php echo esc_attr( trim($this->general_settings['secret_option']) ); ?>" />
		<?php
	}
	
	/*
	 * Advanced Option field callback, same as above.
	 */
	function key_secret_textarea() {
		?>
		<textarea rows="6" cols="25" name="<?php echo trim($this->general_settings_key);?>[key_secret]"> <?php echo esc_attr( trim($this->general_settings['key_secret']) ); ?> </textarea>
		<?php
	}
	
	/*
	 * Called during admin_menu, adds an options
	 * page under Settings called My Settings, rendered
	 * using the plugin_options_page method.
	 */
	function add_admin_menus() {

			//add_action('admin_menu', 'woo_recommendation_add_menu_item_e_commerce', 10);
			$woo_page = 'woocommerce';

			add_submenu_page( $woo_page , 
			__( 'nToklo Recommendation'), 
			__( 'nToklo Recommendation'), 
			'manage_options', 
			$this->plugin_options_key, 
			array( &$this, 'plugin_options_page') );
		//add_options_page( 'My Plugin Settings', 'My Settings', 'manage_options', $this->plugin_options_key, array( &$this, 'plugin_options_page' ) );
	}
	
	/*
	 * Plugin Options page rendering goes here, checks
	 * for active tab and replaces key with the related
	 * settings key. Uses the plugin_options_tabs method
	 * to render the tabs.
	 */
	function plugin_options_page() {
		$tab = isset( $_GET['tab'] ) ? $_GET['tab'] : $this->general_settings_key;
		?>
		<div class="wrap wrap-width"><div id="icon-tools" class="icon32 icon32-posts-shop_order"></div>
			<h2>nToklo Recommendation</h2>
		</div>	
		<div class="wrap wrap-width">
			<?php $this->plugin_options_tabs(); ?>
			<form method="post" action="options.php">
				<?php wp_nonce_field( 'update-options' ); ?>
				<?php settings_fields( $tab ); ?>
				<?php do_settings_sections( $tab ); ?>

				<?php if($_GET['tab'] != 'widget_settings' && $_GET['tab'] != 'analytics_settings'){ ?> 
				<?php submit_button(); ?>
				<?php }?>
			</form>
		</div>
		<?php
	}
	
	/*
	 * Renders our tabs in the plugin options page,
	 * walks through the object's tabs array and prints
	 * them one by one. Provides the heading for the
	 * plugin_options_page method.
	 */
	function plugin_options_tabs() {
		$current_tab = isset( $_GET['tab'] ) ? $_GET['tab'] : $this->general_settings_key;
		screen_icon();
		echo '<h2 class="nav-tab-wrapper">';
		foreach ( $this->plugin_settings_tabs as $tab_key => $tab_caption ) {
			$active = $current_tab == $tab_key ? 'nav-tab-active' : '';
			echo '<a class="nav-tab ' . $active . '" href="?page=' . $this->plugin_options_key . '&tab=' . $tab_key . '">' . $tab_caption . '</a>';	
		}
		echo '</h2>';
	}

	/*
	 * Render the instruction how to place a widget 
	 *
	 */
	function how_to_place_widgets() {
?>
		<div class="nt_settings_section">
			<p>You can place recommendations or charts on your store pages using the nToklo widgets, either on the widgets page by dragging a widget on to a sidebar, or by calling the_widget() function from within a template.</p>
			<div id="nt_accordion">
				<h4 class="nt_accordion_toggle">
					<a href="#">
						<span>
							<svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="20px" height="10px" viewBox="0 0 20 10" enable-background="new 0 0 20 10" xml:space="preserve">
								<path fill="#010101" d="M2.427,0.21c-0.283-0.28-0.741-0.28-1.024,0c-0.283,0.279-0.283,0.734,0,1.014l8.275,8.194c0.283,0.28,0.741,0.28,1.024,0l8.276-8.194c0.282-0.28,0.283-0.734,0-1.014c-0.283-0.28-0.741-0.28-1.024,0L10.19,7.683L2.427,0.21z"/>
							</svg>
						</span>
						1) Via the widgets menu (easy) 
					</a>
				</h4>
				<div class="nt_accordion_container">
					<p>This is the easiest way and is recommended for non-technical users. Go to the <a href="widgets.php">Appearance > Widgets</a> page and drag either or both of the WooCommerce widgets on to your sidebar (WooCommerce chart or WooCommerce recommendations). From there you can configure settings for each widget and preview them on your store.</p>
				</div>
				<h4 class="nt_accordion_toggle">
					<a href="#">
						<span>
							<svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="20px" height="10px" viewBox="0 0 20 10" enable-background="new 0 0 20 10" xml:space="preserve">
								<path fill="#010101" d="M2.427,0.21c-0.283-0.28-0.741-0.28-1.024,0c-0.283,0.279-0.283,0.734,0,1.014l8.275,8.194c0.283,0.28,0.741,0.28,1.024,0l8.276-8.194c0.282-0.28,0.283-0.734,0-1.014c-0.283-0.28-0.741-0.28-1.024,0L10.19,7.683L2.427,0.21z"/>
							</svg>
						</span>
							2) Using shortcodes
					</a>
				</h4>
				<div class="nt_accordion_container">
					<p>This method gives you greater flexibility when positioning your widget but is not recommended for non-technical users.</p>
					<p class="nt_subsection">For recommendations, you should place the following code in the appropriate pages:</p>
					<p class="nt_code">[ntoklo_recommendations $arguments]</p>
					<p>Where $arguments can be any of the following:</p>
					<table cellpadding="10" cellspacing="0" class="nt_settings_table">
						<thead>
							<tr>
								<th>Key</th>
								<th>Accepted values</th>
								<th>Defaults</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td>title</td>
								<td>String</td>
								<td>Recommended for you</td>
							</tr>
							<tr>
								<td>max_items</td>
								<td>An integer between 1 - 9.</td>
								<td>3</td>
							</tr>
							<tr>
								<td>layout</td>
								<td>
									<ul>
										<li>row_3</li>
										<li>row_4</li> 			
										<li>column_image_above</li>	
										<li>column_image_right</li> 	
										<li>grid_2_column</li> 		
										<li>grid_3_column</li>		
										<li>grid_4_column</li>	
									</ul>
								</td>
								<td>
									row_3
								</td>
							</tr>
							<tr>
								<td>widget_color</td>
								<td>
									<ul>
										<li>plum</li>
										<li>pink</li>
										<li>orange</li>
										<li>green</li>	
										<li>blue</li>	
										<li>dark_blue</li>								
									</ul>
								</td>
								<td>
									plum
								</td>
								</tr>
							</tbody>
						</table>
						<p>These arguments should be passed as query string parameters, such as:</p>
						<p class="nt_code">layout=grid_2_column widget_color=blue max_items=4</p>
						<p>Meaning that call to a recommendation widget might look like this:</p>
						<p class="nt_code">[ntoklo_recommendations title="We recommend" layout=grid_2_column widget_color=blue max_items=4]</p>
						<p class="nt_subsection"><strong>Charts</strong> are called in a similar way, but with different options. Once again you should place the following code in the appropriate pages:</p>
						<p class="nt_code">[ntoklo_chart $arguments]</p>
						<p>$arguments for charts can be any of the following:</p>
						<table cellpadding="10" cellspacing="0" class="nt_settings_table">
							<thead>
								<tr>
									<th>Key</th>
									<th>Accepted values</th>
									<th>Defaults</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td>title</td>
									<td>String</td>
									<td>Best sellers</td>
								</tr>
								<tr>
									<td>max_items</td>
									<td>Integer between 1 and 100</td>
									<td>10</td>
								</tr>
								<tr>
									<td>tw</td>
									<td>
										<ul>
											<li>DAILY</li>
											<li>WEEKLY</li>
										</ul>
									</td>
									<td>DAILY</td>
								</tr>
								<tr>
									<td>widget_color</td>
									<td>
										<ul>
											<li>plum</li>
											<li>pink</li>
											<li>orange</li>
											<li>green</li>	
											<li>blue</li>	
											<li>dark_blue</li>
										</ul>
									</td>
									<td>
										plum
									</td>
								</tr>
							</tbody>
						</table>
						<p>A call to a chart widget might look like this:</p>
						<p class="nt_code">[ntoklo_chart title="Top 10" max_items=10 widget_color=dark_blue]</p>
					</div>
				</div>
				<script type="text/javascript">
					if (jQuery) {
						jQuery("#nt_accordion").addClass("nt_collapsed");
						jQuery(".nt_accordion_toggle a").on("click", function () {
							if (jQuery(this).parent().next().is(":visible")) {
								jQuery(this).removeClass();
								jQuery(".nt_accordion_container").slideUp();
							} else {
								jQuery(".nt_accordion_toggle a").removeClass();
								jQuery(".nt_accordion_container").slideUp();
								jQuery(this).addClass("nt_rotate_icon").parent().next().slideDown();
							}
						});
					}
			</script>
	</div>
<?php		
	}

	/*
	 * Rendfer the account creation form
	 *
	 *
	 */
	function ntoklo_account_form() {
		$admin_email 	= get_option( 'admin_email' );
		$app_name 		= get_option( 'blogname' );
		$app_domain 	= get_option( 'siteurl' );
?>		
		<div class="wrap">
			<div class="nt_settings_panel_wrapper">
				<h2>Create your nToklo account</h2>
				<div class="nt_explanation">
					<p>We need to link your store to an nToklo account to provide you with recommendations. Please choose from the following options to get yourself set up.</p>
					<div id="buttons">
						<div class="btn_wrap">
							<a class="nt_btn nt_no_account" href="#" id="ntLaunchRegister">I don't have an nToklo account</a>
							<a class="nt_btn nt_account" href="#" id="ntLaunchLogin">I already have an nToklo account</a>
						</div>
					</div>
					<p>Please follow the instructions in the panel that opens when you click on the buttons.</p>
		
					<p><strong>N.B.</strong> This plugin will send data about user activity on your site to our servers. The data is stored there (so we can process it and to avoid filling up your server) and sent back to this plugin each time you ask for recommendations or charts. For more details about how this works, please <a href="http://www.ntoklo.com/">take a look at our site</a>.</p>
				</div>
			</div>	
		</div>
		<div id="ntIFrameWrapper"></div>
			<script type="text/javascript">
				window.ntParams = {
					"p" : "woo",
					"e" : "<?=$admin_email?>",
					"n" : "<?=$app_name?>",
					"d" : "<?=$app_domain?>"
				}

				if (jQuery) {
					var 
						nt_confirmation_code_wrapper 	= jQuery("#nt_confirmation_code_wrapper"),
						nt_launch_register_button 		= jQuery("#ntLaunchRegister"),
						nt_launch_login_button 			= jQuery("#ntLaunchLogin"),
						nt_key_and_secret 				= jQuery("#nt_key_and_secret");

						nt_launch_register_button.on("click", function () {
							nt_confirmation_code_wrapper.addClass("nt_active");
						});

						nt_launch_login_button.on("click", function () {
							nt_confirmation_code_wrapper.addClass("nt_active");
						});

						nt_key_and_secret.on("focus", function () {
							jQuery("#nt_confirmation_assist_text_2").show();
							jQuery("#nt_confirmation_code_wrapper.nt_active").css({"padding": "1em"});
						});
				}
			</script>
			<script src="https://console.ntoklo.com/js/ntoklo.js" type="text/javascript"></script>
<?php
						
	}

	/*
	 * Render the console link 
	 *
	 */
	function nt_render_console_link(){
?>	
	<div class='wrap'>
		<div class="nt_settings_section">
			<img class="nt_console_img" src="<?php echo WOORMD_IMAGES_URL .'/console.png'; ?>" alt="nToklo console" />
				<h2>Recommendation analytics on your console</h2>
				<p>The nToklo console shows information about user activity on your store - think of it like Google Analytics, with a retail focus. You can:</p>
				
				<ul class="nt_features">
					<li>See a snapshot of all activity on your site on the <strong>clickthrough tab</strong>. How busy are you today / this week / this month?</li>
					<li>See how well your recommendations are converting on the <strong>conversion tab</strong>.</li>
					<li>Find out what the best performing location for recommendations is and reposition them if necessary.</li>
					<li>View your purchase funnel on the <strong>item activity tab</strong>, where user browsing history is broken down for you into browse, preview and purchase events.</li>
					<li>See which times of the day, week and month are the busiest on the <strong>user activity tab</strong>.</li>
					<li>See summary figures for today, this week and this month, in relation to the average, busiest and quietest days / weeks / months on <strong>on all four tabs</strong>.</li>
					<li>Keep track of real-world events such as promotional campaigns, overlaying the data on the graphs using our annotations.</li>
				</ul>
				<p>We've packed a ton of features into this console but still kept it easy-to-use, so why not take a look? Please note that you'll need an up-to-date browser, such as Chrome, Safari or Firefox (or IE10).</p>
				<p>
					<a class="nt_btn nt_console_link" href="https://console.ntoklo.com/login" target="_blank">
						<span class="nt_svg_label">
							Launch console
						</span>
						<span class="nt_svg_wrap">
							<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="18px" height="16px" viewBox="0 0 25.51 22.68" enable-background="new 0 0 25.51 22.68" xml:space="preserve">
								<defs>
									<linearGradient id="gradConsole" x1="0%" y1="0%" x2="0%" y2="100%">
										<stop offset="0%" style="stop-color:#6384a8;stop-opacity:1" />
										<stop offset="20%" style="stop-color:#375575;stop-opacity:1" />
										<stop offset="100%" style="stop-color:#1c344e;stop-opacity:1" />
									</linearGradient>
								</defs>
								<g>
									<path fill="#6A9B1D" d="M7.791,12.842v-4h4c0-2.209-1.793-4-4-4c-2.211,0-4,1.791-4,4C3.791,11.05,5.58,12.842,7.791,12.842z"/>
									<path fill="#C79C00" d="M8.791,13.842c2.208,0,4-1.791,4-4h-4V13.842z"/>
									<rect x="14.42" y="5" fill="#B10F62" width="7" height="1.6"/>
									<path fill="url(#gradConsole)" d="M23.01,0.838H2.5c-1.128,0-2.051,0.921-2.051,2.051v12.306c0,1.125,0.923,2.051,2.051,2.051h8.204v3.102H9.678c-1.025,0-1.025,1.027-1.025,1.027h0.002v0.467h8.202v-0.469c-0.002-1.025-1.024-1.025-1.024-1.025h-1.025v-3.102h8.203c1.127,0,2.051-0.926,2.051-2.051V2.889C25.061,1.76,24.137,0.838,23.01,0.838z M23.01,15.195H2.5V2.889h20.51V15.195z"/>
									<rect x="14.42" y="8" fill="#910077" width="7" height="1.6"/>
									<rect x="14.42" y="11" fill="#B10F62" width="7" height="1.6"/>
								</g>
							</svg>
						</span>
					</a>
				</p>
			</div>	
		</div>
<?php		
	}

	/*
	 * Render the charts
	 */
	public static function nt_render_no_chart_data_to_display() {		
?>		<div class='wrap'>
			<div class="nt_chart_wrapper">
		<?php
				$args = array(
					'title'     => 'Chart',
					'max_items' => 10,
					'tw' 		=> 'WEEKLY'
					);
					the_widget("Ntoklo_Chart_Widget", $args); 
					
				if(get_option('response') == FALSE){
		?>	
				<div class="nt_warning">
					<h2>There is no chart data to display</h2>
					<p>This could mean that no activity has been posted from your site recently, in which case we're unable to give you recommendations or charts. Please <a href="https://console.ntoklo.com/login">check your nToklo console</a>.</p>
				</div>
		<?php
				}
		?>		
			</div>	
		</div>
	<?php				
	}
	
	public function error_msg_setting_page(){
		if($this->general_settings['key_secret'] == null){	
    	?>
    		<div class="error">
       			 <p><?php _e( 'You need to insert your key and secret', 'my-text-domain' ); ?></p>
    		</div>	
    	<?php
    	}elseif(!is_object(json_decode($this->general_settings['key_secret'])) && $this->general_settings['key_secret'] != null){	
    	?>
    		<div class="error">
       			 <p><?php _e( 'Please insert the correct key and secret', 'my-text-domain' ); ?></p>
    		</div>	
    		<?php
    	}
	}
	
	public function redirect_to_widget_options(){
		wp_redirect( admin_url( 'admin.php?page=ntoklo_options&tab=widget_settings&saved_key_secret=true', 'relative' ) );
    	exit;
	}
}
// Initialize the plugin
add_action( 'plugins_loaded', create_function( '', '$settings_api_tabs_plugin = new Settings_API_Tabs_Plugin;' ));
?>