<?php

// =============================================================================
// VIEWS/RENEW/WP-SINGLE.PHP
// -----------------------------------------------------------------------------
// Single post output for Renew.
// =============================================================================

?>

<style>
.h-landmark, .entry-title.h-landmark {
  color: #fff !important;
}

.spinner {
	background: url('<?php echo get_site_url(); ?>/wp-admin/images/wpspin_light.gif') no-repeat;
	background-size: 16px 16px;
	display: none;
	float: left;
	opacity: .7;
	filter: alpha(opacity=70);
	width: 16px;
	height: 16px;
	margin: 15px 0 0 10px;
}

.register-btn {
  margin-bottom: 0px !important;
}
</style>

<?php get_header(); ?>
  
  <div class="x-container max width offset">
    <div class="<?php x_main_content_class(); ?>" role="main">

      <?php while ( have_posts() ) : the_post(); ?>
        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
  			<div class="entry-wrap">

				<?php    			
				$stack = x_get_stack();
				$is_full_post_content_blog = is_home() && x_get_option( 'x_blog_enable_full_post_content' ) == '1';
				$job_ready_course = JobReadyCourseOperations::loadJobReadyCourse( $post->ID );
				$job_ready_dates = JobReadyDateOperations::loadJobReadyDatesByCourseID( $post->ID );
				if(count($job_ready_dates) > 0)
				{
					foreach($job_ready_dates as $job_ready_date)
					{
						if(!$job_ready_date->meta->full)
						{
							$job_ready_course->next_intake = new stdClass();
							$job_ready_course->next_intake->start_date = $job_ready_date->meta->start_date_clean;
							$job_ready_course->next_intake->end_date = $job_ready_date->meta->end_date_clean;
							break;
						}
					}
				}
				$parent_id = wp_get_post_parent_id($post->ID);
				?>
				
					<div class="entry-content content" style="margin-top:0px;">

						<!--  Course Details goes here -->
						<div class="cs-content" id="cs-content">
							<div class="x-section" id="x-section-1" style="margin: 0px;padding: 0px; background-color: transparent;">
								<div class="x-container" style="margin: 0px auto;padding: 0px;">
									<div class="x-column x-sm x-1-1" style="padding: 0px;">
										<h4 class="h-custom-headline h4" style="margin-top: 0;"><span><?php echo $job_ready_course->course_scope_name; ?></span></h4>
										<div class="x-text">
											<table class="course_details">
												<tbody>
													<tr>
														<th nowrap="nowrap">Course Code</th>
														<td><?php echo $job_ready_course->course_scope_code; ?></td>
													</tr>
													<tr>
														<th nowrap="nowrap">Course Name</th>
														<td><?php echo $job_ready_course->course_scope_name; ?></td>
													</tr>
													<tr>
														<th nowrap="nowrap">Duration</th>
														<td><?php echo $job_ready_course->duration; ?></td>
													</tr>
													<tr>
														<th nowrap="nowrap">Mode of Study</th>
														<td><?php echo $job_ready_course->mode_of_study; ?></td>
													</tr>
													<tr>
														<th nowrap="nowrap">Next Intake</th>
														<td>
														<?php 
														if(isset($job_ready_course->next_intake))
															echo $job_ready_course->next_intake->start_date . " to " . $job_ready_course->next_intake->end_date . " - see below for more dates";
                										else
                    										echo "No date - Please check back again soon for next course date.";
                										?>
														</td>
													</tr>
													<?php if($job_ready_course->cost > 0) : ?>
														<tr>
															<th nowrap="nowrap">Cost</th>
															<td><?php echo "$" . number_format( $job_ready_course->cost, 2); ?></td>
														</tr>
													<?php endif; ?>
												</tbody>
											</table>
											<div>
												<?php 
													$description = apply_filters('the_content', $job_ready_course->description);
													echo $description;
												?>
											</div>
											
											<h4>Course Dates</h4>
											
											<?php 
											$register_content = apply_filters('the_content', $job_ready_course->register);
											echo $register_content;
											?>
														
											<?php if(count($job_ready_dates) > 0) : ?>
												<table id="table-responsive">
													<thead>
														<tr>
															<th>Time of day</th>
															<th>Start Date</th>
															<?php 

															if ($job_ready_course->course_scope_code != 'UEE30811')
															{
																echo "<th>Availability</th>";
															}
															?>
														</tr>
													</thead>
													<tbody>
													
														<?php foreach($job_ready_dates as $job_ready_date) :  ?>
															<tr>
																<td>
																<?php
																	echo $job_ready_date->meta->course_scope_name;
																	if(trim($job_ready_date->meta->course_name) != '')
																	{
																		echo '<br>';
																		echo '(' . $job_ready_date->meta->course_name . ')';
																	}
																?>
																</td>
																<td><?php echo $job_ready_date->meta->start_date_clean; ?> to <?php echo $job_ready_date->meta->end_date_clean; ?></td>
																<?php 

																if ($job_ready_course->course_scope_code != 'UEE30811')
																{
																
																	echo "<td>";
																
																	if(isset($job_ready_date->meta->show_enrol) && $job_ready_date->meta->show_enrol === true)
																	{
																		//echo '<a href="'.$job_ready_date->meta->link_enrol.'" target="_blank" class="x-btn" style="outline: none;">Enrol</a>';
																		if(isset($job_ready_date->meta->enrolments_remaining_formatted))
																		{
																			echo '<div style="font-size:smaller;">'.$job_ready_date->meta->enrolments_remaining_formatted.'</div>';
																		}																		
																		echo '<a href="'.$job_ready_course->apply_url.'?ajax=1&course_scope_code='.$job_ready_course->course_scope_code.'&course_number='.urlencode($job_ready_date->meta->course_number).'" class="x-btn register-btn" style="outline: none; float:left;">Register</a>';
																		echo '<div class="spinner"></div>';
																	}
												                    else
												                    {
												                        echo '<span style="font-size: smaller">Not available</span>';
												                        if(isset($job_ready_date->meta->full))
												                            echo '<br/>FULL';
												                    }
												                    echo "</td>";
																}
												                ?>
															</tr>
														<?php endforeach; ?>
														
													</tbody>
												</table>
											<?php 
											else:
												if($job_ready_course->course_scope_code != 'UEE22020')
												{
													echo "<p>Sorry, we don't have any dates scheduled at the moment, but keep an eye out they won't be far off!</p>";
												}
											endif;
											?>
											
										</div>
									</div>
								</div>
							</div>
							<div class="x-section" id="x-section-2" style="margin: 0px;padding: 15px 0px; background-color: transparent;">
								<div class="x-container" style="margin: 0px auto;padding: 0px;">
									<div class="x-column x-sm x-1-1" style="padding: 0px;">
										<div class="x-accordion">
																					
											<?php if($job_ready_course->course_information!= '') : ?>
											<div class="x-accordion-group">
												<div class="x-accordion-heading">
													<a id="tab-course-information" class="x-accordion-toggle collapsed" role="tab" data-x-toggle="collapse-b" data-x-toggleable="course-information" aria-selected="false" aria-expanded="false" aria-controls="panel-course-information">Course Information:</a>
												</div>
												<div id="panel-course-information" class="x-accordion-body x-collapsed" role="tabpanel" data-x-toggle-collapse="1" data-x-toggleable="course-information" aria-hidden="true" aria-labelledby="tab-course-information">
													<div class="x-accordion-inner">
														<?php 
														$content = apply_filters('the_content', $job_ready_course->course_information);
														echo $content;
														?>
													</div>
												</div>
											</div>
											<?php endif; ?>
																						
											<?php if($job_ready_course->prerequisites!= '') : ?>
											<div class="x-accordion-group" data-cs-collapse-group="">
												<div class="x-accordion-heading">
													<a id="tab-pre-requisites" class="x-accordion-toggle collapsed" role="tab" data-x-toggle="collapse-b" data-x-toggleable="pre-requisites" aria-selected="false" aria-expanded="false" aria-controls="panel-pre-requisites">Pre-Requisites:</a>
												</div>
												<div id="panel-pre-requisites" class="x-accordion-body x-collapsed" role="tabpanel" data-x-toggle-collapse="1" data-x-toggleable="pre-requisites" aria-hidden="true" aria-labelledby="tab-pre-requisites">
													<div class="x-accordion-inner">
														<?php 
														$content = apply_filters('the_content', $job_ready_course->prerequisites);
														echo $content;
														?>
													</div>
												</div>
											</div>
											<?php endif; ?>
																						
											<?php if($job_ready_course->selection_criteria!= '') : ?>
											<div class="x-accordion-group" data-cs-collapse-group="">
												<div class="x-accordion-heading">
													<a id="tab-selection-criteria" class="x-accordion-toggle collapsed" role="tab" data-x-toggle="collapse-b" data-x-toggleable="selection-criteria" aria-selected="false" aria-expanded="false" aria-controls="panel-selection-criteria">Selection Criteria:</a>
												</div>
												<div id="panel-selection-criteria" class="x-accordion-body x-collapsed" role="tabpanel" data-x-toggle-collapse="1" data-x-toggleable="selection-criteria" aria-hidden="true" aria-labelledby="tab-selection-criteria">
													<div class="x-accordion-inner">
														<?php 
														$content = apply_filters('the_content', $job_ready_course->selection_criteria);
														echo $content;
														?>
													</div>
												</div>
											</div>
											<?php endif; ?>
																						
											<?php if($job_ready_course->pathways!= '') : ?>
											<div class="x-accordion-group" data-cs-collapse-group="">
												<div class="x-accordion-heading">
													<a id="tab-pathways" class="x-accordion-toggle collapsed" role="tab" data-x-toggle="collapse-b" data-x-toggleable="pathways" aria-selected="false" aria-expanded="false" aria-controls="panel-pathways">Pathways:</a>
												</div>
												<div id="panel-pathways" class="x-accordion-body x-collapsed" role="tabpanel" data-x-toggle-collapse="1" data-x-toggleable="pathways" aria-hidden="true" aria-labelledby="tab-pathways">
													<div class="x-accordion-inner">
														<?php 
														$content = apply_filters('the_content', $job_ready_course->pathways);
														echo $content;
														?>
													</div>
												</div>
											</div>
											<?php endif; ?>
																						
											<?php if($job_ready_course->licensing_exam!= '') : ?>
											<div class="x-accordion-group" data-cs-collapse-group="">
												<div class="x-accordion-heading">
													<a id="tab-licensing-exam" class="x-accordion-toggle collapsed" role="tab" data-x-toggle="collapse-b" data-x-toggleable="licensing-exam" aria-selected="false" aria-expanded="false" aria-controls="panel-licensing-exam">Licensing Exam:</a>
												</div>
												<div id="panel-licensing-exam" class="x-accordion-body x-collapsed" role="tabpanel" data-x-toggle-collapse="1" data-x-toggleable="licensing-exam" aria-hidden="true" aria-labelledby="tab-licensing-exam">
													<div class="x-accordion-inner">
														<?php 
														$content = apply_filters('the_content', $job_ready_course->licensing_exam);
														echo $content;
														?>
													</div>
												</div>
											</div>
											<?php endif; ?>
																						
											<?php if($job_ready_course->rpl!= '') : ?>
											<div class="x-accordion-group" data-cs-collapse-group="">
												<div class="x-accordion-heading">
													<a id="tab-recognition" class="x-accordion-toggle collapsed" role="tab" data-x-toggle="collapse-b" data-x-toggleable="recognition" aria-selected="false" aria-expanded="false" aria-controls="panel-recognition">Recognition or Credit Transfer:</a>
												</div>
												<div id="panel-recognition" class="x-accordion-body x-collapsed" role="tabpanel" data-x-toggle-collapse="1" data-x-toggleable="recognition" aria-hidden="true" aria-labelledby="tab-recognition">
													<div class="x-accordion-inner">
														<?php 
														$content = apply_filters('the_content', $job_ready_course->rpl);
														echo $content;
														?>
													</div>
												</div>
											</div>
											<?php endif; ?>
																						
											<?php if($job_ready_course->course_structure!= '') : ?>
											<div class="x-accordion-group" data-cs-collapse-group="">
												<div class="x-accordion-heading">
													<a id="tab-course-structure" class="x-accordion-toggle collapsed" role="tab" data-x-toggle="collapse-b" data-x-toggleable="course-structure" aria-selected="false" aria-expanded="false" aria-controls="panel-course-structure">Course Structure:</a>
												</div>
												<div id="panel-course-structure" class="x-accordion-body x-collapsed" role="tabpanel" data-x-toggle-collapse="1" data-x-toggleable="course-structure" aria-hidden="true" aria-labelledby="tab-course-structure">
													<div class="x-accordion-inner">
														<?php 
														$content = apply_filters('the_content', $job_ready_course->course_structure);
														echo $content;
														?>
													</div>
												</div>
											</div>
											<?php endif; ?>
																						
											<?php if($job_ready_course->fees!= '') : ?>
											<div class="x-accordion-group" data-cs-collapse-group="">
												<div class="x-accordion-heading">
													<a id="tab-fees" class="x-accordion-toggle collapsed" role="tab" data-x-toggle="collapse-b" data-x-toggleable="fees" aria-selected="false" aria-expanded="false" aria-controls="panel-fees">Fees:</a>
												</div>
												<div id="panel-fees" class="x-accordion-body x-collapsed" role="tabpanel" data-x-toggle-collapse="1" data-x-toggleable="fees" aria-hidden="true" aria-labelledby="tab-fees">
													<div class="x-accordion-inner">
														<?php 
														$content = apply_filters('the_content', $job_ready_course->fees);
														echo $content;
														?>
													</div>
												</div>
											</div>
											<?php endif; ?>
																						
											<?php if($job_ready_course->how_to_apply!= '') : ?>
											<div class="x-accordion-group" data-cs-collapse-group="">
												<div class="x-accordion-heading">
													<a id="tab-how-to-apply" class="x-accordion-toggle collapsed" role="tab" data-x-toggle="collapse-b" data-x-toggleable="how-to-apply" aria-selected="false" aria-expanded="false" aria-controls="panel-how-to-apply">How to Apply:</a>
												</div>
												<div id="panel-how-to-apply" class="x-accordion-body x-collapsed" role="tabpanel" data-x-toggle-collapse="1" data-x-toggleable="how-to-apply" aria-hidden="true" aria-labelledby="tab-how-to-apply">
													<div class="x-accordion-inner">
														<?php 
														$content = apply_filters('the_content', $job_ready_course->how_to_apply);
														echo $content;
														?>
													</div>
												</div>
											</div>
											<?php endif; ?>

										</div>

										<div>
											<?php 
											$content = apply_filters('the_content', $job_ready_course->footer);
											echo $content;
											?>
										</div>										

									</div>
								</div>
							</div>
						</div>
						<!--  END OF COURSE DETAILS -->
					
					</div>

				<?php
					if(function_exists('social_warfare')):
						social_warfare();
					endif;
				?>

				<?php do_action( 'x_after_the_content_end' ); ?>

  			</div>
		</article>
      <?php endwhile; ?>

    </div>

	<aside class="<?php x_sidebar_class(); ?>" role="complementary">
		<?php if ( get_option( 'ups_sidebars' ) != array() ) : ?>
			<?php dynamic_sidebar( apply_filters( 'ups_sidebar', 'sidebar-main' ) ); ?>
		<?php else : ?>
			<?php dynamic_sidebar( 'sidebar-main' ); ?>
		<?php endif; ?>
    </aside>

  </div>

<?php get_footer(); ?>