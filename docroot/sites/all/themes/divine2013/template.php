<?php
/**
 * @file
 * Contains the theme's functions to manipulate Drupal's default markup.
 *
 * A QUICK OVERVIEW OF DRUPAL THEMING
 *
 *   The default HTML for all of Drupal's markup is specified by its modules.
 *   For example, the comment.module provides the default HTML markup and CSS
 *   styling that is wrapped around each comment. Fortunately, each piece of
 *   markup can optionally be overridden by the theme.
 *
 *   Drupal deals with each chunk of content using a "theme hook". The raw
 *   content is placed in PHP variables and passed through the theme hook, which
 *   can either be a template file (which you should already be familiary with)
 *   or a theme function. For example, the "comment" theme hook is implemented
 *   with a comment.tpl.php template file, but the "breadcrumb" theme hooks is
 *   implemented with a theme_breadcrumb() theme function. Regardless if the
 *   theme hook uses a template file or theme function, the template or function
 *   does the same kind of work; it takes the PHP variables passed to it and
 *   wraps the raw content with the desired HTML markup.
 *
 *   Most theme hooks are implemented with template files. Theme hooks that use
 *   theme functions do so for performance reasons - theme_field() is faster
 *   than a field.tpl.php - or for legacy reasons - theme_breadcrumb() has "been
 *   that way forever."
 *
 *   The variables used by theme functions or template files come from a handful
 *   of sources:
 *   - the contents of other theme hooks that have already been rendered into
 *     HTML. For example, the HTML from theme_breadcrumb() is put into the
 *     $breadcrumb variable of the page.tpl.php template file.
 *   - raw data provided directly by a module (often pulled from a database)
 *   - a "render element" provided directly by a module. A render element is a
 *     nested PHP array which contains both content and meta data with hints on
 *     how the content should be rendered. If a variable in a template file is a
 *     render element, it needs to be rendered with the render() function and
 *     then printed using:
 *       <?php print render($variable); ?>
 *
 * ABOUT THE TEMPLATE.PHP FILE
 *
 *   The template.php file is one of the most useful files when creating or
 *   modifying Drupal themes. With this file you can do three things:
 *   - Modify any theme hooks variables or add your own variables, using
 *     preprocess or process functions.
 *   - Override any theme function. That is, replace a module's default theme
 *     function with one you write.
 *   - Call hook_*_alter() functions which allow you to alter various parts of
 *     Drupal's internals, including the render elements in forms. The most
 *     useful of which include hook_form_alter(), hook_form_FORM_ID_alter(),
 *     and hook_page_alter(). See api.drupal.org for more information about
 *     _alter functions.
 *
 * OVERRIDING THEME FUNCTIONS
 *
 *   If a theme hook uses a theme function, Drupal will use the default theme
 *   function unless your theme overrides it. To override a theme function, you
 *   have to first find the theme function that generates the output. (The
 *   api.drupal.org website is a good place to find which file contains which
 *   function.) Then you can copy the original function in its entirety and
 *   paste it in this template.php file, changing the prefix from theme_ to
 *   divine2013_. For example:
 *
 *     original, found in modules/field/field.module: theme_field()
 *     theme override, found in template.php: divine2013_field()
 *
 *   where divine2013 is the name of your sub-theme. For example, the
 *   zen_classic theme would define a zen_classic_field() function.
 *
 *   Note that base themes can also override theme functions. And those
 *   overrides will be used by sub-themes unless the sub-theme chooses to
 *   override again.
 *
 *   Zen core only overrides one theme function. If you wish to override it, you
 *   should first look at how Zen core implements this function:
 *     theme_breadcrumbs()      in zen/template.php
 *
 *   For more information, please visit the Theme Developer's Guide on
 *   Drupal.org: http://drupal.org/node/173880
 *
 * CREATE OR MODIFY VARIABLES FOR YOUR THEME
 *
 *   Each tpl.php template file has several variables which hold various pieces
 *   of content. You can modify those variables (or add new ones) before they
 *   are used in the template files by using preprocess functions.
 *
 *   This makes THEME_preprocess_HOOK() functions the most powerful functions
 *   available to themers.
 *
 *   It works by having one preprocess function for each template file or its
 *   derivatives (called theme hook suggestions). For example:
 *     THEME_preprocess_page    alters the variables for page.tpl.php
 *     THEME_preprocess_node    alters the variables for node.tpl.php or
 *                              for node--forum.tpl.php
 *     THEME_preprocess_comment alters the variables for comment.tpl.php
 *     THEME_preprocess_block   alters the variables for block.tpl.php
 *
 *   For more information on preprocess functions and theme hook suggestions,
 *   please visit the Theme Developer's Guide on Drupal.org:
 *   http://drupal.org/node/223440 and http://drupal.org/node/1089656
 */


