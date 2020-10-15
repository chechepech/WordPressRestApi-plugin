<?php
/*
Plugin Name: Go Further with AJAX
Description: Displays links to related posts through the WP-API
Version: 0.1
Author: Cheche Pech
Author URI: https:chechepech.gq
License: GPL2
License URI: https://www.gnu.or/licenses/gpl-2.0.html
Domain Path /languages
Text Domain: gofurther
*/

// Add various fields to the JSON output
function gofurther_register_fields(){

	//Add Author Name
	register_rest_field('post',
		'author_name',
		array(
			'get_callback' => 'gofurther_get_author_name',
			'update_callback' => null,
			'schema' => null
		)
	);

	//Add Featured Image
	register_rest_field('post',
		'featured_image_src',
		array(
			'get_callback' => 'gofurther_get_image_src',
			'update_callback' => null,
			'schema' => null
		)
	);	
}

function gofurther_get_author_name($object, $field_name, $request){
	return get_the_author_meta('display_name');
}

function gofurther_get_image_src($object, $field_name, $request){
	
	$feat_img_array = wp_get_attachment_image_src($object['featured_image'],'thumbnail',true);
	return $feat_img_array[0];
}

add_action('rest_api_init', 'gofurther_register_fields');

// Hook in all the important things
function gofurther_scripts(){
	if(is_single() && is_main_query()){
	//Get plugin stylesheet
	wp_enqueue_style('gofurther-style',plugin_dir_url(__FILE__).'css/styles.css',array(),'0.1','all');
	wp_enqueue_script('gofurther-script',plugin_dir_url(__FILE__).'js/gofurther.ajax.js',array('jquery'),'0.1',true);

	global $post;
	$post_id = $post->ID;
	
	wp_localize_script('gofurther-script', 'postdata',
			array(
				'post_id' => $post_id,
				'json_url' => gofurther_get_json_query()
			)
		);
	}
}
add_action('wp_enqueue_scripts','gofurther_scripts');

/**
* Create REST API url
* - Get the current categories
* - Get the category IDs
* - Create the arguments for categories and posts-per-page
* - Create the url
*/

function gofurther_get_json_query(){
	$cats = get_the_category();
	$cat_ids = array();

	foreach ($cats as $cat) {
		$cat_ids[] = $cat->term_id;
	}

	$args = array(
		'filter[cat]' => implode(',', $cat_ids),
		'filter[posts_per_page]' => 5
	);

	$url = add_query_arg($args, rest_url('wp/v2/posts'));

	return $url;
}

//Base HTML to be added to the bottom of a post
function gofurther_baseline_html(){
	$baseline = '<section id="related-posts" class="related-posts">';
	/*$baseline .= gofurther_get_json_query();*/
	$baseline .= '<a href="#" class="get-related-posts">Get related posts</a>';
	$baseline .= '<div class="ajax-loader"><img src="' . plugin_dir_url( __FILE__ ) . 'css/spinner.svg" width="32" height="32" /></div>';
	$baseline .= '</section><!-- .related-posts -->';
	return $baseline;
}

//Bootstrap this whole thing onto the botom of single posts
function gofurther_display($content){
	if(is_single() && is_main_query()){
		$content .= gofurther_baseline_html();
	}
	return $content;
}

add_filter('the_content','gofurther_display');