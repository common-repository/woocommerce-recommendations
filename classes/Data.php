<?php
/* This software is distributed under the terms of GNU GENERAL PUBLIC LICENSE Version 3, 29 June 2007 */
/**
 * WooCommerce Account setting manager
 *
 * Table of content
 *
 */

class Data_Object{

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
		add_action( 'wp_head', array( $this, 'getCurrentWpVersion'));
		add_action( 'wp_head', array( $this, 'getWooCurrentVersion'));

		add_action( 'woocommerce_before_shop_nt_loop', array( $this, 'getUvObjectUser'));
		add_action( 'woocommerce_before_shop_nt_loop', array( $this, 'getEventType'));

		add_action( 'wp_head', array( $this, 'getUvMapPage'));

		add_action( 'woocommerce_before_single_product', array( $this, 'getEventType'));
		add_action( 'woocommerce_before_single_product', array( $this, 'getUvMapProduct'));
	}

	/**
	 * return wordpress current version number
	 */
	public function getCurrentWpVersion(){
		$wp_version = get_bloginfo( 'version' );
		return $wp_version;
	}

	/**
	 * return woo version number
	 */
	public function getWooCurrentVersion(){
		$woo_version = wpbo_get_woo_version_number();
		return $woo_version;
	}

	/**
	 * return user id
	 * */
	public function getUvObjectUser(){
		$user_id = get_current_user_id();

		$user = array('visitor_id' => $this->getUvObjectVistor());
		if($user_id != 0){
			$user += array('user_id' => (string)$user_id);
		}
		return $user;
	}

	public function getUvObjectVistor(){

		if($_COOKIE['visitor_id']!= null){
			return (string)$_COOKIE['visitor_id'];
		}
	}

	/**
	 * Maps the event types to page categories
	 * */
	public function getEventType(){

		if(is_shop()){
			return 'browse';
		}

		if(is_product()){
			return 'preview';
		}

		if(is_checkout()){
			return 'purchase';
		}

		if(is_page('order-received')){
			return 'purchase';
		}

		if(is_product_tag()){
			$product_cats = wp_get_post_terms( get_the_ID(), 'product_cat' );
			$catagory_name = $product_cats[0]->name;
			if(is_product_tag($catagory_name)){
				return 'browse';
			}
			return 'browse';
		}

		if(is_product_category()){
			$product_cats = wp_get_post_terms( get_the_ID(), 'product_cat' );
			$catagory_name = $product_cats[0]->name;
			if(is_product_category($catagory_name)){
				return 'browse';
			}
			return 'browse';
		}
	}

	/**
	 * Creates the product UV object
     * @return array
	 */
	public function getUvMapProduct(){

		if(is_product()){
			$postID = get_the_ID();
			$args = array('post_type' => 'product', 'p' => $postID);
			$nt_loop = new WP_Query( $args );

			$category = wp_get_object_terms($nt_loop->post->ID, 'product_cat');
			$url = wp_get_attachment_image_src( get_post_thumbnail_id($nt_loop->post->ID));
			$unit_price = get_post_meta( $nt_loop->post->ID, '_regular_price', true);
			$unit_sale_price = get_post_meta( $nt_loop->post->ID, '_sale_price', true);

			$product_details = array(
				'id' => (string)$nt_loop->post->ID,
				'url' => get_permalink(),
				'name' => $nt_loop->post->post_title,
				'unit_price' => (float)$unit_price,
				'unit_sale_price' => (float)$unit_sale_price,
				'currency' => get_woocommerce_currency(),
				'image_url' => $url[0],
				'category' => $category[0]->name
			);
			//print_r($product_details);
			return $product_details;
		}
	}

	public function getUvMapPage(){

		if( is_shop() || is_product() || is_product_category() || is_page('order-received') || is_checkout()){
			return	$breadcrumb = array(
				"type" => "category",
				"breadcrumb" => array(
					trim(wp_title("", FALSE))
				)
			);
		}

		if(is_home()){
			return	$breadcrumb = array(
				"type" => "category",
				"breadcrumb" => 'Home'
			);
		}
	}
}//End of class
?>