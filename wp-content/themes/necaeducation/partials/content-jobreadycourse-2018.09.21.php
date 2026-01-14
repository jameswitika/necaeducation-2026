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
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<div class="entry-wrap">
			<div class="entry-content content">

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
												<th><?php echo $job_ready_course->course_scope_code; ?></th>
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
									
									<h4>Register and More Dates</h4>
									
									<?php echo $job_ready_course->register; ?>
												
									<?php if(count($job_ready_dates) > 0) : ?>
										<table>
											<thead>
												<tr>
													<th>Time of day</th>
													<th>Start Date</th>
													<th>Availability</th>
												</tr>
											</thead>
											<tbody>
											
												<?php foreach($job_ready_dates as $job_ready_date) :  ?>
													<tr>
														<td><?php echo $job_ready_date->meta->course_scope_name; ?><br>
														(<?php echo $job_ready_date->meta->course_name; ?>)</td>
														<td><?php echo $job_ready_date->meta->start_date_clean; ?> to <?php echo $job_ready_date->meta->end_date_clean; ?></td>
														<td>
														<?php
														if(isset($job_ready_date->meta->show_enrol) && $job_ready_date->meta->show_enrol === true)
															{
																//echo '<a href="'.$job_ready_date->meta->link_enrol.'" target="_blank" class="x-btn" style="outline: none;">Enrol</a>';
																echo '<a href="'.$job_ready_course->apply_url.'?course_scope_code='.$job_ready_course->course_scope_code.'&course_number='.urlencode($job_ready_date->meta->course_number).'" class="x-btn" style="outline: none;">Register</a>';
															}
									                        else
									                        {
									                            echo '<span style="font-size: smaller">Not available</span>';
									                            if(isset($job_ready_date->meta->full))
									                                echo '<br/>FULL';
									                        } ?>
														</td>
													</tr>
												<?php endforeach; ?>
												
											</tbody>
										</table>
									<?php else: ?>
									<?php 
										if(isset($job_ready_dates) && count($job_ready_dates) > 0 && $job_ready_dates[0]->meta->course_scope_code != "NECNRA001")
											echo "<p>There are currently no dates available for this course. Please check again later.</p>";
										?>
									<?php endif; ?>
									
								</div>
							</div>
						</div>
					</div>
					<div class="x-section" id="x-section-2" style="margin: 0px;padding: 15px 0px; background-color: transparent;">
						<div class="x-container" style="margin: 0px auto;padding: 0px;">
							<div class="x-column x-sm x-1-1" style="padding: 0px;">
								<div class="x-accordion">
																			
									<?php if($job_ready_course->course_information!= '') : ?>
									<div class="x-accordion-group" data-cs-collapse-group="">
										<div class="x-accordion-heading">
											<a class="x-accordion-toggle collapsed" data-cs-collapse-toggle="">Course Information:</a>
										</div>
										<div class="x-accordion-body collapse" data-cs-collapse-content="" style="height: 0px;">
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
											<a class="x-accordion-toggle collapsed" data-cs-collapse-toggle="">Pre-Requisites:</a>
										</div>
										<div class="x-accordion-body collapse" data-cs-collapse-content="" style="height: 0px;">
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
											<a class="x-accordion-toggle collapsed" data-cs-collapse-toggle="">Selection Criteria:</a>
										</div>
										<div class="x-accordion-body collapse" data-cs-collapse-content="" style="height: 0px;">
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
											<a class="x-accordion-toggle collapsed" data-cs-collapse-toggle="">Pathways:</a>
										</div>
										<div class="x-accordion-body collapse" data-cs-collapse-content="" style="height: 0px;">
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
											<a class="x-accordion-toggle collapsed" data-cs-collapse-toggle="">Licensing Exam:</a>
										</div>
										<div class="x-accordion-body collapse" data-cs-collapse-content="" style="height: 0px;">
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
											<a class="x-accordion-toggle collapsed" data-cs-collapse-toggle="">Recognition or Credit Transfer:</a>
										</div>
										<div class="x-accordion-body collapse" data-cs-collapse-content="" style="height: 0px;">
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
											<a class="x-accordion-toggle collapsed" data-cs-collapse-toggle="">Course Structure:</a>
										</div>
										<div class="x-accordion-body collapse" data-cs-collapse-content="" style="height: 0px;">
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
											<a class="x-accordion-toggle collapsed" data-cs-collapse-toggle="">Fees:</a>
										</div>
										<div class="x-accordion-body collapse" data-cs-collapse-content="" style="height: 0px;">
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
											<a class="x-accordion-toggle collapsed" data-cs-collapse-toggle="">How to Apply:</a>
										</div>
										<div class="x-accordion-body collapse" data-cs-collapse-content="" style="height: 0px;">
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
			if(function_exists('social_warfare'))
			{
				social_warfare();
			}
		?>

		<?php do_action( 'x_after_the_content_end' ); ?>
	</div>
</article>