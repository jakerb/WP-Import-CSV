<?php

if ( ! defined( 'WPINC' ) ) {
	die;
}


/* 
 * @name 	bc_import_csv__options_page
 * @info 	Register WP Admin Options for Import CSV Plugin
 * @author  Basecamp
 */ 

add_action( 'init', 'bc_import_csv__options_page', 20 );
add_action( 'init', 'bc_import_csv__options_page_save_hook', 10 );
add_action( 'admin_notices', 'bc_import_csv__get_complete_notice' );

function bc_import_csv__options_page_save_hook() {

	$posts = bc_import_csv__prepare_posts();

	if(is_user_logged_in() && is_array($posts)) {

		$import_count = count($posts);

		bc_import_csv__log("Beginning import...");

		foreach($posts as $index => $item) {
			$import_iteration = $index+1;

			bc_import_csv__log("Importing {$import_iteration} of {$import_count}...");

			$post = $item['post'];
			$meta_data = $item['meta_data'];

			$post_id = wp_insert_post( $post );

			if($post_id) {	

				foreach($meta_data as $key => $value) {
					$meta_id = update_field( $key, $value , $post_id );

					if($meta_id) {
						bc_import_csv__log("Import SUCCEEDED for post {$import_iteration} meta {$key}!");
					} else {
						bc_import_csv__log("Import FAILED for post {$import_iteration} meta {$key}!");
					}

				}

				bc_import_csv__log("Import SUCCEEDED for post {$import_iteration}!");


			} else {

				bc_import_csv__log("Import FAILED for post {$import_iteration}!");

			}

		}

		header('Location: admin.php?page=wp_import_csv&imported=true'); exit();
	}
}



function bc_import_csv__options_page() {

		if(is_user_logged_in()) {

			$pages = array(
				'bc_import_csv' => array(
					'page_title' => __('WP Import CSV', 'bc_import_csv'),
					'sections' => array(
						'select_a_post_type' => array(
							'title' 	=> __('Step 1. Select a Post Type', 'bc_import_csv'),
							'text' 		=> '<p>' . __('Choose which post type you want to import to.') . '</p>',
							'fields' 	=> array(
								'post_type' => array(
									'title'			=> __( 'Post Type', 'bc_import_csv' ),
									'type'			=> 'select',
									'choices'		=> get_post_types( '', 'names' ),
								)
							)
						),
						'upload_a_csv_file' => array(
							'title' 	=> __('Step 2. Upload a CSV File', 'bc_import_csv'),
							'text' 		=> '<p>' . __('Choose a valid CSV to import, ensure the first row is the header.') . '</p>',
							'fields' 	=> array(
								'csv_file' => array(
									'title'			=> __( 'CSV File', 'bc_import_csv' ),
									'type'			=> 'media'
								)
							)
						),
						'prepare_csv_file' => array(
							'title' 	=> __('Step 3. Prepare CSV File', 'bc_import_csv'),
							'text'		=> '<p>' . __('Click the button below before moving to Step 4. This will prepare your CSV file ready for import.', 'bc_import_csv') . '</p>',
							'include'		=> plugin_dir_path( __FILE__ ) . '/import/prepare_csv_file.php',
						),
						'map_csv_file' => array(
							'title' 	=> __('Step 4. Map your CSV File', 'bc_import_csv'),
							'text'		=> '<p>' . __('Map the columns of your CSV file to properties and fields of your post type.', 'bc_import_csv') . '</p>',
							'include'		=> plugin_dir_path( __FILE__ ) . '/import/map_csv_file.php',
						)	
					)
				)
			);

			$options = new RationalOptionPages( $pages );
		}
	}