/**
 * Override or insert variables into the maintenance page template.
 *
 * @param $variables
 *   An array of variables to pass to the theme template.
 * @param $hook
 *   The name of the template being rendered ("maintenance_page" in this case.)
 */
/* -- Delete this line if you want to use this function
function divine2013_preprocess_maintenance_page(&$variables, $hook) {
  // When a variable is manipulated or added in preprocess_html or
  // preprocess_page, that same work is probably needed for the maintenance page
  // as well, so we can just re-use those functions to do that work here.
  divine2013_preprocess_html($variables, $hook);
  divine2013_preprocess_page($variables, $hook);
}
// */

/**
 * Override or insert variables into the html templates.
 *
 * @param $variables
 *   An array of variables to pass to the theme template.
 * @param $hook
 *   The name of the template being rendered ("html" in this case.)
 */
/* -- Delete this line if you want to use this function
function divine2013_preprocess_html(&$variables, $hook) {
  $variables['sample_variable'] = t('Lorem ipsum.');

  // The body tag's classes are controlled by the $classes_array variable. To
  // remove a class from $classes_array, use array_diff().
  //$variables['classes_array'] = array_diff($variables['classes_array'], array('class-to-remove'));
}
// */

/**
 * Override or insert variables into the page templates.
 *
 * @param $variables
 *   An array of variables to pass to the theme template.
 * @param $hook
 *   The name of the template being rendered ("page" in this case.)
 */
/* -- Delete this line if you want to use this function
function divine2013_preprocess_page(&$variables, $hook) {
  $variables['sample_variable'] = t('Lorem ipsum.');
}
// */

/**
 * Override or insert variables into the node templates.
 *
 * @param $variables
 *   An array of variables to pass to the theme template.
 * @param $hook
 *   The name of the template being rendered ("node" in this case.)
 */
/* -- Delete this line if you want to use this function
function divine2013_preprocess_node(&$variables, $hook) {
  $variables['sample_variable'] = t('Lorem ipsum.');

  // Optionally, run node-type-specific preprocess functions, like
  // divine2013_preprocess_node_page() or divine2013_preprocess_node_story().
  $function = __FUNCTION__ . '_' . $variables['node']->type;
  if (function_exists($function)) {
    $function($variables, $hook);
  }
}
// */

/**
 * Override or insert variables into the comment templates.
 *
 * @param $variables
 *   An array of variables to pass to the theme template.
 * @param $hook
 *   The name of the template being rendered ("comment" in this case.)
 */
/* -- Delete this line if you want to use this function
function divine2013_preprocess_comment(&$variables, $hook) {
  $variables['sample_variable'] = t('Lorem ipsum.');
}
// */

/**
 * Override or insert variables into the region templates.
 *
 * @param $variables
 *   An array of variables to pass to the theme template.
 * @param $hook
 *   The name of the template being rendered ("region" in this case.)
 */
/* -- Delete this line if you want to use this function
function divine2013_preprocess_region(&$variables, $hook) {
  // Don't use Zen's region--sidebar.tpl.php template for sidebars.
  //if (strpos($variables['region'], 'sidebar_') === 0) {
  //  $variables['theme_hook_suggestions'] = array_diff($variables['theme_hook_suggestions'], array('region__sidebar'));
  //}
}
// */

/**
 * Override or insert variables into the block templates.
 *
 * @param $variables
 *   An array of variables to pass to the theme template.
 * @param $hook
 *   The name of the template being rendered ("block" in this case.)
 */
/* -- Delete this line if you want to use this function
function divine2013_preprocess_block(&$variables, $hook) {
  // Add a count to all the blocks in the region.
  // $variables['classes_array'][] = 'count-' . $variables['block_id'];

  // By default, Zen will use the block--no-wrapper.tpl.php for the main
  // content. This optional bit of code undoes that:
  //if ($variables['block_html_id'] == 'block-system-main') {
  //  $variables['theme_hook_suggestions'] = array_diff($variables['theme_hook_suggestions'], array('block__no_wrapper'));
  //}
}
// */
function divine2013_form_alter(&$form, &$form_state, $form_id) {
  if ($form_id == 'search_block_form') {
    $form['search_block_form']['#title'] = t('Search'); // Change the text on the label element
    $form['search_block_form']['#title_display'] = 'before'; // Toggle label visibilty
    $form['search_block_form']['#size'] = 8;  // define size of the textfield
    $form['actions']['submit']['#value'] = t('Search'); // Change the text on the submit button
   // $form['actions']['submit'] = array('#type' => 'image_button', '#src' => base_path() . path_to_theme() . '/images/search-button.png');
  }

} 

