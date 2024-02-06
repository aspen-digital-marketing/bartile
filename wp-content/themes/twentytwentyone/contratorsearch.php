<?php 
include('../../../wp-load.php');
global $wpdb;
if(isset($_POST['contractorState'])){
	$state=$_POST['contractorState'];
	$results = $wpdb->get_results( "SELECT * FROM wp_contractor WHERE state='".$state."' AND status='1'", OBJECT );
}
echo json_encode($results);