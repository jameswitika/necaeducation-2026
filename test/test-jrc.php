<?php
include("../wp-load.php");

/*
function display_next_course_date_shortcode($atts) {
	// Extract attributes and set defaults
	$atts = shortcode_atts(
			array(
					'course_id' => 0, // Default value if course_id is not provided
			),
			$atts,
			'next_course_date'
			);
	
	// Ensure course_id is valid
	$course_id = intval($atts['course_id']);
	if ($course_id <= 0) {
		return 'Invalid course ID.';
	}
	
	// Load course and dates
	$job_ready_course = JobReadyCourseOperations::loadJobReadyCourse($course_id);
	$job_ready_dates = JobReadyDateOperations::loadJobReadyDatesByCourseID($course_id);
	
	// Check if dates exist
	if (count($job_ready_dates) > 0) {
		foreach ($job_ready_dates as $job_ready_date) {
			if (!$job_ready_date->meta->full) {
				$next_intake_start_date = $job_ready_date->meta->start_date_clean;
				$next_intake_end_date = $job_ready_date->meta->end_date_clean;
				
				// Format and return the next intake information
				return sprintf(
						'The next available course starts on %s and ends on %s.',
						esc_html($next_intake_start_date),
						esc_html($next_intake_end_date)
						);
			}
		}
	}
	
	// If no available dates were found
	return 'No available course dates at the moment.';
}

// Register the shortcode
add_shortcode('next_course_date', 'display_next_course_date_shortcode');



function display_course_dates_shortcode($atts) {
	// Extract shortcode attributes and set defaults
	$atts = shortcode_atts(
			array('course_id' => 0),
			$atts,
			'course_dates_table'
	);
	
	// Ensure course_id is valid
	$course_id = intval($atts['course_id']);
	if ($course_id <= 0) {
		return 'Invalid course ID.';
	}
	
	// Load course and dates
	$job_ready_course = JobReadyCourseOperations::loadJobReadyCourse($course_id);
	$job_ready_dates = JobReadyDateOperations::loadJobReadyDatesByCourseID($course_id);
	
	// Check if dates exist
	if (count($job_ready_dates) > 0) {
		ob_start(); // Start output buffering to capture the table HTML
		?>
        <table id="table-responsive">
            <thead>
                <tr>
                    <th>Time of day</th>
                    <th>Start Date</th>
                    <?php if ($job_ready_course->course_scope_code != 'UEE30811') : ?>
                        <th>Availability</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($job_ready_dates as $job_ready_date) : ?>
                    <tr>
                        <td>
                            <?php
                            echo $job_ready_date->meta->course_scope_name;
                            if (trim($job_ready_date->meta->course_name) != '') {
                                echo '<br>(' . $job_ready_date->meta->course_name . ')';
                            }
                            ?>
                        </td>
                        <td>
                            <?php echo $job_ready_date->meta->start_date_clean; ?> to <?php echo $job_ready_date->meta->end_date_clean; ?>
                        </td>
                        <?php if ($job_ready_course->course_scope_code != 'UEE30811') : ?>
                            <td>
                                <?php
                                if (isset($job_ready_date->meta->show_enrol) && $job_ready_date->meta->show_enrol === true) {
                                    if (isset($job_ready_date->meta->enrolments_remaining_formatted)) {
                                        echo '<div style="font-size:smaller;">' . $job_ready_date->meta->enrolments_remaining_formatted . '</div>';
                                    }
                                    echo '<a href="' . esc_url($job_ready_course->apply_url) . '?ajax=1&course_scope_code=' . urlencode($job_ready_course->course_scope_code) . '&course_number=' . urlencode($job_ready_date->meta->course_number) . '" class="x-btn register-btn" style="outline: none; float:left;">Register</a>';
                                    echo '<div class="spinner"></div>';
                                } else {
                                    echo '<span style="font-size: smaller">Not available</span>';
                                    if (isset($job_ready_date->meta->full)) {
                                        echo '<br/>FULL';
                                    }
                                }
                                ?>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php
        return ob_get_clean(); // Return the captured output
    } else {
        if ($job_ready_course->course_scope_code != 'UEE22020') {
            return "<p>Sorry, we don't have any dates scheduled at the moment, but keep an eye out—they won't be far off!</p>";
        }
    }

    return ''; // Fallback if nothing matches
}

// Register the shortcode
add_shortcode('course_dates_table', 'display_course_dates_shortcode');
*/


// Call the shortcode and pass the attributes
$next_intake_shortcode_output = do_shortcode('[neca_next_course_date course_id="22668"]');

// Display the output of the shortcode
echo "Next Intake Shortcode Output: <br/>";
echo $next_intake_shortcode_output;
echo "<br/><br/>";


// Call the shortcode and pass the attributes
$course_dates_shortcode_output = do_shortcode('[neca_course_dates_table course_id="22668"]');

// Display the output of the shortcode
echo "Course Dates Shortcode Output: <br/>";
echo $course_dates_shortcode_output;
echo "<br/><br/>";


/*
$course_id = 22668; // CPDSM-A

$job_ready_course = JobReadyCourseOperations::loadJobReadyCourse( $course_id );
$job_ready_dates = JobReadyDateOperations::loadJobReadyDatesByCourseID( $course_id );

if(count($job_ready_dates) > 0) : 
?>
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
*/