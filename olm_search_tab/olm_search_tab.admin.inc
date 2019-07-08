<?php

/**
 * @file
 * Functions for administration page.
 */

/**
 * Administration form.
 *
 * @param $form
 * @param $form_state
 */
function olm_search_tab_settings_form($form, $form_state) {
  // Search Settings
  $form['search_tab'] = array(
    '#type' => 'fieldset',
    '#title' => t('Search Tab Settings'),
    '#collapsible' => FALSE,
    '#collapsed' => FALSE,
  );

  $form['search_tab']['olm_search_tab_unit_size'] = [
    '#type' => 'textfield',
    '#title' => t('Unit Size'),
    '#default_value' => variable_get('olm_search_tab_unit_size', '0,100,200,300,500,1000,1200'),
    '#description' => t('Comma-separated values. Example: 0,100,200,300,400,500,1000,1200'),
    '#required' => TRUE,
  ];

  $form['search_tab']['olm_search_tab_property_types_merged'] = [
    '#type' => 'textfield',
    '#title' => t('Merge Property Types'),
    '#default_value' => variable_get('olm_search_tab_property_types_merged'),
    '#description' => t('This will merger the property types to single request. Pipe and comma-separated values. Example: Label En|Label CN|type_param1,type_param2'),
  ];

  // Get property type
  $property_type = json_decode(olm_search_property_type_get());

  // Get main property type
  foreach ($property_type as $key => $value) {
    foreach ($value as $v) {
      $index = str_replace("/", "_", strtolower($v->{'DescriptionValue'}));
      $main_property_type[$index] = $key;
    }
  }

  foreach ($main_property_type as $index => $property_type_value) {

    $form['search_tab_cities_'.$index] = array(
      '#type' => 'fieldset',
      '#title' => t('Search Tab '. $property_type_value .' City Districts Settings'),
      '#collapsible' => TRUE,
      '#collapsed' => TRUE,
      '#description' => t('Comma-separated values. Example: Pudong,Minhang,Yangpu'),
    );

    $cities = _olm_search_tab_get_searchable_cities();
    foreach ($cities as $city) {
      $city_id = $city['id'];
      $districts = olm_search_tab_get_district_with_properties($city_id);

      $display_district = '';
      foreach ($districts as $district) {
        $display_district .= $district['Description']."<br>";
      }
      $form['search_tab_cities_'.$index]['olm_search_tab_districts_'.$index.'_'.$city_id] = [
        '#type' => 'textfield',
        '#title' => $city['name'],
        '#default_value' => variable_get('olm_search_tab_districts_'.$index.'_'.$city_id),
        '#suffix' => '<div style="border: 1px solid #ddd;padding: 5px;margin-bottom: 10px;height: 65px;overflow: scroll;max-width: 348px;"">'.$display_district.'</div><hr>',
      ];
    }

  }

  $form['#validate'][] = 'olm_search_tab_settings_form_validate';

  return system_settings_form($form);
}

/**
 * Form validation handler.
 *
 * @param $form
 * @param $form_state
 */
function olm_search_tab_settings_form_validate($form, &$form_state) {
  $error = FALSE;

  if ($form_state['values']['olm_search_tab_unit_size']) {
    $olm_search_tab_unit_size = $form_state['values']['olm_search_tab_unit_size'];
    $unit_sizes = _olm_search_tab_check_numeric_value(explode(',', $olm_search_tab_unit_size));
    sort($unit_sizes);
    if (count($unit_sizes) > 7) {
      // Only get the first 7 values.
      $unit_sizes = array_slice($unit_sizes, 0, 7);
    } else if (count($unit_sizes) < 7) {
      form_set_error('olm_search_tab_settings_form', t('Please make sure unit sizes has 7 values.'));
    }
    // Clean value.
    $form_state['values']['olm_search_tab_unit_size'] = implode(",", $unit_sizes);
  }

  if ($form_state['values']['olm_search_tab_property_types_merged']) {
    $olm_search_tab_property_types_merged = $form_state['values']['olm_search_tab_property_types_merged'];
    $olm_search_tab_property_types_merged_array = explode('|',$olm_search_tab_property_types_merged);
    if (count($olm_search_tab_property_types_merged_array) !== 3) {
      form_set_error('olm_search_tab_settings_form', t('Please make sure merge property types pattern is correct.'));
    }
  }
}

/**
 * Function to check numeric value.
 *
 * @param $unit_sizes
 */
function _olm_search_tab_check_numeric_value($unit_sizes) {
  $output = array();
  foreach ($unit_sizes as $unit_size) {
    if (is_numeric(trim($unit_size))) {
      $unit_size = trim($unit_size);
      $output[$unit_size] = $unit_size;
    }
  }
  return $output;
}
