<?php
//add <meta name="robots" content="noindex,follow"> to paged pages eg: /page/2
add_filter( 'wpseo_robots', 'my_robots_func' );
function my_robots_func( $robotsstr ) {
	if (  is_paged() ) {

		$robotsstr = 'noindex,follow';		
	}
	return $robotsstr;
}


//add alt atribute to woocoommerce images
add_filter('wp_get_attachment_image_attributes', 'change_attachement_image_attributes', 20, 2);

function change_attachement_image_attributes( $attr, $attachment ){
    // Get post parent
    $parent = get_post_field( 'post_parent', $attachment);

    // Get post type to check if it's product
    // $type = get_post_field( 'post_type', $parent);
    // if( $type != 'product' ){
    //     return $attr;
    // }

    /// Get title
    $title = get_post_field( 'post_title', $parent)." A4office";

    $attr['alt'] = $title;
    $attr['title'] = $title;

    return $attr;
}

//get woocoommerce prices from widget
function get_price_range_funtion_custom() {
 	$method = new ReflectionMethod("WC_Widget_Price_Filter" , "get_filtered_price");
	$method->setAccessible(true);

	return $method->invoke(new WC_Widget_Price_Filter);
}

//yoast breadcrumbs change schema.org/schema
//special tanks to https://gist.github.com/beaverbuilder
$breadcrumb_count = 1;
add_filter( 'wpseo_breadcrumb_single_link', 'ss_breadcrumb_single_link', 10, 2 );
function ss_breadcrumb_single_link( $link_output, $link ) {
    
    global $breadcrumb_count; 
    $element = 'li';
    $element = esc_attr( apply_filters( 'wpseo_breadcrumb_single_link_wrapper', $element ) );
    $link_output = '<' . $element . ' itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">';
    
    if ( isset( $link['url'] )) {
       
        $link_output .= '<a itemprop="item" href="' . esc_url( $link['url'] ) . '"><span itemprop="name">' . esc_html( $link['text'] ) . '</span></a><meta itemprop="position" content="'.$breadcrumb_count++.'" />'; 
    } 
    
    $link_output .= '</' . $element . '>';
    return $link_output;
}
add_filter( 'wpseo_breadcrumb_output_wrapper', 'ss_breadcrumb_output_wrapper', 10, 1 );
function ss_breadcrumb_output_wrapper( $wrapper) {
     
    $wrapper = 'ol';
    return $wrapper;
}

// define the wpseo_breadcrumb_output callback 
function filter_wpseo_breadcrumb_output( $output ) { 
    // make filter magic happen here... 
    return '<div id="breadcrumbs">' .$output . '</div>';
}; 
         
// add the filter 
add_filter( 'wpseo_breadcrumb_output', 'filter_wpseo_breadcrumb_output', 10, 1 ); 



/*
* Redirection page redirect
*/
add_action('template_redirect', 'redirect_agency', 10);
function redirect_agency() {
	// i think wp_redirect() will work too, instead of header()
	header("Location: ".$_GET['link'].'/');
	exit;	

}



