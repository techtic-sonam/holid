<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://holid.io
 * @since             1.0.0
 * @package           Holid
 *
 * @wordpress-plugin
 * Plugin Name:       Holid
 * Plugin URI:        https://holid.io/
 * Description:       Holid helps publishers to take on new heights by providing the latest Ad Tech, optimization of ad inventory and sales towards leading advertisers & agencies. We help 100's of websites and apps to face a continuously evolving market.
 * Version:           1.0.0
 * Author:            Holid
 * Author URI:        https://holid.io
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       holid
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) { 
    exit; // Exit if accessed directly
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'HOLID_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-holid-activator.php
 */
function activate_holid() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-holid-activator.php';
	Holid_Activator::activate();
    $site_url = get_site_url();
    $org = preg_replace( "#^[^:/.]*[:/]+#i", "", $site_url );
    $head_tag = esc_url("https://ads.holid.io/auto/".$org."/holid.js");
    //echo $head_tag;
    //define (ROOT_DIR, get_theme_root() );
    if (@file_exists($head_tag)) {
        //echo 'text';
        $wp_root_path = str_replace('/wp-content', '', get_theme_root());
        $base = dirname($wp_root_path);
        $fileName = "ads.txt";
        $fileNameWithPath = $base.'/'.$fileName;
        header("Content-Type: text/plain");               
        $wp_remot_get = wp_remote_get( 'https://holid.io./ads.txt' );
        $wp_remote_data = wp_remote_retrieve_body( $wp_remot_get );
       // echo $fileNameWithPath;
        if (@file_exists($fileNameWithPath)) {
            $myadfile = fopen($fileNameWithPath, "w+");
            fwrite($myadfile, $wp_remote_data);
            fclose($myadfile);
        } else {
            $contents = $wp_remote_data;
            if($contents != ''){
                file_put_contents($fileNameWithPath,  $contents);
            }
        }
    }
    // exit;
    $settings = array(
        'header_tag' => $head_tag,
    );
    update_option( 'plug_holid_settings', $settings );
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-holid-deactivator.php
 */
function deactivate_holid() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-holid-deactivator.php';
	Holid_Deactivator::deactivate();
}
register_activation_hook( __FILE__, 'activate_holid' );
register_deactivation_hook( __FILE__, 'deactivate_holid' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-holid.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function Holid() {
	$plugin = new Holid();
	$plugin->run();
}
Holid();

// Creating the widget 
class holid_widget extends WP_Widget {
    function __construct() {
        parent::__construct( 
            // Base ID of your widget
            'holid_widget', 
            // Widget name will appear in UI
            esc_html('Holid Body Tag Section', 'holid'), 
            // Widget description
            array( 'description' => esc_html( 'Add holidAds box Code', 'holid' ), ) 
        );
    }
      
    // Creating widget front-end
    public function widget( $args, $instance ) {
        $ads_section = apply_filters( 'widget_title', $instance['ads_section'] );
        // before and after widget arguments are defined by themes
        echo esc_html($args['before_widget']);
        // This is where you run the code and display the output
        echo esc_html( '<div class="holidAds '.$ads_section.'"></div>' );
        echo esc_html($args['after_widget']);
    }
              
    // Widget Backend 
    public function form( $instance ) {
        if ( isset( $instance[ 'ads_section' ] ) ) {
            $title = esc_attr($instance[ 'ads_section' ]);
        } else {
            $title = esc_html( 'Body Tag Section', 'holid' );
        }
        // Widget admin form
        ?>
        <p>
        <label for="<?php echo esc_attr($this->get_field_id( 'ads_section' )); ?>"><?php esc_html( 'Section:' ); ?></label> 
        <select   class="widefat" id="<?php echo esc_attr($this->get_field_id( 'ads_section' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'ads_section' )); ?>">
            <option value="widescreen" <?php if(esc_attr( $title ) == 'widescreen'){echo esc_html('selected="selected"');}?>>Widescreen</option>
            <option value="box" <?php if(esc_attr( $title ) == 'box'){echo esc_html('selected="selected"');}?>>Box</option>
            <option value="tower" <?php if(esc_attr( $title ) == 'tower'){echo esc_html('selected="selected"');}?>>Tower</option>
            <option value="mobile" <?php if(esc_attr( $title ) == 'mobile'){echo esc_html('selected="selected"');}?>>Mobile</option>
        </select>
        <!-- <input class="widefat" id="<?php //echo $this->get_field_id( 'title' ); ?>" name="<?php //echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php //echo esc_attr( $title ); ?>" /> -->
        </p>
    <?php }
          
    // Updating widget replacing old instances with new
    public function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance['ads_section'] = ( ! empty( $new_instance['ads_section'] ) ) ? strip_tags( $new_instance['ads_section'] ) : '';
        return $instance;
    }
    // Class holid_widget ends here
} 
 
// Register and load the widget
function wpb_load_holid_widget() {
    register_widget( 'holid_widget' );
}
add_action( 'widgets_init', 'wpb_load_holid_widget' );

add_shortcode("holid_ads_code","holid_ads_code");
function holid_ads_code($attrs){
    /*echo '<pre>';
    print_r($attrs);
    echo '</pre>';*/
    if($attrs['ads_section'] != ''){
      echo esc_html( '<div class="holidAds '.$attrs['ads_section'].'"></div>' );
    }   
}