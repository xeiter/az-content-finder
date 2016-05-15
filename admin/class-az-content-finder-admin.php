<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://anton.zaroutski.com
 * @since      1.0.0
 *
 * @package    Az_Content_Finder
 * @subpackage Az_Content_Finder/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Az_Content_Finder
 * @subpackage Az_Content_Finder/admin
 * @author     Anton Zaroutski <anton@zaroutski.com>
 */
class Az_Content_Finder_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Az_Content_Finder_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Az_Content_Finder_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/az-content-finder-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Az_Content_Finder_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Az_Content_Finder_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/az-content-finder-admin.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Add an options page under the Settings submenu
	 *
	 * @since  1.0.0
	 */
	public function add_options_page() {

		$this->plugin_screen_hook_suffix = add_options_page(
			__( 'AZ Content Finder Settings', 'az-content-finder' ),
			__( 'AZ Content Finder', 'az-content-finder' ),
			'manage_options',
			$this->plugin_name,
			array( $this, 'display_options_page' )
		);

	}

	/**
	 * Render the options page for plugin
	 *
	 * @since  1.0.0
	 */
	public function display_options_page() {
		include_once 'partials/az-content-finder-admin-display.php';
	}

	/**
	 * Perform search for the specified search keyword
	 *
	 * @since 1.0.0
	 */
	public function perform_search() {

		$matches = array();

		if ( ! isset( $_POST['nonce_az_content_finder'] ) || ! wp_verify_nonce( $_POST['nonce_az_content_finder'], 'search_az_content_finder' ) ) {

			print 'Sorry, your nonce did not verify.';
			exit;

		} else {

			if ( !empty( $_POST['az_content_finder_keyword'] ) ) {

				$post_fields_to_search_in = array(
					'post_content',
					'post_title',
					'post_excerpt',
				);

				$all_posts = get_posts( array( 'post_type' => 'post', 'numberposts' => '0' ) );
				$all_pages = get_pages();


				$values_to_search = array(
					$_POST['az_content_finder_keyword']
				);

				foreach ( $values_to_search as $value_to_search ) {

					foreach ( $all_pages as $page ) {

						$result = $this->_search_in_post( $page, $post_fields_to_search_in, $value_to_search, $matches );

						if ( $result === FALSE ) {
							$fields  = get_post_meta( $page->ID );
							$matches = $this->_drill_in( $fields, $page, $value_to_search, $matches );
						} else {
							$matches = $result;
						}

					}

					foreach ( $all_posts as $page ) {

						// Search through content
						$matches = $this->_search_in_post( $page, $post_fields_to_search_in, $value_to_search, $matches );

						$fields  = get_post_meta( $page->ID );

						foreach ($fields as $key => $value) {
							if ( $key[0] != '_' ) {
								if ( is_array( $value ) ) {
									$fields[ $key ] = $value[0];
								} else {
									$fields[ $key ] = $value;
								}
							}
						}

						// Search through custom fields
						$matches = $this->_drill_in( $fields, $page, $value_to_search, $matches );

					}
				}
			}
		}

		/*echo '<pre>';
		print_r( $this->_combine_posts_meta( $matches, 'post_id' ) );
		echo '</pre>';
		exit;*/

		// Save the search results in the transient layer
		set_transient( 'az_content_finder_matches', $this->_combine_posts_meta( $matches, 'post_id' ), 60 );

		wp_redirect( admin_url( 'options-general.php?page=az-content-finder&keyword=' . $_POST['az_content_finder_keyword'] ) );
		die();

	}

	/**
	 * Combine array of post meta into sub arrays of meta related
	 * to the same post
	 *
	 * @param array $meta
	 * @param string $join_key
	 * @return array
	 */
	protected function _combine_posts_meta( $meta, $join_key ) {

		$result = array();

		foreach ( $meta as $meta_item ) {

			if ( !isset( $meta_item[ $join_key ] ) ) continue;

			if ( !isset( $result[ $meta_item[ $join_key ] ] ) ) {
				$result[ $meta_item[ $join_key ] ] = array();
			}

			$result[ $meta_item[ $join_key ] ][] = $meta_item;

		}

		return $result;
	}

	/**
	 * Search in the next level of next custom fields
	 *
	 * @param array $fields
	 * @param Post $page
	 * @param string $value_to_search
	 * @param array $matches
	 *
	 * @return array
	 */
	protected function _drill_in( $fields, $page, $value_to_search, $matches ) {

		foreach ( $fields as $field_key => $field_value ) {

			if ( is_array( $field_value ) ) {

				$matches = $matches + $this->_drill_in( $field_value, $page, $value_to_search, $matches );

			} else {

				if ( stripos( $field_value, $value_to_search ) === FALSE ) {

				} else {

					$matches[] = array(
						'type' => 'custom-field',
						'post_id' =>  $page->ID,
						'title' =>  $page->post_title,
						'url' =>  get_permalink( $page->ID ),
						'field_key' => $field_key,
						'field_value' => trim( htmlspecialchars( $field_value ) )
					);

				}

			}

		}

		return $matches;

	}

	/**
	 * Search in post by specified value
	 *
	 * @param Post $post
	 * @param array $fields_to_search_in
	 * @param stinrg $value_to_search
	 * @param array $matches
	 *
	 * @return array|bool
	 */
	protected function _search_in_post( $post, $fields_to_search_in, $value_to_search, $matches ) {

		foreach ( $fields_to_search_in as $field_key ) {

			$search_result = stripos( $post->$field_key, $value_to_search );

			if ( $search_result !== FALSE ) {

				$matches[] = array(
					'type' => 'post/page',
					'post_id' =>  $post->ID,
					'title' =>  $post->post_title,
					'url' =>  get_permalink( $post->ID ),
					'field_key' => $field_key,
					'field_value' => trim( htmlspecialchars( $post->$field_key ) )
				);

				return $matches;

			}

		}

		return array();

	}
	
}