/*
* Custom post type Agentii
*/
// Register Custom Post Type
function agentie() {

	$labels = array(
		'name'                  => _x( 'Agentii', 'Post Type General Name', 'cazino' ),
		'singular_name'         => _x( 'Agentie', 'Post Type Singular Name', 'cazino' ),
		'menu_name'             => __( 'Agentii', 'cazino' ),
		'name_admin_bar'        => __( 'Agentii', 'cazino' ),
		'archives'              => __( 'Item Archives', 'cazino' ),
		'attributes'            => __( 'Item Attributes', 'cazino' ),
		'parent_item_colon'     => __( 'Parent Item:', 'cazino' ),
		'all_items'             => __( 'All Items', 'cazino' ),
		'add_new_item'          => __( 'Add New Item', 'cazino' ),
		'add_new'               => __( 'Add New', 'cazino' ),
		'new_item'              => __( 'New Item', 'cazino' ),
		'edit_item'             => __( 'Edit Item', 'cazino' ),
		'update_item'           => __( 'Update Item', 'cazino' ),
		'view_item'             => __( 'View Item', 'cazino' ),
		'view_items'            => __( 'View Items', 'cazino' ),
		'search_items'          => __( 'Search Item', 'cazino' ),
		'not_found'             => __( 'Not found', 'cazino' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'cazino' ),
		'featured_image'        => __( 'Featured Image', 'cazino' ),
		'set_featured_image'    => __( 'Set featured image', 'cazino' ),
		'remove_featured_image' => __( 'Remove featured image', 'cazino' ),
		'use_featured_image'    => __( 'Use as featured image', 'cazino' ),
		'insert_into_item'      => __( 'Insert into item', 'cazino' ),
		'uploaded_to_this_item' => __( 'Uploaded to this item', 'cazino' ),
		'items_list'            => __( 'Items list', 'cazino' ),
		'items_list_navigation' => __( 'Items list navigation', 'cazino' ),
		'filter_items_list'     => __( 'Filter items list', 'cazino' ),
	);
	$args = array(
		'label'                 => __( 'Agentie', 'cazino' ),
		'description'           => __( 'Agentiile siteului', 'cazino' ),
		'labels'                => $labels,
		'supports'              => array( 'title', 'thumbnail' ),
		'hierarchical'          => false,
		'public'                => true,
		'show_ui'               => true,
		'show_in_menu'          => true,
		'menu_position'         => 20,
		'show_in_admin_bar'     => true,
		'show_in_nav_menus'     => true,
		'can_export'            => true,
		'has_archive'           => true,
		'exclude_from_search'   => true,
		'publicly_queryable'    => true,
		'capability_type'       => 'page',
	);
	register_post_type( 'agentie', $args );

}
add_action( 'init', 'agentie', 0 );

/*
* get all post_types
*/
add_action( 'init', 'wpse34410_init', 0, 99 );
function wpse34410_init() 
{
    $types = get_post_types( [], 'objects' );
   // print_r($types);
}


/*add article microdata to articles*/

function article_schemaorg() {
    if( is_single() && 'post' == get_post_type() ) {
        global $post;
        $author = get_the_author();
        $post_image = get_the_post_thumbnail_url();

        echo '<script type="application/ld+json">
{
  "@context": "http://schema.org",
  "@type": "Article",
  "author": [{
            "@type": "Person",
            "name": "'.$author.'"
        }],
  "interactionStatistic": [
    {
      "@type": "InteractionCounter",
      "interactionService": {
        "@type": "WebSite",
        "name": "Twitter",
        "url": "http://www.twitter.com"
      },
      "interactionType": "http://schema.org/ShareAction",
      "userInteractionCount": "1203"
    }
  ],
  "datePublished": "'. $post->post_date.'",
  "dateModified": "'. $post->post_modified.'",
  "headline": "'. $post->post_title .'",
  "image": "yoursite_url_logohere_or_featuredimage",
  "name": "'. $post->post_title .'",
  "publisher": {
    "@type": "Organization",
    "name": "yoursitehere",
    "logo": {
      "@type": "ImageObject",
      "url": "yoursite_url_here"
    }
  }
}
</script>';

    }
}
add_action('wp_footer', 'article_schemaorg');


/* add schemaorg json for breadcrumbs and there is no need for the html */
function breadcrumbs_schemaorg() {

	if( !is_front_page() ) {
        global $post;
		$i=0;
		$breadcrumb_elements = '{
			   "@type": "ListItem",
			   "position": '.($i+1).',
			   "item":
			   {
			    "@id": "'.get_site_url().'",
			    "name": "BusinessLease"
			    }
			  },';
		$i = $i+1;

 		if( $post->post_parent ) {
 			$breadcrumb_elements .= '{
 			   "@type": "ListItem",
 			   "position": '.($i+1).',
 			   "item":
 			   {
 			    "@id": "'.get_permalink( $post->post_parent ).'",
 			    "name": "'.get_the_title( $post->post_parent ).'"
 			    }
 			  },';
			$i = $i+1;
 		}
		
		$breadcrumb_elements .= '{
			   "@type": "ListItem",
			   "position": '.($i+1).',
			   "item":
			   {
			    "@id": "'.get_permalink($post->ID).'",
			    "name": "'.$post->post_title.'"
			    }
			  }';
	
			echo '<script type="application/ld+json">
				{
				 "@context": "http://schema.org",
				 "@type": "BreadcrumbList",
				 "itemListElement":
				 [
				  '.$breadcrumb_elements.'
				 ]
				}
			</script>';
		
		}
	

}
add_action('wp_footer', 'breadcrumbs_schemaorg');


