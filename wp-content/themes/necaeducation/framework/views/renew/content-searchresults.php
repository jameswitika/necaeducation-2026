<?php

// =============================================================================
// VIEWS/RENEW/CONTENT-LINK.PHP
// -----------------------------------------------------------------------------
// Link post output for Renew.
// =============================================================================

$link = get_post_meta( get_the_ID(), '_x_link_url',  true );

?>
	<li>
    	<h4>
    		<a href="<?php echo get_permalink(); ?>">
				<?php the_title();  ?>
            </a>
		</h4>
		<?php
			if (trim(get_the_excerpt()) != '')
			{
				echo substr(get_the_excerpt(), 0,200);
        		        echo '<div class="h-readmore"> <a href="' . get_permalink() . '">Read More</a></div>';
        		}
		?>
	</li>
      
      