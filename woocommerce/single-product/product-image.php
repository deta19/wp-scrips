<?php
/**
 * Single Product Image
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/product-image.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://woo.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 7.8.0
 */

defined( 'ABSPATH' ) || exit;

// Note: `wc_get_gallery_image_html` was added in WC 3.3.2 and did not exist prior. This check protects against theme overrides being used on older versions of WC.
if ( ! function_exists( 'wc_get_gallery_image_html' ) ) {
	return;
}

global $product;

$columns           = apply_filters( 'woocommerce_product_thumbnails_columns', 4 );
$post_thumbnail_id = $product->get_image_id();
$wrapper_classes   = apply_filters(
	'woocommerce_single_product_image_gallery_classes',
	array(
		'woocommerce-product-gallery',
		'woocommerce-product-gallery--' . ( $post_thumbnail_id ? 'with-images' : 'without-images' ),
		'woocommerce-product-gallery--columns-' . absint( $columns ),
		'images',
	)
);


$attachment_ids = $product->get_gallery_image_ids();

$all_product_images = array();

if ( $post_thumbnail_id ) {
	
	$all_product_images[] = (int) $post_thumbnail_id;
}
if ( $attachment_ids ) {
	foreach ( $attachment_ids as $attachment_id ) {
		$all_product_images[] = $attachment_id;
	}
}

?>
<div class="<?php echo esc_attr( implode( ' ', array_map( 'sanitize_html_class', $wrapper_classes ) ) ); ?>" data-columns="<?php echo esc_attr( $columns ); ?>" style="opacity: 0; transition: opacity .25s ease-in-out;">
	<div class="product_slider">
		<div class="slider_menu_bar">
            <div class="slider-nav-thumbnails">
			<?php
			foreach ( $all_product_images as $all_product_image_id ) {
				$flexslider = false;
				$image_size        = apply_filters( 'woocommerce_gallery_image_size', $flexslider || $main_image ? 'woocommerce_single' : $thumbnail_size );
	
				$image 	= wp_get_attachment_image(
													$all_product_image_id,
													$image_size,
													false,
													apply_filters(
														'woocommerce_gallery_image_html_attachment_image_params',
														array(
															'title'                   => _wp_specialchars( get_post_field( 'post_title', $all_product_image_id ), ENT_QUOTES, 'UTF-8', true ),
															'data-caption'            => _wp_specialchars( get_post_field( 'post_excerpt', $all_product_image_id ), ENT_QUOTES, 'UTF-8', true ),
															'data-src'                => esc_url( $full_src[0] ),
															'data-large_image'        => esc_url( $full_src[0] ),
															'data-large_image_width'  => esc_attr( $full_src[1] ),
															'data-large_image_height' => esc_attr( $full_src[2] ),
															'class'                   => '',
														),
														$all_product_image_id,
														$image_size,
														false
													)
												);
		 	?>
				<div class="item">
				<?php echo $image;?>
				</div>
			<?php
			}
			?>
			</div>
			<div class="slider-nav-arrows"></div>
		</div>
		<div class="slider_with_side_menu">
			<?php
			foreach ( $all_product_images as $all_product_image_id ) {
				$flexslider = false;
				$image_size        = apply_filters( 'woocommerce_gallery_image_size', $flexslider || $main_image ? 'woocommerce_single' : $thumbnail_size );
	
				$image 	= wp_get_attachment_image(
													$all_product_image_id,
													$image_size,
													false,
													apply_filters(
														'woocommerce_gallery_image_html_attachment_image_params',
														array(
															'title'                   => _wp_specialchars( get_post_field( 'post_title', $all_product_image_id ), ENT_QUOTES, 'UTF-8', true ),
															'data-caption'            => _wp_specialchars( get_post_field( 'post_excerpt', $all_product_image_id ), ENT_QUOTES, 'UTF-8', true ),
															'data-src'                => esc_url( $full_src[0] ),
															'data-large_image'        => esc_url( $full_src[0] ),
															'data-large_image_width'  => esc_attr( $full_src[1] ),
															'data-large_image_height' => esc_attr( $full_src[2] ),
															'class'                   => '',
														),
														$all_product_image_id,
														$image_size,
														false
													)
												);
		 	?>
			<div class="s_item">
				<?php echo wc_get_gallery_image_html( $all_product_image_id );?>
			</div>
			<?php
			}
			?>
		</div>
	</div>