/* a more complexte  sceham org for articles */
function blogarticle_schemaorg() {
	
    if( is_single() && 'post' == get_post_type() ) {
        global $post;
        $author = get_the_author();
        $post_image = get_the_post_thumbnail_url();
				
        echo '<script type="application/ld+json">
	{
		"@context":"http://schema.org",
		"@type": "BlogPosting",
		"image": "'.$post_image.'",
		"url": "'.get_permalink($post->ID).'",
		"headline": "'.$post->post_title.'",
		"alternativeHeadline": "'.$post->post_title.'",
		"dateCreated": "'. $post->post_date.'",
		"datePublished": "'. $post->post_date.'",
		"dateModified": "'.$post->post_modified.'",
		"inLanguage": "'.get_locale().'",
		"isFamilyFriendly": "true",
		"copyrightYear": "'.date("Y", strtotime($post->post_modified) ).'",
		"copyrightHolder": "",
		"contentLocation": {
			"@type": "Place",
			"name": ""
		},
		"accountablePerson": {
			"@type": "Person",
			"name": "'.$author.'"
		},
		"author": {
			"@type": "Person",
			"name": "'.$author.'"
		},
		"creator": {
			"@type": "Person",
			"name": "'.$author.'"
		},
		"publisher": {
			"@type": "Organization",
			"name": "'.$author.'",
			"logo": {
				"@type": "ImageObject",
				"url": "'.get_site_url().'/wp-content/uploads/logo.png",
				"width":"400",
				"height":"55"
			}
		},
		"sponsor": {
			"@type": "Organization",
			"name": "sitename",
			"url": "'.get_site_url().'",
			"logo": {
				"@type": "ImageObject",
				"url": "'.get_site_url().'/wp-content/uploads/logo.png"
			}
		},
		"mainEntityOfPage": "True",
		"keywords": [
			""
		],
		"genre":["SEO","JSON-LD"],
		"articleSection": "",
		"articleBody": "'.wp_strip_all_tags( $post->post_content ).'"
	}
</script>';

    }

}
add_action('wp_footer', 'blogarticle_schemaorg');

// Function to change email address
//more info: https://www.wpbeginner.com/plugins/how-to-change-sender-name-in-outgoing-wordpress-email/
function wpb_sender_email( $original_email_address ) {
    return 'tim.smith@example.com';
}
 
// Function to change sender name
function wpb_sender_name( $original_email_from ) {
    return 'Tim Smith';
}
 
// Hooking up our functions to WordPress filters 
add_filter( 'wp_mail_from', 'wpb_sender_email' );
add_filter( 'wp_mail_from_name', 'wpb_sender_name' );


// polylang language switcher
 pll_the_languages( array( 
                                'dropdown' => 1,
                                'show_names' => 1,
                                'show_flags' => 0,
                                'hide_current' => 1, 
                            )
                        );



<div class="change_language">
<style type="text/css">

.change_language {
     width: 65px;
     position: absolute;
     top: 10px;
     right: -40px;
}
.languageswitcher {
     max-width: 65px;
     width: 100%;
     list-style: none;
     display: inline-block;
     background: #fff;
     padding-left: 15px;
     height: 35px;
     line-height: 35px;
     text-align: center;
     border: 1px solid #d4d4d4;
     padding: 0;
     border-radius: 5px 5px;
     margin: 0 0;
     position: relative;
     border-radius: 30px;
}
header .navbar .right ul.languageswitcher li.item {
     padding: 0 0;
}

header .navbar .right ul.languageswitcher li.item a {
     color: #000;
}

ul.languageswitcher li.item {
     display: inline-block;
     list-style: none;
     padding: 0 0;
     width: 100%;
     text-align: center;
}
.languageswitcher .submenu {
     display: none;
     width: 65px;
     list-style: none;
     padding-left: 15px;
     text-align: center;
     border: 1px solid #d4d4d4;
     padding: 0;
     border-radius: 0 0 5px 5px;
     border-bottom: 0;
     background: #fff;
     position: absolute;
     top: 30px;
     left: -1px;
     overflow: hidden;
}
.languageswitcher .submenu .item {
     display: inline-block;
     list-style: none;
     padding: 0;
     width: 100%;
}
.languageswitcher .submenu .item {
     display: inline-block;
     list-style: none;
     padding: 0;
     width: 100%;
}
.languageswitcher .submenu .item a.active {
     color: #A9AFB3;
     position: relative;
}

