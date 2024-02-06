<?php
/**
 * Template Name: Contractor Search
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
$pageid=get_the_ID();
//$p = get_page($pageid);
$p = get_page($pageid);
get_header(); ?>
<section class="inner-banner-sec contractor clearfix" style="background: #e2e2e2 url(<?php echo get_field('inner_page_banner_image','options');?>) no-repeat right bottom;">
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
<section class="c-search-page contractor-bottom">
  <div class="container">
            <div class="c-heading-1">
                 <?php echo apply_filters('the_content', $p->post_content);?>
            </div>
            <form class="contractorSearch" action="#" method="post" enctype="multipart/form-data" id="contractorSearch">
              <select name="contractorState" required >
			    <option value="">State</option>
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
              <input type="submit" value="Find Now" id="submitbtn" class="custom-button">
            </form>
            <div class="table-responsive">
                <table class="table table-striped">
                   
					<thead class="d-none">
                     <tr>
                      <th>Contractor</th>
                      <th>Address</th>
                      <th>City</th>
                      <th>Certified Since</th>
                      <th>Stars Earned</th>
                      <th>Total Squares</th>
                    </tr>
					</thead>
					<tbody class="search_content">
                    <tr>
                      <td colspan="6"><p style="text-align:center;">Please select a state.</p></td>
                    </tr>      
                  </tbody>
                </table>
            </div>  
            
  </div>
</section>
<script>

jQuery(document).ready(function(e){
    // Submit form data via Ajax
    jQuery("#contractorSearch").on('submit', function(e){
        e.preventDefault();
        jQuery.ajax({
            type: 'POST',
            url: '<?php echo get_template_directory_uri(); ?>/contratorsearch.php',
            data: new FormData(this),
            dataType: 'json',
            contentType: false,
            cache: false,
            processData:false,
            beforeSend: function(){
                jQuery('#submitbtn').attr("disabled","disabled");
                jQuery('#contractorSearch').css("opacity",".5");
            },
            success: function(response){ //console.log(response);
                console.log(response);
				var html ='';
				if(response.length > 0){
				for (var i = 0; i < response.length; i++) {
		$('.contractor-bottom .table-responsive thead').removeClass('d-none');
			  		html+='<tr><td><a href="/bartile-certified/contractor-details/?contractor_id='+response[i].id+'">'+response[i].companyname+'</a></td><td>'+response[i].zipcode+', '+response[i].address1+', '+response[i].address2+'</td><td>'+response[i].city+'</td><td>'+response[i].submitedate+'</td><td><div class="star"><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i></div></td><td>'+response[i].estimateanroof+'</td></tr>   ';
				}}else{
		$('.contractor-bottom .table-responsive thead').addClass('d-none');
				html+='<tr><td colspan="6"><p style="text-align:center;"> No contractors found in the selected state. Please try a different state. please use this <a target="_blank" href="https://tileroofing.org/find-a-contractor/">link</a> to find a certified tile installer near you.</p></td></tr>';	
				//We do not have a bartile certified installer in your state,
				}
				jQuery('.search_content').html(html);
                jQuery('#contractorSearch').css("opacity","");
                jQuery("#submitbtn").removeAttr("disabled");
            }
        });
    });
});
</script>
<?php get_footer(); ?>