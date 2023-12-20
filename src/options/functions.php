<?php

if ( ! defined( 'WPINC' ) ) {
	die;
}

/* 
 * @name 	bc_import_csv__get_options
 * @info 	Retrieve the list of options set in the plugin page.
 * @author  Basecamp
 */ 

function bc_import_csv__get_options() {

	$options =  get_option( 'bc_import_csv', array() );

	if(empty($options) || empty($options['post_type']) || empty($options['csv_file'])) {
		return (object) array(
			'uploaded' => false,
			'post_type' => null,
			'csv_file' => null
		);
	}

	$options['uploaded'] = true;

	return (object) $options;

}

function bc_import_csv__get_complete_notice() {
	if(isset($_GET['page']) && $_GET['page'] == 'wp_import_csv' && isset($_GET['imported'])) {
		bc_import_csv__get_notice(
			'<strong>Imported!</strong> CSV import has complete, please check the <code>debug.log</code> file for results.'
		);
	}

}

function bc_import_csv__get_notice($text, $classes = 'notice-success') {

	$class = 'notice ' . $classes ;
	$message = __( $text, 'bc_import_csv' );

	printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), ( $message ) );


}

function bc_import_csv__log($line) {
	$log_path = plugin_dir_path(BC_IMPORT_CSV_PLUGIN_DIR).'debug.log';
	$log_line = '[' . date('Y-m-d H:i:s', strtotime('now')) . '] ' . $line . PHP_EOL;
	error_log($log_line, 3, $log_path);
}

function bc_import_csv__prepare_posts() {

	if(isset($_POST['do_import_csv'])) {

		$import = $_POST['import_field'];
		$posts = array();
		$data = bc_import_csv__get_csv_data(true);
		$options = bc_import_csv__get_options();
		$new_post_template = array();

		/*
		 * Filter posted fields.
		 */
		if($options->uploaded) {

			foreach($import as $key => $value) {

				if($value !== '0') {
					$new_post_template[$key] = $value;
				}

			}

			/*
			 * Map template to a new post array.
			 */ 
			foreach($data as $item) {

				$post = array(
					'post_type' => $options->post_type,
					'post_author' => get_current_user_id()
				);

				$meta_data = array();

				foreach($new_post_template as $key => $value) {

					if(isset($item[$value])) {

						$default_value = $item[$value];
						$filter_value = apply_filters('bc_import_csv__property_hook', array($key, $default_value, $value));

						if(!empty($filter_value)) {

							// If theres no filter applied, the array is returned, let's just check it's not the same
							// as what we sent before setting it in the post.
							if(is_array($filter_value) && json_encode($filter_value) == json_encode(array($key, $default_value))) {
								$filter_value = $default_value;
							}

							$default_value = $filter_value;
						}

						if(substr($key, 0, strlen('field_')) == 'field_') {
							$meta_data[$key] = $default_value;
						} else {
							$post[$key] = $default_value;
						}
						

					}
				}

				$posts[] = array(
					'post' => $post,
					'meta_data' => $meta_data
				);
			}

			if(!empty($posts)) {

				return $posts;

			}
		}
	}

	return false;

} 

function bc_import_csv__get_csv_data_header() {

	$data = bc_import_csv__get_csv_data();

	if(empty($data)) {
		return array();
	}

	return array_values(current($data));
}

function bc_import_csv__get_csv_data($combineHeaders = false) {

	$site_url = get_site_url();
	$options = bc_import_csv__get_options();

	if($options->uploaded) {
		$csv_file_path = ABSPATH . ltrim(str_replace($site_url, '', $options->csv_file), '/');

		$csv_data = fopen($csv_file_path, 'r');
	    while (!feof($csv_data) ) {
	        $lines[] = fgetcsv($csv_data, 1000, ',');
	    }

	    fclose($csv_data);

	    if($combineHeaders) {

	    	$header = array_shift($lines);
		    $csv    = array();
		    
		    foreach($lines as $row) {
		        $csv[] = array_combine($header, $row);
		    }

		    return $csv;

	    }

	    return $lines;

	}	

	return array();

}

function bc_import_csv__get_post_fields() {

	$fields = array(
		'Post Title' => 'post_title',
		'Post Author' => 'post_author',
		'Post Date' => 'post_date',
		'Post Content' => 'post_content',
		'Post Excerpt' => 'post_excerpt',
		'Post Name' => 'post_name',
		'Post Password' => 'post_password',
		'Post Menu Order' => 'menu_order',
		'Post Status' => 'post_status',
	);


	$options = bc_import_csv__get_options();

	if($options->uploaded && function_exists('acf_get_field_groups')) {
		$groups = acf_get_field_groups(array('post_type' => $options->post_type));

		foreach($groups as $group) {
			foreach(acf_get_fields($group['key']) as $field) {
				$fields['[ACF Field] ' . $field['label']] = $field['key'];
			}
		}

	}

	return $fields;
}