.languageswitcher:hover {
     border-radius: 5px 5px 0 0;
}
.languageswitcher:hover .submenu {
     display: inline-block;
}
.languageswitcher .submenu .item a:hover {
     color: #0082e6;
}

@media( max-width:  768px) {
     .change_language {
         width: 65px;
         position: relative;
         top: 15px;
         right: 0;
         float: right;
         z-index: 0;
     }
     header .navbar .right #menu {
         z-index: 1;
     }
}
</style>
<?php /* pll_the_languages( array( 'show_flags' => 1, 'show_names' => 1,  ) ); */ ?>
<?php

	$output = '';
		    if ( function_exists( 'pll_the_languages' ) ) {
			$args   = [
			    'show_flags' => 1,
			    'show_names' => 1,
			    'echo'       => 0,
			    'dropdown'   => 0,
			    'raw' => 1
			];
			// var_dump(  pll_the_languages( $args ) );

			$languages = pll_the_languages( $args ); 
			$output = '<ul class="languageswitcher">';
			$current_lang = '';
			$all_the_languages = '';
			foreach ($languages as $key => $language) {

				// echo $language['flag'];
				if( $language['current_lang'] ) {
					$current_lang = '<li class="item current">'.$language['flag'].' '.$language['name'];
				}
				$all_the_languages .= '<li class="item"><a href="'.$language['url'].'">'.$language['flag'].' '.$language['name'].'</a></li>';
			}
			$output .= $current_lang . '<ul class="submenu">'.$all_the_languages.'</ul></li></ul>';
		    }

		    echo $output;
		?>
								
</div>




// Shortcode to creata a owl carousel with similar posts, taht can be used in article page
//[similar_articles]
function similar_articles_func( $atts ){
	$current_post_categories = get_the_category( get_the_ID() );
	$cats = '';

	if( empty($current_post_categories) ) {
		return false;
	}


	foreach ($current_post_categories as $key => $category) {
		if ( $key < count($current_post_categories) - 1 ) {
			$cats .= $category->term_id . ", ";

		}else{
			$cats .= $category->term_id;
		}
	}

	$args = array( 
		'post_type'   => 'post',
		'cat' => $cats,
		'post_status' => 'publish'
	);
	$posts = new WP_Query( $args );

	if ( $posts->have_posts() ) {

				?>
		<div id="similar_posts">
			<div class="head_title"><?php echo __('Ai putea fi interesat si de:', 'organix'); ?></div>
			<div class="similar_articless owl-carousel owl-theme">
				<?php 
					while ( $posts->have_posts() ) {
						$posts->the_post();

						$post_image = get_the_post_thumbnail_url( get_the_ID() );
				?>
					<div class="item">
						<a href="<?php echo get_permalink( get_the_ID() ); ?>">
							<img src="<?php echo $post_image; ?>" width="150" height="auto" class="img">
							<div class="title">
								<?php  
									echo get_the_title();
								?>
							</div>
						</a>
					</div>
				<?php 
					}
				?>
			</div>
			<style>
			/*move in your style.css file*/
				
			#similar_posts .head_title {
			    font-size: 1.61rem;
			    color: #124a2f;
			    font-family: Montserrat, "Helvetica Neue", Helvetica, Arial, sans-serif;
			    font-weight: 500;
			    line-height: 1.2;
			}

			#similar_posts .img {
				width: 100%;
				height: auto;
			}

			#similar_posts .title {
				font-size: 16px;
				font-weight: 700;
			}
			</style>
			<script type="text/javascript">
				jQuery(document).ready(function() {

					jQuery('.similar_articless').owlCarousel({
					    loop:true,
					    margin:10,
					    responsiveClass:true,
					    responsive:{
					        0:{
					            items:1,
					            nav:true
					        },
					        600:{
					            items:3,
					            nav:false
					        },
					        1000:{
					            items:5,
					            nav:true,
					            loop:false
					        }
					    }
					})

				})

			</script>
		</div>	
	<?php
	}

	wp_reset_postdata();

}
add_shortcode( 'similar_articles', 'similar_articles_func' );

