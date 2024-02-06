<?php
/**
 * Template Name: Warranty
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
$pageid=get_the_ID();
$p = get_page($pageid);

get_header(); ?>
	<section class="inner-banner-sec clearfix contractor" style="background: #e2e2e2 url(<?php echo get_field('inner_page_banner_image','options');?>) no-repeat right bottom;">
  <div class="container">

   <div class="inner-con">
      <h1 class="h1-custom-white"><?php echo get_the_title($pageid);?></h1>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="<?php echo site_url();?>">Home/</a></li>
          <li class="breadcrumb-item active" aria-current="page"><?php echo get_the_title($pageid);?></li>
        </ol>
      </nav>
    </div>


  </div>
</section>
<section class="contact-us-wr c-application-page warranty-quiz">
  <div class="container">
         
           <form class="warranty-wrapper" id="fupForm2">

              <div class="Diveder">
                   <div class="row">
                    
                    <div class="col-md-4">
                       
                         <h3>Type of Tile </h3>
                         <div class="form-group">
                              <input type="text" name="typeoftitle" placeholder="Type of Tile" class="c-form-control">
                         </div> 
                    </div>
                    <div class="col-md-4">
                         <h3>Date of Purchase</h3>
                      <div class="form-group">
                              <input type="text" name="dateofpuchase" placeholder="Date of Purchase" class="c-form-control datepicker">
                         </div> 
                          
                    </div>
                     <div class="col-md-4">
                      <h3>Email Address</h3>
                      <div class="form-group">
                              <input type="email" name="emailaddress" placeholder="Email Address" class="c-form-control">
                         </div> 
                          
                    </div>
                    
                    
                    
                </div>
                <div class="row">
                    <div class="col-md-4">
                         <h3>Address of Project</h3>
                         <div class="form-group">
                              <input type="text" name="addressofproject" placeholder="Address of Project" class="c-form-control">
                         </div> 
                    </div>
                    <div class="col-md-4 Checkbox">
					<h3>Mailing address different?</h3>
					<div class="form-group">
                      <label class="checkbox-inline">
					    <input type="checkbox" value="1" id="mailingaddressifdifferent" name="mailingaddressifdifferent">
					</label>
                     </div>     
                    </div>
                    <div class="col-md-4 displayoptionscontrol">
					<h3>Mailing address</h3>
					<div class="form-group">
                      <input type="text" name="mailingaddress" placeholder="Mailing address" class="c-form-control">
                     </div>     
                    </div>
                    
         
                    
                    
                    
                </div>
              </div>
               <div class="row">
				    <div class="col-md-6">
					 <div class="pass_error"></div>
					</div>
                     <div class="col-md-12 text-right">
                          <input type="submit" class="custom-button" name="submitbtn" id="submitbtn" value="SUBMIT" />
                     </div>
				   </div>
               







              
        </form>
</div>
</section>
<script>
jQuery('.displayoptionscontrol').hide();
jQuery('input[type="checkbox"]').click(function(){
            if(jQuery(this).prop("checked") == true){
                jQuery('.displayoptionscontrol').show();
            }
            else if(jQuery(this).prop("checked") == false){
                jQuery('.displayoptionscontrol').hide();
            }
        });
</script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<script>
jQuery(document).ready(function(e){
    // Submit form data via Ajax
    jQuery("#fupForm2").on('submit', function(e){
        e.preventDefault();
        jQuery.ajax({
            type: 'POST',
            url: '<?php echo get_template_directory_uri(); ?>/warrentysubmit.php',
            data: new FormData(this),
            dataType: 'json',
            contentType: false,
            cache: false,
            processData:false,
			 beforeSend: function(){
                jQuery('#submitbtn').attr("disabled","disabled");
                jQuery('#fupForm2').css("opacity",".5");
            },
            success: function(response){ 
			console.log(response);
                jQuery('.pass_error').html('');
                if(response.status == 1){
                    jQuery('#fupForm2')[0].reset();
                    jQuery('.pass_error').html('<span class="alert alert-success" style="color:green;">'+response.message+'</span>');
                }else{
                    jQuery('.pass_error').html('<span class="alert alert-danger" style="color:red;">'+response.message+'</span>');
                }
                jQuery('#fupForm2').css("opacity","");
               jQuery("#submitbtn").removeAttr("disabled");
            }
        });
    });
});
</script>
<?php get_footer(); ?> 