</div>
<link rel="stylesheet" type="text/css" href="slick/slick.css"/>
// Add the new slick-theme.css if you want the default styling
<link rel="stylesheet" type="text/css" href="slick/slick-theme.css"/>
<script type="text/javascript" src="//code.jquery.com/jquery-1.11.0.min.js"></script>
<script type="text/javascript" src="//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>

<style type="text/css">
	/* copy this in your css file */
	/* product slider css*/
.product_slider {
    display: flex;
    flex-flow: wrap;
    margin: 0 -15px;
}

.product_slider .slider_menu_bar {
    position: relative;
    flex: 0 0 auto;
    width: 24%;
    display: flex;
    flex-flow: column;
    justify-content: space-between;
}
.product_slider .slider_menu_bar.hidden{
    visibility: hidden;
}
.product_slider .slider_with_side_menu {
    flex: 0 0 auto;
    width: 76%;
    padding-left: 10px;
    padding-right: 22%;
}

.product_slider .slider-nav-thumbnails {
    margin: 65px 0;
    z-index: 1;
    position: relative;
}

.product_slider .slider-nav-thumbnails .item {
    padding: 8px 10px;
    overflow: hidden;
}

.product_slider .slider-nav-thumbnails .item.slick-current {
    opacity: 0.5;
}

.product_slider .slider-nav-thumbnails .item img {
    max-width: 100%;
    width: 100%;
    height: auto;
    
}

.product_slider .slider-nav-arrows {
    display: flex;
    flex-flow: column;
    justify-content: flex-end;
    position: absolute;
    top: 0;
    left: 0;
    height: 100%;
    width: 100%;
    z-index: 0;
}

.product_slider .slider-nav-arrows .slick-arrow {
    display: inline-block;
    padding: 15px 15px 15px 15px;
    cursor: pointer;
    
}
.product_slider .slider-nav-arrows .slick-arrow svg {
    display: inline-block;
    position: absolute;
    left: 1px;
    top: 8px;
}
.product_slider .slider-nav-arrows .slick-arrow.slick-prev:before {
    display: none;
}
.product_slider .slider-nav-arrows .slick-arrow.slick-next:before {
    display: none;
}
.product_slider .slider-nav-arrows .slick-arrow.slick-prev {
    top: initial;
    left: 50%;
    transform: translateX( -50% );
    bottom: 0;
}
.product_slider .slider-nav-arrows .slick-arrow.slick-next {
    top: 0;
    left: 50%;
    transform: translateX( -50% );
}



.product_slider .slider-nav-arrows .slick-arrow scf path {
    fill: #00263C;
}


.product_slider .slider-nav-arrows .slick-arrow.slick-disabled {
    border-color: #A9C5E5;
}
.product_slider .slider-nav-arrows .slick-arrow.slick-disabled svg path{
    fill: #A9C5E5;
    
}


@media ( max-width: 768px ) { 
	.product_slider .slider-nav-arrows .slick-arrow svg {
	    transform: rotate(-90deg);
	}
	.product_slider .slider-nav-arrows .slick-arrow.slick-next {
	    top: 50%;
	    transform: translateX(-50%);
	    left: 24px;
	}
	.product_slider .slider-nav-arrows .slick-arrow.slick-prev {
	    top: 50%;
	    left: initial;
	    right: 0;
	    transform: translateX(-50%);
	    bottom: initial;
	}
	.product_slider .slider_with_side_menu {
		width: 100%;
		padding-right: 0;
	}
	.product_slider .slider_menu_bar {
		width: 100%;
		order: 2;
	}
	.product_slider .slider-nav-thumbnails {
	    margin: 0px 45px;
	}
	
	
}
/* end product slider css*/