/* Slider with similar products rom a category that you select in the post you read*/
//add meta box in posts to connect to products
	function products_add_custom_box() {
	    $screens = [ 'post' ];
	    foreach ( $screens as $screen ) {
	        add_meta_box(
	            'products',                 // Unique ID
	            'Produse relevante',      // Box title
	            'products_custom_box_html',  // Content callback, must be of type callable
	            $screen                            // Post type
	        );
	    }
	}
	add_action( 'add_meta_boxes', 'products_add_custom_box' );

	function products_custom_box_html( $post ) {
		$values = get_post_meta( $post->ID, 'products' );

		$args = array(
				'post_type'             => 'product',
				'post_status'           => 'publish',
		); 

		$categories = get_terms( 'product_cat', $args );

		$selected = ( isset( $values ) && !empty($values) )  ? esc_attr( $values[0] ) : '';

		wp_nonce_field( 'products', 'products_nonce' );
		    ?>
		    <p>Alegeti din ce categorie sa fie preluate produsele in sliderul "Produse relevante" pentru acest articol.</p>
		    <p>
		        <label for="products">Categorie produse</label>
		        <select name="products" id="products">
		           <?php 
			        	foreach ($categories as $key => $category) {

					?>
					<option value="<?php echo $category->term_id; ?>" 
	        			 <?php selected( $selected, $category->term_id ); ?>>
	        			<?php echo $category->name; ?>
	    			</option>

					<?php
			        	}
			        ?>
		        </select>

		    </p>
	    <?php    

	}

	function products_save_postdata( $post_id ) {

		// Bail if we're doing an auto save
	    if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;

	    // if our nonce isn't there, or we can't verify it, bail
	    if( !isset( $_POST['products_nonce'] ) || !wp_verify_nonce( $_POST['products_nonce'], 'products' ) ) return;
	     
	    // if our current user can't edit this post, bail
	    if( !current_user_can( 'edit_post' ) ) return;

	    //actual save data
	    if( isset( $_POST['products'] ) )
	        update_post_meta( $post_id, 'products', esc_attr( $_POST['products'] ) );
	        
	    
	}
	add_action( 'save_post', 'products_save_postdata' );


	//[products_related]
	function products_related_func( $atts ) {

		$product_category = get_post_meta( get_the_ID(), 'products', true );

		if( empty($product_category) ) {
			return false;
		}

		$args = array( 
			'numberposts' => 8,
			'tax_query' => array(
							      array(
							          'taxonomy' => 'product_cat',
									  'field' => 'id',
									  'terms' => $product_category
						          	)
						       ),
			'post_type' => 'product',
			'post_status' => 'publish'
		);
		$products = new WP_Query( $args );

		if ( $products->have_posts() ) {

					?>
			<div id="similar_products">
				<div class="head_title"><?php echo __('Produse recomandate:', 'organix'); ?></div>
				<div class="similar_products owl-carousel owl-theme">
					<?php 
						while ( $products->have_posts() ) {
							$products->the_post();
							$post_image = get_the_post_thumbnail_url( get_the_ID() );
					?>
						<div class="item">
							<a href="<?php echo get_permalink( get_the_ID() ); ?>">
								<img src="<?php echo $post_image; ?>" width="150" height="auto" class="img">
								<div class="title">
									<?php  
										echo get_the_title();
									?>
								</div>
							</a>
						</div>
					<?php 
						}
					?>
				</div>
				<style type="text/css">
					#similar_products .head_title {
					    font-size: 1.61rem;
					    color: #124a2f;
					    font-family: Montserrat, "Helvetica Neue", Helvetica, Arial, sans-serif;
					    font-weight: 500;
					    line-height: 1.2;
					}

					#similar_products .img {
						width: 100%;
						height: auto;
					}

					#similar_products .title {
						font-size: 14px;
						font-weight: 700;
						line-height: 16px;
					}

				</style>
				<script type="text/javascript">
					jQuery(document).ready(function() {

						jQuery('.similar_products').owlCarousel({
						    loop:true,
						    margin:10,
						    responsiveClass:true,
						    responsive:{
						        0:{
						            items:1,
						            nav:true
						        },
						        600:{
						            items:3,
						            nav:false
						        },
						        1000:{
						            items:5,
						            nav:true,
						            loop:false
						        }
						    }
						})

					})

				</script>
			</div>	
		<?php
		}

		wp_reset_postdata();

	}
	add_shortcode( 'products_related', 'products_related_func' );

/*Woocommece product category, Change yoast meta decription based on category , if is aprent or child */
function change_yoast_desc( $desc , $presentation ) {

	$queried_object = get_queried_object();
	$term_id = $queried_object->term_id;

	if( !empty($queried_object)) {
		if ( $queried_object->taxonomy == 'product_cat' ) {
			if ( $queried_object->parent > 0) {
				//child
				$parent = get_term( $queried_object->parent );
				$desc = 'Alege '.$queried_object->name.' produse '.$parent->name.' pentru sanatatea ta.';
			}else{
				$desc = 'Alege dintre '.$queried_object->name.' naturiste si bio pentru sanatatea ta.';
			}
		}
	}

	return $desc;

}

