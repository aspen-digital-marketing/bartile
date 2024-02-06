<?php
/**
 * Template Name: Contractor Application
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
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
		  <li class="breadcrumb-item"><a href="<?php echo site_url();?>/bartile-certified/">Bartile Certified/</a></li>
          <li class="breadcrumb-item active" aria-current="page"><?php echo get_the_title($pageid);?></li>
        </ol>
      </nav>
    </div>


  </div>
</section>

<section class="contact-us-wr c-application-page contractor-quiz">
  <div class="container">
            <!--<div class="c-heading-1">-->
            <!--<?php //echo apply_filters('the_content', $p->post_content);?>-->
            <!--</div>-->
            <div class="PickAny_one">
                  <p data-link="Application" class="pick-button active_picked"> <span>1</span>Application</p>
                  <p data-link="Quiz" class="pick-button quiz-part"><span class="Two_num">2</span>Quiz</p>
            </div>
           <form action="" method="post" enctype="multipart/form-data" id="fupForm">
               <div id="Application" class="content" style="display: block;" data-link="Application">
                  <div class="c-heading-1" style="text-align:left;">
                    <h2 style="text-align:left;">Contractor Application</h2>		
                 </div>
              <div class="row">
                    <div class="col-md-6">
                         <div class="form-group">
                                      <input type="date" name="submitedate" class="c-form-control datepicker require-option" required />

                         </div> 
                    </div>
                    <div class="col-md-6">
                         <div class="form-group">
                              <input type="text" name="companyname" placeholder="Company Name*" class="c-form-control require-option" required />
                         </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                         
                         <div class="form-group">
                              <input type="text" name="pcontactnumber" placeholder="Primary Contact Name*" class="c-form-control require-option" required />
                         </div> 
                    </div>
                    <div class="col-md-6">
                      <div class="form-group">
                              <input type="text" name="contractorstatelno" placeholder="Contractor State License Number*" class="c-form-control require-option" required />
                         </div> 
                          
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                         
                         <div class="form-group">
                              <input type="text" name="address1" placeholder="Address 1*" class="c-form-control require-option" required /> 
                         </div> 
                    </div>
                    <div class="col-md-6">
                      <div class="form-group">
                              <input type="text" name="address2" placeholder="Address 2" class="c-form-control">
                         </div> 
                          
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                         <div class="form-group">
                              <input type="text" name="city" placeholder="City*" class="c-form-control require-option" required />
                         </div> 
                    </div>
                    <div class="col-md-4">
                      <div class="form-group">
                              <select class="c-form-control require-option" required="" name="state">
                                      <option value="">State*</option>
                                      <option value="AL">Alabama</option>
                                      <option value="AK">Alaska</option>
                                      <option value="AZ">Arizona</option>
                                      <option value="AR">Arkansas</option>
                                      <option value="CA">California</option>
                                      <option value="CO">Colorado</option>
                                      <option value="CT">Connecticut</option>
                                      <option value="DE">Delaware</option>
                                      <option value="DC">District Of Columbia</option>
                                      <option value="FL">Florida</option>
                                      <option value="GA">Georgia</option>
                                      <option value="HI">Hawaii</option>
                                      <option value="ID">Idaho</option>
                                      <option value="IL">Illinois</option>
                                      <option value="IN">Indiana</option>
                                      <option value="IA">Iowa</option>
                                      <option value="KS">Kansas</option>
                                      <option value="KY">Kentucky</option>
                                      <option value="LA">Louisiana</option>
                                      <option value="ME">Maine</option>
                                      <option value="MD">Maryland</option>
                                      <option value="MA">Massachusetts</option>
                                      <option value="MI">Michigan</option>
                                      <option value="MN">Minnesota</option>
                                      <option value="MS">Mississippi</option>
                                      <option value="MO">Missouri</option>
                                      <option value="MT">Montana</option>
                                      <option value="NE">Nebraska</option>
                                      <option value="NV">Nevada</option>
                                      <option value="NH">New Hampshire</option>
                                      <option value="NJ">New Jersey</option>
                                      <option value="NM">New Mexico</option>
                                      <option value="NY">New York</option>
                                      <option value="NC">North Carolina</option>
                                      <option value="ND">North Dakota</option>
                                      <option value="OH">Ohio</option>
                                      <option value="OK">Oklahoma</option>
                                      <option value="OR">Oregon</option>
                                      <option value="PA">Pennsylvania</option>
                                      <option value="RI">Rhode Island</option>
                                      <option value="SC">South Carolina</option>
                                      <option value="SD">South Dakota</option>
                                      <option value="TN">Tennessee</option>
                                      <option value="TX">Texas</option>
                                      <option value="UT">Utah</option>
                                      <option value="VT">Vermont</option>
                                      <option value="VA">Virginia</option>
                                      <option value="WA">Washington</option>
                                      <option value="WV">West Virginia</option>
                                      <option value="WI">Wisconsin</option>
                                      <option value="WY">Wyoming</option>
                                    </select>
                         </div> 
                    </div>
                    <div class="col-md-4">
                         <div class="form-group">
                              <input type="text" name="zipcode" placeholder="Zip Code*" class="c-form-control require-option" required />
                         </div> 
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                         <div class="form-group">
                              <input type="text" name="phone" placeholder="Phone*" class="c-form-control require-option" required />
                         </div> 
                    </div>
                    <div class="col-md-4">
                         <div class="form-group">
                              <input type="text" name="emailaddress" placeholder="email*" class="c-form-control require-option" required />
                         </div> 
                    </div> 
                    <div class="col-md-4">
                         <div class="form-group">
                              <input type="text" name="website" placeholder=" Website (Please include the http://)" class="c-form-control">
                         </div> 
                    </div>
                </div>  
                 <div class="row">
                    <div class="col-md-6">
                         
                         <div class="form-group">
                              <label>How many employees do you have?* </label>
                              <input type="text" name="howmanyemp" placeholder="" class="c-form-control require-option" required />
                         </div> 
                    </div>
                    <div class="col-md-6">
                         <div class="form-group">
                              <label>What are your estimated annual roofing sales?* </label>
                              <input type="text" name="estimateanroof" placeholder="" class="c-form-control require-option" required />
                         </div> 
                          
                    </div>
                </div> 
                <div class="row">
                    <div class="col-md-12">
                         
                         <div class="form-group">
                              <label>Services Provided (select all that apply): </label>
                                 <div class="c-form-check"> 
                                 <div class="form-check form-check-inline">
                                  <input class="form-check-input require-option" type="checkbox" id="inlineCheckbox1" name="servicveprovider[]" value="Tile">
                                  <label class="form-check-label" for="inlineCheckbox1">Tile</label>
                                </div>
                                <div class="form-check form-check-inline">
                                  <input class="form-check-input require-option" type="checkbox" name="servicveprovider[]" id="inlineCheckbox2" value="New Construction">
                                  <label class="form-check-label" for="inlineCheckbox2"> New Construction</label>
                                </div>
                                <div class="form-check form-check-inline">
                                  <input class="form-check-input require-option" type="checkbox" name="servicveprovider[]" id="inlineCheckbox3" value="Commercial" >
                                  <label class="form-check-label" for="inlineCheckbox3">Commercial</label>
                                </div>
                                <div class="form-check form-check-inline">
                                  <input class="form-check-input require-option" type="checkbox" name="servicveprovider[]" id="inlineCheckbox4" value="Reroof" >
                                  <label class="form-check-label" for="inlineCheckbox4">Reroof</label>
                                </div>
                               </div>
                         </div> 
                    </div>
                </div> 
                 <div class="row">
                    <div class="col-md-6">
                         
                         <div class="form-group">
                              <label>Years in Business*: </label>
                              <input type="text" name="yesrbusiness" placeholder="" class="c-form-control require-option" required />
                         </div> 
                    </div>
                    <div class="col-md-6">
                         <div class="form-group">
                              <label>How many tiled roofs completed per year?*</label>
                              <input type="text" name="roofcomepleteperyear" placeholder="" class="c-form-control require-option" required />
                         </div> 
                          
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                         <div class="form-group">
                              <label>How many commercial roofs completed per year?* </label>
                              <input type="text" name="commercialroofcomplete" placeholder="" class="c-form-control require-option" required />
                         </div> 
                    </div>
                    <div class="col-md-6">
                         <div class="form-group">
                              <label>Do you agree to our Code of Ethics*?</label>
                              <div class="c-form-check"> 
                                 <div class="form-check form-check-inline">
                                  <input class="form-check-input require-option" type="checkbox" name="agreeourcode" id="inlineCheckbox5" required value="Yes">
                                  <label class="form-check-label" for="inlineCheckbox5">Yes</label>
                                </div>
                              </div>  
                         </div> 
                          
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6 col-md-12">
                         <div class="form-group">
                              <label>Company Logo (500kb max - .gif, .jpg, .png files only)*: </label>
                              <input type="file" name="companylogo" placeholder="" id="companylogo" class="c-form-control " required />
							  <p class="error_company_logo"></p>
                         </div> 
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                         <div class="form-group">
                              <label>Sample Images (500kb each max - .gif, .jpg, .png files only):</label>
                              <div class="row">
                                  <div class="col-md-4">
                                       <input type="file" name="sampleimage1" id="sampleimage1" placeholder="" class="c-form-control ">
                                       <p class="error_sample_image1"></p>									   
                                  </div>  
                                  <div class="col-md-4">
                                       <input type="file" name="sampleimage2" id="sampleimage2" placeholder="" class="c-form-control "> 
									   <p class="error_sample_image2"></p> 
                                  </div> 
                                  <div class="col-md-4">
                                       <input type="file" name="sampleimage3" id="sampleimage3" placeholder="" class="c-form-control "> 
									   <p class="error_sample_image3"></p>
                                  </div> 
                              </div>
                              
                         </div> 
                    </div>
                </div>

                <div class="row">
                     <div class="col-md-6">
                          <div class="form-group">
                           <label>What differentiates your from your competitors?*</label> 
                          <textarea class="c-form-control require-option" name="whatdifferentiates" placeholder="" required ></textarea>
                          </div>  
                     </div>
                     <div class="col-md-6">
                          <div class="form-group">
                            <label>Testimonials:*</label> 
                          <textarea class="c-form-control Uniq require-option" name="testimonials" placeholder="Message" required ></textarea>
                          </div>  
                     </div>
                 </div>
                     <div class="col-md-6 text-right BUTTON2">
                         
                         <a href="#" data-link="Quiz" class="next-btn">Next</a>
                          <!--<input type="submit" class="custom-button Uniq_btn" name="submitbtn" id="submitbtn" value="SUBMIT" />-->
                     </div>
                 </div>
                 
                 
				<div id="Quiz" class="content" data-link="Quiz">
				     <div class="c-heading-1" style="text-align:left;">
                <h2 style="text-align:left;">Test Your Bartile Knowledge</h2>
                <p>Welcome to the Bartile Contractor Quiz! As a dedicated contractor, it's essential to stay updated and knowledgeable about Bartile products and practices. This quiz is designed to test your expertise and ensure you're at the forefront of our industry standards. Dive in and see how well you know Bartile!</p>
                 </div>
               		 
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
                   <div class="row No-block">
                        <div class="col-md-6">
                           <a href="#" class="Back_first">Back</a>
                      </div>
                      <div class="col-md-6 text-right BUTTON">
                          <input type="submit" class="custom-button" name="submitbtn" id="submitbtn" value="SUBMIT" />
                         <img src="https://bartile.goaspendigital.com/wp-content/uploads/2023/03/arrow.svg" />
                     </div>
                     
      <!--               <div class="col-md-6">-->
					 <!--   <p class="pass_error">&nbsp;</p>-->
					 <!--</div>				 -->
                    
				</div>
				</div>	 
                <?php /* <div class="row">
                     <div class="col-md-6">
                          <div class="form-group">
                           <label>Account Password:*</label> 
                            <input type="password" name="accountpasord" placeholder="Password" id="accountpasord" class="c-form-control" required /> 
                          </div>  
                     </div>
                     <div class="col-md-6">
                          <div class="form-group">
                            <label>Confirm Password:*</label> 
                           <input type="password" name="confirmpassword" placeholder="Confirm Password" id="confirmpassword" class="c-form-control" required />
                          </div>  
                     </div>
					 <div class="col-md-12">
					    <p class="pass_error">&nbsp;</p>
					 </div>
                 </div>  */?> 
				 
              <div id="custom-popup" style="display: none;">
    <div id="popup-content">
        <span id="popup-message"></span>
        <button id="close-popup">Close</button>
    </div>
