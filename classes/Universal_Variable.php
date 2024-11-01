<?php
/* This software is distributed under the terms of GNU GENERAL PUBLIC LICENSE Version 3, 29 June 2007 */
/**
 * WooCommerce Account setting manager
 *
 * Table of content
 *
 */
 class Universal_Variable{

	public $Data;
	public $universal_variable = array();

 	/**
	 * Constructor
	 */
	public function __construct(){
		$this->Data = new Data_Object();
	}

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
		add_action( 'woocommerce_page_titled', array( $this, 'getJsonUniversalVariable'));
		add_action( 'wp_head', array( $this, 'getJsonUniversalVariable'), 10, 1);
		add_action( 'wp_head', array( $this, 'getUniversalVariable'), 10, 1);
		add_action(	'woocommerce_thankyou', array($this, 'getUniversalVariable'), 10, 1);
		add_action(	'woocommerce_thankyou', array($this, 'getJsonUniversalVariable'), 10, 1);
	 }

	public function getJsonUniversalVariable( $order_id ){

	?>
			<script type="text/javascript">
				window.universal_variable = <?=json_encode($this->getUniversalVariable( $order_id ));?>
			</script>
	<?php

	}

	public function getUniversalVariable($order_id){

		$universal_variable = array(
			'version' => '1.2',
			'woocommerce_version' => $this->Data->getWooCurrentVersion(),
			'wordpress_version' => $this->Data->getCurrentWpVersion(),
			'ntoklo_version' => '1.0.1'
		);

		//user uv object
		$universal_variable['user'] = $this->Data->getUvObjectUser();

		//events uv object
		if(is_shop() || is_product() || is_product_category() || is_page('order-received') || is_checkout()){
		$universal_variable['events'] = array(
			array(
				'category' => 'conversion_funnel',
				'action' => $this->Data->getEventType()
			));
		}

		/**
		 * create page UV Object
		 */
		$universal_variable['page']= $this->Data->getUvMapPage();

		/**
		 * create single page product
		 */
		if(is_product()){
			$universal_variable['product'] = $this->Data->getUvMapProduct();
		}

		/**
		 * Create product listing on product page
		 */
		if(is_shop()){
			$universal_variable['listing']['items'] = array();
			$args = array('post_type' => 'product');
			$nt_loop = new WP_Query( $args );
			if ( $nt_loop->have_posts() ) {
				while ( $nt_loop->have_posts() ) : $nt_loop->the_post();
						$category = wp_get_object_terms($nt_loop->post->ID, 'product_cat');
					$url = wp_get_attachment_image_src( get_post_thumbnail_id($nt_loop->post->ID));

					$unit_price = get_post_meta( $nt_loop->post->ID, '_regular_price', true);
					$unit_sale_price = get_post_meta( $nt_loop->post->ID, '_sale_price', true);

					$product_details = array(
						'id' => (string)$nt_loop->post->ID,
						'url' => get_permalink( $nt_loop->post->ID ),
						'name' => $nt_loop->post->post_title,
						'unit_price' => (float)$unit_price,
						'unit_sale_price' => (float)$unit_sale_price,
						'currency' => get_woocommerce_currency(),
						'image_url' => $url[0],
						'category' => $category[0]->name
					);
					array_push($universal_variable['listing']['items'], $product_details);
				endwhile;
			}
			wp_reset_postdata();
		}

		$product_cats = wp_get_post_terms( get_the_ID(), 'product_cat' );
		$catagory_name = $product_cats[0]->name;
		if(is_product_category($catagory_name)){
			$universal_variable['listing']['items'] = array();
			$args = array('product_cat' => $catagory_name);
			$nt_loop = new WP_Query( $args );
			if ( $nt_loop->have_posts() ) {
				while ( $nt_loop->have_posts() ) : $nt_loop->the_post();
					$category = wp_get_object_terms($nt_loop->post->ID, 'product_cat');
					$url = wp_get_attachment_image_src( get_post_thumbnail_id($nt_loop->post->ID));

					$unit_price = get_post_meta( $nt_loop->post->ID, '_regular_price', true);
					$unit_sale_price = get_post_meta( $nt_loop->post->ID, '_sale_price', true);
						$product_details = array(
							'id' => (string)$nt_loop->post->ID,
							'url' => get_permalink( $nt_loop->post->ID ),
							'name' => $nt_loop->post->post_title,
							'unit_price' => (float)$unit_price,
							'unit_sale_price' => (float)$unit_sale_price,
							'currency' => get_woocommerce_currency(),
							'image_url' => $url[0],
							'category' => $category[0]->name
						);
					array_push($universal_variable['listing']['items'], $product_details);
				endwhile;
			}
			wp_reset_postdata();
		}

		/**
		 * create UV transaction
		 */
		if($order_id != null){
			$universal_variable['transaction']['line_items'] = array();
			$order = new WC_Order($order_id);
			$items = $order->get_items();

			if(count($items) > 1){
				foreach ($items as $key => $value):
					$quantity = $value['qty'];
					$subtotal =	$value['line_subtotal'];
					$args = array( 'p' => $value['product_id'], 'post_type' => 'product');
					$nt_loop = new WP_Query( $args );

					$category = wp_get_object_terms($nt_loop->post->ID, 'product_cat');
					$url = wp_get_attachment_image_src( get_post_thumbnail_id($nt_loop->post->ID));
					$unit_price = get_post_meta( $nt_loop->post->ID, '_regular_price', true);
					$unit_sale_price = get_post_meta( $nt_loop->post->ID, '_sale_price', true);

					$product_details = array(
					'product' => array(
						'id' => (string)$nt_loop->post->ID,
						'url' => get_permalink( $nt_loop->post->ID ),
						'name' => $nt_loop->post->post_title,
						'unit_price' => (float)$unit_price,
						'unit_sale_price' => (float)$unit_sale_price,
						'currency' => get_woocommerce_currency(),
						'image_url' => $url[0],
						'category' => $category[0]->name
					),

					'quantity' => $quantity,
					'subtotal' => $subtotal

					);
				array_push($universal_variable['transaction']['line_items'], $product_details);
				endforeach;
			}else{
				foreach ($items as $key => $value):
					$productID = $value['product_id'];
					$quantity = $value['qty'];
					$subtotal =	$value['line_subtotal'];
				endforeach;
				$args = array( 'p' => $productID, 'post_type' => 'product');
				$nt_loop = new WP_Query( $args );

				$category = wp_get_object_terms($nt_loop->post->ID, 'product_cat');
				$url = wp_get_attachment_image_src( get_post_thumbnail_id($nt_loop->post->ID));
				$unit_price = get_post_meta( $nt_loop->post->ID, '_regular_price', true);
				$unit_sale_price = get_post_meta( $nt_loop->post->ID, '_sale_price', true);
				$product_details = array(
				'product' => array(
					'id' => (string)$nt_loop->post->ID,
					'url' => get_permalink( $nt_loop->post->ID ),
					'name' => $nt_loop->post->post_title,
					'unit_price' => (float)$unit_price,
					'unit_sale_price' => (float)$unit_sale_price,
					'currency' => get_woocommerce_currency(),
					'image_url' => $url[0],
					'category' => $category[0]->name
				),
				'quantity' => $quantity,
				'subtotal' => $subtotal
				);
				array_push($universal_variable['transaction']['line_items'], $product_details);
			}
		}
		return $universal_variable;
	}
}//End of class
?>