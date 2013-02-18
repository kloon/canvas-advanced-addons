<?php 

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly.

/**
 * Canvas Advanced Addons Class
 *
 * All functionality pertaining to the dashboard widget feature.
 *
 * @package WordPress
 * @subpackage Canvas_Advanced_Addons
 * @category Plugin
 * @author Stuart Duff
 * @since 1.0.0
 */
class Canvas_Advanced_Addons {
	private $dir;
	private $assets_dir;
	private $assets_url;
	private $token;
	public $version;
	private $file;

	/**
	 * Constructor function.
	 * 
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public function __construct( $file ) {
		$this->dir = dirname( $file );
		$this->file = $file;
		$this->assets_dir = trailingslashit( $this->dir ) . 'assets';
		$this->assets_url = esc_url( trailingslashit( plugins_url( '/assets/', $file ) ) );
		$this->token = 'canvas_advanced_addons';

		$this->load_plugin_textdomain();
		add_action( 'init', array( &$this, 'load_localisation' ), 0 );

		$woo_options = get_option( 'woo_options' );

		// Run this on activation.
		register_activation_hook( $this->file, array( &$this, 'activation' ) );

		//add_action( 'admin_print_styles', array( &$this, 'enqueue_admin_styles' ), 5 );

		add_action( 'init', array( &$this, 'woo_canvas_options_add' ) );

		// Enable full width header
		if ( isset( $woo_options['woo_head_full_width'] ) && ( 'true' == $woo_options['woo_head_full_width'] ) ) {
		add_action( 'init', array( &$this, 'full_width_header_logic' ) );
		}

		// Add Social Icons To Header
		if ( isset( $woo_options['woo_head_social_icons'] ) && ( 'true' == $woo_options['woo_head_social_icons'] ) ) {
		add_action( 'woo_header_inside', array( &$this, 'header_social_icons_logic' ) );
		}

		// Enable full width footer
		if ( isset( $woo_options['woo_foot_full_width'] ) && ( 'true' == $woo_options['woo_foot_full_width'] ) ) {
		add_action( 'init', array( &$this, 'full_width_footer_logic' ) );
		}

		// Enable Business Slider On Homepage
		if ( isset( $woo_options['woo_biz_slider_homepage'] ) && ( 'true' == $woo_options['woo_biz_slider_homepage'] ) ) {
		add_action( 'get_header', array( &$this, 'business_slider_logic' ) );
		}

		// Enable Magazine Slider On Homepage
		if ( isset( $woo_options['woo_magazine_slider_homepage'] ) && ( 'true' == $woo_options['woo_magazine_slider_homepage'] ) ) {
		add_action( 'get_header', array( &$this, 'magazine_slider_logic' ) );
		}

		// Enable Magazine Page Content
		if ( isset( $woo_options['woo_magazine_page_content'] ) && ( 'true' == $woo_options['woo_magazine_page_content'] ) ) {
		add_action( 'init', array( &$this, 'magazine_page_content_logic' ) );
		}

		// WooCommerce Mini Cart Location
		if ( isset( $woo_options['woo_mini_cart_location'] ) && ( 'top-nav' == $woo_options['woo_mini_cart_location'] ) ) {
			add_action( 'init', array( &$this, 'remove_mini_cart_main_nav' ) );
			add_action( 'wp_nav_menu_items', array( &$this, 'move_mini_cart_to_top_nav' ), 10, 2 );
		}

		// Loads Custom Styling
		add_action( 'woo_head', array( &$this, 'canvas_custom_styling' ) );

	} // End __construct()

	/**
	 * Load the plugin's localisation file.
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public function load_localisation () {
		load_plugin_textdomain( 'canvas-advanced-addons', false, dirname( plugin_basename( $this->file ) ) . '/lang/' );
	} // End load_localisation()

	/**
	 * Load the plugin textdomain from the main WordPress "languages" folder.
	 * @since  1.0.0
	 * @return  void
	 */
	public function load_plugin_textdomain () {
	    $domain = 'canvas-advanced-addons';
	    // The "plugin_locale" filter is also used in load_plugin_textdomain()
	    $locale = apply_filters( 'plugin_locale', get_locale(), $domain );
	 
	    load_textdomain( $domain, WP_LANG_DIR . '/' . $domain . '/' . $domain . '-' . $locale . '.mo' );
	    load_plugin_textdomain( $domain, FALSE, dirname( plugin_basename( $this->file ) ) . '/lang/' );
	} // End load_plugin_textdomain()

