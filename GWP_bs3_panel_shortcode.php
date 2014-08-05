<?php
/*
Plugin Name: BS3 panel shortcode
Plugin URI:
Description: A plugin to add Bootstrap 3 panel shortcode as a part to the GWP tutorial
Version: 1.0
Author: Ohad Raz
Author URI: http://generatewp.com
*/
class GWP_bs3_panel_shortcode{
	/**
	 * $shortcode_tag 
	 * holds the name of the shortcode tag
	 * @var string
	 */
	public $shortcode_tag = 'bs3_panel';

	/**
	 * __construct 
	 * class constructor will set the needed filter and action hooks
	 * 
	 * @param array $args 
	 */
	function __construct($args = array()){
		//add shortcode
		add_shortcode( $this->shortcode_tag, array( $this, 'shortcode_handler' ) );
		
		if ( is_admin() ){
			add_action('admin_head', array( $this, 'admin_head') );
			add_action( 'admin_enqueue_scripts', array($this , 'admin_enqueue_scripts' ) );
		}
	}

	/**
	 * shortcode_handler
	 * @param  array  $atts shortcode attributes
	 * @param  string $content shortcode content
	 * @return string
	 */
	function shortcode_handler($atts , $content = null){
		// Attributes
		extract( shortcode_atts(
			array(
				'header' => 'no',
				'footer' => 'no',
				'type' => 'default',
			), $atts )
		);
		
		//make sure the panel type is a valid styled type if not revert to default
		$panel_types = array('primary','success','info','warning','danger','default');
		$type = in_array($type, $panel_types)? $type: 'default';

		//start panel markup
		$output = '<div class="panel panel-'.$type.'">';

		//check if pannel has a header
		if ('no' != $header)
			$output .= '<div class="panel-heading">'.$header.'</div>';

		//add panel body content and allow shortcode in it
		$output .= '<div class="panel-body">'.trim(do_shortcode( $content )).'</div>';

		//check if pannel has a footer
		if ('no' != $footer)
			$output .= '<div class="panel-footer">'.$footer.'</div>';

		//add closing div tag
		$output .= '</div>';

		//return shortcode output
		return $output;
	}

	/**
	 * admin_head
	 * calls your functions into the correct filters
	 * @return void
	 */
	function admin_head() {
		// check user permissions
		if ( !current_user_can( 'edit_posts' ) && !current_user_can( 'edit_pages' ) ) {
			return;
		}
		
		// check if WYSIWYG is enabled
		if ( 'true' == get_user_option( 'rich_editing' ) ) {
			add_filter( 'mce_external_plugins', array( $this ,'mce_external_plugins' ) );
			add_filter( 'mce_buttons', array($this, 'mce_buttons' ) );
		}
	}

	/**
	 * mce_external_plugins 
	 * Adds our tinymce plugin
	 * @param  array $plugin_array 
	 * @return array
	 */
	function mce_external_plugins( $plugin_array ) {
		$plugin_array[$this->shortcode_tag] = plugins_url( 'js/mce-button.js' , __FILE__ );
		return $plugin_array;
	}

	/**
	 * mce_buttons 
	 * Adds our tinymce button
	 * @param  array $buttons 
	 * @return array
	 */
	function mce_buttons( $buttons ) {
		array_push( $buttons, $this->shortcode_tag );
		return $buttons;
	}

	/**
	 * admin_enqueue_scripts 
	 * Used to enqueue custom styles
	 * @return void
	 */
	function admin_enqueue_scripts(){
		 wp_enqueue_style('bs3_panel_shortcode', plugins_url( 'css/mce-button.css' , __FILE__ ) );
	}
}//end class

new GWP_bs3_panel_shortcode();