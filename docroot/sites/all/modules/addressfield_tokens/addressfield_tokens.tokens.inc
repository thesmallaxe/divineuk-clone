<?php

/**
 * @file
 * Provides token replacements for address fields.
 */

/**
 * Implements hook_token_info_alter().
 *
 * Attaches tokens to all addressfield properties. The default names of each
 * addressfield component can be altered by administrators according to the
 * site's locale.
 */
function addressfield_tokens_token_info_alter(&$info) {
  // Define the address field token types.
  $info['types']['addressfield'] = array(
    'name' => t('Address field'),
    'description' => t('An address associated with an entity.'),
    'needs-data' => 'addressfield',
  );

  // Add tokens for each component of the address.
  $info['tokens'] += array('addressfield' => array());

  $props = addressfield_data_property_info();
  $names = addressfield_tokens_property_names();
  $params = array(
    '@default_country' => addressfield_tokens_default_country(),
  );

  foreach ($props as $field => $data) {
    $fieldtoken = str_replace('_', '-', $field);

    if (!empty($names[$field])) {
      $name = $names[$field];
      $descr = $data['label'];
    }
    else {
      $name = $data['label'];
      $descr = $name;

      $matches = array();
      if (preg_match('/^(.*)\s+\(i\.e\.\s+(.*)\)$/', $name, $matches)) {
        $name = $matches[1];
        $descr = $matches[2];
      }
    }
    $info['tokens']['addressfield'][$fieldtoken] = array(
      'name' => $name,
      'description' => $descr,
      'type' => 'text',
    );
    $params['@' . $field] = $name;
  }
  $info['tokens']['addressfield']['administrative-area']['name'] .= ' (abbreviation)';
  $info['tokens']['addressfield']['country']['name'] .= ' (abbreviation)';

  // Add tokens for the formatted address and text-only version.
  $info['tokens']['addressfield'] += array(
    'full' => array(
      'name' => t('Formatted address'),
      'description' => t('The full formatted address.'),
      'type' => 'text',
    ),
    'text' => array(
      'name' => t('Text-only address'),
      'description' => t('The full address with line breaks but no formatting.'),
      'type' => 'text',
    ),
    'city-state' => array(
      'name' => t('City, State'),
      'description' => t('@locality and @administrative_area separated by commas (and @country if outside @default_country)', $params),
      'type' => 'text',
    ),
    'state-name' => array(
      'name' => t('@administrative_area (full name)', $params),
      'description' => t('The full name of the @administrative_area', $params),
      'type' => 'text',
    ),
    'country-name' => array(
      'name' => t('@country (full name)', $params),
      'description' => t('The full name of the @country', $params),
      'type' => 'text',
    ),
  );

  // Add user tokens that are useful for MailChimp.
  if (module_exists('mailchimp')) {
    $info['tokens']['addressfield'] += array(
      'mc-address' => array(
        'name' => t('MailChimp Address'),
        'description' => t('A full address formatted for integration with MailChimp.'),
        'type' => 'text',
      ),
    );
  }

  // Add extra text to webform submission values help.
  if (array_key_exists('submission', $info['tokens'])) {
    $info['tokens']['submission']['values']['description'] .= '
      <div>
      ' . t('For addressfield components you can also choose individual elements of the address using the syntax field_key:element, For example:') . '
      <ul>
        <li>[submission:values:address:thoroughfare]</li>
        <li>[submission:values:address:locality]</li>
        <li>[submission:values:address:administrative_area]</li>
        <li>[submission:values:address:postal_code]</li>
        <li>[submission:values:address:country]</li>
      </ul>
    </div>';
  }

  // Attach tokens to all address fields.
  $valid_types = entity_token_types();

  foreach ($valid_types as $token_type => $type) {
    foreach (entity_get_all_property_info($type) as $name => $property) {
      $name = str_replace('_', '-', $name);

      if (!isset($info['tokens'][$token_type][$name]) && isset($property['type']) && $property['type'] == 'addressfield') {
        $info['tokens'][$token_type][$name] = array(
          'name' => $property['label'],
          'type' => 'addressfield',
          'description' => isset($property['description']) ? $property['description'] : t('Address field'),
        );
      }
    }
  }
}

/**
 * Implements hook_tokens().
 */
