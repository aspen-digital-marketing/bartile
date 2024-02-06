<?php 
     wp_enqueue_style ('admin-style1','https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css');
    wp_enqueue_script( 'script-name1', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js');
   // wp_enqueue_script( 'script-name2', 'http://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js');
   wp_enqueue_script( 'script-name4', 'https://code.jquery.com/jquery-3.5.1.js');
   wp_enqueue_script( 'script-name3', 'https://cdn.datatables.net/1.10.23/js/jquery.dataTables.min.js');
   //wp_enqueue_script( 'script-name1', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js');
    wp_enqueue_style ('admin-style2','https://cdn.datatables.net/1.10.23/css/jquery.dataTables.min.css');
?>

    
<?php 
error_reporting(1);
require_once ($_SERVER['DOCUMENT_ROOT'].'/wp-load.php');
global $wpdb;	
if(isset($_POST['deleteid'])){
	$tablename = 'wp_quiz';
	$wpdb->delete($tablename, array( 'id' => $_POST['deleteid'] ) );
}
$url='http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];?>
<h3>Quiz Answer List</h3>
<div class="container-fluid">
  <div class="row">
    <div class="col-sm-12">
      <table class="table" id="example">
        <thead>
          <tr>
            <th>Id</th>
			<th>Email</th>
            <th>Q1</th>
            <th>Q2</th>
            <th>Q3</th>
            <th>Q4</th>
			<th>Q5</th>
            <th>Q6</th>
			<th>Q7</th>
            <th>Q8</th>
			<th>Q9</th>
            <th>Q10</th>
			<th>Q11</th>
            <th>Q12</th>
			<th>Q13</th>
            <th>Q14</th>
			<th>Q15</th>
            <th>Q16</th>
			<th>Q17</th>
            <th>Q18</th>
			<th>Q19</th>
			<th>&nbsp;</th>
          </tr>
        </thead>
        <tbody>
         <?php $post_id = $wpdb->get_results("SELECT * FROM wp_quiz ORDER BY id DESC" ); ?>
         <?php 
		 $i=1;
		 foreach($post_id as $post) { ?>
           <tr>
            <td><?php echo $i; ?></td>
			<td><?php echo $post->email; ?></td>
            <td><?php if($post->ques1 == 1){?>TRUE<?php } else{?>FALSE<?php } ?></td>
            <td><?php if($post->ques2 == 1){?>TRUE<?php } else{?>FALSE<?php } ?></td>
			<td><?php if($post->ques3 == 1){?>TRUE<?php } else{?>FALSE<?php } ?></td>
			<td><?php if($post->ques4 == 1){?>TRUE<?php } else{?>FALSE<?php } ?></td>
			<td><?php if($post->ques5 == 1){?>TRUE<?php } else{?>FALSE<?php } ?></td>
			<td><?php if($post->ques6 == 1){?>TRUE<?php } else{?>FALSE<?php } ?></td>
			<td><?php if($post->ques7 == 1){?>TRUE<?php } else{?>FALSE<?php } ?></td>
			<td><?php if($post->ques8 == 1){?>TRUE<?php } else{?>FALSE<?php } ?></td>
			<td><?php if($post->ques9 == 1){?>TRUE<?php } else{?>FALSE<?php } ?></td>
			<td><?php if($post->ques10 == 1){?>TRUE<?php } else{?>FALSE<?php } ?></td>
			<td><?php if($post->ques11 == 1){?>TRUE<?php } else{?>FALSE<?php } ?></td>
			<td><?php if($post->ques12 == 1){?>TRUE<?php } else{?>FALSE<?php } ?></td>
			<td><?php if($post->ques13 == 1){?>TRUE<?php } else{?>FALSE<?php } ?></td>
			<td><?php if($post->ques14 == 1){?>TRUE<?php } else{?>FALSE<?php } ?></td>
			<td><?php if($post->ques15 == 1){?>TRUE<?php } else{?>FALSE<?php } ?></td>
			<td><?php if($post->ques16 == 1){?>TRUE<?php } else{?>FALSE<?php } ?></td>
			<td><?php if($post->ques17 == 1){?>TRUE<?php } else{?>FALSE<?php } ?></td>
			<td><?php if($post->ques18 == 1){?>TRUE<?php } else{?>FALSE<?php } ?></td>
			<td><?php if($post->ques19 == 1){?>TRUE<?php } else{?>FALSE<?php } ?></td>
			<td><form method="post" action=""><input type="hidden" name="deleteid" value="<?php echo $post->id; ?>" /><input type="submit" class="btn btn-danger" value="Delete"/></form></td>
        </tr>
          <?php $i++;} ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
</div>
<script>
jQuery(document).ready(function() {
    jQuery('#example').DataTable();
} );
</script>
<style>
#footer-thankyou{
	display:none !important;
}
</style>
<?php



 /*?>function runMyFunction($id){
	global $wpdb;
	require_once ($_SERVER['DOCUMENT_ROOT'].'/trashbros/wp-load.php');
	$tablename = $wpdb->prefix.'quote';
	$wpdb->delete($tablename, array( 'id' => $id ) );
	//$wpdb->query( 'DELETE FROM '.$tablename.' WHERE id="'.$id.'"');
	//header('Location:'.$url.'?page=orders');
	wp_redirect(admin_url('/admin.php?page=orders'));
	}
if (isset($_GET['hello'])) { //if value of hello is true then enter this loop otherwise not
    runMyFunction($_GET['id']); //call the function runMyFunction
  }<?php */?>	




   



    

