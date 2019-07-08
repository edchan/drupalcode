<?php

/**
 * @file
 * Custom implementation to display the cities block.
 *
 * Available variables:
 * - $cities (array): An array containing the available searchable cities.
 * - $default_city: The default city.
 */
?>
<div class="dropdown">
  <button class="dropbtn"><?php print $default_city ?></button>
  <div class="dropdown-content">
    <?php foreach ($cities as $key => $city) { ?>
      <a href="<?php print url(NULL, array('query'=> array('city' => $key))) ?>"><?php print $city['name'] ?></a>
    <?php } ?>
  </div>
</div>
