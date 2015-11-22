<?php

/*
 * Plugin Name: EventPress Redux
 * Description: Like EventPress, Only Better! ;-)
 * Author: The Atlanta WordPress Coder's Guild™
 * Author URI: http://thecodersguild.com
 */
class EventPress_Redux {

	/**
	 *
	 */
	const EVENT_POST_TYPE = 'epr_event';

	/**
	 *
	 */
	const VENUE_POST_TYPE = 'epr_venue';

	/**
	 *
	 */
	const REGISTRATION_POST_TYPE = 'epr_register';

	/**
	 *
	 */
	const EVENT_TYPE_TAXONOMY = 'epr_event_type';


	/**
	 * Hook all actions and filters required for the plugin
	 */
	static function on_load() {

		add_action( 'init', array( __CLASS__, '_init' ) );

		add_action( 'edit_form_after_title', array( __CLASS__, '_edit_form_after_title' ) );
		add_action( 'save_post', array( __CLASS__, '_save_post' ) );

		add_action( 'admin_enqueue_scripts', array( __CLASS__, '_admin_enqueue_scripts' ) );
		add_action( 'wp_enqueue_scripts', array( __CLASS__, '_wp_enqueue_scripts' ) );

		add_action( 'wp_ajax_epr-registration', array( __CLASS__, '_wp_ajax_epr_registration' ) );
		add_action( 'wp_ajax_nopriv_epr-registration', array( __CLASS__, '_wp_ajax_epr_registration' ) );
		add_filter( 'the_content', array( __CLASS__, '_the_content' ) );

	}

	/*
	 * Load JS & CSS for the theme template files
	 */
	static function _wp_enqueue_scripts() {

		if ( is_singular( self::EVENT_POST_TYPE ) ) {

			wp_enqueue_script(
				'eventpress-redux',
				plugins_url( 'js/eventpress-redux.js', __FILE__ ),
				array( 'jquery' )
			);
			wp_localize_script(
				'eventpress-redux',
				'EPR', array(
					'ajaxurl' => admin_url( 'admin-ajax.php' ),
					'nonce'   => wp_create_nonce( 'epr-registration' ),
				)
			);

		}
	}

	/*
	 * Load JS & CSS for the admin console
	 */
	static function _admin_enqueue_scripts() {

		wp_enqueue_style(
			'select2',
			'//cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/css/select2.min.css'
		);

		wp_enqueue_script(
			'select2',
			plugins_url( 'js/select2.js', __FILE__ ), array( 'jquery' )
		);

		wp_enqueue_script(
			'eventpress-redux-admin',
			plugins_url( 'js/eventpress-redux-admin.js', __FILE__ ),
			array( 'select2' ),
			false,
			true
		);

	}

	/**
	 * Initialize plugin functionality
	 */
	static function _init() {

		self::_register_post_types();
		self::_register_taxonomies();
		self::_flush_urls();

	}

	/**
	 * Add Post Type registrations
	 */
	private static function _register_post_types() {

		register_post_type( self::EVENT_POST_TYPE, array(

			'public'    => true,
			'label'     => __( 'Events', 'eventpress-redux' ),
			'rewrite'   => array(
				'slug' => 'events'
			),

		) );

		register_post_type( self::VENUE_POST_TYPE, array(

			'public'   => true,
			'label'    => __( 'Venues', 'eventpress-redux' ),
			'rewrite'  => array(
				'slug' => 'venues'
			),
			'supports' => array(
				'title',
			),

		) );

		register_post_type( self::REGISTRATION_POST_TYPE, array(

			'public'       => false,
			'show_ui'      => true,
			'label'        => __( 'Registrations', 'eventpress-redux' ),
			'supports'     => array(
				'title',
			),

		) );


	}

	/**
	 * Add Taxonomy registrations
	 */
	private static function _register_taxonomies() {

		register_taxonomy( self::EVENT_TYPE_TAXONOMY, self::EVENT_POST_TYPE, array(

			'label'             => __( 'Event Type', 'eventpress-redux' ),
			'public'            => true,
			'show_ui'           => true,
			'show_admin_column' => true,
			'hierarchical'      => true,

		) );

	}

	/**
	 * Flush the URLs any time this file changes.
	 */
	static function _flush_urls() {

		$datetime = date( 'Ymdhis', filemtime( __FILE__ ) );

		if ( ! get_option( $option_name ="epr-event-urls-flushed-{$datetime}" ) ) {

			flush_rewrite_rules();
			update_option( $option_name, true );

		}

	}

	/**
	 * Add an Event registration form to the end of Content
	 *
	 * @param $content
	 *
	 * @return string
	 */
	static function _the_content( $content ) {

		if ( is_singular( self::EVENT_POST_TYPE ) ) {

			ob_start();
			include __DIR__ . '/templates/registration.php';
			$content .= ob_get_clean();

		}

		return $content;

	}

