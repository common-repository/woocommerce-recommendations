<?php
function nt_chart_shortcode ( $atts ) {
	extract( shortcode_atts( array(
		"title" => "Best sellers",
		"max_items" => "10",
		"image_width" => 100,
		"image_height" => 100,
		"widget_color" => plum,
		"tw" => "DAILY"
	), $atts ) );

	the_widget("Ntoklo_Chart_Widget", "title=${title}&image_width=${image_width}&image_height=${image_height}&widget_color=${widget_color}&max_items=${max_items}&tw=${tw}");
}
add_shortcode("ntoklo_chart", "nt_chart_shortcode");
?>