/* ADDING JS & CSS FOR THE ADDTHISEVENT */
function divine2013_preprocess_page(&$variables) {
  global $base_url;
if (isset($variables['node']) && $variables['node']->type == 'blog_event') {
drupal_add_js(drupal_get_path('theme', 'divine2013') . '/js/addthisevent/js/atemay.js');
$variables['scripts'] = drupal_get_js();

}

/**
* UNSET THE FRONTPAGE DEFAULT TITLE AND MESSAGE 
*/
if (drupal_is_front_page()) {
  $variables['title']="";
  unset($variables['page']['content']['system_main']['default_message']); 
}
  $variables['header_image'] = '';
  
  if(drupal_is_front_page()) {
    $fid = variable_get('header_image',0);
    if($fid != 0) {
      $image_style = variable_get('image_header_style_name', '');
      $image = file_load($fid);
      if ($image_style == '') {
        $imgpath = $image->uri;
      }
      else {
        $imgpath = image_style_path($image_style, $image->uri);        
        $final_image_path = drupal_realpath($imgpath);
        if (!file_exists($final_image_path)) {
          $style_definition = image_style_load($image_style);
          image_style_create_derivative($style_definition, $image->uri, $imgpath);
        }
      }
    }
    else {
      $imgpath = $base_url . '/' .drupal_get_path('theme','divine2013') . '/images/homepage-banner/Divine-sharing-bars-black.jpg';
    }
    $variables['header_image'] = file_create_url($imgpath);
  }
}

/**
* Implements hook_form_BASE_FORM_ID_alter() to change the Add to Cart parameters.
*/
function divine2013_form_commerce_cart_add_to_cart_form_alter(&$form, &$form_state) {
    $form['submit']['#value']  = 'Buy now';
	$form['submit']['#prefix'] = '<div class="buy-now button-wrapper"><div class="button-inner-wrapper">';
	$form['submit']['#suffix'] = '</div></div>';
    $form['quantity']['#title'] = '';
}

/**
* Implements function hook_form_BASE_FORM_ID_alter()
* Read more http://drupal.org/node/1537394#comment-7003814
*/
function divine2013_form_webform_client_form_alter(&$form, &$form_state, $form_id) {
	$form['actions']['#attributes']['class'][] = 'form-submit-prefix form-submit-suffix';
}

/**
* Implements hook_commerce_shipping_service_info_alter().
*/

function divine2013_commerce_shipping_service_info_alter(&$shipping_services) {
  if (isset($shipping_services['free_shipping'])) {
    $shipping_services['free_shipping']['weight'] = -10;
    $shipping_services['free_shipping']['checked'] = 'checked';
  }
 if (isset($shipping_services['free_shipping_site_wide'])) {
    $shipping_services['free_shipping_site_wide']['weight'] = -9;
    $shipping_services['free_shipping_site_wide']['checked'] = 'checked';
  }
if (isset($shipping_services['standard_1st_class'])) {
    $shipping_services['standard_1st_class']['weight'] = -8;
    $shipping_services['standard_1st_class']['checked'] = 'checked';
  }
if (isset($shipping_services['standard_courier'])) {
    $shipping_services['standard_courier']['weight'] = -7;
  }
}

function divine2013_commerce_checkout_page_info_alter(&$pages) {
  if(!empty($pages['review'])) {
    $pages['review']['submit_value'] = t('Proceed with purchase');
  }
}

/**
* Implements theme_field().
* Used to adda class to items on Nutritional Value field in Product 
*/

function divine2013_field__field_nutritional_information($variables) {
  $output = '';
  // Render the label, if it's not hidden.
  if (!$variables['label_hidden']) {
    $output .= '<div class="field-label"' . $variables['title_attributes'] . '>' . $variables['label'] . ':&nbsp;</div>';
  }

  // Render the items.
  $output .= '<div class="field-items"' . $variables['content_attributes'] . '>';
  foreach ($variables['items'] as $delta => $item) {
    $sub = drupal_render($item);
    $classes = 'field-item ' . str_replace(" " , "-", $sub);
    $output .= '<div class="' . $classes . '"' . $variables['item_attributes'][$delta] . '>' . $sub . '</div>';
  }
  $output .= '</div>';

  // Render the top-level DIV.
  $output = '<div class="' . $variables['classes'] . '"' . $variables['attributes'] . '>' . $output . '</div>';

  return $output;
}

  // Add template suggestions for the html.tpl.php.
function divine2013_preprocess_html (&$vars) {
if (module_exists('path')) {
    $alias = drupal_get_path_alias(str_replace('/edit','',$_GET['q']));
    if ($alias != $_GET['q']) {
      $template_filename = 'html';
      foreach (explode('/', $alias) as $path_part) {
        $template_filename = $template_filename . '__' . $path_part;
        $vars['theme_hook_suggestions'][] = $template_filename;
      }
    }
  }
}