	/**
	 * Enqueue post type admin CSS.
	 * 
	 * @access public
	 * @since   1.0.0
	 * @return   void
	 */
	public function enqueue_admin_styles () {
		wp_register_style( 'canvas-advanced-addons-admin', $this->assets_url . 'css/admin.css', array(), '1.0.0' );
		wp_enqueue_style( 'canvas-advanced-addons-admin' );
	} // End enqueue_admin_styles()


	/**
	 * Run on activation.
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public function activation () {
		$this->register_plugin_version();
	} // End activation()

	/**
	 * Register the plugin's version.
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	private function register_plugin_version () {
		if ( $this->version != '' ) {
			update_option( 'canvas-advanced-addons' . '-version', $this->version );
		}
	} // End register_plugin_version()	

	/**
	 * Full WIdth Header Logic.
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */	

	public function full_width_header_logic() {

		// Add header container
		add_action( 'woo_header_before', 'header_container_start' );
		function header_container_start() { ?>
		    <!--#header-container-->
		    <div id="header-container">
		<?php
		}
		add_action( 'woo_header_after', 'header_container_end', 8 );
		function header_container_end() { ?>
		    </div><!--/#header-container-->
		<?php
		}
		 
		// Add navigation container
		add_action( 'woo_nav_before', 'nav_container_start' );
		function nav_container_start() { ?>
		    <!--#nav-container-->
		    <div id="nav-container">
		<?php
		}
		add_action( 'woo_nav_after', 'nav_container_end' );
		function nav_container_end() { ?>
		    </div><!--/#nav-container-->
		<?php
		}

	} // End full_width_header_logic()

	/**
	 * Display Social Icons In The Header.
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */	
	public function header_social_icons_logic() {

		 global $woo_options;
		 
		 $html = '';
		 
		 // Open DIV tag.
		 $html .= '<div id="social-links" class="social-links fr">' . "\n";
		 
		 $template_directory = get_template_directory_uri();
		 
		 $profiles = array(
		 'twitter' => __( 'Follow us on Twitter' , 'canvas-advanced-addons' ),
		 'facebook' => __( 'Connect on Facebook' , 'canvas-advanced-addons' ),
		 'youtube' => __( 'Watch on YouTube' , 'canvas-advanced-addons' ),
		 'flickr' => __( 'See photos on Flickr' , 'canvas-advanced-addons' ),
		 'linkedin' => __( 'Connect on LinkedIn' , 'canvas-advanced-addons' ),
		 'delicious' => __( 'Discover on Delicious' , 'canvas-advanced-addons' ),
		 'googleplus' => __( 'View Google+ profile' , 'canvas-advanced-addons' )
		 );
		 
		 foreach ( $profiles as $key => $text ) {
		 	if ( isset( $woo_options['woo_connect_' . $key] ) && $woo_options['woo_connect_' . $key] != '' ) {
		 		$html .= '<a class="social-icon-' . $key . '" href="' . $woo_options['woo_connect_' . $key] . '" title="' . esc_attr( $text ) . '"></a>' . "\n";
		 	}
		 }
		 
		 // Add a custom RSS icon, linking to Feedburner or default RSS feed.
		 $rss_url = get_bloginfo_rss( 'rss2_url' );
		 $text = __( 'Subscribe to our RSS feed', 'canvas-advanced-addons' );
		 if ( isset( $woo_options['woo_feed_url'] ) && ( $woo_options['woo_feed_url'] != '' ) ) { $rss_url = $woo_options['woo_feed_url']; }
		 
		 $html .= '<a href="' . $rss_url . '" title="' . esc_attr( $text ) . '"></a>' . "\n";
		 
		 $html .= '</div><!--/#social-links .social-links fr-->' . "\n";
		 
		 echo $html;	

	} // End header_social_icons_logic()	