function addressfield_tokens_tokens($type, $tokens, array $data = array(), array $options = array()) {
  $url_options = array();
  // @todo: why are we setting $language_code and not using it?
  if (isset($options['language'])) {
    $url_options['language'] = $options['language'];
    $language_code = $options['language']->language;
  }
  else {
    $language_code = LANGUAGE_NONE;
  }

  $sanitize = !empty($options['sanitize']);
  $replacements = array();
  $last_original = NULL;

  // Process webform submission addressfield tokens.
  if ($type == 'submission' && !empty($data['webform-submission'])) {
    $submission = $data['webform-submission'];
    $node = isset($data['node']) ? $data['node'] : node_load($submission->nid);
    $value_tokens = token_find_with_prefix($tokens, 'values');
    // Loop through the components of the webform looking for addressfield
    // components with the expected form_key.
    foreach ($node->webform['components'] as $cid => $component) {
      if ($component['type'] == 'addressfield' && isset($submission->data[$cid][0])) {
        $address = $submission->data[$cid][0];
        if (is_string($address)) {
          $address = unserialize($address);
        }
        $address_tokens = token_find_with_prefix($value_tokens, $component['form_key']);
        foreach ($address_tokens as $token => $original) {
          if (isset($address[$token])) {
            $replacements[$original] = $sanitize ? filter_xss($address[$token]) : $address[$token];
          }
        }
      }
    }
  }
  // Process address field tokens.
  elseif ($type == 'addressfield' && !empty($data['addressfield'])) {
    foreach ($tokens as $name => $original) {
      $last_original = $original;
      $name = str_replace('-', '_', $name);
      $address = $data['addressfield'];

      // If the address field exists, use it.
      if (isset($address[$name])) {
        $replacements[$original] = $sanitize ? filter_xss($address[$name]) : $address[$name];
      }
      else {
        // Otherwise, it's a special token.
        switch ($name) {
          case 'full':
            $render = addressfield_generate($address, array('address'), array(
              'mode' => 'render',
            ));
            $replacements[$original] = $sanitize ? filter_xss(drupal_render($render)) : drupal_render($render);
            break;

          case 'text':
            $out = array();
            if (!empty($address['thoroughfare'])) {
              $out[0] = $address['thoroughfare'];
            }
            $out[1] = array();
            if (!empty($address['locality'])) {
              $out[1][] = $address['locality'];
            }
            if (!empty($address['administrative_area'])) {
              $out[1][] = $address['administrative_area'];
            }
            $out[1] = implode(', ', $out[1]);
            if (!empty($address['postal_code'])) {
              $out[1] .= ' ' . $address['postal_code'];
            }
            if (!empty($address['country']) && $address['country'] != addressfield_tokens_default_country()) {
              $out[2] = _addressfield_tokens_country($address['country']);
            }
            $replacements[$original] = $sanitize ? filter_xss(implode("\n", $out)) : implode("\n", $out);
            break;

          case 'city_state':
            $out = array();
            if (!empty($address['locality'])) {
              $out[] = $address['locality'];
            }
            if (!empty($address['administrative_area'])) {
              $out[] = $address['administrative_area'];
            }
            if (!empty($address['country']) && $address['country'] != addressfield_tokens_default_country()) {
              $out[] = _addressfield_tokens_country($address['country']);
            }
            $replacements[$original] = $sanitize ? filter_xss(implode(", ", $out)) : implode(", ", $out);
            break;

          case 'state_name':
            if (!empty($address['administrative_area']) && !empty($address['country'])) {
              if ($sanitize) {
                $replacements[$original] = filter_xss(addressfield_tokens_state($address['country'], $address['administrative_area']));
              }
              else {
                $replacements[$original] = addressfield_tokens_state($address['country'], $address['administrative_area']);
              }
            }
            break;

          case 'country_name':
            if (!empty($address['country'])) {
              if ($sanitize) {
                $replacements[$original] = filter_xss(_addressfield_tokens_country($address['country']));
              }
              else {
                $replacements[$original] = _addressfield_tokens_country($address['country']);
              }
            }
            break;

          case 'mc_address':
            $address_components = array(
              'thoroughfare',
              'premise',
              'locality',
              'administrative_area',
              'postal_code',
              'country',
            );
            $mc_address = array();
            foreach ($address_components as $component) {
              if (!empty($address[$component])) {
                $mc_address[] = check_plain($address[$component]);
              }
            }
            // MailChimp requires the address to be a string of double-space
            // delimited address fields. (http://kb.mailchimp.com/article/how-do-i-set-up-the-address-field-type-for-import)
            $replacements[$original] = !empty($mc_address) ? implode('  ', $mc_address) : '';
            break;
        }
      }
    }
    if (!isset($replacements[$last_original])) {
      $replacements[$last_original] = '';
    }
  }
  else {
    $token_types = entity_token_types();
    $info = token_info();
    if (isset($info['tokens'][$type])) {
      // Otherwise, chain address fields attached to other entities.
      foreach ($info['tokens'][$type] as $name => $token_info) {
        if (isset($token_info['type']) && $token_info['type'] == 'addressfield') {
          if ($chained_tokens = token_find_with_prefix($tokens, $name)) {
            $wrapper = !isset($wrapper) ? _entity_token_wrap_data($type, $token_types[$type], $data[$type], $options) : $wrapper;
            $property_name = str_replace('-', '_', $name);
            try {
              $address = $wrapper->$property_name->value();
              $replacements += token_generate('addressfield', $chained_tokens, array('addressfield' => $address), $options);
            }
            catch (EntityMetadataWrapperException $e) {
              // The property doesn't exist, so skip it.
              $replacements[$last_original] = '';
            }
          }
        }
      }
    }
  }

  return $replacements;
}
