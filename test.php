<?php

require_once "includes/config.php";
require_once "includes/functions.php";
require_once "includes/readfile.php";
?>

<html>
  <head>
  </head>

  <body>

  <?php print_r ( read_wifi_config( "wlan1", "HomeNet" ) ); ?>
  
  </body>
</html>


