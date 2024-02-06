<?php
/**
 * Template Name: Contractor Details
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
$pageid=get_the_ID();
//$p = get_page($pageid);
$p = get_page($pageid);
$rowid=$_GET['contractor_id'];
if(!isset($rowid)){
	header("Location: ".site_url()."/bartile-certified/contractor-search/");
    exit;
}
$results = $wpdb->get_results( "SELECT * FROM wp_contractor WHERE id='".$rowid."' AND status='1'", OBJECT );
$result=$results[0];
get_header(); ?>
<section class="inner-banner-sec  clearfix contractor" style="background: #e2e2e2 url(<?php echo get_field('inner_page_banner_image','options');?>) no-repeat right bottom;">
  <div class="container">

   <div class="inner-con">
      <h1 class="h1-custom-white"><?php echo $result->companyname;?></h1>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="<?php echo site_url();?>">Home/</a></li>
		  <li class="breadcrumb-item"><a href="<?php echo site_url();?>/bartile-certified/contractor-search/">Contractor Search/</a></li>
          <li class="breadcrumb-item active" aria-current="page"><?php echo $result->companyname;?></li>
        </ol>
      </nav>
    </div>


  </div>
</section>
<section class="contractor-details-pw contractor_list ">
      <div class="container">
          <div class="row"> 
			  <div class="col-lg-12 col-md-12 col-sm-12">
				  <div class="contractor-details-sidebar">
					  <a href="<?php echo $result->website;?>" target="_blank">
						  <img src="<?php echo get_template_directory_uri(); ?>/uploads/<?php echo $result->companylogo;?>" alt="">
					  </a>
                   </div>
			  </div>
		  </div>
		  <div class="row contractor_listrow"> 
			  <div class="col-lg-5 col-md-5 col-sm-12">				  
				   <div class="contractor-details-list">
                        <ul>
                            <li>Date #:  <strong><?php echo $result->submitedate;?></strong> </li>
                            <li>Company Name: <strong><?php echo $result->companyname;?></strong> </li>
                            <li>Contact Name:  <strong><?php echo $result->pcontactnumber;?></strong> </li>
							<li>Contractor #:  <strong><?php echo $result->contractorstatelno;?></strong> </li>
							<li>Address:  <strong><?php echo $result->address1;?><?php if(isset($result->address2)){?>, <?php echo $result->address2;?><?php } ?></strong> </li>
							<li>City:  <strong><?php echo $result->city;?></strong> </li>
							<li>State:  <strong><?php echo $result->state;?></strong> </li>
							<li>Zipcode:  <strong><?php echo $result->zipcode;?></strong> </li>
							<li>Phone:  <strong><a href="tel:<?php echo $result->phone;?>"><?php echo $result->phone;?></a></strong> </li>
							<li>Email:  <strong><a href="mailto:<?php echo $result->emailaddress;?>"><?php echo $result->emailaddress;?></a></strong> </li>
					   </ul>
				  </div>				  
			  </div>
			  <div class=" col-lg-7 col-md-7 col-sm-12">
				  <div class="contractor-details-list">
                      <ul>
						  <li>How many employees do you have?:  <strong><?php echo $result->howmanyemp;?></strong> </li>
							<li>What are your estimated annual roofing sales?:  <strong><?php echo $result->estimateanroof;?></strong> </li>
							<li>Services Provided (select all that apply):  <strong><?php echo $result->servicveprovider;?></strong> </li>
							<li>Years in Business:  <strong><?php echo $result->yesrbusiness;?></strong> </li>
							<li>How many tiled roofs completed per year?:  <strong><?php echo $result->roofcomepleteperyear;?></strong> </li>
							<li>How many commercial roofs completed per year?:  <strong><?php echo $result->commercialroofcomplete;?></strong> </li>
							<li>Do you agree to our Code of Ethics:  <strong><?php echo $result->agreeourcode;?></strong> </li>
						    <li>Sample Images:
							  <div class="contractor-details-sample-images">
                               <?php if(isset($result->sampleimage1)){?> <img src="<?php echo get_template_directory_uri(); ?>/uploads/<?php echo $result->sampleimage1;?>" alt="" style="width:150px;"><?php } ?>
							   <?php if(isset($result->sampleimage2)){?> <img src="<?php echo get_template_directory_uri(); ?>/uploads/<?php echo $result->sampleimage2;?>" alt="" style="width:150px;"/><?php } ?>
							   <?php if(isset($result->sampleimage3)){?> <img src="<?php echo get_template_directory_uri(); ?>/uploads/<?php echo $result->sampleimage3;?>" alt="" style="width:150px;"/><?php } ?>
							  </div>
 							</li>
					  </ul>
				  </div>
			  </div>
		  </div>
		  <hr>
		  <div class="row">
			  <div class=" col-lg-12 col-md-12 col-sm-12">
				  <div class="contractor-testimonial-wr">
                      <h3>What differentiates your from your competitors?</h3>
                      <div class="contractor-testimonial-in">
                            <p><?php echo $result->whatdifferentiates;?></p>
                      </div>  
                  </div>
                  <div class="contractor-testimonial-wr">
                      <h3>Testimonials</h3>
                      <div class="contractor-testimonial-in">
                            <p><?php echo $result->testimonials;?></p>
                      </div>  
                  </div>
                  <div class="contractor-rating-wr">
                      <h4>Bartile Rating: <span>Not rated yet</span> </h4>
                      <h4>Squares installed to date: <span class="squares-number">689</span> </h4>
                  </div>
			  </div>
		  </div>
          <!--<div class="row"> 		    
              <div class=" col-lg-8 col-md-7 col-sm-12">
                  <div class="contractor-details-list">
                        <ul>
                            <li>Date #:  <strong><?php echo $result->submitedate;?></strong> </li>
                            <li>Company Name: <strong><?php echo $result->companyname;?></strong> </li>
                            <li>Contact Name:  <strong><?php echo $result->pcontactnumber;?></strong> </li>
							<li>Contractor #:  <strong><?php echo $result->contractorstatelno;?></strong> </li>
							<li>Address:  <strong><?php echo $result->address1;?><?php if(isset($result->address2)){?>, <?php echo $result->address2;?><?php } ?></strong> </li>
							<li>City:  <strong><?php echo $result->city;?></strong> </li>
							<li>State:  <strong><?php echo $result->state;?></strong> </li>
							<li>Zipcode:  <strong><?php echo $result->zipcode;?></strong> </li>
							<li>Phone:  <strong><a href="tel:<?php echo $result->phone;?>"><?php echo $result->phone;?></a></strong> </li>
							<li>Email:  <strong><a href="mailto:<?php echo $result->emailaddress;?>"><?php echo $result->emailaddress;?></a></strong> </li>
							<li>How many employees do you have?:  <strong><?php echo $result->howmanyemp;?></strong> </li>
							<li>What are your estimated annual roofing sales?:  <strong><?php echo $result->estimateanroof;?></strong> </li>
							<li>Services Provided (select all that apply):  <strong><?php echo $result->servicveprovider;?></strong> </li>
							<li>Years in Business:  <strong><?php echo $result->yesrbusiness;?></strong> </li>
							<li>How many tiled roofs completed per year?:  <strong><?php echo $result->roofcomepleteperyear;?></strong> </li>
							<li>How many commercial roofs completed per year?:  <strong><?php echo $result->commercialroofcomplete;?></strong> </li>
							<li>Do you agree to our Code of Ethics:  <strong><?php echo $result->agreeourcode;?></strong> </li>
							<li>Sample Images:  <br>
							  <div>
                               <?php if(isset($result->sampleimage1)){?> <img src="<?php echo get_template_directory_uri(); ?>/uploads/<?php echo $result->sampleimage1;?>" alt="" style="width:200px;"><?php } ?>
							   <?php if(isset($result->sampleimage2)){?> <img src="<?php echo get_template_directory_uri(); ?>/uploads/<?php echo $result->sampleimage2;?>" alt="" style="width:200px;"/><?php } ?>
							   <?php if(isset($result->sampleimage3)){?> <img src="<?php echo get_template_directory_uri(); ?>/uploads/<?php echo $result->sampleimage3;?>" alt="" style="width:200px;"/><?php } ?>
							  </div>
 							</li>
                        </ul>
                  </div>
				  <div class="contractor-testimonial-wr">
                      <h3>What differentiates your from your competitors?</h3>
                      <div class="contractor-testimonial-in">
                            <p><?php echo $result->whatdifferentiates;?></p>
                      </div>  
                  </div>
                  <div class="contractor-testimonial-wr">
                      <h3>Testimonials</h3>
                      <div class="contractor-testimonial-in">
                            <p><?php echo $result->testimonials;?></p>
                      </div>  
                  </div>
                  <div class="contractor-rating-wr">
                      <h4>Bartile Rating: <span>Not rated yet</span> </h4>
                      <h4>Squares installed to date: <span class="squares-number">689</span> </h4>
                  </div>
              </div>
			  <div class=" col-lg-4 col-md-5 col-sm-12">
                   <div class="contractor-details-sidebar">
                        <div class="contractor-ds-logo">
                            <a href="<?php echo $result->website;?>" target="_blank">
                                <img src="<?php echo get_template_directory_uri(); ?>/uploads/<?php echo $result->companylogo;?>" alt="">
                            </a>
                        </div>
                   </div> 
              </div>
          </div>-->
      </div>
</section>
<?php get_footer(); ?>