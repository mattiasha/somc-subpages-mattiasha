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
add_action( 'widgets_init', 'ssm_register_widgets' );	// Our widget


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



/* WIDGETS */

/**
 * Function to register widgets
 */
function ssm_register_widgets() {
	register_widget( 'Ssm_Somc_Subpages_Widget' );
}


/**
 * Class for subpage hierarchy widget
 */ 
class Ssm_Somc_Subpages_Widget extends WP_Widget {

	/** 
	 * Constructor
	 */
	function __construct() {
		parent::__construct(
			// Base ID
			'Ssm_Somc_Subpages_Widget', 

			// Widget name
			__('Subpage Hierarchy Widget', 'ssm_widget_domain'), 

			// Widget description
			array( 'description' => __( 'Sample widget based on WPBeginner Tutorial', 'ssm_widget_domain' ), ) 
		);
	}

	/**
	 * Widget front-end
	 */
	public function widget( $args, $instance ) {
		// Before widget
		echo $args['before_widget'];
		
		// Title
		$title = apply_filters( 'widget_title', $instance['title'] );
		if ( ! empty( $title ) ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}
		
		// Widget content
		echo Subpage_Hierarchy::get_hierarchy();
		
		// After widget
		echo $args['after_widget'];
	}
			
	
	/**
	 * Widget backend
	 */ 
	public function form( $instance ) {
		// Title
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}
		else {
			$title = __( 'New title', 'ssm_widget_domain' );
		}
		
		// Widget admin form
		?>
			<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
			</p>
		<?php 
	}
		
	/**
	 * Widget update
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		return $instance;
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