add_filter( 'wpseo_metadesc', 'change_yoast_desc', 10, 2);


// define the wpseo_xml_sitemap_post_url callback 
add_filter( 'wpseo_xml_sitemap_post_url', 'filter_function_name_6354', 10, 2 );
function filter_function_name_6354( $url, $post ){
	// works with page, articles but 
	//		doesn't work with taxonomy sitemaps
    $slugs = array(
    	"domain/product-tag/tagtwo/"
    );

    if ( $url !== $slugs[0]  ) {
    	return $url; 
    }
};
/* Exclude One Content Type From Yoast SEO Sitemap */
/* Exclude One Taxonomy From Yoast SEO Sitemap */
function sitemap_exclude_taxonomy( $value, $taxonomy ) {
	//if ( $taxonomy == 'product_tag' ) return true;
}
add_filter( 'wpseo_sitemap_exclude_taxonomy', 'sitemap_exclude_taxonomy', 10, 2 );

function max_entries_per_sitemap() {
    return 10000;
}

add_filter( 'wpseo_sitemap_entries_per_page', 'max_entries_per_sitemap' );
// }

add_filter( 'wpseo_exclude_from_sitemap_by_post_ids', function () {
  return array( 4);
} );


/* this removed the shop link from the product sitemap*/
add_filter( 'wpseo_sitemap_post_type_archive_link', 'my_wpseo_cpt_archive_link', 10, 2);
 
function my_wpseo_cpt_archive_link( $link, $post_type ) {

        // Disable product/post archives in the sitemaps
        if ( $post_type === 'product' )
                return false;

        return $link;
}

//exclude link  from any yoast generated sitemap
add_filter('wpseo_sitemap_entry', 'exclude_link', 20, 1);
function exclude_link( $url ) {
	$url_array = array(
'http://127.0.0.1/wordpress4517/product-tag/tagone/',

	);

	if( !in_array($url['loc'], $url_array) ) {
		return $string;
	}
}
/*
Redirect to Checkout on add to cart
First in WooCommerce > Product Settings > Product tab uncheck these options:
Redirect to the cart page after successful addition
Enable AJAX add to cart buttons on archives
Then use this code:
*/
add_filter( 'woocommerce_add_to_cart_redirect', 'goya_custom_redirect_checkout_add_cart' );
function goya_custom_redirect_checkout_add_cart() {
  return wc_get_checkout_url();
}

/*
Remove Additional Information tab
*/
add_filter( 'woocommerce_product_tabs', 'goya_custom_remove_product_tabs', 9999 );
function goya_custom_remove_product_tabs( $tabs ) {
  unset( $tabs['additional_information'] );
  return $tabs;
}
/*
Remove Description tab if empty
*/
add_filter( 'woocommerce_product_tabs', 'goya_custom_product_remove_empty_tabs', 20, 1 );

function goya_custom_product_remove_empty_tabs( $tabs ) {
 if ( ! empty( $tabs ) ) {
  foreach ( $tabs as $title => $tab ) {
   if ( $title == 'description' && empty( $tab['content'] ) ) {
    unset( $tabs[ $title ] );
   }
  }
 }
 return $tabs;
}
/**
Change product tabs titles
*/
add_filter( 'woocommerce_product_tabs', 'woo_customize_tabs' );
function woo_customize_tabs( $tabs ) {
 $tabs['description']['title'] = __( 'Custom Name' );
 return $tabs;
}
/*
Limit the product title on catalog pages
If your products have very long titles you can make them short for catalog pages only. The product page will show the full name.
*/
add_filter( 'the_title', 'custom_shorten_woo_product_title', 10, 2 );
function custom_shorten_woo_product_title( $title, $id ) {
 if ( ! is_singular( array( 'product' ) ) && get_post_type( $id ) === 'product' ) {
  return wp_trim_words( $title, 3, '...' ); // change last number to the number of words you want
 } else {
  return $title;
 }
}
/*
Auto refresh totals on quantity change
The cart page doesn’t refresh automatically on cart page. However, it’s possible to make it automatic.
*/
add_action( 'wp_footer', 'custom_cart_update_on_quantity_change' );
function custom_cart_update_on_quantity_change() {
 if (is_cart()) :
 ?>
 <script>
  jQuery('div.woocommerce').on('change', '.woocommerce-cart-form .qty', function(){
   jQuery("[name='update_cart']").prop("disabled", false);
   jQuery("[name='update_cart']").trigger("click"); 
  });
 </script>
 <?php
 endif;
}

