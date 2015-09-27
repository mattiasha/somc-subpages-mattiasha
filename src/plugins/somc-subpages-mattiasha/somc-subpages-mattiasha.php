<?php

/*
	Plugin Name: Somc Subpages Mattiasha
	Plugin URI: http://www.github.com/mattiasha/somc-subpages-mattiasha
	Description: Plugin for displaying hierarchical representation of subpages of current page
	Version: 1.0
	Author: Mattias Andersson
	Author URI: http://www.github.com/mattiasha
	License: GPL2
	Text Domain: somc-subpages-mattiasha
*/


/* HOOKS */

add_action( 'init', 'ssm_register_shortcodes' );		// Our shortcode



/* SHORTCODES */

/**
 * Function that registers all shortcodes of plugin
 */
function ssm_register_shortcodes() {
	// add_shortcode('slb_form', 'slb_form_shortcode');
	add_shortcode('somc-subpages-mattiasha', [new Ssm_Somc_Subpages(), 'ssm_shortcode']);
}


/**
 * Class for creating shortcode for hierarchial subpage structure
 */
class Ssm_Somc_Subpages {
	/**
	 * Function that creates shortcode that lists hierarchical subpage structure
	 * of current page
	 */
	public function ssm_shortcode($args, $content = '') {
		return Subpage_Hierarchy::get_hierarchy();
  }
}



/* HELPER CLASSES */

/**
 * Helper class for creating page hierarchy
 */
class Subpage_Hierarchy {
  /**
   * Function that creates shortcode that lists hierarchical subpage structure
   * of current page
   */
  public static function get_hierarchy() {
    // Get child hierarchy of current page
    global $post;
    $current_page = get_post($post->ID);
    self::create_hierarchy($current_page);  // Adds 'children' property to $current_page
    
    // Create html output for page structure, using the new 'children' property
    $output = '<div class="subpagelistContainer">';
    $level = 0;
    if ( count ( $current_page->children ) > 0 && $current_page->children != NULL ) {
      self::create_hierarchy_html($current_page->children, $output, $level);
    }
    else {
      $output .= 'No subpages found';
    }
    // self::create_hierarchy_html($current_page->children, $output, $level);
    $output .= "</div>";

    return $output;
  }


  /**
   * Function that creates hierarchical list structure corresponding
   * to the hierarchical page structure
   *
   * @param array $pages      - The array of page objects for one specific level
   * @param string $output    - the resulting html
   * @param int $level            - The recursion level
   */
  private static function create_hierarchy_html($pages, &$output, $level) {
    // Only add expList id on first level of list
    $id = $level == 0 ? " class='subpageList'" : '';

    $output .= "<ul$id>";

    // Sort icon
    $output .= '<li class="ssm-list-sort dashicons dashicons-arrow-up-alt2"></li>'; 
    // <span class="dashicons dashicons-arrow-up-alt2"></span>

    // Loop through all pages and add info in list
    foreach ( $pages as $page) {
      $output .= '<li>'; 

      // The page title, truncated after 20 chars
      $output .= mb_substr($page->post_title, 0, 20);

      // The page thumbnail
      $thumbnail_size = array(15, 15);
      $output .=  get_the_post_thumbnail( $page->ID, $thumbnail_size );

      // If page has children, add sublist recursively
      if ( count($page->children) > 0 && $page->children != NULL ) {
        self::create_hierarchy_html($page->children, $output, $level + 1);
      }

      $output .= '</li>';
    }

    $output .= '</ul>';

    // $output .= '<div class="ssm-list-sort">sort></div>';
  }


  

	/**
	 * Function that adds a 'children' property to a page WP_Post object, where
	 * the property represents the hierarchical page structure of the page
	 * and its children, as an array of objects
	 * 
	 * @param object $page - The page represented by WP_Post object
	 */
	private static function create_hierarchy( &$page ) {
		// Get children of page
		$args = array(
			'child_of' 	=> $page->ID, 
			'parent' 		=> $page->ID,
		);
		$children = get_pages( $args );

		// Handle children
		if ( count( $children ) > 0 ) {
			$page->children = array();
			
			foreach ($children as $child ) {
				// Add child to parent
				$page->children[] = $child;

				// Add children of child, recursively
				self::create_hierarchy( $child );
			}
		}
	}
}






