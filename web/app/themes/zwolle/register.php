<?php
/**
 * Template Name: Registreren
 */

get_header();

include('_slideshow.php');

?>

<div class="content block full">

	<div class="inner">
		<h1><?php echo get_the_title() ?></h1>
		<p><?php echo apply_filters( 'the_content', $post->post_content ); ?></p>

		<?php if(!is_user_logged_in()): ?>
			<div class="register">
				<h3>Een nieuwe account aanmaken</h3>
				<?php acf_form_head(); ?>
				<?php acf_form(array(
					'post_id' => 'user_new',
					'field_groups' => array('group_5602aa805fd34'),
					'submit_value' => 'Registreer',
					'return' => null,
				)); ?>
			</div>
			<div class="login">
				<h3>Aanmelden met bestaand account</h3>
				<?php if(!empty($_GET['forgotten'])): ?>
					<p>Er is een nieuw wachtwoord verstuurd naar uw e-mailadres</p>
				<?php endif; ?>
				<?php acf_form_head(); ?>
				<?php acf_form(array(
					'post_id' => 'user_login',
					'field_groups' => array('group_5603e9fe0cece'),
					'submit_value' => 'Inloggen',
					'return' => null,
				)); ?>
				<p>
					<a href="#" id="show_forgotten">Wachtwoord vergeten?</a>
					<script type="text/javascript">
						jQuery('#show_forgotten').on('click', function(){
							jQuery('#forgot_password').toggleClass('shown');
						});
					</script>
				</p>
			</div>
			<div class="forgotten" id="forgot_password">
				<h3>Nieuw wachtwoord aanvragen</h3>
				<?php acf_form_head(); ?>
				<?php acf_form(array(
					'post_id' => 'user_password',
					'field_groups' => array('group_5628f44c56ca2'),
					'submit_value' => 'Wachtwoord aanvragen',
					'return' => null,
				)); ?>
			</div>
		<?php else: ?>
			U bent al ingelogd, u wordt nu naar uw profiel verwezen.
			<script type="text/javascript">
				setTimeout(function(){
					window.location = <?php echo json_encode(site_url('profiel')); ?>
				}, 1000);
			</script>
		<?php endif; ?>

	</div>

</div>

<?php get_footer(); ?>
