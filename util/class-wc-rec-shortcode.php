<?php
// Recommendations
function nt_rec_shortcode ( $atts ) {
	extract( shortcode_atts( array(
		"title" => "Recommended for you",
		"max_items" => "3",
		"layout" => "row_3",
		"image_width" => 220,
		"image_height" => 140,
		"widget_color" => plum
	), $atts ) );

	the_widget("Ntoklo_Recommendation_Widget", "title=${title}&layout=${layout}&image_width=${image_width}&image_height=${image_height}&widget_color=${widget_color}&max_items=${max_items}");
}
add_shortcode("ntoklo_recommendations", "nt_rec_shortcode");

?>