	/**
	 * Add a data entry form for Events between the Title field and TinyMCE for the content
	 *
	 * @param WP_Post $post
	 */
	static function _edit_form_after_title( $post ) {

		if ( self::EVENT_POST_TYPE === $post->post_type ) :

			$start = get_post_meta( $post->ID, '_epr_event[start]', true );
			$end   = get_post_meta( $post->ID, '_epr_event[end]',   true );
			$options = self::_get_venue_select_options_html( $post, '_epr_event[venue_id]' );
			include __DIR__ . '/templates/event-form.php';

		endif;

	}

	/**
	 * Get <option>s for HTML <select> of Venues
	 *
	 * @param WP_Post $post
	 * @param string $meta_field_name
	 *
	 * @return string
	 */
	private static function _get_venue_select_options_html( $post, $meta_field_name ) {

		$html = array(
			"<option value=\"0\">" .
				__( 'Select a Venue', 'eventpress-redux' ) .
			'</option>'
		);

		$query = new WP_Query( array(
			'post_type'      => self::VENUE_POST_TYPE,
			'posts_per_page' => -1,
		));

		//	print_r( $query );

		if ( $query->post_count ) {

			$current_venue = get_post_meta( $post->ID, $meta_field_name, true );

			foreach ( $query->posts as $venue ) {

				$option = array();
				$option[] = "<option value=\"";
				$option[] = intval( $venue->ID );
				if ( intval( $venue->ID ) === intval( $current_venue ) ) {
					$option[] = '" selected="selected';
				}
				$option[] = '">';
				$option[] = get_the_title( $venue );
				$option[] = '</option>';
				$html[] = implode( $option );

			}

		}

		return implode( "\n", $html );

	}

	/**
	 * Save the custom fields for Events
	 *
	 * @param int $post_id
	 */
	static function _save_post( $post_id ) {

		do {

			if ( self::EVENT_POST_TYPE !== get_post_type( $post_id ) ) {
				break;
			}

			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
				break;
			}

			if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
				break;
			}

			if ( ! isset( $_POST['epr_event_nonce'] ) ) {
				break;
			}

			if ( ! wp_verify_nonce( $_POST['epr_event_nonce'], 'save_event' ) ) {
				break;
			}

			if ( ! current_user_can( 'edit_post', $post_id ) ) {
				break;
			}

			if ( ! isset( $_POST['epr_event'] ) ) {
				break;
			}

			if ( ! is_array( $_POST['epr_event'] ) ) {
				break;
			}

			$event = $_POST['epr_event'];

			if ( ! isset( $event['start'] ) ) {
				$event['start'] = '';
			}
			if ( ! isset( $event['end'] ) ) {
				$event['end'] = '';
			}
			if ( ! isset( $event['venue_id'] ) ) {
				$event['venue_id'] = 0;
			}
			update_post_meta( $post_id, '_epr_event[start]', $event['start'] );
			update_post_meta( $post_id, '_epr_event[end]', $event['end'] );
			update_post_meta( $post_id, '_epr_event[venue_id]', $event['venue_id'] );

		} while ( false );

		return;

	}

	/**
	 * Add an Event Registration for an Email Address
	 */
	static function _wp_ajax_epr_registration() {

		do {

			$nonce    = filter_input( INPUT_POST, 'nonce' );
			$email    = filter_input( INPUT_POST, 'email', FILTER_VALIDATE_EMAIL );
			$event_id = filter_input( INPUT_POST, 'event_id', FILTER_VALIDATE_INT );

			if ( ! wp_verify_nonce( $nonce, 'epr-registration' ) ) {
				break;
			}

			$title = get_the_title( $event_id );
			$post_id = wp_insert_post( array(
				'post_title'  => $email,
				'post_parent' => $event_id,
				'post_status' => 'publish',
				'post_type'   => self::REGISTRATION_POST_TYPE,
			));

			if ( ! $post_id ) {
				break;
			}

			update_post_meta( $post_id, '_epr_email', $email );
			update_post_meta( $post_id, '_epr_event_id', $event_id );

		} while ( false );

		$response = array(
			'nonce'    => $nonce,
			'event_id' => $event_id,
			'email'    => $email,
		);

		if ( ! $post_id ) {
			wp_send_json_error( $response );
		} else {
			wp_send_json_success( $response );
		}

		return;
	}

	/**
	 * Ensure only value HTML for the HTML5 <select> element is used.
	 *
	 * @param string $html
	 *
	 * @return string
	 */
	static function sanitize_html_select( $html ) {

		return wp_kses( $html, array(

			'select' => array(
				'autofocus' => true,
				'disabled'  => true,
				'form'      => true,
				'multiple'  => true,
				'name'      => true,
				'required'  => true,
				'size'      => true,
			),
			'option' => array(
				'value'    => true,
				'label'    => true,
				'disabled' => true,
				'selected' => true,
			),

		));

	}

}

EventPress_Redux::on_load();










