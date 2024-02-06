<?php
/**
 * Template Name: Bartile Certified
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
$pageid=get_the_ID();
//$p = get_page($pageid);

get_header(); ?>
<section class="inner-banner-sec clearfix" style="background: #e2e2e2 url(<?php echo get_field('inner_page_banner_image','options');?>) no-repeat right bottom;">
  <div class="container">

   <div class="inner-con">
      <h1 class="h1-custom-white"><?php echo get_the_title($pageid);?></h1>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="<?php echo site_url();?>">Home</a></li>
          <li class="breadcrumb-item active" aria-current="page"><?php echo get_the_title($pageid);?></li>
        </ol>
      </nav>
    </div>


  </div>
</section>
<section class="certified-pw">
	<div class="container">
		<div class="row justify-content-center" >
		  <?php $rows=get_field('content',$pageid);
		    foreach($rows as $row){?>
			<div class="col-md-5">
				<div class="certified-box-wr">
					<div class="certified-box-in">
						<img src="<?php echo $row['section_image'];?>" alt="">
						<?php echo $row['section_content'];?>
					</div>
				</div>
			</div>
			<?php } ?>
		</div>
	</div>	
</section>
<?php get_footer(); ?>