/*
Show images in order details page
*/
add_filter( 'woocommerce_order_item_name', 'custom_order_display_product_image', 20, 3 );
function custom_order_display_product_image( $item_name, $item, $is_visible ) {
  // Targeting view order pages only
  if( is_wc_endpoint_url( 'view-order' ) ) {
    $product   = $item->get_product();
    $thumbnail = $product->get_image('thumbnail');
  if( $product->get_image_id() > 0 ) {
    $item_name = '<div class="item-thumbnail">' . $thumbnail . '</div>' . $item_name;
  }
 }
 return $item_name;
}


/**
* Woocommerce catalog mode
* @snippet     Hide Price & Add to Cart for Logged Out Users
*/
  
add_action( 'init', 'woocommrece_catalog_mode' );
  
function woocommrece_catalog_mode() {
	remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
	remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
	remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
	remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10 );
}


/*
*  yoast reset indexing 
*     add and run the function in your functions.php from the theme. 
*   This is meant for people who have a lower version of wordpress and the plugin Yoast Test Helper doesn;t install.
* theese lines are from that plugin and a little bit changed.
*  after running this function you need to visit a page so that yoast will recreate its tables and start indexing stuff again.
**/
   
function reset_indexables() {
    global $wpdb;


echo "test";
die; //remove after you sure you're on the corect place

    // Reset the prominent words calculation.
    $wpdb->delete( $wpdb->prefix . 'postmeta', [ 'meta_key' => '_yst_prominent_words_version' ] );

    WPSEO_Options::set( 'prominent_words_indexing_completed', false );
    \delete_transient( 'total_unindexed_prominent_words' );


    // Reset the internal link count.
    \delete_transient( 'wpseo_unindexed_post_link_count' );
    \delete_transient( 'wpseo_unindexed_term_link_count' );

    // phpcs:disable WordPress.DB.DirectDatabaseQuery.SchemaChange -- We know what we're doing. Really.
    $wpdb->query( 'DROP TABLE IF EXISTS ' . $wpdb->prefix . 'yoast_indexable' );
    $wpdb->query( 'DROP TABLE IF EXISTS ' . $wpdb->prefix . 'yoast_indexable_hierarchy' );
    $wpdb->query( 'DROP TABLE IF EXISTS ' . $wpdb->prefix . 'yoast_migrations' );
    $wpdb->query( 'DROP TABLE IF EXISTS ' . $wpdb->prefix . 'yoast_primary_term' );
    $wpdb->query( 'DROP TABLE IF EXISTS ' . $wpdb->prefix . 'yoast_seo_links' );

    // phpcs:enable WordPress.DB.DirectDatabaseQuery.SchemaChange

    WPSEO_Options::set( 'indexing_started', null );
    WPSEO_Options::set( 'indexables_indexing_completed', false );
    WPSEO_Options::set( 'indexing_first_time', true );

    // Found in Indexable_Post_Indexation_Action::TRANSIENT_CACHE_KEY.
    \delete_transient( 'wpseo_total_unindexed_posts' );
    // Found in Indexable_Post_Type_Archive_Indexation_Action::TRANSIENT_CACHE_KEY.
    \delete_transient( 'wpseo_total_unindexed_post_type_archives' );
    // Found in Indexable_Term_Indexation_Action::TRANSIENT_CACHE_KEY.
    \delete_transient( 'wpseo_total_unindexed_terms' );

    \delete_option( 'yoast_migrations_premium' );
    return \delete_option( 'yoast_migrations_free' );
}

