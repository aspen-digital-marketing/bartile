<?php
/**
 * Template Name: Contractor Quiz
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
session_start();
$pageid=get_the_ID();
//$p = get_page($pageid);
$p = get_page($pageid);
get_header(); ?>
<section class="inner-banner-sec clearfix contractor" style="background: #e2e2e2 url(<?php echo get_field('inner_page_banner_image','options');?>) no-repeat right bottom;">
  <div class="container">

   <div class="inner-con">
      <h1 class="h1-custom-white"><?php echo get_the_title($pageid);?></h1>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="<?php echo site_url();?>">Home/</a></li>
		  <li class="breadcrumb-item"><a href="<?php echo site_url();?>/bartile-certified/">Bartile Certified /</a></li>
          <li class="breadcrumb-item active" aria-current="page"><?php echo get_the_title($pageid);?></li>
        </ol>
      </nav>
    </div>


  </div>
</section>

<section class="contact-us-wr c-application-page att contractor-quiz contractor-quiz_divider">
  <div class="container">
            <div class="c-heading-1">
                <?php echo apply_filters('the_content', $p->post_content);?>
            </div>
            <div class="Quiz_Text">
                <h4>Test Your Bartile Knowledge</h4>
                <p>Welcome to the Bartile Contractor Quiz! As a dedicated contractor, it's essential to stay updated and knowledgeable about Bartile products and practices. This quiz is designed to test your expertise and ensure you're at the forefront of our industry standards. Dive in and see how well you know Bartile!</p>
            </div>
           <form action="#" method="POST" enctype="multipart/form-data" id="fupForm2">
		     <input type="hidden" name="clientmail" value="" />
		     <!-- $_SESSION["clientmailaddress"];?>-->
                   <div class="Divide_row">
                       <div class="row">
                    <div class="col-md-12">
                         <div class="form-group">
                              <label>1. The term "Cottage" refers to the staggering of the tile.</label>
                              <div class="c-form-check"> 
                                 <div class="form-check form-check-inline">
                                  <input class="form-check-input" type="radio" name="ques1" id="inlineCheckbox5" required value="1">
                                  <label class="form-check-label" for="inlineCheckbox5">True</label>
                                </div>
								<div class="form-check form-check-inline">
                                  <input class="form-check-input" type="radio" name="ques1" id="inlineCheckbox5" required value="0">
                                  <label class="form-check-label" for="inlineCheckbox5">False</label>
                                </div>
                              </div>  
                         </div> 
                          
                    </div>
					<div class="col-md-12">
                         <div class="form-group">
                              <label>2. Legendary tile is designed to be laid without battens on pitches 12/12 or less.</label>
                              <div class="c-form-check"> 
                                 <div class="form-check form-check-inline">
                                  <input class="form-check-input" type="radio" name="ques2" id="inlineCheckbox5" required value="1">
                                  <label class="form-check-label" for="inlineCheckbox5">True</label>
                                </div>
								<div class="form-check form-check-inline">
                                  <input class="form-check-input" type="radio" name="ques2" id="inlineCheckbox5" required value="0">
                                  <label class="form-check-label" for="inlineCheckbox5">False</label>
                                </div>
                              </div>  
                         </div> 
                          
                    </div>
					<div class="col-md-12">
                         <div class="form-group">
                              <label>3. Rake metal, Solid Gable tile, LUR Rake and USR Rake are all types of gable finishes.</label>
                              <div class="c-form-check"> 
                                 <div class="form-check form-check-inline">
                                  <input class="form-check-input" type="radio" name="ques3" id="inlineCheckbox5" required value="1">
                                  <label class="form-check-label" for="inlineCheckbox5">True</label>
                                </div>
								<div class="form-check form-check-inline">
                                  <input class="form-check-input" type="radio" name="ques3" id="inlineCheckbox5" required value="0">
                                  <label class="form-check-label" for="inlineCheckbox5">False</label>
                                </div>
                              </div>  
                         </div> 
                          
                    </div>
					<div class="col-md-12">
                         <div class="form-group">
                              <label>4. There are 4 different widths of tile on a Yorkshire Cottage installation.</label>
                              <div class="c-form-check"> 
                                 <div class="form-check form-check-inline">
                                  <input class="form-check-input" type="radio" name="ques4" id="inlineCheckbox5" required value="1">
                                  <label class="form-check-label" for="inlineCheckbox5">True</label>
                                </div>
								<div class="form-check form-check-inline">
                                  <input class="form-check-input" type="radio" name="ques4" id="inlineCheckbox5" required value="0">
                                  <label class="form-check-label" for="inlineCheckbox5">False</label>
                                </div>
                              </div>  
                         </div> 
                          
                    </div>
					<div class="col-md-12">
                         <div class="form-group">
                              <label>5. Bartile offers tile in Standard Weight, Super Duty and Ultralite weights.</label>
                              <div class="c-form-check"> 
                                 <div class="form-check form-check-inline">
                                  <input class="form-check-input" type="radio" name="ques5" id="inlineCheckbox5" required value="1">
                                  <label class="form-check-label" for="inlineCheckbox5">True</label>
                                </div>
								<div class="form-check form-check-inline">
                                  <input class="form-check-input" type="radio" name="ques5" id="inlineCheckbox5" required value="0">
                                  <label class="form-check-label" for="inlineCheckbox5">False</label>
                                </div>
                              </div>  
                         </div> 
                          
                    </div>
					<div class="col-md-12">
                         <div class="form-group">
                              <label>6. Bartile provides a 75 Year Warranty.</label>
                              <div class="c-form-check"> 
                                 <div class="form-check form-check-inline">
                                  <input class="form-check-input" type="radio" name="ques6" id="inlineCheckbox5" required value="1">
                                  <label class="form-check-label" for="inlineCheckbox5">True</label>
                                </div>
								<div class="form-check form-check-inline">
                                  <input class="form-check-input" type="radio" name="ques6" id="inlineCheckbox5" required value="0">
                                  <label class="form-check-label" for="inlineCheckbox5">False</label>
                                </div>
                              </div>  
                         </div> 
                          
                    </div>
					<div class="col-md-12">
                         <div class="form-group">
                              <label>7. Bartile does not make custom orders and colors.</label>
                              <div class="c-form-check"> 
                                 <div class="form-check form-check-inline">
                                  <input class="form-check-input" type="radio" name="ques7" id="inlineCheckbox5" required value="1">
                                  <label class="form-check-label" for="inlineCheckbox5">True</label>
                                </div>
								<div class="form-check form-check-inline">
                                  <input class="form-check-input" type="radio" name="ques7" id="inlineCheckbox5" required value="0">
                                  <label class="form-check-label" for="inlineCheckbox5">False</label>
                                </div>
                              </div>  
                         </div> 
                          
                    </div>
					<div class="col-md-12">
                         <div class="form-group">
                              <label>8. Bartile makes an interlocking turret tile..</label>
                              <div class="c-form-check"> 
                                 <div class="form-check form-check-inline">
                                  <input class="form-check-input" type="radio" name="ques8" id="inlineCheckbox5" required value="1">
                                  <label class="form-check-label" for="inlineCheckbox5">True</label>
                                </div>
								<div class="form-check form-check-inline">
                                  <input class="form-check-input" type="radio" name="ques8" id="inlineCheckbox5" required value="0">
                                  <label class="form-check-label" for="inlineCheckbox5">False</label>
                                </div>
                              </div>  
                         </div> 
                          
                    </div>
					<div class="col-md-12">
                         <div class="form-group">
                              <label>9. Old World Vintage is 100 year old salvaged tile.</label>
                              <div class="c-form-check"> 
                                 <div class="form-check form-check-inline">
                                  <input class="form-check-input" type="radio" name="ques9" id="inlineCheckbox5" required value="1">
                                  <label class="form-check-label" for="inlineCheckbox5">True</label>
                                </div>
								<div class="form-check form-check-inline">
                                  <input class="form-check-input" type="radio" name="ques9" id="inlineCheckbox5" required value="0">
                                  <label class="form-check-label" for="inlineCheckbox5">False</label>
                                </div>
                              </div>  
                         </div> 
                          
                    </div>
                   </div>
                   
                  <div class="row ">
                      <div class="col-md-12">
                         <div class="form-group">
                              <label>10. Counter Batten and Ventilated Batten systems greatly prolong the life of a Bartile Roof.</label>
                              <div class="c-form-check"> 
                                 <div class="form-check form-check-inline">
                                  <input class="form-check-input" type="radio" name="ques10" id="inlineCheckbox5" required value="1">
                                  <label class="form-check-label" for="inlineCheckbox5">True</label>
                                </div>
								<div class="form-check form-check-inline">
                                  <input class="form-check-input" type="radio" name="ques10" id="inlineCheckbox5" required value="0">
                                  <label class="form-check-label" for="inlineCheckbox5">False</label>
                                </div>
                              </div>  
                         </div> 
                          
                    </div>
					<div class="col-md-12">
                         <div class="form-group">
                              <label>11. Pipe penetrations on a Bartile roof require only one pipe flashing.</label>
                              <div class="c-form-check"> 
                                 <div class="form-check form-check-inline">
                                  <input class="form-check-input" type="radio" name="ques11" id="inlineCheckbox5" required value="1">
                                  <label class="form-check-label" for="inlineCheckbox5">True</label>
                                </div>
								<div class="form-check form-check-inline">
                                  <input class="form-check-input" type="radio" name="ques11" id="inlineCheckbox5" required value="0">
                                  <label class="form-check-label" for="inlineCheckbox5">False</label>
                                </div>
                              </div>  
                         </div> 
                          
                    </div>
					<div class="col-md-12">
                         <div class="form-group">
                              <label>12. The USR round trim tile can be used on the hip, ridge and rake.</label>
                              <div class="c-form-check"> 
                                 <div class="form-check form-check-inline">
                                  <input class="form-check-input" type="radio" name="ques12" id="inlineCheckbox5" required value="1">
                                  <label class="form-check-label" for="inlineCheckbox5">True</label>
                                </div>
								<div class="form-check form-check-inline">
                                  <input class="form-check-input" type="radio" name="ques12" id="inlineCheckbox5" required value="0">
                                  <label class="form-check-label" for="inlineCheckbox5">False</label>
                                </div>
                              </div>  
                         </div> 
                          
                    </div>
					<div class="col-md-12">
                         <div class="form-group">
                              <label>13. Bartile makes a tile with simulated moss and lichen growth on it.</label>
                              <div class="c-form-check"> 
                                 <div class="form-check form-check-inline">
                                  <input class="form-check-input" type="radio" name="ques13" id="inlineCheckbox5" required value="1">
                                  <label class="form-check-label" for="inlineCheckbox5">True</label>
                                </div>
								<div class="form-check form-check-inline">
                                  <input class="form-check-input" type="radio" name="ques13" id="inlineCheckbox5" required value="0">
                                  <label class="form-check-label" for="inlineCheckbox5">False</label>
                                </div>
                              </div>  
                         </div> 
                          
                    </div>
					<div class="col-md-12">
                         <div class="form-group">
                              <label>14. Bartile makes a pan and cap turret tile for the Sierra Mission tile.</label>
                              <div class="c-form-check"> 
                                 <div class="form-check form-check-inline">
                                  <input class="form-check-input" type="radio" name="ques14" id="inlineCheckbox5" required value="1">
                                  <label class="form-check-label" for="inlineCheckbox5">True</label>
                                </div>
								<div class="form-check form-check-inline">
                                  <input class="form-check-input" type="radio" name="ques14" id="inlineCheckbox5" required value="0">
                                  <label class="form-check-label" for="inlineCheckbox5">False</label>
                                </div>
                              </div>  
                         </div> 
                          
                    </div>
					<div class="col-md-12">
                         <div class="form-group">
                              <label>15. Using 100% ice and water shield under a Bartile Roof will void its warranty.</label>
                              <div class="c-form-check"> 
                                 <div class="form-check form-check-inline">
                                  <input class="form-check-input" type="radio" name="ques15" id="inlineCheckbox5" required value="1">
                                  <label class="form-check-label" for="inlineCheckbox5">True</label>
                                </div>
								<div class="form-check form-check-inline">
                                  <input class="form-check-input" type="radio" name="ques15" id="inlineCheckbox5" required value="0">
                                  <label class="form-check-label" for="inlineCheckbox5">False</label>
                                </div>
                              </div>  
                         </div> 
                          
                    </div>
					<div class="col-md-12">
                         <div class="form-group">
                              <label>16. Bartile can fabricate any of the discontinued tiles they have made since 1942.</label>
                              <div class="c-form-check"> 
                                 <div class="form-check form-check-inline">
                                  <input class="form-check-input" type="radio" name="ques16" id="inlineCheckbox5" required value="1">
                                  <label class="form-check-label" for="inlineCheckbox5">True</label>
                                </div>
								<div class="form-check form-check-inline">
                                  <input class="form-check-input" type="radio" name="ques16" id="inlineCheckbox5" required value="0">
                                  <label class="form-check-label" for="inlineCheckbox5">False</label>
                                </div>
                              </div>  
                         </div> 
                          
                    </div>
					<div class="col-md-12">
                         <div class="form-group">
                              <label>17. Steep Ridges can be used on pitches 12/12 or higher.</label>
                              <div class="c-form-check"> 
                                 <div class="form-check form-check-inline">
                                  <input class="form-check-input" type="radio" name="ques17" id="inlineCheckbox5" required value="1">
                                  <label class="form-check-label" for="inlineCheckbox5">True</label>
                                </div>
								<div class="form-check form-check-inline">
                                  <input class="form-check-input" type="radio" name="ques17" id="inlineCheckbox5" required value="0">
                                  <label class="form-check-label" for="inlineCheckbox5">False</label>
                                </div>
                              </div>  
                         </div> 
                          
                    </div>
					<div class="col-md-12">
                         <div class="form-group">
                              <label>18. Tile Risers and cover metal are types of eave edge details.</label>
                              <div class="c-form-check"> 
                                 <div class="form-check form-check-inline">
                                  <input class="form-check-input" type="radio" name="ques18" id="inlineCheckbox5" required value="1">
                                  <label class="form-check-label" for="inlineCheckbox5">True</label>
                                </div>
								<div class="form-check form-check-inline">
                                  <input class="form-check-input" type="radio" name="ques18" id="inlineCheckbox5" required value="0">
                                  <label class="form-check-label" for="inlineCheckbox5">False</label>
                                </div>
                              </div>  
                         </div> 
                          
                    </div>
					<div class="col-md-12">
                         <div class="form-group">
                              <label>19. Bartile is a family owned company started in 1942.</label>
                              <div class="c-form-check"> 
                                 <div class="form-check form-check-inline">
                                  <input class="form-check-input" type="radio" name="ques19" id="inlineCheckbox5" required value="1">
                                  <label class="form-check-label" for="inlineCheckbox5">True</label>
                                </div>
								<div class="form-check form-check-inline">
                                  <input class="form-check-input" type="radio" name="ques19" id="inlineCheckbox5" required value="0">
                                  <label class="form-check-label" for="inlineCheckbox5">False</label>
                                </div>
                              </div>  
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
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<script>
jQuery(document).ready(function(e){
    // Submit form data via Ajax
    jQuery("#fupForm2").on('submit', function(e){
        e.preventDefault();
        jQuery.ajax({
            type: 'POST',
            url: '<?php echo get_template_directory_uri(); ?>/quizsubmit.php',
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
					window.setTimeout(function() {
						window.location.href = 'https://bartile.com/bartile-certified/';
					}, 3000);
					//window.location.replace("https://bartile.com/bartile-certified/contractor-quiz/");
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