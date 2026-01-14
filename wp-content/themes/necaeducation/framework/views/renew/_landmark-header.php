<?php

// =============================================================================
// VIEWS/RENEW/_LANDMARK-HEADER.PHP
// -----------------------------------------------------------------------------
// Handles content output of large headers for key pages such as the blog or
// search results.
// =============================================================================

$disable_page_title = get_post_meta( get_the_ID(), '_x_entry_disable_page_title', true );
$breadcrumbs        = x_get_option( 'x_breadcrumb_display' );

?>

<?php if ( ! x_is_blank( 1 ) && ! x_is_blank( 2 ) && /*! x_is_blank( 4 ) &&*/ ! x_is_blank( 5 ) ) : ?>
  <?php if ( is_page() && $disable_page_title == 'on' ) : ?>

  <?php else : ?>
    <?php 
    	$ancestor_id = array_reverse(get_post_ancestors($post->ID));
        if($ancestor_id):
            $slug = get_post_field( 'post_name', $ancestor_id[0] );
        else:
            $slug = $post->post_name;
        endif;
	?>
    <?php if($slug == 'job-seekers'): ?>
        <header class="x-header-landmark" style="<?php echo get_field('jobseeker_banner','option') ? "background-image: url(".get_field('jobseeker_banner','option').");" : false; ?>">
    <?php elseif($slug == 'employers'): ?>
        <header class="x-header-landmark" style="<?php echo get_field('employers_banner','option') ? "background-image: url(".get_field('employers_banner','option').");" : false; ?>">
    <?php elseif($slug == 'training-with-us' || is_job_ready_course() ): ?>
        <header class="x-header-landmark" style="<?php echo get_field('training_banner','option') ? "background-image: url(".get_field('training_banner','option').");" : false; ?>">
    <?php elseif($slug == 'career-advisor'): ?>
        <header class="x-header-landmark" style="<?php echo get_field('career_banner','option') ? "background-image: url(".get_field('career_banner','option').");" : false; ?>">
    <?php elseif($slug == 'contact-us'): ?>
        <header class="x-header-landmark" style="<?php echo get_field('contact_us_banner','option') ? "background-image: url(".get_field('career_banner','option').");" : false; ?>">
    <?php else: ?>
        <header class="x-header-landmark" style="<?php echo get_field('default_banner','option') ? "background-image: url(".get_field('default_banner','option').");" : false; ?>">
    <?php endif; ?>
     
      <div class="x-container max width">
        <div class="x-landmark-breadcrumbs-wrap">
          <div class="x-landmark">

          <?php if ( x_is_shop() || x_is_product() ) : ?>

            <h1 class="h-landmark">
              <span>
              <?php 
                    if(isset($_REQUEST['course_scope_code']))
                    {
                    	$course_scope_code = $_REQUEST['course_scope_code'];
                    	$course_scope_name = JobReadyCourseOperations::getJobReadyCourseFieldByCourseScopeCode($course_scope_code, 'jrc_course_scope_name');
                    	echo $course_scope_name;
                    }
                    else
                    {
                    	echo x_get_option( 'x_renew_shop_title' ); 
                    }
              ?>
              </span>
            </h1>

          <?php elseif ( x_is_bbpress() ) : ?>

            <h1 class="h-landmark"><span><?php echo get_the_title(); ?></span></h1>

          <?php elseif ( x_is_buddypress() ) : ?>
            <?php if ( x_buddypress_is_component_with_landmark_header() ) : ?>

              <h1 class="h-landmark"><span><?php echo x_buddypress_get_the_title(); ?></span></h1>

            <?php endif; ?>
          <?php elseif ( is_page() ) : ?>

            <h1 class="h-landmark entry-title">
            	<span>
					    <?php 
                    if(isset($_REQUEST['course_scope_code']))
                    {
                    	//echo $_REQUEST['course_scope_code'];
                    	$course_scope_code = $_REQUEST['course_scope_code'];
                    	$course_scope_name = JobReadyCourseOperations::getJobReadyCourseFieldByCourseScopeCode($course_scope_code, 'jrc_course_scope_name');
                    	echo $course_scope_name;
                    }
                    else
                    {
                    	the_title();
                    }
              ?>
            	</span>
            </h1>
            
            <!--
            <h1 class="h-landmark entry-title"><span><?php the_title(); ?></span></h1>
            -->

          <?php elseif ( is_home() || is_single() ) : ?>
            <?php if ( x_is_portfolio_item() ) : ?>

              <h1 class="h-landmark"><span><?php echo x_get_parent_portfolio_title(); ?></span></h1>
            <?php elseif ( is_singular( 'post' ) ) : ?>
            
                <h1 class="h-landmark">
                  <span>
                    <?php the_title(); ?>
                  </span>
                </h1>

			<?php elseif ( is_job_ready_course()) : ?>
			
				<h1 class="h-landmark"><span><?php the_title(); ?></span></h1>
				<h3><?php //echo JobReadyCourseOperations::getJobReadyCourseName($post); ?></h3>
				
            <?php else : ?>

              <h1 class="h-landmark"><span><?php echo x_get_option( 'x_renew_blog_title' ); ?></span></h1>

            <?php endif; ?>
          <?php elseif ( is_search() ) : ?>

            <h1 class="h-landmark"><span><?php _e( 'Search Results', '__x__' ); ?></span></h1>

          <?php elseif ( is_category() || x_is_portfolio_category() || x_is_product_category() ) : ?>

            <?php

            $meta  = x_get_taxonomy_meta();
            $title = ( $meta['archive-title'] != '' ) ? $meta['archive-title'] : __( 'Category Archive', '__x__' );

            ?>

            <h1 class="h-landmark"><span><?php echo $title; ?></span></h1>

          <?php elseif ( is_tag() || x_is_portfolio_tag() || x_is_product_tag() ) : ?>

            <?php

            $meta  = x_get_taxonomy_meta();
            $title = ( $meta['archive-title'] != '' ) ? $meta['archive-title'] : __( 'Tag Archive', '__x__' );

            ?>

            <h1 class="h-landmark"><span><?php echo $title ?></span></h1>

          <?php elseif ( is_404() ) : ?>

            <h1 class="h-landmark"><span><?php _e( 'Oops!', '__x__' ); ?></span></h1>

          <?php elseif ( is_year() ) : ?>

            <h1 class="h-landmark"><span><?php _e( 'Post Archive by Year', '__x__' ); ?></span></h1>

          <?php elseif ( is_month() ) : ?>

            <h1 class="h-landmark"><span><?php _e( 'Post Archive by Month', '__x__' ); ?></span></h1>

          <?php elseif ( is_day() ) : ?>

            <h1 class="h-landmark"><span><?php _e( 'Post Archive by Day', '__x__' ); ?></span></h1>

          <?php elseif ( x_is_portfolio() ) : ?>

            <h1 class="h-landmark"><span><?php the_title(); ?></span></h1>

          <?php endif; ?>

          </div>

        </div>
      </div>
    </header>
    
    <?php if ( $breadcrumbs == '1' ) : ?>
        <?php if ( ! is_front_page() && ! x_is_portfolio() ) : ?>
          <div class="x-container max width offset">
            <?php x_breadcrumbs(); ?>
          </div>
        <?php endif; ?>
      <?php endif; ?>

      <?php if ( x_is_portfolio() ) : ?>
        <div class="x-container max width offset">
          <?php x_portfolio_filters(); ?>
        </div>
      <?php endif; ?>

  <?php endif; ?>
<?php endif; ?>