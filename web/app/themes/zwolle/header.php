<!DOCTYPE html>
<head>
	<meta charset="<?php bloginfo('charset'); ?>" />
	<meta http-equiv='X-UA-Compatible' content='IE=edge,chrome=1'>
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<title><?php wp_title( '|', true, 'right' ); ?><?php is_front_page() ? '' : ' | ' ?><?php bloginfo('name'); ?> | <?php echo bloginfo('description') ?></title>

	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

	<div class="header block full">
		<div class="container">

			<a href="<?php echo home_url('/') ?>">
				<img src="/app/uploads/2016/03/DWWA-Home@1x.png" alt="Daarwerkenweaan">
			</a>

			<div class="menu">
				<a href="#" class="menu_icon">
					<span></span>
					<span></span>
					<span></span>
				</a>

				<div class="foldout">
					<ul class="nav">
						<?php
							$current_url = get_permalink();
							function print_a($url, $label){
								$url = home_url($url . '/');
								$compare_with = WP_HOME . $_SERVER['REQUEST_URI'];
								return '<a class="' . ($url == $compare_with ? 'active': '') . '" href="' . $url . '">'.$label.'</a>';
							}
						?>
						<li class="">
							<?php echo print_a('cijfers', 'Feiten &amp; Cijfers'); ?>
						</li>
						<li class="">
							<?php echo print_a('regio', 'Regio'); ?>
						</li>
						<li class="">
							<?php echo print_a('agenda', 'Agenda'); ?>
						</li>
						<li class="">
							<?php echo print_a('successen', 'Successen'); ?>
						</li>
					</ul>
				</div>

			</div>

			<div class="menu_underlay"></div>
		</div>
	</div>

	<div class="page_content">