</div>

        </form>
</div>
</section>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<script>



$(document).ready(function () {
   $('.Back_first').click(function () {
    // Hide the "Quiz" content
    $('#Quiz').hide();

    // Show the "Application" content
    $('#Application').show();

    // Remove the "active_picked" class from "Quiz" and add it to "Application"
    $('.pick-button[data-link="Quiz"]').removeClass('active_picked');
    $('.pick-button[data-link="Application"]').addClass('active_picked');
});

        // Select the textarea
    var $textarea = $('textarea[name="testimonials"]');

    // Select the div containing the submit button
    var $submitButtonDiv = $('.BUTTON2');
    // Add an input event listener to the textarea
    $textarea.on('input', function() {
        // Check if the textarea has content
        if ($textarea.val().trim() !== '') {
            // Show the submit button div
            $submitButtonDiv.show();
            
        } else {
            // Hide the submit button div
            $submitButtonDiv.hide();
        }
    });

    console.log("OKOKOKOO")
    
    // Add click event listener to pick buttons
    // $('.next-btn').click(function () {
        
    //     let allInputs = $('#fupForm .require-option');
        
    //     let isValid = true;

    //         for (let input of allInputs) {
                
    //             console.log(input)
    //             // if (input.type === 'checkbox') {
    //             //     // For checkboxes, we check if it's checked
    //             //     if (!input.checked) {
    //             //         isValid = false;
    //             //         break;
    //             //     }
    //             // } else if (input.type === 'file') {
    //             //     // For file inputs, we check if a file is selected
    //             //     if (input.files.length === 0) {
    //             //         isValid = false;
    //             //         break;
    //             //     }
                    
    //             // } else {
          
    //             //    
    //             // }
                
    //              if (input.value.trim() === '') {
    //                     isValid = false;
    //                      break;
    //                  }
    //         }

    //         if (isValid) {
    //                       // Get the data-link attribute value
    //                 var target = $(this).data('link');
                    
    //                 // Hide all content divs
    //                 $('.content').hide();
                    
    //                 // Show the content div with the matching data-link attribute
    //                 $('#' + target).show();
                    
    //                 // Remove active class from all pick buttons
    //                 $('.pick-button').removeClass('active_picked');
                    
    //                 // Add active class to the clicked pick button
    //                 $(".quiz-part").addClass('active_picked');
                    
                
    //         } else {
    //             alert("Form is not valid. Please fill in all required fields.");
    //         }
            
            
        
        
 
        
        
    // });
$('.next-btn').click(function () {
    let allInputs = $('#fupForm .require-option');
    let isValid = true;
    let emptyFields = [];

    for (let input of allInputs) {
        if (input.value.trim() === '') {
            isValid = false;
            emptyFields.push(input.getAttribute('name'));
        }
    }

    if (isValid) {
        var target = $(this).data('link');
        $('.content').hide();
        $('#' + target).show();
        $('.pick-button').removeClass('active_picked');
        $(".quiz-part").addClass('active_picked');
    } else {
      var alertMessage = "Form is not valid. Please fill in the following required fields:<br><strong>" + emptyFields.join('<br>') + "</strong>";
        $("#popup-message").html(alertMessage);
        $("#custom-popup").show();
    }
});

$("#close-popup").click(function () {
    $("#custom-popup").hide();
});

    
    
    $('.pick-button:first').click();
});
jQuery('#accountpasord').keypress(function() {
    jQuery("#submitbtn").attr("disabled", true);
});
jQuery( "#confirmpassword" ).keyup(function() {
    var accountpassowd=document.getElementById('accountpasord').value;
	//console.log(accountpassowd);
	var confirmpassword=document.getElementById('confirmpassword').value;
	//console.log(confirmpassword);
	if(accountpassowd != confirmpassword){
		 jQuery(".pass_error").html('<span style="color:red;">Password not match.</span>');
	}else{
		jQuery(".pass_error").html('<span style="color:green;">Password match.</span>');
		jQuery("#submitbtn").attr("disabled", false);
	}
});