	/**
	 * Display the "Business" slider above the default WordPress homepage.
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */	
	public function business_slider_logic() {

		if ( is_front_page() && ! is_paged() ) {
		    add_action( 'woo_main_before_home', 'woo_slider_biz', 10 );
		    add_action( 'woo_main_before_home', 'woo_custom_reset_biz_query', 11 );
		    add_action( 'woo_load_slider_js', '__return_true', 10 );
		    add_filter( 'body_class', 'woo_custom_add_business_bodyclass', 10 );
	    }  // End woo_custom_load_biz_slider()
		 
		function woo_custom_add_business_bodyclass ( $classes ) {
		    if ( is_home() ) {
		        $classes[] = 'business';
		    }
		    return $classes;
		} // End woo_custom_add_biz_bodyclass()
		 
		function woo_custom_reset_biz_query () {
		    wp_reset_query();
		} // End woo_custom_reset_biz_query()		

	} // End full_width_footer_logic()	

	/**
	 * Display the "Magazine" slider above the default WordPress homepage.
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */	
	public function magazine_slider_logic() {

		if ( is_front_page() && ! is_paged() ) {
		    add_action( 'woo_loop_before_home', 'woo_slider_magazine', 10 );
			add_action( 'woo_loop_before_home', 'woo_custom_reset_query', 11 );
			add_action( 'woo_load_slider_js', '__return_true', 10 );
			add_filter( 'body_class', 'woo_custom_add_magazine_bodyclass', 10 );
	    }  // End woo_custom_load_magazine_slider()
		 
		function woo_custom_add_magazine_bodyclass ( $classes ) {
		    if ( is_home() ) {
		        $classes[] = 'magazine';
		    }
		    return $classes;
		} // End woo_custom_add_magazine_bodyclass()
		 
		function woo_custom_reset_query () {
		    wp_reset_query();
		} // End woo_custom_reset_query()		

	} // End full_width_footer_logic()


	/**
	 * Display the Page Content below the magazine slider .
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */	
	public function magazine_page_content_logic() {

		add_action( 'get_template_part_loop', 'woo_custom_display_page_content', 10, 2 );

	    function woo_custom_display_page_content ( $slug, $name ) {
	        if ( $name != 'magazine' ) { return; }
	            wp_reset_query();
	            global $post;
	            setup_postdata( $post );
		?>
	    <div <?php post_class( 'post' ); ?>>
	    <?php the_content(); ?>
	    </div><!--/.post-->
		<?php
	    } // End woo_custom_display_page_content()

	} // End magazine_page_content_logic()

	/**
	 * Full Width footer Logic.
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */	

	public function full_width_footer_logic() {

		// Add footer widget container
		add_action( 'woo_footer_top', 'footer_widgets_container_start', 8 );
		function footer_widgets_container_start() { ?>
		    <!--#footer-widgets-container-->
		    <div id="footer-widgets-container">
		<?php
		}
		add_action( 'woo_footer_before', 'footer_widgets_container_end' );
		function footer_widgets_container_end() { ?>
		    </div><!--/#footer_widgets_container_end-->
		<?php
		}
		 
		// Add footer container
		add_action( 'woo_footer_before', 'footer_container_start' );
		function footer_container_start() { ?>
		    <!--#footer_container_start-->
		    <div id="footer-container">
		<?php
		}
		add_action( 'woo_footer_after', 'footer_container_end' );
		function footer_container_end() { ?>
		    </div><!--/#footer_container_end-->
		<?php
		}	

	} // End full_width_footer_logic()

	/**
	 * Remove the mini cart from the main navigation
	 * @access public
	 * @since 1.0.1
	 * @return void
	 **/
	public function remove_mini_cart_main_nav() {
		remove_action( 'woo_nav_inside', 'woo_add_nav_cart_link' );
	} // End remove_mini_cart_main_nav

	/**
	 * Move the mini cart to the top navigation
	 * @access public
	 * @since 1.0.1
	 * @param string $items
	 * @param array $args
	 * @return string
	 **/
	public function move_mini_cart_to_top_nav( $items, $args ) {
		global $woocommerce;
		if ( $args->menu_id == 'top-nav' ) {
			$items .= '</ul><ul class="nav cart fr"><li class="menu-item mini-cart-top-nav"><a class="cart-contents" href="'.esc_url( $woocommerce->cart->get_cart_url() ).'" title="'.esc_attr( 'View your shopping cart', 'woothemes' ).'">'.sprintf( _n('%d item', '%d items', $woocommerce->cart->cart_contents_count, 'woothemes' ), $woocommerce->cart->cart_contents_count ).' - '.$woocommerce->cart->get_cart_total().'</a></li>'; 
		}
		return $items;
	} // End move_mini_cart_to_top_nav

