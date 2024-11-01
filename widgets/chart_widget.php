<?php
/* "Copyright 2013 nToklo.com" This software is distributed under the terms of GNU GENERAL PUBLIC LICENSE Version 3, 29 June 2007 */
/**
 * Create WOO Chart Widget
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
add_action( 'wp_head', 'add_styles' );
add_action("admin_init", "add_styles");
function add_styles() {
	wp_enqueue_style("widget_style", WOORMD_CSS_URL . "/widget.css");
	wp_enqueue_style("settings_style", WOORMD_CSS_URL . "/settings.css");
}

add_action( 'widgets_init', create_function( '', 'return register_widget("Ntoklo_Chart_Widget");' ) );

class Ntoklo_Chart_Widget extends WP_Widget {
	public $event_get;

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		$this->event_get = new Event_Get();
		parent::__construct(
			'chart_widget', // Base ID
			__('WooCommerce Chart', 'text_domain'), // Name
			array( 'description' => __( 'A chart for your WooCommerce store ', 'text_domain' ), ) // Args
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

		$title = apply_filters( 'widget_title', $instance['title'] );
		//$max_items = $instance['max_items'];

		if ( isset( $instance[ "max_items" ] ) ) {
			$max_items = $instance[ "max_items" ];
		} else {
			$max_items = 10;
		}

		if ( isset( $instance[ "tw" ] ) ) {
			$tw = $instance["tw"];
		} else {
			$tw = "DAILY";
		}

		$response = $this->event_get->nt_fetch_chart($max_items, $tw);
		$chart_tracker_id =  $response->tracker_id;

		if($_GET['nt_chrt']){
			$chart_object = array(
				"category" => 'clickthrough_goals',
				"action" => 'chart-click',
				"widget_style" => 'chart',
	           	"tracker_id" => $_GET['nt_chrt']
			);
		}else{
			if($chart_tracker_id != null){
				$chart_object = array(
					"category" => 'clickthrough_goals',
					"action" => 'chart-impr',
					"widget_style" => 'chart',
		           	"tracker_id" => $chart_tracker_id
				);
			}
		}
		$widget_options = Widget_Config::nt_set_widget_configuration($instance, "chart");
		?>

		<script type="text/javascript">
				var chart_object = <?=json_encode($chart_object);?>
		</script>

		<?php
		if(is_shop() || is_product() || is_product_category() || is_checkout()){
		?>
			<script type="text/javascript" src="<?php echo WOORMD_JS_URL?>/chart_uv.js"></script>
		<?php
		}

		if( $response != 401 && $response != 500 && !empty($response->items) ){
			update_option("response", true);
		?>
			<div class="nt_wrapper nt_chart nt_<?=$widget_options["widget_color"]?>">
				<p class="nt_header"><?=$title?></p>
				<table cellspacing="0" class="nt_widget">
					<tbody>
				<?php
					foreach ($response->items as $key => $value) {
				?>
						<tr>
						<td class="nt_peak_time_wrapper">
							<table cellspacing="2" class="nt_item_info">
								<tbody>
									<tr>
										<td class="nt_position" rowspan="2"><?=$value->currentPosition?></td>
										<td class="nt_peak" title="Peak position"><?=$value->peakPosition?></td>
									</tr>
									<tr>
										<td class="nt_time"><?=$value->timesOnChart;?></td>
									</tr>
								</tbody>
							</table>
						</td>

						<td class="nt_table_item">
							<a href="<?php echo $value->product->url . '?nt_chrt';?>=<?=$chart_tracker_id?>"><?php echo $value->product->name; ?></a>
						</td>
						<td class="nt_img_wrap">
							<img src="<?php echo $value->product->image_url; ?>" alt="<?php echo $value->product->name; ?>" />
						</td>
						</tr>
				<?php
					}
				?>
					</tbody>
					<tfoot>
						<tr>
							<td class="nt_peak_time_wrapper" data-title="nt_peak">
								<table cellspacing="3" class="nt_item_info">
									<tbody>
										<tr>
											<td class="nt_position" rowspan="2">#</td>
											<td class="nt_peak">
												<span class="nt_table_icon" title="Peak position">
													<svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="14px" height="14px" viewBox="3 3 14 14" enable-background="new 3 3 14 14" xml:space="preserve">
														<path d="M15.635,5.865c0.125-0.172,0.188-0.156,0.188,0.046v9.022H4.499c-0.095,0-0.154-0.028-0.176-0.082c-0.024-0.055-0.004-0.121,0.059-0.2l2.702-3.384c0.156-0.171,0.313-0.179,0.47-0.021l0.869,0.773c0.079,0.063,0.16,0.091,0.247,0.083c0.086-0.009,0.153-0.051,0.199-0.13l1.855-2.796c0.125-0.203,0.274-0.219,0.447-0.047l1.315,1.223c0.156,0.156,0.305,0.14,0.445-0.047L15.635,5.865z"/>
														<path d="M23.018,7.037c0.915-0.914,2.015-1.371,3.303-1.371c1.287,0,2.389,0.458,3.302,1.371c0.914,0.916,1.372,2.016,1.372,3.303c0,1.287-0.458,2.388-1.372,3.304c-0.914,0.914-2.015,1.37-3.302,1.37c-1.288,0-2.389-0.456-3.303-1.37c-0.915-0.916-1.372-2.017-1.372-3.304C21.646,9.054,22.103,7.953,23.018,7.037z M26.32,13.998c1.016,0,1.88-0.358,2.591-1.077c0.711-0.717,1.067-1.577,1.067-2.58c0-1.017-0.356-1.879-1.067-2.59C28.2,7.038,27.337,6.683,26.32,6.683c-1.003,0-1.864,0.355-2.581,1.067c-0.717,0.711-1.077,1.574-1.077,2.59c0,1.003,0.36,1.863,1.077,2.58C24.456,13.64,25.317,13.998,26.32,13.998z M26.686,7.699v2.479l1.524,1.525l-0.508,0.507l-1.728-1.727V7.699H26.686z"/>
													</svg>
												</span>
											</td>
										</tr>
										<tr>
											<td class="nt_time">
												<span class="nt_table_icon" title="Time on chart">
													<svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="14px" height="14px" viewBox="3 3 14 14" enable-background="new 3 3 14 14" xml:space="preserve">
														<path d="M-0.74,5.49c0.125-0.172,0.188-0.156,0.188,0.046v9.022h-11.324c-0.095,0-0.154-0.028-0.176-0.082c-0.024-0.055-0.004-0.121,0.059-0.2l2.702-3.384c0.156-0.171,0.313-0.179,0.47-0.021l0.869,0.773c0.079,0.063,0.16,0.091,0.247,0.083c0.086-0.009,0.153-0.051,0.199-0.13l1.855-2.796C-5.526,8.6-5.377,8.583-5.204,8.755l1.315,1.223c0.156,0.156,0.305,0.14,0.445-0.047L-0.74,5.49z"/>
														<path d="M6.643,6.662c0.915-0.914,2.015-1.371,3.303-1.371c1.287,0,2.389,0.458,3.302,1.371c0.914,0.916,1.372,2.016,1.372,3.303c0,1.287-0.458,2.388-1.372,3.304c-0.914,0.914-2.015,1.37-3.302,1.37c-1.288,0-2.389-0.456-3.303-1.37c-0.915-0.916-1.372-2.017-1.372-3.304C5.271,8.679,5.728,7.578,6.643,6.662z M9.945,13.623c1.016,0,1.88-0.358,2.591-1.077c0.711-0.717,1.067-1.577,1.067-2.58c0-1.017-0.356-1.879-1.067-2.59c-0.711-0.712-1.574-1.067-2.591-1.067c-1.003,0-1.864,0.355-2.581,1.067c-0.717,0.711-1.077,1.574-1.077,2.59c0,1.003,0.36,1.863,1.077,2.58C8.081,13.265,8.942,13.623,9.945,13.623z M10.311,7.324v2.479l1.524,1.525l-0.508,0.507L9.6,10.108V7.324H10.311z"/>
													</svg>
												</span>
											</td>
										</tr>
									</tbody>
								</table>
							</td>
							<td class="nt_table_item" colspan="2"></td>
						</tr>
					</tfoot>
				</table>
			</div>
	<?php
		//echo $args['after_widget'];
		}else{

			update_option("response", false);
		}
	}
	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		$colors = Widget_Config::nt_widget_colors();

		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}
		else {
			$title = __( 'nToklo chart', 'text_domain' );
		}

		if ( isset( $instance[ 'max_items' ] ) ) {
			$max_items = $instance[ 'max_items' ];
		} else {
			$max_items = 9;
		}

		?>

		<div class="nt_row">
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</div>

		<div class="nt_row">
			<label for="<?php echo $this -> get_field_id('maxItems'); ?>"><?php _e('Max items: (must be less than 100)'); ?></label>
			<input class="widefat" id="<?php echo $this -> get_field_id('max_items'); ?>" name="<?php echo $this -> get_field_name('max_items'); ?>" type="text" value="<?php echo esc_attr($max_items); ?>" />
		</div>

		<div class="nt_row">
			<label for="<?php echo $this -> get_field_id('tw'); ?>"><?php _e('Type:'); ?></label>
			<select id="<?php echo $this -> get_field_id('tw'); ?>" name="<?php echo $this -> get_field_name('tw'); ?>">
				<option value="DAILY" <?php if (isset($instance["tw"])) { selected($instance['tw'], 'DAILY');} ?>>Daily</option>
				<option value="WEEKLY" <?php if (isset($instance["tw"])) { selected($instance['tw'], 'WEEKLY');} ?>>Weekly</option>
			</select>
		</div>

		<div class="nt_row">
			<label for="<?php echo $this -> get_field_id('widget_color'); ?>"><?php _e('Color:'); ?></label>
			<select id="<?php echo $this -> get_field_id('widget_color'); ?>" name="<?php echo $this -> get_field_name('widget_color'); ?>">
			<?php
				foreach ($colors as $key => $value) {
			?>
					<option value=<?=$key?>
			<?php
					if (isset($instance['widget_color'])) {
						selected($instance['widget_color'], $key);
					}
			?>
					><?=$value?> </option>
			<?php
				}
			?>
			</select>
		</div>
		<!-- image height and width future fixes-->
		<!--<div class="nt_row">
			<label for="<?php //echo $this -> get_field_id('image_width'); ?>"><?php //_e('Image width:'); ?></label>
			<input class="widefat" id="<?php //echo $this -> get_field_id('image_width'); ?>" name="<?php //echo $this -> get_field_name('image_width'); ?>" type="text" value="<?php
				/*if (isset($instance["image_width"])) {
					 echo esc_attr($instance["image_width"]);
				} else {
					 echo "100";
				}*/
				 ?>" />
		</div>

		<div class="nt_row">
			<label for="<?php //echo $this -> get_field_id('image_height'); ?>"><?php //_e('Image height:'); ?></label>
			<input class="widefat" id="<?php //echo $this -> get_field_id('image_height'); ?>" name="<?php //echo $this -> get_field_name('image_height'); ?>" type="text" value="<?php
				/*if (isset($instance["image_height"])) {
					 echo esc_attr($instance["image_height"]);
				} else {
					 echo "100";
				}
 				*/?>" />
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
		$max_items = ( ! empty( $new_instance["max_items"] ) ) ? strip_tags( $new_instance["max_items"] ) : "";
		if (intval($max_items) != 0) {
			if (intval($max_items) > 100) {
				$instance["max_items"] = 100;
			} else {
				$instance["max_items"] = intval($max_items);
			}
		} else {
			$instance["max_items"] = 10;
		}

		$instance["tw"] = $new_instance["tw"];
		$instance["widget_color"] = $new_instance["widget_color"];
		$instance["image_width"] = $new_instance["image_width"];
		$instance["image_height"] = $new_instance["image_height"];
		return $instance;
	}
}

function nt_chart_widget_colours(){
	return array(	"plum" => "Plum",
					"pink" => "Pink",
					"orange" => "Orange",
					"green" => "Green",
					"blue" => "Blue",
					"dark_blue" => "Dark Blue"
				);
}
?>