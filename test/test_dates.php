<?php
include("../wp-load.php");
echo "Server Date: " . date("d/m/Y H:i:s") . "<br/><br/>";
echo "WordPress Adjusted Date: " . current_time("d/m/Y H:i:s") . "<br/><br/>";