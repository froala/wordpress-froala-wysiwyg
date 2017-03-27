<?php
/**
 * The file is a helper for image plugin
 *
 * The functions are used mainly on the image plugin form
 * the Froala Editor. It will be referenced both from the
 * admin part of the website and also for the front-end
 * part.
 *
 *
 * The file contains 2 verifications used to show the images using
 * the Froala Editor image manager and one used to upload the pictures
 * the the website. Both return data in jSon format.
 *
 * Hint -> Direct path to this file can be obtained using "plugins_url('includes/froala-upload-to-server.php', dirname( __FILE__ ));"
 * It can be used in the "functions.php" file from themes folder.
 *
 * @link       www.froala.com
 * @since      1.0.0
 *
 * @package    Froala
 * @subpackage Froala/includes
 */


include_once( '../../../.././wp-load.php' );

/**
 * Standard function that get's a url of an image or thumb image
 * and returns the attachment id of it.
 *

 * @param $url      * The image url
 *
 * @return int      * The id of the attachment.
 */
function get_attachment_id( $url ) {
	$attachment_id = 0;
	$dir = wp_upload_dir();
	if ( false !== strpos( $url, $dir['baseurl'] . '/' ) ) {
		$file = basename( $url );
		$query_args = array(
			'post_type'   => 'attachment',
			'post_status' => 'inherit',
			'fields'      => 'ids',
			'meta_query'  => array(
				array(
					'value'   => $file,
					'compare' => 'LIKE',
					'key'     => '_wp_attachment_metadata',
				),
			)
		);
		$query = new WP_Query( $query_args );
		if ( $query->have_posts() ) {
			foreach ( $query->posts as $post_id ) {
				$meta = wp_get_attachment_metadata( $post_id );
				$original_file       = basename( $meta['file'] );
				$cropped_image_files = wp_list_pluck( $meta['sizes'], 'file' );
				if ( $original_file === $file || in_array( $file, $cropped_image_files ) ) {
					$attachment_id = $post_id;
					break;
				}
			}
		}
	}
	return $attachment_id;
}

if (isset($_GET['upload_image']) && $_GET['upload_image'] == 1 && !isset($_GET['view_images']) && !isset($_GET['delete_image'])) {

	if ($_FILES) {

		// These files need to be included as dependencies when on the front end.
		require_once( ABSPATH . 'wp-admin/includes/image.php' );
		require_once( ABSPATH . 'wp-admin/includes/file.php' );
		require_once( ABSPATH . 'wp-admin/includes/media.php' );

		// Let WordPress handle the upload.
		$attachment_id = media_handle_upload( 'file', 0 );

		if ( is_wp_error( $attachment_id ) ) {
			// There was an error uploading the image.
		} else {
			// The image was uploaded successfully!
			$file_path      = wp_get_attachment_url( $attachment_id );
			$response       = new StdClass;
			$response->link = $file_path;

			echo stripslashes( json_encode( $response ) );
		}
	}

}

if (isset($_GET['view_images']) && $_GET['view_images'] == 1 && !isset($_GET['upload_image']) && !isset($_GET['delete_image'])) {


	$query_images_args = array (
		'post_type'      => 'attachment',
		'post_mime_type' => 'image',
		'post_status'    => 'inherit',
		'posts_per_page' => - 1,
	);

	$query_images = new WP_Query( $query_images_args );

	$images = array();
	$obj = array();
	foreach ( $query_images->posts as $image ) {
		$images['url']   = wp_get_attachment_url( $image->ID );
		$images['thumb'] = wp_get_attachment_image_src( $image->ID, $size = 'thumbnail', $icon = false )[0];
		$images['tag']   = get_post_meta( $image->ID, '_wp_attachment_image_alt', true );
		$obj[]           = $images;
	}

	echo(stripslashes(json_encode($obj)));

}



