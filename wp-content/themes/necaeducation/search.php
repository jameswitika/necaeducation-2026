<?php
/*
 Template Name: Search Page
 */
?>

<?php get_header(); ?>

  <div class="x-container max width offset">
    <div class="<?php x_main_content_class(); ?>" role="main">
    
      <h5 class="search-title">
      	<?php echo $wp_query->found_posts; ?>
        <?php _e( 'Search results found for', 'locale' ); ?>: "<?php the_search_query(); ?>"
      </h5>

      <ul>
        <?php while ( have_posts() ) : the_post(); ?>
	      <?php x_get_view( 'renew', 'content', 'searchresults' ); ?>
	    <?php endwhile; ?>
      </ul>
    </div>
  </div>

<?php get_footer(); ?>