<?php

// =============================================================================
// VIEWS/GLOBAL/_NAV-PRIMARY.PHP
// -----------------------------------------------------------------------------
// Outputs the primary nav.
// =============================================================================
	
if( function_exists( 'ubermenu' ) && $config_id = ubermenu_get_menu_instance_by_theme_location( 'primary' ) ):
	ubermenu( $config_id, array( 'theme_location' => 'primary') ); 
 else: ?>
 
<a href="#" class="x-btn-navbar collapsed" data-toggle="collapse" data-target=".x-nav-wrap.mobile">
  <i class="x-icon-bars" data-x-icon="&#xf0c9;"></i>
  <span class="visually-hidden"><?php _e( 'Navigation', '__x__' ); ?></span>
</a>

<nav class="x-nav-wrap desktop" role="navigation">
    <div class="social-upper">
    	<?php echo do_shortcode('[job_ready_logout]'); ?>
        <a href="#" class="x-btn-navbar-search"><span><i class="x-icon-search" data-x-icon="" aria-hidden="true"></i><span class="x-hidden-desktop"> Search</span></span></a>
        <a href="https://www.instagram.com/necaeducation/" target="_blank"><img src="<?php echo get_site_url(); ?>/wp-content/uploads/2017/10/icon-instagram.jpg" alt=""></a>
        <a href="https://www.facebook.com/necaeducationVIC" target="_blank"><img src="<?php echo get_site_url(); ?>/wp-content/uploads/2017/05/icon-fb.jpg" alt=""></a>
        <a href="https://www.youtube.com/channel/UCn3WsUN_cnQDCtaU8zLyvxA" target="_blank"><img src="<?php echo get_site_url(); ?>/wp-content/uploads/2017/05/icon-yt.jpg" alt=""></a>
        <a href="http://www.linkedin.com/company/neca-education-and-careers" target="_blank"><img src="<?php echo get_site_url(); ?>/wp-content/uploads/2017/05/icon-in.jpg" alt=""></a>
    </div>
  <?php x_output_primary_navigation(); ?>
</nav>

<div class="x-nav-wrap mobile collapse">
  <?php x_output_primary_navigation(); ?>
</div>

<?php endif; ?>