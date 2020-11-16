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
