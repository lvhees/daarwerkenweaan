<?php

	include 'custom_fields.php';

	function p(){
		var_dump(func_get_args());
	}

	function is_ajax_call(){
		return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
	}

	/**
	 * Add menu support
	 */
	function register_header_menu() {
//		register_nav_menu('compare-menu',__('Vergelijken'));
//		register_nav_menu('secondary-menu',__('Rechts boven'));
	}
	add_action( 'init', 'register_header_menu' );

	/**
	 * Register sidebars
	 */
	function widget_sidebar() {
		register_sidebar(array(
			'name' => 'Homepage',
			'id' => 'feed',
			'description' => 'Twitter feed op homepage',
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget' => '</div>',
			'before_title' => '<h3>',
			'after_title' => '</h3>')
		);
	}
	add_action( 'widgets_init', 'widget_sidebar');

	/**
	 * Create custom page types for content
	 */
	function register_theme_options() {

		// Images sizes
		add_image_size('slideshow', 1920, 700);

		// Allow featured images
		//add_theme_support('post-thumbnails');

		// Register post types
		register_post_type('nieuws',
			array(
				'menu_icon' => 'dashicons-welcome-widgets-menus',
				'labels' => array(
					'name' => __('Nieuws'),
					'singular_name' => __('Nieuws'),
				),
				'public' => true,
				'menu_position' => 5,
				'supports' => array(
					'title',
					'editor',
					'excerpt',
					'revisions',
					'thumbnail'
				)
			)
		);

		register_post_type('facts',
			array(
				'menu_icon' => 'dashicons-star-filled',
				'labels' => array(
					'name' => __('Feiten &amp; Cijfers'),
					'singular_name' => __('Feiten &amp; Cijfers'),
					'add_new' => __('Nieuwe Feiten &amp; Cijfers'),
				),
				'public' => true,
				'menu_position' => 5,
			)
		);

		register_post_type('agenda',
			array(
				'menu_icon' => 'dashicons-calendar-alt',
				'labels' => array(
					'name' => __('Agenda'),
					'singular_name' => __('Agenda'),
					'add_new' => __('Nieuw item'),
				),
				'public' => true,
				'menu_position' => 5,
			)
		);

		register_post_type('links',
			array(
				'menu_icon' => 'dashicons-link-alt',
				'labels' => array(
					'name' => __('Links'),
					'singular_name' => __('Links'),
					'add_new' => __('Nieuw link'),
				),
				'public' => true,
				'menu_position' => 5,
			)
		);

	}
	add_action('init', 'register_theme_options');

	/**
	 * Remove menu items
	 */
	function remove_menu_items(){
		remove_menu_page( 'edit.php' );
	}
	add_action('admin_menu', 'remove_menu_items');

	/**
	 * Setup styling and script queue
	 */
	function register_app_css(){
		if (in_array($_SERVER['REMOTE_ADDR'], array('127.0.0.1', '::1'))) {
			wp_enqueue_script('livereload', 'http://localhost:35729/livereload.js?snipver=1');
		}

		wp_register_script('base', get_stylesheet_directory_uri() . '/scripts/vendor/base.js');
		wp_enqueue_script('request-animation-frame', get_stylesheet_directory_uri() . '/scripts/vendor/requestAnimationFrame.js', array('jquery'));

		wp_enqueue_script('zwolle-scripts', get_stylesheet_directory_uri() . '/scripts/main.js', array('jquery'));

		wp_deregister_style('main-style'); // We don't use the main style.css
		wp_enqueue_style('zwolle-style', get_stylesheet_directory_uri() . '/style.min.css', array(), filemtime(get_stylesheet_directory() . '/style.min.css'));

	}
	add_action('wp_enqueue_scripts', 'register_app_css', 10000);


	function create_new_user($post_id) {

		// bail early if not a contact_form post
		if($post_id !== 'user_new' ) {
			return;
		}

		$email = sanitize_email($_POST['acf']['field_5602aa86b3cbd']);

		// Check if e-mail already used
		if(get_user_by('email', $email)){
			echo "Dit e-mail adres wordt al gebruikt. Bent u uw wachtwoord vergeten? Gebruik dan de wachtwoord-vergeten function hier beneden.";
		}
		// Register new e-mail
		else {

			$password = wp_generate_password(12, true);
			$hash = sha1(wp_generate_password(20));

			// Create user
			$user_id = wp_create_user($email, $password, $email);
			update_user_meta($user_id, 'login_hash', $hash);
			update_user_meta($user_id, 'email', $email);

			$login_url = site_url('registreren');

			// email data
			$to = $email;
			$headers = 'From: "Daarwerkenweaan.nl" <info@daarwerkenweaan.nl>' . "\r\n";
			$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

			$subject = "Registratie";
			$body = nl2br("Bedankt voor uw registratie.

				U kunt via de volgende url inloggen: $login_url?login=$hash&id=$user_id

				Of met de volgende inloggegevens:
				<strong>E-mail</strong>: $email
				<strong>Wachtwoord</strong>: $password
				Op $login_url

				Als u eenmaal bent ingelogd, wordt u gevraagd om uw profiel in te vullen voor u verder kunt.
			");



			// send email
			wp_mail($to, $subject, $body, $headers);

			echo "Bedank voor uw registratie, er is een e-mail verzonden om uw account te creeeren.";

		}

	}
	add_filter('acf/save_post' , 'create_new_user');


	/**
	 * Login user
	 * And if logged in, force redirect to profile page
	 */
	function login_user() {

		// Ignore on logout url
		if(strpos(wp_logout_url('/'), $_SERVER['REQUEST_URI']) > -1) return;

		if(is_user_logged_in() && !current_user_can('edit_posts') && !isset($_POST)){
			$profile_url = WP_HOME . '/profiel/';

			// Check if profile is filled in
			if($profile_url != WP_HOME . $_SERVER['REQUEST_URI']){
				$user_id = get_current_user_id();
				$profile = get_user_meta($user_id, 'filled_in_profile', true);
				if(!$profile){
					wp_redirect($profile_url);
					exit();
				}
			}

		}
		elseif(!empty($_GET['login']) && !empty($_GET['id'])){

			$password_hash = sanitize_text_field($_GET['login']);
			$user_id = (int) $_GET['id'];

			$user_hash = get_user_meta($user_id, 'login_hash', true);

			if($user_hash == $password_hash){
				wp_clear_auth_cookie();
				wp_set_current_user($user_id);
				wp_set_auth_cookie($user_id);

				wp_redirect(site_url('/profiel'));
				exit();
			}


		}
		if($_POST && !empty($_POST['acf']['field_5628f44c5da82'])){
			generate_new_password($_POST['acf']['field_5628f44c5da82']);
			wp_redirect(site_url('/registreren/?forgotten=send'));
			exit();
		}
		else {

			if(!$_POST || empty($_POST['acf']['field_5603ea0931fe4']) || empty($_POST['acf']['field_5603ea1631fe5']))
				return;

			$email = sanitize_email($_POST['acf']['field_5603ea0931fe4']);
			$password = sanitize_text_field($_POST['acf']['field_5603ea1631fe5']);

			// Check if e-mail already used
			$user = get_user_by('email', $email);
//		p($user, $password, wp_check_password($password, $user->data->user_pass, $user->ID));
//		exit();
			if($user && wp_check_password($password, $user->data->user_pass, $user->ID)){

				$creds = array(
					'user_login' => $user->user_login,
					'user_password' => $password,
					'remember' => true,
				);

				wp_signon($creds, false);
				wp_redirect(site_url('/profiel'));
				exit();
			}

		}

	}
	add_action('after_setup_theme', 'login_user');


	/**
	 * Handles sending password retrieval email to user.
	 *
	 * @uses $wpdb WordPress Database object
	 * @param string $user_login User Login or Email
	 * @return bool true on success false on error
	 */
function generate_new_password($user_login) {
	global $wpdb, $current_site;

	if ( empty( $user_login) ) {
		return false;
	} else if ( strpos( $user_login, '@' ) ) {
		$user_data = get_user_by( 'email', trim( $user_login ) );
		if ( empty( $user_data ) )
			return false;
	} else {
		$login = trim($user_login);
		$user_data = get_user_by('login', $login);
	}

	if ( !$user_data ) return false;

	// redefining user_login ensures we return the right case in the email
	$user_login = $user_data->user_login;
	$user_email = $user_data->user_email;
	$new_password = wp_generate_password(12, true);

	wp_set_password($new_password, $user_data->ID);

	$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

	$message = 'U heeft een nieuw wachtwoord aangevraagd voor ' . $blogname . '.' . "\r\n\r\n";
	$message .= 'U kunt zich aanmelden op ' . network_home_url( '/registreren/' ) . " met de volgende gegevens: \r\n\r\n";
	$message .= sprintf('E-mailadres: %s', $user_login) . "\r\n";
	$message .= sprintf('Wachtwoord: %s', $new_password) . "\r\n\r\n";

	$title = 'Nieuw wachtwoord aanvraag';

	$headers = 'From: "Daarwerkenweaan.nl" <info@daarwerkenweaan.nl>' . "\r\n";
	if ( $message && !wp_mail($user_email, $title, $message, $headers) )
		wp_die( __('The e-mail could not be sent.') . "<br />\n" . __('Possible reason: your host may have disabled the mail() function...') );

	return true;
}


	/**
	 * Remove force redirect if used filled in his profile
	 */
	function update_profile_data($post_id) {

		if(is_user_logged_in()){

			$user_id = get_current_user_id();
			if($post_id == 'user_' . get_current_user_id()){
				update_user_meta($user_id, 'filled_in_profile', true);
			}

		}

	}
	add_filter('acf/save_post' , 'update_profile_data');


	/** Replace Gravatar with uploaded */
	function replace_profile_avatar( $avatar, $id_or_email, $args) {

		// Get user by id or email
		if ( is_numeric( $id_or_email ) ) {
			$user_id   = (int) $id_or_email;
		} elseif ( is_object( $id_or_email ) ) {
			if ( ! empty( $id_or_email->user_id ) ) {
				$user_id   = (int) $id_or_email->user_id;
			}
		} else {
			$user = get_user_by( 'email', $id_or_email );
			$user_id   = $user->ID;
		}

		$image_id = get_user_meta($user_id, 'avatar', true);


		if ( ! $image_id ) {
			return $avatar;
		}

		$size = "thumbnail";
		if($args['size'] > 300) $size = "slideshow";

		$image_url  = wp_get_attachment_image_src( $image_id, $size );

		return $image_url[0];


	}
	add_filter('get_avatar_url', 'replace_profile_avatar', 10, 3);

	/**
	 * Callback function to filter the MCE settings
	 */
	function wpb_mce_buttons_2($buttons) {
		array_unshift($buttons, 'styleselect');
		return $buttons;
	}
	add_filter('mce_buttons_2', 'wpb_mce_buttons_2');

	function insert_sideline_styles( $init_array ) {
		if(!empty($_GET['post']) && (int) get_option('page_on_front') == (int) $_GET['post'])
			return $init_array;

		$style_formats = array(
			array(
				'title' => 'Kantlijn links',
				'inline' => 'span',
				'classes' => 'sidenote left',
			),
			array(
				'title' => 'Kantlijn rechts',
				'inline' => 'span',
				'classes' => 'sidenote right',
			),
		);

		$init_array['style_formats'] = json_encode( $style_formats );

		return $init_array;

	}
	add_filter( 'tiny_mce_before_init', 'insert_sideline_styles' );

	function sideline_editor_style(){
		add_editor_style(get_stylesheet_directory_uri() . '/sideline.editor.css');
	}
	add_action('admin_init', 'sideline_editor_style');

	// show admin bar only for admins and editors
	if (!current_user_can('edit_posts')) {
		add_filter('show_admin_bar', '__return_false');
	}


	// Remove stuff from header
	remove_action('wp_head', 'feed_links_extra', 3 ); // Display the links to the extra feeds such as category feeds
	remove_action('wp_head', 'feed_links', 2 ); // Display the links to the general feeds: Post and Comment Feed
	remove_action('wp_head', 'rsd_link' ); // Display the link to the Really Simple Discovery service endpoint, EditURI link
	remove_action('wp_head', 'wlwmanifest_link' ); // Display the link to the Windows Live Writer manifest file.
	remove_action('wp_head', 'index_rel_link' ); // index link
	remove_action('wp_head', 'parent_post_rel_link', 10, 0 ); // prev link
	remove_action('wp_head', 'start_post_rel_link', 10, 0 ); // start link
	remove_action('wp_head', 'adjacent_posts_rel_link', 10, 0 ); // Display relational links for the posts adjacent to the current post.
	remove_action('wp_head', 'wp_generator' ); // Display the XHTML generator that is generated on the wp_head hook, WP version
	remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0 );
	remove_action('wp_head', 'wp_shortlink_wp_head', 10, 0 );
	remove_action('wp_head', 'wp_canonical_wp_head', 10, 0 );
	remove_action('wp_head', 'print_emoji_detection_script', 7 );
	remove_action('admin_print_scripts', 'print_emoji_detection_script' );
	remove_action('wp_print_styles', 'print_emoji_styles' );
	remove_action('admin_print_styles', 'print_emoji_styles' );
