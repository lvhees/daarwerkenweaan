<?php
/**
 * Template Name: Netwerk
 */

wp_enqueue_script('google-maps', 'https://maps.googleapis.com/maps/api/js?key=AIzaSyDluzxzDG1fGkfBJ3iWyqMaii5SABnnPRg&v=3');
wp_enqueue_script('network', get_stylesheet_directory_uri() . '/scripts/network.js', array('jquery', 'base'));
wp_enqueue_script('google-maps-clusterer', get_stylesheet_directory_uri() . '/scripts/vendor/markerclusterer.js', array('google-maps'));

get_header();

include('_slideshow.php');

$users_raw = get_users(array(
	'role' => 'subscriber'
));

$users = array();
foreach($users_raw as $u){

	// var_dump(get_avatar($u->ID, 80));
	$filled_in_profile = (int) get_user_meta($u->ID, 'filled_in_profile', true);
	if(!$filled_in_profile) continue;

	$users[] = array(
		'avatar' => esc_html(get_avatar_url($u->ID, array(
			'size' => 150
		))),
		'email' => esc_html($u->email),
		'phone' => esc_html($u->phone),
		'name' => esc_html($u->name),
		'function' => esc_html($u->function),
		'fields' => array_filter(array_map('trim', is_array($u->field) ? $u->field : explode(',', esc_html($u->field)))),
		'company' => esc_html($u->company),
		'company_location' => !empty($u->location) ? esc_html($u->location['address']) : null,
		'location' => !empty($u->location) ? array(
			'lat' => (float) $u->location['lat'],
			'lng' => (float) $u->location['lng']
		) : null
	);
}

?>

<script type="text/javascript">

	jQuery(function(){

		new Network('#network-map', {
			zoom: 9,
			latlng: {lat: 52.6573591, lng: 6.0872512}, // Zwolle
			user: <?php echo json_encode(get_current_user_id()) ?>,
			items: '<?php echo base64_encode(json_encode($users)) ?>',
			fields_dropdown: '#fields',
			view_toggle: '#view-toggle',
			view_container: '#view-container',
			list_view: '.list-view',
			map_view: '.map-view',
			marker_icon: <?php echo json_encode(get_stylesheet_directory_uri() . '/images/marker.png'); ?>,
			marker_icon_hover: <?php echo json_encode(get_stylesheet_directory_uri() . '/images/marker_over.png'); ?>
		});

	});

</script>

<div class="network-map">

	<div class="content block text">

		<div class="inner">
			<h1><?php echo get_the_title() ?></h1>
			<p><?php echo apply_filters( 'the_content', $post->post_content ); ?></p>
			<?php if(!is_user_logged_in()): ?>
				<p>Om de gegevens van het netwerk te kunnen bekijken, moet u zich <a href="<?php echo site_url('registreren') ?>">Aanmelden</a></p>
			<?php endif; ?>
		</div>

	</div>

	<div class="block full filters">
		<div class="inner">
			<div class="filter-field">
				<span>Filter op</span>
				<div class="select_wrapper">
					<select id="fields">
						<option value="">Alle specialismen</option>
					</select>
				</div>
			</div>
			<div id="view-toggle">
				<span>Weergave</span>
				<a href="#" data-view="list">Lijst</a><a href="#" data-view="map" class="active">Kaart</a>
			</div>
		</div>
	</div>

	<div class="block full">
		<div class="inner" id="view-container">
			<div class="map-view active" id="network-map"></div>
			<div class="list-view" id="network-list"></div>
		</div>
	</div>

</div>

<?php get_footer(); ?>
