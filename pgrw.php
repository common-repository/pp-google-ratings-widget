<?php
    /*
        Plugin Name: Google Ratings Widget
        Plugin URI: https://platinumplugins.com/google-ratings-widget
        Description: The Google Ratings Widget, displays your Google Place Rating and the Reviews this will increase the professionality of your website and will make the user more comfortable buying the product.
        Author: Platinum Plugins
        Author URI: https://platinumplugins.com/
        Version: 1.3.0
    */

    // Functions // 
    function pgrw_install() {
        /* 
            Creates a table in the wordpress database, to store the ratings of the place.
        */

        // Requires //
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        // Variables
        global $wpdb;
        $charset = $wpdb->get_charset_collate();

        // Places Table //
        $sql = "CREATE TABLE IF NOT EXISTS `pgrw` 
        ( `id` INT(20) NOT NULL AUTO_INCREMENT,
        `name` VARCHAR(255) NOT NULL,
        `place_id` VARCHAR(255) NOT NULL,
        `rating` FLOAT NOT NULL,
        PRIMARY KEY (`id`)) $charset;";

        // Execute SQL //
        dbDelta($sql);
    }

    // Hooks w/ Functions //
    function pgrw_widget() {
        /*
            Registers the widget, by requiring from the class and then registering the class.
        */

        require_once('pgrw-widget.php');
        return register_widget("PGRW_Widget");
    }
    add_action('widgets_init', 'pgrw_widget');

    function pgrw_activate() {
        /*
            Once activated it will call the function which creates the table.
        */
        pgrw_install();
    }
    register_activation_hook(__FILE__, 'pgrw_activate');

    function pgrw_get_handler() {
        /*
            Used so we can make requests to the google api from javascript, or any other sources.
        */
        global $wpdb;
        if(!empty($_GET['pgrw_handler'])) {
            switch($_GET['pgrw_handler']) {
                case 'save':
                    if(current_user_can('edit_theme_options')) {
                        if(!empty($_GET['api_key']) && !empty($_GET['place_id'])) {
                            $GET = filter_var_array($_GET, FILTER_SANITIZE_STRING);
                            $data = file_get_contents("https://maps.googleapis.com/maps/api/place/details/json?placeid=" . $GET['place_id'] . "&key=" . $GET['api_key']);
                            $json = json_decode($data);
                            if($json->result) {
                                $results = $wpdb->get_results($wpdb->prepare("SELECT * FROM pgrw WHERE place_id=%s", $GET['place_id']), ARRAY_A);
                                if(empty($json->result->rating)) {
                                    $json->result->rating = "0.0";
                                }
                                if($results[0] != null) {
                                    
                                }else {
                                    $wpdb->insert("pgrw", array(
                                        'name' => $json->result->name,
                                        'place_id' => $json->result->place_id,
                                        'rating' => $json->result->rating
                                    ));
                                }
                                echo $data;
                            }else {
                                echo $data;
                            }
                            die;
                        }
                    }
                    break;
            }
        }
    }
    add_action('init', 'pgrw_get_handler');

    // Admin Page //
    add_action('admin_menu', function() {
        add_options_page('Google Ratings Widget', 'Google Ratings Widget', 'manage_options', 'pgrw', 'pgrw_page');
    });
    
    add_action('admin_init', function() {
        register_setting('pgrw-settings', 'pgrw_license');
        register_setting('pgrw-settings', 'pgrw_api_key');
    });

    function pgrw_page() {
        wp_register_style('pgrw_bootstrap_css', plugins_url('/web/css/bootstrap.min.css', __FILE__));
        wp_enqueue_style('pgrw_bootstrap_css', plugins_url('/web/css/bootstrap.min.css', __FILE__));

        ?>
            <div class="wrap">
                <?php 
                    $tab = isset($_GET['tab']) ? $_GET['tab'] : 'dashboard';
                ?>
                

                <ul class="nav nav-tabs justify-content-center">
                    <li class="nav-item">
                        <a class="nav-link <?php if($tab == 'dashboard') echo 'active'; ?>" href="?page=pgrw&tab=dashboard">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php if($tab == 'settings') echo 'active'; ?>" href="?page=pgrw&tab=settings">Settings</a>
                    </li>
					<!--
                    <li class="nav-item">
                        <a class="nav-link <?php if($tab == 'documentation') echo 'active'; ?>" href="?page=grw&tab=documentation">Documentation</a>
                    </li>
					-->
                </ul>

                <form action="options.php" method="post">
                    <?php
                        settings_fields('pgrw-settings');
                        do_settings_sections('pgrw-settings');
                    ?>

                    <div class="tab-content">
                        <div class="tab-pane fade show <?php if($tab == 'dashboard') echo 'active'; ?>" id="dashboard" role="tabpanel">
                            <div class="row">
                                <div class="col-sm-6">
                                    <h4 id="title">Google Ratings Widget</h4>
                                    <p class="description">The most advanced widget, on the market for the cheapest price, providing your customers with more confidence. This plugin requires a Google Places API Key, which are free at the Google Website, but they have a limitation of one request per day, unless you add a billing account, in my opinion there is no need.</p>

                                    <div class="card" style="padding: 0;">
                                        <div class="card-body">
                                            <h5 class="card-title">License Status: Free</h5>
                                            <p class="card-text">You are currently using the Free Version, you can upgrade to the pro version, which will provide you with many more features, from improving seo to design: <a href="https://www.platinumplugins.com/google-ratings-widget">Upgrade</a></p>
                                        </div>
                                    </div>

                                    <br>
                                    <p class="description">If you require any more support, feel free to contact us at <a href="mailto:support@platinumplugins.com">support@platinumplugins.com</a> we will be happy to answer any of your questions, make sure to check our FAQ page first as you may resolve your problem faster.</p>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade show <?php if($tab == 'settings') echo 'active'; ?>" id="settings" role="tabpanel">
                            <div class="row">
                                <div class="col-sm-6">
                                    <h4 id="title">Google Ratings Widget Settings</h4>
                                    <p class="description">The most advanced widget, on the market for the cheapest price, providing your customers with more confidence. This plugin requires a Google Places API Key, which are free at the Google Website, but they have a limitation of one request per day, unless you add a billing account, in my opinion there is no need.</p>
                                    <hr>

                                    <div class="form-group">
                                        <label for="pgrw_api_key">Google Places API Key:</label>
                                        <input class="form-control" type="text" id="pgrw_api_key" name="pgrw_api_key" value="<?php echo wp_strip_all_tags(get_option('pgrw_api_key')); ?>">
                                        <p style="margin-top: 12px;">You can simply visit <a href="https://developers.google.com/places/web-service/get-api-key">Google API Key</a>, at the webpage you can follow the instructions and make an api key.</p>
                                    </div>
                                    <hr>

                                    <?php
                                    submit_button();
                                    ?>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade show <?php if($tab == 'documentation') echo 'active'; ?>" id="documentation" role="tabpanel">
                            
                        </div>
                    </div>
                </form>
            </div>
        <?php
    }
?>