	/**
	 * Canvas Custom Styling.
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */	
	public function canvas_custom_styling() {

		global $woo_options;

		$output = '';

		// Add css if full width footer or header is activated
		if ( isset( $woo_options['woo_head_full_width'] ) && ( 'true' == $woo_options['woo_head_full_width'] )  ||  isset( $woo_options['woo_foot_full_width'] ) && ( 'true' == $woo_options['woo_foot_full_width'] ) ) {

			$output .= 'body {padding: 0;}'. "\n";
			$output .= '#wrapper {padding:0;min-width: 100% !important;width:100%}' . "\n";
			$output .= '#content {padding:0}' . "\n";

		}	

		// Add css for the full width header
		if ( isset( $woo_options['woo_head_full_width'] ) && ( 'true' == $woo_options['woo_head_full_width'] ) ) {

			$output .= '#top {padding:0 23px 0 0;width: 100%;}' . "\n";
	
			$full_head_bg = $woo_options['woo_full_head_bg'];
			$full_primary_menu_bg = $woo_options['woo_full_primary_menu_bg'];

			if ( $full_head_bg )
				$output .= '#header-container {padding:0;background-color:' . $full_head_bg . '}'. "\n";		
			if ( $full_primary_menu_bg )
				$output .= '#nav-container {padding:0;background-color:' . $full_primary_menu_bg . '}'. "\n";	

		}

		// Add css for the header social icons
		if ( isset( $woo_options['woo_head_social_icons'] ) && ( 'true' == $woo_options['woo_head_social_icons'] ) ) {

			$output .= '#header #social-links a { display: inline-block; height: 28px; width: 30px; background-image: url('.$this->assets_url.'/images/ico-subscribe-social.png); background-repeat: no-repeat; }' . "\n";
			$output .= '#header #social-links a.subscribe { background-position: 0px 0; }' . "\n";
			$output .= '#header #social-links a.social-icon-twitter { background-position: -30px 0; }' . "\n";
			$output .= '#header #social-links a.social-icon-facebook { background-position: -60px 0; }' . "\n";
			$output .= '#header #social-links a.social-icon-youtube { background-position: -90px 0; }' . "\n";
			$output .= '#header #social-links a.social-icon-flickr { background-position: -120px 0; }' . "\n";
			$output .= '#header #social-links a.social-icon-linkedin { background-position: -150px 0; }' . "\n";
			$output .= '#header #social-links a.social-icon-delicious { background-position: -180px 0; }' . "\n";
			$output .= '#header #social-links a.social-icon-googleplus { background-position: -210px 0; }' . "\n";
			$output .= '#header #social-links .heading-text { vertical-align: top; }' . "\n";	

		}	

		// Add css for aligning the top navigation menu
		if ( isset( $woo_options['woo_top_nav_align'] ) && ( 'false' != $woo_options['woo_top_nav_align'] ) ) {

			$align_primary_nav = $woo_options['woo_top_nav_align'];

			if ( $align_primary_nav == 'centre' ) :
				$output .= '#top {text-align:center;}'. "\n";
		        $output .= '#top .col-full {float:none;display:inline-block;vertical-align:top;}'. "\n";
		        $output .= '#top .col-full li {display:inline;}'. "\n";
			elseif ( $align_primary_nav == 'right' ) : 
		        $output .= 'ul#top-nav {float:right;}'. "\n";
		    endif;    		        	        

		}				

		// Add css for aligning the primary navigation menu
		if ( isset( $woo_options['woo_primary_nav_align'] ) && ( 'false' != $woo_options['woo_primary_nav_align'] ) ) {

			$align_primary_nav = $woo_options['woo_primary_nav_align'];

			if ( $align_primary_nav == 'centre' ) :
				$output .= '#navigation {text-align:center;}'. "\n";
		        $output .= 'ul#main-nav {float:none;display:inline-block;vertical-align:top;}'. "\n";
		        $output .= 'ul#main-nav li {display:inline;}'. "\n";
			elseif ( $align_primary_nav == 'right' ) : 
		        $output .= 'ul#main-nav {float:right;}'. "\n";
		    endif;    		        	        

		}		

		// Add css for the full width footer
		if ( isset( $woo_options['woo_foot_full_width'] ) && ( 'true' == $woo_options['woo_foot_full_width'] ) ) {

			$full_foot_widget_bg = $woo_options['woo_full_foot_widget_bg'];
			$full_foot_bg = $woo_options['woo_full_foot_bg'];
			$footer_bg = $woo_options['woo_footer_bg'];	

			$output .= '#footer {height:auto;}'. "\n";

			if ( $full_foot_widget_bg )
				$output .= '#footer-widgets-container {padding:0;background-color:' . $full_foot_widget_bg . '}'. "\n";		
			if ( $full_foot_bg )
				$output .= '#footer-container {padding:0;background-color:' . $full_foot_bg . '}'. "\n";
			if ( $footer_bg ) :
				$output .= '#footer {border-top: 1px solid ' .$footer_bg . '}'. "\n";
			else :
				$output .= '#footer {border-top: 1px solid ' . $full_foot_bg . '}'. "\n";
			endif;
		}
		
		// Add css for top nav WooCommerce mini cart
		if ( isset( $woo_options['woo_mini_cart_location'] ) && ( 'top-nav' == $woo_options['woo_mini_cart_location'] ) ) {
			$output .= '#top .cart-contents::before {font-family: \'FontAwesome\';display: inline-block;font-size: 100%;margin-right: .618em;font-weight: normal;line-height: 1em;width: 1em;content: "\f07a";}' ."\n";
			$output .= '#top .cart{ margin-right:0px !important;}';
		}

		// Output the CSS to the woo_head function
		if ( '' != $output ) {
			echo "\n" . '<!-- Advanced Canvas CSS Styling -->' . "\n";
			echo '<style type="text/css">' . "\n";
			echo $output;
			echo '</style>' . "\n";
		}

	} // End canvas_custom_styling()