</style>
<script>
	/* copy this in your js file*/
if(jQuery('.product_slider').length > 0 ) {
		/* check if there are more than 1 images*/
		
		jQuery('.product_slider').each(function( index ) {
		  
		  	if(  jQuery( this ).find('.slider_with_side_menu .s_item').length > 1 ) {
			    jQuery( this ).find('.slider_with_side_menu').slick({
			       slidesToShow: 1,
			       slidesToScroll: 1,
			       arrows: false,
			       fade: true,
			       // asNavFor: '.slider-nav-thumbnails',
			       asNavFor: jQuery( this ).find('.slider-nav-thumbnails'),
				
			     });
			     jQuery( this ).find('.slider-nav-thumbnails').slick({
					slidesToShow: 2,
					slidesToScroll: 1,
					// asNavFor: '.slider_with_side_menu',
					asNavFor: jQuery( this ).find('.slider_with_side_menu'),
					dots: false,
					arrows: true,
					focusOnSelect: true,
			       
			       	vertical: true,
		    		verticalSwiping: true,
			            
			       	appendArrows: jQuery( this ).find('.slider-nav-arrows'),
					prevArrow: '<span class="slick-prev pull-left carousel-btns"><svg width="26" height="15" viewBox="0 0 26 15" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M14.5405 14.484C14.2101 14.8144 13.7619 15 13.2946 15C12.8272 15 12.3791 14.8144 12.0486 14.484L1.47445 3.90989C1.15343 3.5775 0.975791 3.13233 0.979808 2.67024C0.983823 2.20816 1.16917 1.76614 1.49592 1.43939C1.82268 1.11263 2.2647 0.927288 2.72678 0.923272C3.18886 0.919257 3.63404 1.09689 3.96642 1.41792L13.2946 10.7461L22.6227 1.41792C22.9551 1.09689 23.4003 0.919259 23.8623 0.923274C24.3244 0.92729 24.7664 1.11263 25.0932 1.43939C25.4199 1.76614 25.6053 2.20816 25.6093 2.67025C25.6133 3.13233 25.4357 3.5775 25.1147 3.90989L14.5405 14.484Z" fill="#939598"/></svg></span>',
					nextArrow: '<span class="slick-next pull-right carousel-btns"><svg width="26" height="15" viewBox="0 0 26 15" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M14.5405 0.515994C14.2101 0.185604 13.7619 1.03574e-06 13.2946 1.0766e-06C12.8272 1.11745e-06 12.3791 0.185604 12.0486 0.515995L1.47445 11.0901C1.15343 11.4225 0.975791 11.8677 0.979808 12.3298C0.983823 12.7918 1.16917 13.2339 1.49592 13.5606C1.82268 13.8874 2.2647 14.0727 2.72678 14.0767C3.18886 14.0807 3.63404 13.9031 3.96642 13.5821L13.2946 4.25394L22.6227 13.5821C22.9551 13.9031 23.4003 14.0807 23.8623 14.0767C24.3244 14.0727 24.7664 13.8874 25.0932 13.5606C25.4199 13.2339 25.6053 12.7918 25.6093 12.3298C25.6133 11.8677 25.4357 11.4225 25.1147 11.0901L14.5405 0.515994Z" fill="#939598"/></svg></span>',
			  		
			  		autoplay: true,
    				autoplaySpeed: 2000,
    
			  		responsive: [
								    {
								      breakpoint: 768,
								      settings: {
								        slidesToShow: 2,
								        slidesToScroll: 1,
								        infinite: true,
								        dots: false,
								        vertical: false,
		    							verticalSwiping: false,
								      }
								    },
								]
			     });
		  		
		  	} else {
		  		 jQuery( this ).find('.slider_menu_bar').addClass('hidden');
		  	}
			
		});
		
	    
	}
	
</script>