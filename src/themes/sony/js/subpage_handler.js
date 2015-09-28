
/**
 * Js to make subpage hierarchy list sortable, expandable and collapsible
 */

jQuery(document).ready(function($){
	
	main();

	/**
	 * Main function
	 */
	function main() {
		collapseLists();
		makeListsExpandable();
		sortLists();
		makeListsSortable();
	}
	

	/**
	 * Function that collapses all subpage lists
	 */
	function collapseLists() {
		$('.subpageList').find('li:has(ul)')
			.addClass('collapsed')
			.children('ul').hide();
	}


	/**
	 * Function that makes list expandable/collapsable
	 */
	function makeListsExpandable() {
		// Find all expandable lists and add click event
		$('.subpageList').find('li:has(ul)')
			.click( function(event) {
				if (this == event.target) {
					$(this).toggleClass('expanded');
					$(this).children('ul').toggle('medium');
				}
				return false;
			}); 	
	};


	/**
	 * Function that sorts all available subpage lists in ascending order
	 */
	function sortLists() {
		// Loop all ul, and for each list do initial sort
		var ul = $(".subpageListContainer ul");
		$.each(ul, function() {
			sortList($(this), false);
		});
	}


	/**
	 * Function that adds click events to make lists sortable
	 * in ascending or descending order
	 */
	function makeListsSortable() {
		// Get all lists
		var ul = $(".subpagelistContainer ul");
		
		// Add click events on each sort arrow, toggle between ascending and descending order
		$.each(ul, function() {
			$(this).children('.ssm-list-sort').click(function(e) {
				if($(e.target).hasClass('dashicons-arrow-up-alt2')) {
					// Show down icon and sort list in descending order
					var sortArrow = $(e.target);
					sortArrow.removeClass('dashicons-arrow-up-alt2');
					sortArrow.addClass('dashicons-arrow-down-alt2');
					sortList($(sortArrow.parent()), false);
				} else {
					// Show up icon and sort list in ascending order
					var sortArrow = $(e.target);
					sortArrow.addClass('dashicons-arrow-up-alt2');
					sortArrow.removeClass('dashicons-arrow-down-alt2');
					sortList($(sortArrow.parent()), true);
				}
			})
		});
	}


	/**
	 * Function to sort list
	 * Note: Sort arrow included in list so needs to be sorted first in list
	 *
	 * @param bool asc - Whether or not to sort in ascending order
	 */
	function sortList(ul, asc) {
		// Sort ascending or descending
		var sortFlag = asc ? 1 : -1;

		// Get the list items as array
		var arr = $.makeArray(ul.children("li"));

		// Sort the array
		arr.sort(function(a, b) {
				var textA = $(a).text();
				var textB = $(b).text();

				if($(a).hasClass('ssm-list-sort')) return -1;  // Sort arrow always top of list
				if(textA < textB) return -sortFlag;
				if(textA > textB) return sortFlag;

				return 0;
		});

		// Empty list and instead append the sorted list
		ul.empty();
		$.each(arr, function() {
				ul.append(this);
		});
	
		// Make lists expandable and sortable again after removing/adding elements
		makeListsExpandable();
		makeListsSortable();
	}	
})