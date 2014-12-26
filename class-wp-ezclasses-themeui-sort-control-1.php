<?php
/** 
 * For rendering (blog post) sort controls
 *
 * This is NOT paging in a here's a list of pages sense. This is simply Next and Previous.
 *
 * PHP version 5.3
 *
 * LICENSE: TODO
 *
 * @package WP ezClasses
 * @author Mark Simchock <mark.simchock@alchemyunited.com>
 * @since 0.5.0
 * @license TODO
 */
 
/**
 * == Change Log == 
 *
 * --- 
 */

if ( !defined('ABSPATH') ) {
	header('HTTP/1.0 403 Forbidden');
    die();
}

if (! class_exists('Class_WP_ezClasses_ThemeUI_Sort_Control_1') ) {
  class Class_WP_ezClasses_ThemeUI_Sort_Control_1 extends Class_WP_ezClasses_Master_Singleton {
  
  	protected $_arr_init;
	
	protected $_str_orderby_default;
	protected $_str_order_default;
	
		
		public function __construct() {
			parent::__construct();
		}
		
		public function ez__construct($arr_args = NULL){
		
		  $arr_init_defaults = $this->init_defaults();
		  $this->_arr_init = WPezHelpers::ez_array_merge(array($arr_init_defaults, $arr_args));
		  
		  $this->_str_orderby_default = $arr_args['orderby'];
		  $this->_str_order_default = strtoupper($arr_args['order']);
		}
		
		protected function init_defaults(){
		
		  $arr_defaults = array(
		    'echo' 			=> false,
		    'filters' 		=> false,
			'validation' 	=> false,
			'orderby' 		=> 'post_date',
			'order'			=> 'DESC'
			); 
		  return $arr_defaults;
		}

		
		/**
		 *
		 */
		public function set_orderby($str_arg = '') {
			if ( isset($str_arg) && ! empty($str_arg) && is_string($str_arg)){
				$this->_str_orderby_default = $str_arg;
			} 
		}
		
		/**
		 *
		 */
		public function set_order($str_arg = '') {
			if ( isset($str_arg) && ! empty($str_arg) && (strtolower($str_arg) == 'desc' || strtolower($str_arg) == 'asc') ){
				$this->_str_order_default = strtoupper($str_arg);
			} 
		}
		
		/**
		 * TODO - review this code. 
		 */
		public function sort_validate($arr_args = ''){
			

				if ( ! WPezHelpers::ez_array_pass($arr_args) ){
					return array('status' => false, 'msg' => 'ERROR: arr_args[] ! is_array() || empty()', 'source' => get_class(), 'arr_args' => 'error');
				}
				
				$arr_msg = array();
					// let make sure you have the parms we need
					// note: we're not checking for validity of the parms, just that they exist and they're loosely valid
				
				$arr_supported = $this->sort_validate_supported();
				foreach ($arr_supported['top_level'] as $support_top) {
					if ( !isset( $arr_args[$support_top] ) ){
						$arr_msg[] = 'arr_args[' . $support_top . '] !isset()';
					}
				}
				
				if ( isset($arr_args['status']) && ($arr_args['status'] === false || !is_bool($arr_args['status'])) ){
					$arr_msg[] = 'arr_args[status] === false || !is_bool()';
				}
				
				if ( isset($arr_args['label_order']) && (!is_array($arr_args['label_order']) || empty($arr_args['label_order'])) ) {
					$arr_msg[] = 'arr_args[label_order] !is_array() || empty()';
				} elseif ( !is_array($arr_args['labels']) || empty($arr_args['labels']) ) {
					$arr_msg[] = 'arr_args[labels] !is_array() || arr_args[labels]';
				} else {
				
					foreach ($arr_args['label_order'] as $str_label) {
						if ( !isset( $arr_args['labels']['label_' . $str_label]) || (isset($arr_args['labels']['label_' . $str_label]) && !is_string($arr_args['labels']['label_' . $str_label])) ){
							
							$arr_msg[] = 'arr_args[labels][label_'. $str_label . '] !isset() || !is_string()';

						}
						
						if ( !isset( $arr_args['labels']['label_' . $str_label . '_hover_title'] ) || (isset($arr_args['labels']['label_' . $str_label.'_hover_title']) && !is_string($arr_args['labels']['label_' . $str_label . '_hover_title'])) ){

							$arr_msg[] = 'arr_args[labels][label_'. $str_label . '_hover_title] !isset() || !is_string()';
						}
					}
				}
				if ( empty($arr_msg) ) {
					return array('status' => true, 'msg' => 'success', 'source' => get_class(), 'arr_args' => $arr_args);

				} else {
					return array('status' => false, 'msg' => $arr_msg, 'source' => get_class(), 'arr_args' => 'error');
				}

		}
		
		/*
		 * What is going to be validated?
		 */
		protected function sort_validate_supported(){

			$arr_tier_labels	= array(
										'label_author_name', 
										'label_author_name_hover_title', 
										'label_comment_count', 
										'label_comment_count_hover_title', 			
										'label_post_date', 
										'label_post_date_hover_title',
										'label_rand',
										'label_rand_hover_title',									
										'label_title', 
										'label_title_hover_title', 
									);
									
			$arr_level_top		= array (
										'blank_class',						
										'label_order', 
										'labels', 
										'li_class', 
										'sort_down_class', 
										'sort_random_class',
										'sort_up_class',
										'status',
										'ul_class', 
									);
			
			return array(
						'top_level'		=> $arr_level_top,
						'labels_level'	=> $arr_tier_labels,
					);
		}
		
		/**
		 * The foundation of this markup is rooted in Twitter Bootstraps' tabs, but pass your own selectors, style up some CSS and it's you, all you. 
		 */
		public function sort( $arr_args = '' ) {
		
		// are we going to echo or return the str_return
		  $bool_echo = $this->_arr_init['echo'];
		  if ( isset($arr_args['echo']) && is_bool($arr_args['echo']) ){
		    $bool_echo = $arr_args['echo'];
		  }

		  $bool_validate = $this->_arr_init['validation'];
		  if ( isset($arr_args['validation']) && is_bool($arr_args['validation']) ){
		    $bool_validate = $arr_args['validation'];
		  }
			
			if ( ! WPezHelpers::ez_array_pass($arr_args) ){
				$arr_args = $this->sort_defaults();
			} else {
			
				$arr_defaults = $this->sort_defaults();
				// pull the labels out and merge them first (since they are an array within an array
				$arr_args_labels = $arr_defaults['labels'];
					
				if ( WPezHelpers::ez_array_pass($arr_args['labels']) ){
					$arr_args_labels = array_merge($arr_defaults['labels'], $arr_args['labels']);
				}
				
				$arr_args = WPezHelpers::ez_array_merge(array($arr_defaults, $arr_args));
				// now put the labels in place since the all array_merge won't array_merge the labels arr
				$arr_args['labels'] = $arr_args_labels;
				
				/**
				 * There's probably a more elegant way to do the above but what it is, it is, at least for now
				 */
			}
			
			global $wp_query;
			
			// you can turn off validation once the site has proven to be stable. why waste the CPU cycles retesting the tested wheel?
			if ( $bool_validate === true ){
				$arr_return = $this->sort_validate($arr_args);
				// do we have an error?
				if ( $arr_return['status'] !== true ){
					return array('status' => false, 'msg' => $arr_ret['msg'], 'source' => get_class(), 'arr_args' => 'error');			
				}
				//else use the "clean" args
				$arr_args = $arr_return['arr_args'];
			}
		
			$str_orderby = '';
			$str_order = '';
			if ( isset($wp_query->query_vars['orderby']) && isset($wp_query->query_vars['order']) ) {
				$str_orderby = strtolower($wp_query->query_vars['orderby']);
				$str_order = strtolower($wp_query->query_vars['order']);					
			}
			
			$str_search_term = get_query_var('s');
			if ( ! empty($str_search_term)) {
				$str_search_term = str_replace(' ', '+', $str_search_term);
				$str_search_term = '&s=' . strtolower($str_search_term);
			}	

			$str_to_return = '<ul class="' . sanitize_text_field($arr_args['ul_class']) . '">';
			foreach ( $arr_args['label_order'] as $str_label) {
			  $str_label = strtolower(sanitize_text_field($str_label));
			
				$str_display_sort_icon = ' <span class="' . sanitize_text_field($arr_args['blank_class']) . '"></span>';
				$str_click_sort_order = '&order=asc';
				
				$str_active_class = '';
				if ( $str_label == 'rand' ){
				
					$str_click_sort_order = '';	
					
				} elseif ($str_orderby == '' && $str_order == '' && $str_label == 'post_date') {
					$str_active_class = ' active';
					$str_display_sort_icon = ' <span class="' . sanitize_text_field($arr_args['sort_down_class']) . '"></span>';
					$str_click_sort_order = '&order=asc';
				}
				if ($str_orderby == strtolower($str_label)) {
				
					$str_active_class = ' active';
					if ($str_orderby == 'rand') {
					
						$str_active_class = ' active';
						$str_display_sort_icon = ' <span class="' . sanitize_text_field($arr_args['sort_random_class']) . '"></span>'; //TODO - icon-sort not displaying
						$str_click_sort_order = '';
						
					} elseif ($str_order == 'asc') {
					
						$str_display_sort_icon = ' <span class="' . sanitize_text_field($arr_args['sort_up_class']) . '"></span>'; 
						$str_click_sort_order = '&order=desc';
					} elseif ($str_order == 'desc'){
						$str_display_sort_icon =  ' <span class="' . sanitize_text_field($arr_args['sort_down_class']) . '"></span>';
					}
				}
				$str_to_return .= '<li class="' . sanitize_text_field($arr_args['li_class']) . ' ' . sanitize_text_field($str_active_class) . '">'.'<a href="?orderby='. $str_label . $str_click_sort_order . $str_search_term . '" title="' . sanitize_text_field($arr_args['labels']['label_' . $str_label . '_hover_title']) . '">'. sanitize_text_field($arr_args['labels']['label_' . $str_label]) . $str_display_sort_icon .'</a>'. '</li>';
			}
			$str_to_return .= '</ul><!-- /.nav -->';	

			if ( $bool_echo ) {
				echo $str_to_return;
			} 
			return array('status' => true, 'msg' => 'success', 'source' => get_class(), 'str_to_return' => $str_to_return, 'arr_args' => $arr_args);			
		}

		
		/**
		 *
		 */
		public function sort_defaults(){
		
			$arr_defaults_labels = array(	
										'label_post_date'					=> 'Date',	// format: label- . value from label_order array
										'label_post_date_hover_title'		=> 'Sort on Date', // format: label- . value from label_order array . -hover-title
										'label_title'						=> 'Title',
										'label_title_hover_title'			=> 'Sort on Title',
										'label_author_name'					=> 'Author',
										'label_author_name_hover_title' 	=> 'Sort on Author',
										'label_comment_count'				=> 'Popular',
										'label_comment_count_hover_title'	=> 'Sort on Comment Count',
										'label_rand'						=> 'Random',
										'label_rand_hover_title'			=> 'Surprize me',
									);
			
			$arr_defaults = array(	
								'status'				=> true, 
								'label_order'			=> array('post_date', 'title', 'author_name','comment_count','rand'), // display order left to right. 
								'labels' 				=> '', // $arr_defaults_labels will go here
								'blank_class'			=> 'icon-sign-blank opacity-zero',  // TODO 
								'ul_class'				=> 'nav nav-tabs',  // FYI - you might also wish to try Bootstrap nav-pills
								'li_class'				=> 'menu-item',
								'sort_up_class'			=> 'icon-chevron-up', 		//
								'sort_down_class'		=> 'icon-chevron-down',	// Theses are here and not in the UX section because that's how wpezClasses does it. 
								'sort_random_class'		=> 'icon-repeat',		//
							);
			
			/*
			 * Allow filters?
			 */			
			if ( $this->_arr_init['filters'] ){
				$arr_defaults_via_filter = apply_filters('filter_ezc_themeui_sort_control_1_labels_defaults', $arr_defaults_labels);
				$arr_defaults_labels = WPezHelpers::ez_array_merge(array($arr_defaults_labels, $arr_defaults_via_filter));
			}
			
			/*
			 * Allow filters?
			 */			
			if ( $this->_arr_init['filters'] ){
				$arr_defaults_via_filter = apply_filters('filter_ezc_themeui_sort_control_1_defaults', $arr_defaults);
				$arr_defaults_labels = WPezHelpers::ez_array_merge(array($arr_defaults, $arr_defaults_via_filter));
			}
			
			$arr_defaults['labels'] = $arr_defaults_labels;
						 
			return $arr_defaults;
		}
		

		/**
		 * -- Have other ideas? See also: http://codex.wordpress.org/Function_Reference/is_post_type_archive
		 */		
		public function pre_get_posts_sorting( $query ) {

			if ( is_admin() || !$query->is_main_query() ){
				return;
			}
				
			$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
			$query->query_vars['paged'] = $paged;	

			if (isset($_GET['s'])) {
				$query->query_vars['s'] = strtolower($_GET['s']);
			}			
			
			if (isset($_GET['orderby'])) {

				switch( strtolower($_GET['orderby']) ) {
					case 'title':
					case 'author_name':
					case 'comment_count':
					case 'post_date':
					case 'rand':
						$query->query_vars['orderby'] = strtolower($_GET['orderby']);	 
					break;
					 
					default:
						$query->query_vars['orderby'] = $this->_str_orderby_default;				
					break;
				}
			} else {
				$query->query_vars['orderby'] = $this->_str_orderby_default;
			}
			
			if (isset($_GET['order'])) {
			
				switch(strtolower($_GET['order'])) {
				
					case 'asc':
					  $query->query_vars['order'] = 'ASC';	
					break;
					
					case 'desc':
						$query->query_vars['order'] = 'DESC';			 
					break;
					
					default:
						$query->query_vars['order'] = strtoupper($this->_str_order_default);	
					break;
				}
			}
			return $query;
		}
	
		/**
		 *
		 */
		public function add_action_pre_get_posts_sorting() {
			add_action( 'pre_get_posts', array( &$this, 'pre_get_posts_sorting' ) ); 
		}
		

	} // close class
} // close if class_exists()
?>