	/**
	 * Integrate Setting into WooFramework
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */	

	public function woo_canvas_options_add() {

		function woo_options_add($options) {

		 	$shortname = 'woo';

		    // Full Width Header Options
		    $options[] = array( 'name' => __( 'Advanced Settings', 'canvas-advanced-addons' ),
								'icon' => 'misc',
							    'type' => 'heading');    

			// Canvas Header Options
			$options[] = array( 'name' => __( 'Header Settings', 'canvas-advanced-addons' ),
								'type' => 'subheading');

			$options[] = array( "name" => __( 'Enable Full Width Header', 'canvas-advanced-addons' ),
								"desc" => __( 'Enabling this selection will set your site to display its header at full width, Dont enable this setting with the themes Boxed Layout', 'canvas-advanced-addons' ),
								"id" => $shortname."_head_full_width",
								"std" => "false",
								"class" => 'collapsed',
								"type" => "checkbox" );

			$options[] = array( "name" => __( 'Full Width Header Background Color', 'canvas-advanced-addons' ),
								"desc" => __( 'Select the background color you want for your full width header.', 'canvas-advanced-addons' ),
								"id" => $shortname."_full_head_bg",
								"std" => "#316594",
								"class" => 'hidden',
								"type" => "color");  

			$options[] = array( "name" => __( 'Full Width Primary Menu Background Color', 'canvas-advanced-addons' ),
								"desc" => __( 'Select the background color you want for your full width header primary menu area.', 'canvas-advanced-addons' ),
								"id" => $shortname."_full_primary_menu_bg",
								"std" => "#CDDAE3",
								"class" => 'hidden last',
								"type" => "color"); 			

			$options[] = array( "name" => __( 'Add Social Icons To Header', 'canvas-advanced-addons' ),
								"desc" => __( 'Enabling this setting will add the subscribe and connect social icons to your header.', 'canvas-advanced-addons' ),
								"id" => $shortname."_head_social_icons",
								"std" => "false",
								"type" => "checkbox" );

			$options[] = array( "name" => __( 'Adjust Top Navigation Menu Position', 'canvas-advanced-addons' ),
								"desc" => __( 'Use these settings to adjust the alignment of the items within your Top Navigation Menu area.', 'canvas-advanced-addons' ),
								"id" => $shortname."_top_nav_align",							
								"type" => "select2",
								"options" => array( "false" => __( 'Align Left', 'canvas-advanced-addons' ), "centre" => __( 'Align Centre', 'canvas-advanced-addons' ), "right" => __( 'Align Right', 'canvas-advanced-addons' ) ) );									

			$options[] = array( "name" => __( 'Adjust Primary Navigation Menu Position', 'canvas-advanced-addons' ),
								"desc" => __( 'Use these settings to adjust the alignment of the items within your Primary Navigation Menu area.', 'canvas-advanced-addons' ),
								"id" => $shortname."_primary_nav_align",							
								"type" => "select2",
								"options" => array( "false" => __( 'Align Left', 'canvas-advanced-addons' ), "centre" => __( 'Align Centre', 'canvas-advanced-addons' ), "right" => __( 'Align Right', 'canvas-advanced-addons' ) ) );										


			// Canvas Homepage Options
			$options[] = array( 'name' => __( 'Homepage Settings', 'canvas-advanced-addons' ),
								'type' => 'subheading');

			$options[] = array( "name" => __( 'Add Business Slider To The Homepage', 'canvas-advanced-addons' ),
								"desc" => __( 'This setting will add the business slider to the homepage of your canvas theme.', 'canvas-advanced-addons' ),
								"id" => $shortname."_biz_slider_homepage",
								"std" => "false",
								"type" => "checkbox" );

			$options[] = array( "name" => __( 'Add Magazine Slider To The Homepage', 'canvas-advanced-addons' ),
								"desc" => __( 'This setting will add the magazine slider to the homepage of your canvas theme.', 'canvas-advanced-addons' ),
								"id" => $shortname."_magazine_slider_homepage",
								"std" => "false",
								"type" => "checkbox" );
			

			// Canvas Magazine Template Options
			$options[] = array( 'name' => __( 'Magazine Template Settings', 'canvas-advanced-addons' ),
								'type' => 'subheading');	


			$options[] = array( "name" => __( 'Display Page Content Below The Magazine Slider', 'canvas-advanced-addons' ),
								"desc" => __( 'This setting will display the page content below the magazine slider on the magazine page template.', 'canvas-advanced-addons' ),
								"id" => $shortname."_magazine_page_content",
								"std" => "false",
								"type" => "checkbox" );		


			// Canvas Footer Options
			$options[] = array( 'name' => __( 'Footer Settings', 'canvas-advanced-addons' ),
								'type' => 'subheading');

			// Full Width Footer Options
			$options[] = array( "name" => __( 'Enable Full Width Footer', 'canvas-advanced-addons' ),
								"desc" => __( 'Enabling this selection will set your site to display its footer at full width, Dont enable this setting with the themes Boxed Layout', 'canvas-advanced-addons' ),
								"id" => $shortname."_foot_full_width",
								"std" => "false",
								"class" => 'collapsed',
								"type" => "checkbox" );

			$options[] = array( "name" => __( 'Full Width Footer Widget Area Background Color', 'canvas-advanced-addons' ),
								"desc" => __( 'Select the background color you want for your full width widget area.', 'canvas-advanced-addons' ),
								"id" => $shortname."_full_foot_widget_bg",
								"std" => "#CDDAE3",
								"class" => 'hidden',
								"type" => "color"); 		

			$options[] = array( "name" => __( 'Full Width Footer Background Color', 'canvas-advanced-addons' ),
								"desc" => __( 'Select the background color you want for your full width footer.', 'canvas-advanced-addons' ),
								"id" => $shortname."_full_foot_bg",
								"std" => "#316594",
								"class" => 'hidden last',
								"type" => "color");

			// Canvas WooCommerce Options
			$options[] = array( 'name' => __( 'WooCommerce Settings', 'canvas-advanced-addons' ),
								'type' => 'subheading' );

			$options[] = array( 'name' => __( 'Mini Cart Location', 'canvas-advanced-addons' ),
								'desc' => __( 'Location where the mini cart is displayed, by default this is in the main navigation.', 'canvas-advanced-addons' ),
								'id' => $shortname . '_mini_cart_location',
								'type' => 'select2',
								'options' => array( 'main-nav' => __( 'Main Navigation', 'canvas-advanced-addons' ), 'top-nav' => __( 'Top Navigation', 'canvas-advanced-addons' ) ),
								'std' => 'main-nav' );
																									
			return $options;
		 
		}	

	}



} // End Class	




