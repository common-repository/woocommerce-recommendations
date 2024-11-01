<?php
add_action('init', 'setVisitorId');
function setVisitorId(){

	$visitor_id = uniqid('nt_');
    $expiretime = time()+3600*24*30;
	setcookie('visitor_id', $visitor_id, $expiretime, '/');
}
?>