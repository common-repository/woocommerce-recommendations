<?php
/* "Copyright 2013 nToklo.com" This software is distributed under the terms of GNU GENERAL PUBLIC LICENSE Version 3, 29 June 2007 */
/**
 * Create WOO Recommendation Widget
 */

/**
 * WooCommerce Recommendation Widget
 *
 * Table Of Contents
 *
 * WC_Compare_Widget()
 * widget()
 * update()
 * form()
 */
add_action( 'widgets_init', create_function( '', 'return register_widget("Ntoklo_Recommendation_Widget");' ), 10 );

class Ntoklo_Recommendation_Widget extends WP_Widget {
	public $event_get;

	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {
		$this->event_get = new Event_Get();

		parent::__construct(
			'recommendation_widget', // Base ID
			__('WooCommerce Recommendation', 'text_domain'), // Name
			array( 'description' => __( 'Recommendations for your WooCommerce store ', 'text_domain' ), ) // Args
		);
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {

		$current_user = wp_get_current_user();
		if($current_user->ID > 0 ){
			$userId = $current_user->ID;
		}else{
			$userId = $_COOKIE['visitor_id'];
		}

		if(is_product()){
			global $post;
			$productId = $post->ID;
		}

		if(is_product_category()){
			$product_cats = wp_get_post_terms( get_the_ID(), 'product_cat' );
			$value = $product_cats[0]->name;
			$scope = "category";
		}

		$response = $this->event_get->nt_fetch_recommendation($userId, $productId, $scope, $value);

		foreach ($response->items as $key => $value) {
    		$product_id[] = $value->id;
		}

		if($_GET['nt_rec']){
			$recommendation_object = array(
				"category" => 'clickthrough_goals',
				"action" => 'recommendation-click',
	           	"tracker_id" => $_GET['nt_rec']
			);
		}else{
			if($response->tracker_id != null){
				$recommendation_object = array(
					"category" => 'clickthrough_goals',
					"action" => 'recommendation-impr',
		           	"tracker_id" => $response->tracker_id
				);
			}else{
				$recommendation_object = null;
			}
		}
		?>

			<script type="text/javascript">
				var recommendation_object = <?=json_encode($recommendation_object);?>
			</script>

		<?php
			if(is_shop() || is_product() || is_product_category() || is_checkout()){
		?>
			<script type="text/javascript" src="<?php echo WOORMD_JS_URL; ?>/recommendation_uv.js"></script>
		<?php
			}


		$title = apply_filters( 'widget_title', $instance['title'] );

		if ( isset($instance["layout"]) ) {
			$layout = $instance["layout"];
		} else {
			$layout = "row";
		}

		if ( isset($instance["layout"]) ) {
			if ($instance["layout"] == "column_image_above") {
				$layout = "nt_column nt_img_above";
			} elseif ($instance["layout"] == "column_image_right") {
				$layout = "nt_column nt_img_right";
			} elseif ($instance["layout"] == "grid_2_column") {
				$layout = "nt_grid nt_2_column";
				$is_grid = true;
				$grid_columns = 2;
			} elseif ($instance["layout"] == "grid_3_column") {
				$layout = "nt_grid nt_3_column";
				$is_grid = true;
				$grid_columns = 3;
			} elseif ($instance["layout"] == "grid_4_column") {
				$layout = "nt_grid nt_4_column";
				$is_grid = true;
				$grid_columns = 4;
			} elseif ($instance["layout"] == "chart") {
				$layout = "nt_chart";
			} elseif ($instance["layout"] == "row_3") {
				$layout = "nt_row nt_r3";
			} elseif ($instance["layout"] == "row_4") {
				$layout = "nt_row nt_r4";
			}
		} else {
			$layout = "nt_row nt_r3";
		}

		update_option('widget_style', $instance["layout"]);
		$widget_options = Widget_Config::nt_set_widget_configuration($instance, "recommendation");

		if( $response != 401 && $response != 500 && !empty($response->items) ){

			if( $is_grid == false ){
				/*
				if ($layout == "nt_column nt_img_right") {

					echo '<style type="text/css">
							.nt_wrapper.nt_img_right .nt_widget .nt_item_wrap .nt_product_title {
								margin-right: ' . ($widget_options['image_width'] + 10) . 'px;
							}
							.nt_wrapper.nt_img_right .nt_widget div.nt_img_wrap,
							.nt_wrapper.nt_img_right .nt_widget div.nt_img_wrap img {
								height: ' . $widget_options['image_height'] . 'px;
								width: ' . $widget_options['image_width'] . 'px;
							}
						</style>';
				}
				*/
			?>
			<div class="nt_wrapper clearfix <?=$layout?> nt_<?=$widget_options['widget_color']?>">
				<p class="nt_header"><?=$title?></p>
					<ul class="nt_widget clearfix">
			<?php
				$max = (count($response->items) < $instance[ 'max_items' ]) ? count($response->items) : $instance[ 'max_items' ];
				$args = array(
					'post_type' => 'product',
					'post__in' => $product_id,
					'posts_per_page' => $max
				);

				$query = new WP_Query( $args );

				if ( $query->have_posts() ) :
					 while ( $query->have_posts() ) : $query->the_post();
						$url = wp_get_attachment_image_src( get_post_thumbnail_id($query->post->ID));
				?>
					<li>
						<div class="nt_item_wrap">
							<div class="nt_img_wrap">
								<a href="<?php echo get_permalink();?>?nt_rec=<?php echo $response->tracker_id; ?>">
									<img src="<?php echo $url[0]; ?>" alt="<?php echo the_title();?>" />
								</a>
							</div>
							<div class="nt_info_wrap">
								<span class="nt_product_title"><?php echo the_title(); ?></span>
								<span class="nt_product_price"><?php echo get_woocommerce_currency_symbol(); echo get_post_meta( $query->post->ID, '_price', true); ?></span>
								<a class="nt_btn" href="<?php echo get_permalink();?>?nt_rec=<?php echo $response->tracker_id; ?>">
									<svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="10.853px" height="11.229px" viewBox="0 0 10.853 11.229" enable-background="new 0 0 10.853 11.229" xml:space="preserve">
										<g>
											<path fill="#fff" d="M8.825,6.164l-4.367,4.361c-0.154,0.154-0.375,0.248-0.603,0.248c-0.229,0-0.449-0.094-0.604-0.248l-0.509-0.502C2.589,9.862,2.495,9.64,2.495,9.413c0-0.229,0.094-0.449,0.248-0.604l3.255-3.255L2.743,2.305c-0.154-0.16-0.248-0.382-0.248-0.609s0.094-0.449,0.248-0.603L3.252,0.59C3.406,0.43,3.627,0.336,3.855,0.336c0.228,0,0.448,0.094,0.603,0.254l4.367,4.361c0.154,0.153,0.248,0.375,0.248,0.603S8.979,6.003,8.825,6.164z"/>
										</g>
									</svg>
								</a>
							</div>
						</div>
					</li>

				<?php
					endwhile;
				endif;
				?>
					</ul>
				<div class="nt_logo"></div>
			</div>
		<?php
			//}
			}else{
		?>
			<div class="nt_wrapper nt_grid nt_<?=$grid_columns?>_column nt_<?=$widget_options['widget_color']?>">
				<p class="nt_header"><?php echo $title; ?></p>
				<div class="nt_widget clearfix">
			<?php
					$i 					= 0;
					$offset_for_closing = 0;
					$how_many_products 	= count($response->items);
					$how_many_rows 		= floor($how_many_products / $grid_columns) == 0 ? 1 : floor($how_many_products / $grid_columns);
					$last_item 			= $how_many_rows * $grid_columns;

					if ($grid_columns > $how_many_products) {
						$grid_columns = $how_many_products;
					}

					$max = (count($response->items) < $instance[ 'max_items' ]) ? count($response->items) : $instance[ 'max_items' ];
					$args = array(
						'post_type' => 'product',
						'post__in' => $product_id,
						'posts_per_page' => $max
					);

					$query = new WP_Query( $args );

					if ( $query->have_posts() ) :
						 while ( $query->have_posts() ) : $query->the_post();
							$url = wp_get_attachment_image_src( get_post_thumbnail_id($query->post->ID));

							if ($i < $last_item && isset($response->items) ) {
								if ($i % $grid_columns == 0) {
										$offset_for_closing = $i;
										echo '<div class="nt_row clearfix">';
								}
			?>
								<div class="nt_item_wrap">
									<div class="nt_img_wrap">
										<a href="<?php echo get_permalink();?>?nt_rec=<?php echo $response->tracker_id; ?>">
											<img src="<?php echo $url[0]; ?>" alt="<?php echo the_title(); ?>" />
										</a>
									</div>
									<span class="nt_product_title"><?php echo the_title(); ?></span>
									<span class="nt_product_price"><?php echo get_woocommerce_currency_symbol();  echo get_post_meta( $query->post->ID, '_price', true);  ?></span>
									<a class="nt_btn" href="<?php echo get_permalink();?>?nt_rec=<?php echo $response->tracker_id; ?>">
										<svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="10.853px" height="11.229px" viewBox="0 0 10.853 11.229" enable-background="new 0 0 10.853 11.229" xml:space="preserve">
											<g>
												<path fill="#fff" d="M8.825,6.164l-4.367,4.361c-0.154,0.154-0.375,0.248-0.603,0.248c-0.229,0-0.449-0.094-0.604-0.248l-0.509-0.502C2.589,9.862,2.495,9.64,2.495,9.413c0-0.229,0.094-0.449,0.248-0.604l3.255-3.255L2.743,2.305c-0.154-0.16-0.248-0.382-0.248-0.609s0.094-0.449,0.248-0.603L3.252,0.59C3.406,0.43,3.627,0.336,3.855,0.336c0.228,0,0.448,0.094,0.603,0.254l4.367,4.361c0.154,0.153,0.248,0.375,0.248,0.603S8.979,6.003,8.825,6.164z"/>
											</g>
										</svg>
									</a>
					 			</div>
				<?php
								if ($i - $offset_for_closing + 1 == $grid_columns) {
											echo '</div>';
											$offset_for_closing = $i;
								}
						}
							$i = $i + 1;
						endwhile;
					endif;
				?>
				</div>
				<div class="nt_logo"></div>
			</div>

		<?php
			}
		}else{
			return false;
		}
		?>
		<?php
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {

		$widget_layout_options = Widget_Config::nt_recommendations_widget_layout_options();
		$recommendations_colors = Widget_Config::nt_widget_colors();

		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}
		else {
			$title = __( 'Recommendation', 'text_domain' );
		}

		if ( isset( $instance[ 'max_items' ] ) ) {
			$max_items = $instance[ 'max_items' ];
		} else {
			$max_items = 9;
		}

		?>
			<div class="nt_row">
				<label for="<?php echo $this -> get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
				<input class="widefat" id="<?php echo $this -> get_field_id('title'); ?>" name="<?php echo $this -> get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
			</div>

			<div class="nt_row">
				<label for="<?php echo $this -> get_field_id('max_items'); ?>"><?php _e('Max items: (must be 9 or less)'); ?></label>
				<input class="widefat" id="<?php echo $this -> get_field_id('max_items'); ?>" name="<?php echo $this -> get_field_name('max_items'); ?>" type="text" value="<?php echo esc_attr($max_items); ?>" />
			</div>

			<div class="nt_row">
				<label for="<?php echo $this -> get_field_id('layout'); ?>"><?php _e('Layout:'); ?></label>
				<select id="<?php echo $this -> get_field_id('layout'); ?>" name="<?php echo $this -> get_field_name('layout'); ?>">
					<?php
					foreach ($widget_layout_options as $key => $value) {
						echo '<option value="' . $key . '"';
						if (isset($instance['layout'])) {
							selected($instance['layout'], $key);
						}
						echo '>' . $value . '</option>';
					}
					?>
				</select>
			</div>

			<div class="nt_row">
				<label for="<?php echo $this -> get_field_id('widget_color'); ?>"><?php _e('Color:'); ?></label>
				<select id="<?php echo $this -> get_field_id('widget_color'); ?>" name="<?php echo $this -> get_field_name('widget_color'); ?>">
					<?php
					foreach ($recommendations_colors as $key => $value) {
						echo '<option value="' . $key . '"';
						if (isset($instance['widget_color'])) {
							selected($instance['widget_color'], $key);
						}
						echo '>' . $value . '</option>';
					}
					?>
				</select>
			</div>
			<!-- image height and width future fixes-->
			<!--<div class="nt_row">
				<label for="<?php //echo $this -> get_field_id('image_width'); ?>"><?php// _e('Image width:'); ?></label>
				<input class="widefat" id="<?php //echo $this -> get_field_id('image_width'); ?>" name="<?php //echo $this -> get_field_name('image_width'); ?>" type="text" value="<?php
				/*
				if (isset($instance["image_width"])) {
					echo esc_attr($instance["image_width"]);
				} else {
					echo "220";
				}*/
 ?>" />
			</div>

			<div class="nt_row">
				<label for="<?php //echo $this -> get_field_id('image_height'); ?>"><?php //_e('Image height:'); ?></label>
				<input class="widefat" id="<?php //echo $this -> get_field_id('image_height'); ?>" name="<?php //echo $this -> get_field_name('image_height'); ?>" type="text" value="<?php

				/*if (isset($instance["image_height"])) {
					 echo esc_attr($instance["image_height"]);
				} else {
					 echo "140";
				}*/
 ?>" />
			</div>-->
		<?php
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance[ 'max_items' ] = $new_instance['max_items'];
		$instance['layout'] = $new_instance['layout'];
		$instance["widget_color"] = $new_instance["widget_color"];
		$instance["image_width"] = $new_instance["image_width"];
		$instance["image_height"] = $new_instance["image_height"];
		return $instance;
	}
}
?>