jQuery(document).ready(function(e){
    // Submit form data via Ajax
    jQuery("#fupForm").on('submit', function(e){
        e.preventDefault();
        jQuery.ajax({
            type: 'POST',
            url: '<?php echo get_template_directory_uri(); ?>/contratorsubmit.php',
            data: new FormData(this),
            dataType: 'json',
            contentType: false,
            cache: false,
            processData:false,
            beforeSend: function(){
                jQuery('#submitbtn').attr("disabled","disabled");
                jQuery('#fupForm').css("opacity",".5");
            },
            success: function(response){ console.log(response);
                jQuery('.pass_error').html('');
                if(response.status == 1){
					
                    jQuery('#fupForm')[0].reset();
                    jQuery('.pass_error').html('<span class="alert alert-success" style="color:green;">'+response.message+'</span>');
					//window.location.replace("https://bartile.com/bartile-certified/contractor-quiz/");
                }else{
                    jQuery('.pass_error').html('<span class="alert alert-danger" style="color:red;">'+response.message+'</span>');
                }
                jQuery('#fupForm').css("opacity","");
                jQuery("#submitbtn").removeAttr("disabled");
            }
        });
    });
});

jQuery("#companylogo").change(function() {	
    var file = this.files[0];
    var fileType = file.type;
    var match = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
    if(!((fileType == match[0]) || (fileType == match[1]) || (fileType == match[2]) || (fileType == match[3]) )){
        jQuery("#companylogo").val('');
        jQuery(".error_company_logo").html('<span style="color:red;">Sorry, only  JPG, JPEG, GIF & PNG files are allowed to upload.</span>');
        return false;
    }else{
		 jQuery(".error_company_logo").html('');
	}
});
jQuery("#sampleimage1").change(function() {
    var file = this.files[0];
    var fileType = file.type;
    var match = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
    if(!((fileType == match[0]) || (fileType == match[1]) || (fileType == match[2]) || (fileType == match[3]) )){
        jQuery("#sampleimage1").val('');
        jQuery(".error_sample_image2").html('<span style="color:red;">Sorry, only  JPG, JPEG, GIF & PNG files are allowed to upload.</span>');
        return false;
    }else{
		 jQuery(".error_sample_image2").html('');
	}
});
jQuery("#sampleimage3").change(function() {
    var file = this.files[0];
    var fileType = file.type;
    var match = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
    if(!((fileType == match[0]) || (fileType == match[1]) || (fileType == match[2]) || (fileType == match[3]) )){
        jQuery("#sampleimage3").val('');
        jQuery(".error_sample_image3").html('<span style="color:red;">Sorry, only  JPG, JPEG, GIF & PNG files are allowed to upload.</span>');
        return false;
    }else{
		 jQuery(".error_sample_image3").html('');
	}
});
jQuery("#sampleimage2").change(function() {
    var file = this.files[0];
    var fileType = file.type;
    var match = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
    if(!((fileType == match[0]) || (fileType == match[1]) || (fileType == match[2]) || (fileType == match[3]) )){
        jQuery("#sampleimage2").val('');
        jQuery(".error_sample_image2").html('<span style="color:red;">Sorry, only  JPG, JPEG, GIF & PNG files are allowed to upload.</span>');
        return false;
    }else{
		 jQuery(".error_sample_image2").html('');
	}
});
</script>

<?php get_footer(); ?>