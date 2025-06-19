<?php
	
	$wp_host='localhost';
	$wp_user='root';
	$wp_pass='';
	$wp_name='WPDatabase';
		
	// login to MySQL Server from PHP
	$conn = mysqli_connect($wp_host, $wp_user, $wp_pass, $wp_name);
?>