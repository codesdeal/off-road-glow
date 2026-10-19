<?php


add_action( 'woocommerce_product_options_pricing', 'WooCommerce_video_product_field' );
function WooCommerce_video_product_field(){

  woocommerce_wp_textarea_input(
    array(
	'id'      => 'video_url',
	'value'   => get_post_meta( get_the_ID(), 'video_url', true ),
	'label'   => 'Video URL',
	'desc_tip' => true,
	'description' => 'A comma separated list of video URL\'s',
    )
  );
}

add_action('woocommerce_process_product_meta', 'wc_product_video_save');
function wc_product_video_save($post_id){
    $video_list = $_POST['video_url'];
    if (!empty($video_list)) {
        update_post_meta($post_id, 'video_url', esc_attr($video_list));
    }
}

add_action( 'woocommerce_product_thumbnails', 'wc_add_gallery_videos', 100, 0 );
function wc_add_gallery_videos(){
    
    $product     = wc_get_product(get_the_ID());
    $video_list  = $product->get_meta('video_url');  
    
    if(isset($video_list  ) && !empty($video_list  )) {
      $video_array = explode(",", $video_list);
      
       if( $video_array ):
         foreach ( $video_array as $video_embed ): ?>
           <div class="woocommerce-product-gallery__image">
             <?php  echo  '<div class="ResponsivevideoWrapper">' . wp_oembed_get( $video_embed) . '</div>'; ?>
           </div>
         <?php endforeach;
       endif;   
    }               
}


 add_action('wp_footer', 'wc_video_thumbnails');
function wc_video_thumbnails(){
	
	$video_url_list = get_post_meta(get_the_ID(), 'video_url', true);

	if(isset($video_url_list ) && !empty($video_url_list )) {
?>
<script>
	(function($) {

		$(function() {
			let videoList = "<?php echo $video_url_list; ?>";
			let videoArray = videoList.split(',');
			$( ".flex-control-thumbs img" ).ready(function() {
				let i = 0;
				$('.flex-control-thumbs img').each(function () {
					if (this.src.length == 0) {
						if(videoArray[i].includes("youtube")){
							videoArray[i] = videoArray[i].replace("https://www.youtube.com/watch?v=", "https://img.youtube.com/vi/");
							$(this).attr('src', videoArray[i]+'/3.jpg');
							i++;
						} else if (videoArray[i].includes("vimeo")) {
							videoArray[i] = videoArray[i].replace("https://vimeo.com/", "https://vumbnail.com/");

							$(this).attr('src', videoArray[i]+'_small.jpg');
							i++;
						} else {

							$(this).attr('src', 'https://offroadglowco.com/wp-content/uploads/2026/07/play-button.png');
							i++;
						}
					}
				});
			});
		});

	})( jQuery );
</script>
<style>
.ResponsivevideoWrapper {
  position: relative;
  padding-bottom: 56.25%; /* 16:9 */
  padding-top: 25px;
  height: 0;
  float:left;
  width: 100%;
}
.ResponsivevideoWrapper iframe {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
}
</style>
<?php
	}
}