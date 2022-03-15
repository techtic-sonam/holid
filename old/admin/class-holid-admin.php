<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://holid.io
 * @since      1.0.0
 *
 * @package    Holid
 * @subpackage Holid/includes
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Holid
 * @subpackage Holid/admin
 * @author     Your Name <email@example.com>
 */
class Holid_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $Holid    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;
		add_action( 'admin_menu', array( $this, 'holid_admin_menu') );
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function holid_enqueue_styles() {
		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Plugin_Name_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Plugin_Name_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/holid-admin.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function holid_enqueue_scripts() { 
		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Plugin_Name_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Plugin_Name_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/holid-admin.js', array( 'jquery' ), $this->version, false );
	}

	/**
	 * Register admin settings menu & Hide page from admin
	 *
	 * @since 1.0
	 * @return void
	*/
	public function holid_admin_menu() {
		add_menu_page(
			'Holid',
			'Holid',
			'manage_options',
			$this->plugin_name,
			array( $this, 'holid_settings_page' ),
			plugin_dir_url( __FILE__ ).'images/holid-site-icon.png',
			90
		);
	}

	/**
	 * Renders Settings page
	 *
	 * @access public
	 * @return mixed
	 */
	public function holid_settings_page() {
		if (!current_user_can('manage_options')) {
            wp_die(esc_html('You do not have sufficient permissions to access this page.'));
        }
        // Save Settings
		if ( isset( $_POST['holid_settings_nonce'] ) && wp_verify_nonce( $_POST['holid_settings_nonce'], 'holid_settings' ) ) {
			$holid_settings = sanitize_post($_POST['holid_settings']);
            update_option( 'plug_holid_settings', $holid_settings );

           // $ads_code = $_POST['holid_settings']['header_ads_code'];
			define (ROOT_DIR, get_theme_root() );
			/*$wp_root_path = str_replace('/wp-content', '', get_theme_root());
			$base = dirname($wp_root_path);
			$fileName = "ads.txt";
            $fileNameWithPath = $base.'/'.$fileName;

            if (@file_exists($fileNameWithPath)) {			  	
			  	$myadfile = fopen($fileNameWithPath, "w+") or die('<div id="message" class="updated fade"><p><strong>Unable to open file!.</div>');
				fwrite($myadfile, $ads_code);
				fclose($myadfile);
			} else {
				$contents = $ads_code;
				if($contents != ''){
					if(file_put_contents($fileNameWithPath, $contents)){
					  echo '<div id="message" class="updated fade"><p><strong>File  was successfully created.</div>';
					}
					else{
					  echo '<div id="message" class="updated fade"><p><strong>File Failed to create file</div>';
					}
				}
			}*/
			echo wp_kses_post('<div id="message" class="updated fade"><p><strong>Settings saved.</strong></p></div>');
        }
        $settings = get_option( 'plug_holid_settings', array() );
        /*if ( empty( $settings ) ) {
            $settings = array(
                'header_tag' => '',
            );
		}*/
		?>
		<div class="wrap" id="holid-wrap">
		    <h2><?php echo esc_html( 'Holid', 'holid' ); ?></h2>
		    <form id="holid-settings" action="" method="post">
		        <input type="hidden" name="action" value="update">
		        <?php wp_nonce_field( 'holid_settings', 'holid_settings_nonce' ); 
		        if((isset($settings['header_tag']))){
		        	$head_tag = $settings['header_tag'];
		        }else{
		        	$site_url = get_site_url();
		        	$org = preg_replace( "#^[^:/.]*[:/]+#i", "", $site_url );

		        	$head_tag = esc_url("https://ads.holid.io/auto/".$org."/holid.js");
		        }
		        	$site_url = get_site_url();
		        	$org = preg_replace( "#^[^:/.]*[:/]+#i", "", $site_url );

		        	 $reg_url= esc_url("https://ads.holid.io/auto/".$org."/holid.js");
		        	/*if (@file_exists($reg_url)) {
			        	echo 'exist';
			        }else{
			        	echo 'not exist';
			        }*/
		        ?>
		        <table class="form-table">
		        	<?php if (@file_exists($reg_url)) { ?>
	        		<tr valign="top">
		                <th scope="row">Header Tag</th>
		                <td>
		                    <textarea name="holid_settings[header_tag]"
		                        class="large-text code"><?php echo esc_attr( $head_tag); ?></textarea>
		                    <p class="description">Add SRC for the script only</p>
		                </td>
		            </tr>
		        	<?php }else{ ?>
	        		<tr valign="top">			                
		                <th>
		                    <p style="border: 1px solid red;width: auto;display: inherit;padding: 10px 20px;font-size: 18px;color: red;background-color: #ffe0e0;">In order to show ads register an account at <a href="https://holid.io/">holid.io</a></p>
		                </th>
		            </tr>
		        	<?php } ?>
		            <!-- <tr valign="top">
		                <th scope="row">Ads Code</th>
		                <td>
		                    <textarea name="holid_settings[header_ads_code]"
		                        class="large-text code"><?php //echo esc_attr( $settings['header_ads_code'] ); ?></textarea>
		                    <p class="description">Insert Ads Code</p>
		                </td>
		            </tr> -->
		            <!-- <tr valign="top">
		                <th scope="row">Body Tag Selction</th>
		                <td>
		                    <select name="holid_settings[body_tag]">
		                        <option value="widescreen"
		                            <?php //echo esc_attr( $settings['body_tag'] ) == 'widescreen' ? 'selected="selected"' : ''; ?>>
		                            Widescreen</option>
		                        <option value="box"
		                            <?php //echo esc_attr( $settings['body_tag'] ) == 'box' ? 'selected="selected"' : ''; ?>>Box
		                        </option>
		                        <option value="tower"
		                            <?php //echo esc_attr( $settings['body_tag'] ) == 'tower' ? 'selected="selected"' : ''; ?>>
		                            Tower</option>
		                        <option value="mobile"
		                            <?php //echo esc_attr( $settings['body_tag'] ) == 'mobile' ? 'selected="selected"' : ''; ?>>
		                            Mobile</option>
		                    </select>-->
		                    <?php
								/*echo esc_attr( $settings['body_tag'] ) == 'widescreen' ? '<xmp><div class="holidAds widescreen"></div> (responsive)</xmp>' : '';
								echo esc_attr( $settings['body_tag'] ) == 'box' ? '<xmp><div class="holidAds box"></div> (responsive)</xmp>' : '';
								echo esc_attr( $settings['body_tag'] ) == 'tower' ? '<xmp><div class="holidAds tower"></div></xmp>' : '';
								echo esc_attr( $settings['body_tag'] ) == 'mobile' ? '<xmp><div class="holidAds mobile"></div></xmp>' : '';*/
								?>
		              <!--  </td>
		            </tr> -->
		        </table>
		        <?php if (@file_exists($reg_url)) { ?>
		        <p class="submit">
		            <input name="Submit" type="submit" class="button-primary" value="Save Changes" />
		        </p>
		        <?php } ?>
		    </form>
		</div>
<?php } 
}