/*
* add custom fields to users admin page
*/
function extra_profile_fields( $user ) { 
	$pages = get_pages();
	?>
   
    <h3><?php _e('Extra User Details'); ?></h3>
    <table class="form-table">
        <tr>
            <th><label for="estrafield">estrafield</label></th>
            <td>
            	<select name="estrafield" id="estrafield">
            		<option value="volvo">-</option>
            		<?php foreach ( $pages as $key => $page ) { ?>
						<option value="<?php echo $page->ID; ?>" <?php echo (get_the_author_meta( 'estrafield', $user->ID ) == $page->ID)? 'selected="selected"':''; ?>><?php echo $page->post_name; ?></option>
            		<?php } ?>
            	</select><br />
            </td>
        </tr>
    </table>
    <tt>In cazul utilizatorilor de tip "specialist", alegeti de aici specialistul pentru care se creeaza contul.</tt>
<?php

}

// Then we hook the function to "show_user_profile" and "edit_user_profile"
add_action( 'show_user_profile', 'extra_profile_fields', 10 );
add_action( 'edit_user_profile', 'extra_profile_fields', 10 );

function save_extra_profile_fields( $user_id ) {

    if ( !current_user_can( 'edit_user', $user_id ) )
        return false;

    /* Edit the following lines according to your set fields */
    update_usermeta( $user_id, 'estrafield', $_POST['estrafield'] );
}

add_action( 'personal_options_update', 'save_extra_profile_fields' );
add_action( 'edit_user_profile_update', 'save_extra_profile_fields' );


/*
* add alt to images in all woocommerce products
*
*/
add_filter('wp_get_attachment_image_attributes', 'change_attachement_image_attributes', 20, 2);
function change_attachement_image_attributes( $attr, $attachment ){

    // Get post parent
    $parent = get_post_field( 'post_parent', $attachment);

    // Get post type to check if it's product
    $type = get_post_field( 'post_type', $parent);
    if( $type != 'product' ){
        return $attr;
    }

    /// Get title
    $title = get_post_field( 'post_title', $parent);

    $attr['alt'] = $title . ' - Mayore';
    $attr['title'] = $title .' - Mayore';

    return $attr;
}


function yoast_seo_canonical_change( $canonical ) {
    if ( is_paged() ) {
        $current_link = home_url() . $_SERVER['REQUEST_URI'];
        return substr($current_link, 0, strpos( $current_link , 'page' ) );
    }
    return $canonical;
}
add_filter( 'wpseo_canonical', 'yoast_seo_canonical_change', 10, 1 );

//Ajax stuff
$title_nonce = wp_create_nonce( 'cstm_car_instal_calc' );
	wp_localize_script(
	    'your-javascript_script_js',
	    'my_ajax_obj',
	    array(
	        'ajax_url' => admin_url( 'admin-ajax.php' ),
	        'nonce'    => $title_nonce,
	    )
	);


//javscript file
		$.post(
			my_ajax_obj.ajax_url, 
			{         //POST request
				_ajax_nonce: my_ajax_obj.nonce,     //nonce
				action: "calculator_offer",            //action
				data: {data: data}                 //data
			}, function(data) {   
// console.log(data);
// return;   
				result = JSON.parse(data);
				
			}
		);


//php files

add_action( 'wp_ajax_nopriv_mycustomfunction', 'mycustomfunction' ); //not logged
add_action( 'wp_ajax_mycustomfunction', 'mycustomfunction' );			// loged
function mycustomfunction() {
	echo "testing";
}


/* contact form 7 add custom element*/
add_action( 'wpcf7_init', 'custom_add_form_tag_clock' );
function custom_add_form_tag_clock() {
    /* this creates the shortcode [clock]that you cna use in contact form 7 like
        [select clock "s" "b" "c"] or [text clock] or simply [clock]
     *      */
  wpcf7_add_form_tag( 'clock', 'custom_clock_form_tag_handler' ); // "clock" is the type of the form-tag
}
 
function custom_clock_form_tag_handler( $tag ) {
    /* here is the shortcode function that you can edit to add custom stuff in form*/
    /* !!NOTE: the name should be the same as the shortcode name
            because when you submit when the email ius send it will get the 
     *          values via the html select name value
     *      */
    $html = '<select name="clock" id="cars">
  <option value="volvo">Volvo</option>
  <option value="saab">Saab</option>
  <option value="mercedes">Mercedes</option>
  <option value="audi">Audi</option>
</select>';
    
  return $html;
}
/* optional you cna use this to mke sure the email sends the value*/
function my_special_mail_tag( $output, $name, $html ) {
if ( '[clock]' == $name )
$output = do_shortcode( "[$name]" );
 
return $output;
}
add_filter( 'wpcf7_special_mail_tags', 'my_special_mail_tag', 10, 3 );
/*end  contact form 7 add custom element*/
