<?php
/**
 * Plugin Name: Canvas Addons
 * Plugin URI: http://stuartduff.com/
 * Description: Adds some advanced styling features to WooThemes Canvas theme.
 * Author: Stuart Duff
 * Version: 1.0
 * Author URI: http://stuartduff.com/
 *
 * @package WordPress
 * @subpackage Canvas_Advanced_Addons
 * @author Stuart
 * @since 1.0.0
 */

require_once( 'classes/class-canvas-advanced-addons.php' );

global $canvas_advanced_addons;
$canvas_advanced_addons = new Canvas_Advanced_Addons( __FILE__ );

?>