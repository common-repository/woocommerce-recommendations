<?php

class Widget_Config{
	
	public static function nt_set_widget_configuration($instance, $chart_or_recommendation){
		
		if ( isset($instance["image_width"]) ) {
			$image_width 	= $instance["image_width"];
		} else {
			if ($chart_or_recommendation == "recommendation") {
				$image_width 	= 220;
			} else {
				$image_width 	= 100;
			}
		}

		if ( isset($instance["image_height"]) ) {
			$image_height 	= $instance["image_height"];
		} else {
			if ($chart_or_recommendation == "recommendation") {
				$image_height 	= 140;
			} else {
				$image_height 	= 100;
			}
		}

		if ( isset($instance["widget_color"]) ) {
			$widget_color 	= strtolower($instance["widget_color"]);
		} else {
			$widget_color 	= "plum";
		}

		if ( isset($instance["max_items"]) ) {
			$max_items 		= $instance["max_items"];
		} else {
			$max_items 		= 10;
		}

		return array(
			"image_height" 	=> $image_height,
			"image_width" 	=> $image_width,
			"widget_color" 	=> $widget_color,
			"max_items" 	=> $max_items
		);
	}
	
	
	public static function nt_recommendations_widget_layout_options(){
		
		return array(
				"row_3" 				=> "3 images in a single row",
				"row_4" 				=> "4 images in a single row",
				"column_image_above" 	=> "Single column (image above)",
				"column_image_right" 	=> "Single column (image at right)",
				"grid_2_column" 		=> "2-column grid",
				"grid_3_column" 		=> "3-column grid",
				"grid_4_column" 		=> "4-column grid"
			);
	}
	
	public static function nt_widget_colors(){
		
		return array(	
					"plum" => "Plum",
					"pink" => "Pink",
					"orange" => "Orange",
					"green" => "Green",
					"blue" => "Blue",
					"dark_blue" => "Dark Blue"
				);
	}
	
	
}

?>