<?php
/*
Plugin Name: CoreFactor Simple Schedule
Plugin URI: http://URI_Of_Page_Describing_Plugin_and_Updates
Description: Manage timeslots for classes / workshops / seminars using shortcode
Version: 1.0
Author: Rui Cruz @ CoreFactor
Author URI: http://URI_Of_The_Plugin_Author
License: GPL2
*/

$CfSimpleScheduler = new CfSimpleScheduler();

add_action('admin_menu', array(&$CfSimpleScheduler, 'add_menu'));
register_activation_hook(__FILE__, array(&$CfSimpleScheduler, 'install'));
add_shortcode('cfss_time_slots', array(&$CfSimpleScheduler, 'shortCode'));


Class CfSimpleScheduler {
	
	var $title = 'CoreFactor Simple Scheduler';
	
	var $table_name = null;		
	var $plugin_url = null;
	var $plugin_directory = null;
	
	function __construct() {
		
		global $wpdb;
		$this->table_name = $wpdb->prefix . 'cf_scheduler';
				
		$this->plugin_url = WP_PLUGIN_URL . '/' . basename(dirname(__FILE__)) . '/';
		$this->plugin_directory = WP_PLUGIN_DIR . '/' . basename(dirname(__FILE__)) . '/';
				
	}


	public function index() {
	
		global $wpdb;
	
		if (!current_user_can('publish_posts'))  {
			wp_die( __('You do not have sufficient permissions to access this page.') );
		}
		
		echo "<div class='wrap'><div id='icon-themes' class='icon32'></div><h2>{$this->title}</h2>";

		if ($this->handleDelete() === true) {
			echo '<div id="message" class="updated fade"><p><strong>Time slot removed!</strong></p></div>';
		}
	
		$timeslots = $wpdb->get_results( "SELECT * FROM {$this->table_name}" );			
		
		include($this->plugin_directory . 'templates/admin_index.php');
		echo '</div>';
		
	}

	public function edit() {
	
		if (!current_user_can('publish_posts'))  {
			wp_die( __('You do not have sufficient permissions to access this page.') );
		}
		
		if ($this->postOk()) {

			$this->savePost();			
			
		} 
		
		echo "<div class='wrap'><div id='icon-themes' class='icon32'></div><h2>{$this->title}</h2>";
		
		if ($this->getEditData()) {
			
			echo '<h3>Edit time slot</h3>';
			
		} else {
			
			echo '<h3>Add a new time slot</h3>';
			
		}
	
		include($this->plugin_directory . 'templates/admin_edit.php');

		echo '</div>';		
		
	}
	
	/**
	 * Handles the deleting of a row
	 *
	 * @return void
	 * @author Rui Cruz
	 */
	private function handleDelete() {
		
		if ($_GET['action'] == 'delete' && is_numeric($_GET['id'])) {
			
			global $wpdb;
			
			if ($wpdb->query("DELETE FROM $this->table_name WHERE id = '{$_GET['id']}' LIMIT 1") === 1) {
				
				return true;
				
			}
			
		}		
		
		return false;
		
	}
		
	/**
	 * Handles the editing / saving of a table entry using the $_POST variable
	 *
	 * @return bol
	 * @author Rui Cruz
	 */
	private function savePost() {
		
		global $wpdb;
		
		if (is_numeric($_POST['id'])) {
			
			if (!empty($_POST['week_day'])) {
				unset($_POST['date']);
			}
			
			$result = $wpdb->update($this->table_name, $_POST, array( 'id' => $_POST['id'] ), array( '%s', '%s' ), array( '%d' ) );		
			
		} else {
			
			$result = $wpdb->insert($this->table_name, $_POST, array('%s', '%s'));
			
		}
		
		if ($result !== false) {
			
			unset($_POST);
			echo '<div id="message" class="updated fade"><p><strong>Time slot Saved!</strong></p></div>';	
			return true;
			
		} else {
			
			$wpdb->print_error();
			
		}
		
		return false;
		
	}
	
	
	private function getEditData() {
		
		global $wpdb;
		
		if (is_numeric($_GET['id'])) {
			
			$row = $wpdb->get_row("SELECT * FROM {$this->table_name} WHERE id = {$_GET['id']}", ARRAY_A);
			
			if (!empty($row)) {
				
				foreach($row as $field => $value) {
					
					$_POST[ 'cfss_' . $field ] = $value;
					
				}
				
				return true;
				
			}
			
		}
		
		return false;
		
	}
	
	/**
	 * Prepare $_POST data
	 *
	 * @return bol
	 * @author Rui Cruz
	 */
	private function postOk() {

		$post_data = array();
		
		foreach($_POST as $field => $value) {
			
			$post_data[ str_replace('cfss_', '', $field) ] = $value;
			
		}

		$_POST = $post_data;
		
		return !empty($_POST);
		
	}
	
	
	/**
	 * Hooks to generate the Administration panel options
	 *
	 * @return void
	 * @author Rui Cruz
	 */
	public function add_menu() {

		add_menu_page('Simple Scheduler', 'Simple Scheduler', 'publish_posts', 'cf_simple_schedule_index',  array(&$this, 'index'));
		add_submenu_page('cf_simple_schedule_index', 'Time slots', 'Time slots', 'publish_posts', 'cf_simple_schedule_index', array(&$this, 'index'));
		add_submenu_page('cf_simple_schedule_index', 'Add', 'Add', 'publish_posts', 'cf_simple_schedule_edit', array(&$this, 'edit'));
		add_filter('favorite_actions', array(&$this, 'favoriteMenu'));
		
	}
	
	/**
	 * Handle shortcode in the Posts and Pages
	 *
	 * @param array $atts 
	 * @return string
	 * @author Rui Cruz
	 */
	public function shortCode($atts) {
		
		// [cfss_time_slots name="foo-value"]
		
		global $wpdb;

		if (empty($atts['name'])) return;
		
		$rows = $wpdb->get_results("SELECT * FROM {$this->table_name} WHERE name = '{$atts['name']}'", ARRAY_A);

		if ($rows === false) return;
		
		$html = "<ul class='cfss_time_slots cfss_{$atts['name']}'>";
				
		foreach($rows as $row) {
			
			$time_start = date('H:i', strtotime($row['time_start']));
			$time_end = date('H:i', strtotime($row['time_end']));
			
			$html .= "<li><span class=\"title\">{$row['name']}</span>";
			$html .= "<span class=\"date\">{$row['date']}</span>";
			$html .= "<span class=\"week_day\">{$row['week_day']}</span> das ";
			$html .= "<span class=\"time_start\">{$time_start}</span> às ";
			$html .= "<span class=\"time_end\">{$time_end}</span>";
			$html .= "<span class=\"notes\">{$row['notes']}</span>";
			$html .= "</li>";
			
		}
				
		$html .= '</ul>';

		return $html;
		
	}
	
	
	
	/**
	 * Adds a new link to the Quick/Favorites menu
	 *
	 * @param array $actions 
	 * @return array
	 * @author Rui Cruz
	 */
	public function favoriteMenu($actions) {
		
		$actions['admin.php?page=cf_simple_schedule_edit'] =  array('New Time slot', 'publish_posts');
		
		return $actions;
			
	}
	
	/**
	 * Generate the necessary tables on plugin activation
	 *
	 * @return void
	 * @author Rui Cruz
	 */
	public function install() {
		
		global $wpdb;
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

		if ($wpdb->get_var("SHOW TABLES LIKE '$this->table_name'") != $this->table_name) {
		
			/*
			 * Source: http://codex.wordpress.org/Creating_Tables_with_Plugins
			 * You have to put each field on its own line in your SQL statement.
		     * You have to have two spaces between the words PRIMARY KEY and the definition of your primary key.
		     * You must use the key word KEY rather than its synonym INDEX and you must include at least one KEY.
			*/			
			$sql = "CREATE TABLE " . $this->table_name . " (
				  id mediumint(9) NOT NULL AUTO_INCREMENT,
				  name tinytext NOT NULL,
				  notes text NULL,
				  date date NULL,
				  week_day varchar(70) NULL,
				  time_start time NOT NULL,
  				  time_end time NOT NULL,
				  UNIQUE KEY id (id)
				);";

			
			dbDelta($sql);			
			
		}
		
	}
	
}

?>