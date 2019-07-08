
<?php

/**
 * @file
 * Custom implementation to display the search tab block.
 *
 * Available variables:
 * - $property_type (object): An object containing all available property types.
 * - $default_city (array): An array containing the name and id of the default city.
 * - $main_property_type (array): An array containing the main property types.
 * - $districts (array): An array containing the available searchable districts.
 * - $all_positioning (array): An array containing the available positioning.
 * - $unit_sizes (array): An array containing the available unit sizes.
 */
?>

<?php global $language; ?>
<ul class="nav nav-tabs">
  <?php
    $count = 0;
    foreach($main_property_type as $key => $value) {
      $class_active = ($count <= 0) ? 'active' : '';
      $count++;
  ?>
    <li class="<?php print $class_active ?>"><a data-toggle="tab" href="#<?php print $key ?>"><?php print t($value) ?></a></li>
  <?php } ?>
</ul>

<div class="tab-content">
  <?php
    $default_city_index = key($default_city);
    $count = 0;
    foreach($main_property_type as $key => $value) {

      $class_active = ($count <= 0) ? 'active' : '';
      $count++;

      $all_subtype = array();
      foreach ($property_type->{$value} as $subtype) {
        $all_subtype[] = $subtype->PropertySubTypeDescriptionValue;
      }
      $all_property_types = implode(',', $all_subtype);
  ?>
  <div id="<?php print $key ?>" class="tab-pane fade in <?php print $class_active ?>" data-all-types="<?php print $all_property_types ?>">
    <div class="options opt-type">
      <div class="tab-label"><?php print t('Property Type') ?></div>
      <ul class="tab-list tab-list-line">
        <li><a href="<?php print url('q', array('query'=> array('city' => $default_city_index, 'type' => $all_property_types))) ?>"><span><?php print t('All') ?></span></a></li>
        <?php
          $olm_search_tab_property_types_merged_array = explode('|', variable_get('olm_search_tab_property_types_merged'));
          if (count($olm_search_tab_property_types_merged_array) === 3) {
            list($label_en, $label_cn, $params) = $olm_search_tab_property_types_merged_array;
            $merge_property_types_label = ($language->language == 'en') ? $label_en : $label_cn;
            $merge_property_types = explode(',', $params);
          }

          $merge_property_types_index = $value.'_merged';
          $custom_property_types = array();
          foreach ($property_type->{$value} as $subtype) {
            if (in_array($subtype->PropertySubTypeDescriptionValue, $merge_property_types)) {
              $custom_property_types[$merge_property_types_index] = [
                'label' => $merge_property_types_label,
                'link' => implode(',', $merge_property_types)
              ];
            } else {
              $custom_property_types[$subtype->PropertySubTypeDescriptionValue] = [
                'label' => $subtype->PropertySubTypeDescription,
                'link' => $subtype->PropertySubTypeDescriptionValue
              ];
            }
          }

          foreach ($custom_property_types as $custom_property_type) {
        ?>
          <li><a href="<?php print url('q', array('query'=> array('city' => $default_city_index, 'type' => $custom_property_type['link']))) ?>"><?php print $custom_property_type['label'] ?></a></li>
        <?php } ?>
      </ul>
    </div>
    <div class="options opt-district">
      <div class="tab-label"><?php print t('District') ?></div>
      <ul class="tab-list tab-list-line">
        <li><a href="<?php print url('q', array('query'=> array('city' => strtolower($default_city['name']), 'type' => $all_property_types))) ?>"><span><?php print t('All') ?></span></a></li>
        <?php
          $total_districts = count($districts);

          // Check custom order from CMS settings.
          $custom_ordered_districts = _olm_search_tab_check_district_custom_order($districts, $key, $default_city['id']);

          $district_counter = 0;
          foreach ($custom_ordered_districts as $district) {
            $district_value = ($language->language == 'en') ? $district['Description'] : $district['DescriptionLocal'];
        ?>
          <li><a href="<?php print url('q', array('query'=> array('city' => $default_city_index, 'type' => $all_property_types, 'district' => $district_value))) ?>"><?php print $district_value ?></a></li>
        <?php
            $district_counter++;
            if ($district_counter >= 10) {
              break;
            }
          }

          if ($total_districts > 10) {
        ?>
          <li><a href="<?php print url('q', array('query'=> array('city' => $default_city_index, 'type' => $all_property_types))) ?>"><span><?php print t('More') ?></span></a></li>
        <?php } ?>
      </ul>
    </div>
    <?php if ($key === "office_commercial") { ?>
      <div class="options opt-size">
        <div class="tab-label"><?php print t('Unit Size') ?></div>
        <ul class="tab-list">
          <?php foreach ($unit_sizes as $key => $unit_size) { ?>
            <li><a href="<?php print url('q', array('query'=> array('city' => $default_city_index, 'type' => $all_property_types, 'area' => $unit_size))) ?>"><span><?php print ($key > 0) ? t("@sizem²", array('@size' => $key)) : $key; ?></span></a></li>
          <?php } ?>
        </ul>
      </div>
    <?php } ?>
    <?php if ($key === "retail") { ?>
    <div class="options opt-positioning">
      <div class="tab-label"><?php print t('Positioning') ?></div>
      <ul class="tab-list">
        <li><a href="<?php print url('q', array('query'=> array('city' => $default_city_index, 'type' => $all_property_types))) ?>"><span><?php print t('All') ?></span></a></li>
        <?php
          foreach ($all_positioning as $positioning) {
            $positioning_value = ($language->language == 'en') ? $positioning['Description'] : $positioning['DescriptionLocal'];
        ?>
          <li><a href="<?php print url('q', array('query'=> array('city' => $default_city_index, 'type' => $all_property_types, 'positioning' => $positioning_value))) ?>"><span><?php print $positioning_value ?></span></a></li>
        <?php } ?>
      </ul>
    </div>
    <?php } ?>
  </div>
  <?php } ?>
  <?php
    $search_form = drupal_get_form('olm_search_tab_form');
    print drupal_render($search_form);
  ?>
</div>
