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
error_reporting(0);
require_once ($_SERVER['DOCUMENT_ROOT'].'/wp-load.php');
global $wpdb;
if(isset($_POST['update_status'])){
 $tablename = 'wp_contractor';
 $wpdb->update($tablename, array( 'status' => $_POST['update_status'] ) , array( 'id' => $_POST['rowid'] ) );
}	
if(isset($_POST['deleteid'])){
	$tablename = 'wp_contractor';
	$wpdb->delete($tablename, array( 'id' => $_POST['deleteid'] ) );
}
$url='http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
if(isset($_GET['detailsview'])){?>
<h3>Contractor Details</h3>	
<div class="container-fluid">
  <div class="row">
    <div class="col-sm-12">
      <table class="table" id="">
        <tbody>
         <?php $post_id = $wpdb->get_results("SELECT * FROM wp_contractor WHERE id='".$_GET['detailsview']."'" ); ?>
         <?php 
		 $i=1;
		 foreach($post_id as $post) { ?>
         <tr>
		    <td>Date</td>
            <td><?php echo $post->submitedate; ?> </td>
		 </tr><tr>
			<td>Company Name</td>
            <td><?php echo $post->companyname; ?> </td>
		 </tr><tr>	
			<td>Primary Contact Name</td>
            <td><?php echo $post->pcontactnumber; ?> </td>
		 </tr><tr>	
			 <td>License Number</td>
            <td> <?php echo $post->contractorstatelno; ?></td>
	     </tr><tr>		
			<td>Address 1</td>
            <td><?php echo $post->address1; ?></td>
		 </tr><tr>	
			 <td>Address 2</td>
             <td><?php echo $post->address2; ?></td>
		 </tr><tr>	 
			 <td>City</td>
            <td><?php echo $post->city; ?></td>
		 </tr><tr>	
			<td>State</td>
            <td><?php echo $post->state; ?></td>
		 </tr><tr>	
			 <td>Zip Code</td>
            <td><?php echo $post->zipcode; ?></td>
		 </tr><tr>	
			<td>Phone</td>
            <td><?php echo $post->phone; ?></td>
		 </tr><tr>	
            <td>Email</td>
            <td><?php echo $post->emailaddress; ?></td>
		 </tr><tr>	
            <td>Website</td>
			<td><?php echo $post->website; ?> </td>
		 </tr><tr>	
			<td>How many employees do you have?</td>
            <td><?php echo $post->howmanyemp; ?> </td>
		 </tr><tr>	
            <td>What are your estimated annual roofing sales?</td>
		 
            <td><?php echo $post->estimateanroof; ?> </td>
		</tr><tr>		
			<td>Services Provided (select all tdat apply)</td>
			
            <td> <?php echo $post->servicveprovider; ?></td>
		 </tr><tr>	
            <td>Years in Business*</td>
            <td><?php echo $post->yesrbusiness; ?></td>
		</tr><tr>		
			 <td>How many tiled roofs completed per year?</td>
             <td><?php echo $post->roofcomepleteperyear; ?></td>
		</tr><tr>		 
            <td>How many commercial roofs completed per year?</td>
            <td><?php echo $post->commercialroofcomplete; ?></td>
		</tr><tr>		
			 <td>Do you agree to our Code of Etdics?</td>
            <td><?php echo $post->agreeourcode; ?></td>
		</tr><tr>		
			 <td>Company Logo</td>
            <td><img src="<?php echo get_template_directory_uri(); ?>/uploads/<?php echo $post->companylogo;?>" alt="" style="width:200px;"></td>
		</tr><tr>		
			 <td>Sample Images1</td>
            <td><img src="<?php echo get_template_directory_uri(); ?>/uploads/<?php echo $post->sampleimage1;?>" alt="" style="width:200px;"></td>
		</tr><tr>		
			<td>Sample Images2</td>
            <td><img src="<?php echo get_template_directory_uri(); ?>/uploads/<?php echo $post->sampleimage2;?>" alt="" style="width:200px;"/></td>
		</tr><tr>		
			<td>Sample Images3</td>
			<td><img src="<?php echo get_template_directory_uri(); ?>/uploads/<?php echo $post->sampleimage3;?>" alt="" style="width:200px;"/></td>
		</tr><tr>		
			 <td>What differentiates your from your competitors?</td>
            <td><?php echo $post->whatdifferentiates; ?></td>
		</tr><tr>		
			<td>Testimonials</td>
            <td><?php echo $post->testimonials; ?></td>
		</tr><tr>	
			<td>Status</td>
			<td><form method="post"><input type="hidden" name="rowid" value="<?php echo $post->id;?>" /><select name="update_status" onchange="this.form.submit()"><option value="0" <?php if($post->status == 0){ ?> selected <?php } ?>>Inactive</option><option value="1" <?php if($post->status == 1){ ?> selected <?php } ?>>Active</option></select> </form></td>
          </tr>
          <?php $i++;} ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
</div>

<?php }else{	
?>
<h3>Contractor List</h3>
<div class="container-fluid">
  <div class="row">
    <div class="col-sm-12">
      <table class="table" id="example">
        <thead>
          <tr>
            <th>Id</th>
            <th>Date</th>
            <th>Company Name</th>
            <th>Contact Name</th>
			<th>Contact Email</th>
            <th>State</th>
			<th>Quiz Result</th>
            <th>Status</th>
		    <th>&nbsp;</th>
			<th>&nbsp;</th>
			<th>&nbsp;</th>
          </tr>
        </thead>
        <tbody>
         <?php $post_id = $wpdb->get_results("SELECT * FROM wp_contractor ORDER BY id DESC" ); ?>
         <?php 
		 $i=1;
		 foreach($post_id as $post) { 
		 $result = $wpdb->get_var( "SELECT result from wp_quiz where email = '".$post->emailaddress."'" );
		 ?>
           <tr data-toggle="collapse" data-target=".demo<?php echo $i; ?>">
            <td><?php echo $i; ?></td>
            <td><?php echo $post->submitedate; ?></td>
            <td><?php echo $post->companyname; ?></td>
            <td><?php echo $post->pcontactnumber; ?></td>
			<td><?php echo $post->emailaddress; ?></td>
            <td><?php echo $post->state; ?></td>
			<td><?php if($result > 14){?><span style="color:green;font-weight:bold;">Pass</span><?php } else{?><span style="color:red;font-weight:bold;">Fail</span><?php } ?></td>
			<td><form method="post"><input type="hidden" name="rowid" value="<?php echo $post->id;?>" /><select name="update_status" onchange="this.form.submit()"><option value="0" <?php if($post->status == 0){ ?> selected <?php } ?>>Inactive</option><option value="1" <?php if($post->status == 1){ ?> selected <?php } ?>>Active</option></select> </form></td>
			<td><form method="post" action=""><input type="hidden" name="deleteid" value="<?php echo $post->id; ?>" /><input type="submit" class="btn btn-danger" value="Delete"/></form>&nbsp;</td><td><a href="<?php echo site_url();?>/wp-admin/admin.php?page=orders&detailsview=<?php echo $post->id;?>" class="btn btn-primary">View Details</a></td><td><a href="<?php echo site_url();?>/update-contactor-details?contractor_id=<?php echo $post->id;?>" class="btn btn-primary" target="_blank">Edit</a></td>
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
<?php } ?>
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




   



    

