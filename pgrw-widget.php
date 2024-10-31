<?php
    /*
        Google Ratings Widget
            by Luaa (Aigars Aldermanis)
    */

    if(!defined('ABSPATH')) exit;

    class PGRW_Widget extends WP_Widget {
        public function __construct() {
            parent::__construct(
                'pgrw_widget',
                esc_html__('Google Rating Widget', 'text_domain'),
                array(
                    'description' => esc_html__('Display a rating of your Google Place.', 'text_domain'),
                )
            );

            wp_register_style('pgrw_style', plugins_url('/web/css/widgets.css', __FILE__));
            wp_enqueue_style('pgrw_style', plugins_url('/web/css/widgets.css', __FILE__));
        }

        public function form($instance) {
            $defaults = array(
                'title' => '',
                'place_id' => '',
                'design' => ''
            );

            extract(wp_parse_args((array)$instance, $defaults));

            // Main Settings //
            ?>
            <br>
            <button class="collapsible" type="button">Main Settings</button>
            <div class="content">
                <p>
                    <label for="<?php echo esc_attr($this->get_field_id('title')); ?>">
                        <?php esc_attr_e('Title:', 'text_domain'); ?>
                    </label>
                    <input
                        class="widefat" 
                        id="<?php echo esc_attr($this->get_field_id('title')); ?>" 
                        name="<?php echo esc_attr($this->get_field_name('title')); ?>" 
                        type="text" 
                        value="<?php echo esc_attr($title); ?>" 
                    />

                    <?php
                    if(!empty($instance['place_id'])) {
                        ?>
                        <p>
                            <label for="<?php echo esc_attr( $this->get_field_id( 'place_id' ) ); ?>"><?php _e( 'Place ID:', 'text_domain' ); ?></label>
                            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'place_id' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'place_id' ) ); ?>" type="text" value="<?php echo esc_attr( $place_id ); ?>" readonly />
                        </p>
                        <?php
                    }else {
                        ?>
                        <p>
                            <label for="<?php echo esc_attr( $this->get_field_id( 'place_id' ) ); ?>"><?php _e( 'Place ID:', 'text_domain' ); ?></label>
                            <input class="pgrw-place-id widefat" id="<?php echo esc_attr( $this->get_field_id( 'place_id' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'place_id' ) ); ?>" type="text" value="<?php echo esc_attr( $place_id ); ?>" />
                            <span name='pgrw-errormessage'>The place id can be got from the <a href="https://developers.google.com/maps/documentation/javascript/examples/places-placeid-finder">Google Place ID Finder</a>.</span>
                        </p>

                        <p>
                            <button type="button" class="widefat" style="height: 25px; background-color: white; border: 1px solid #e5e5e5;" id="pgrw-fetch-place" onClick="fetchPlace();">Fetch Place</button>
                            <script>
                                function fetchPlace() {
                                    var place_id = document.getElementsByClassName("pgrw-place-id")[1].value;
                                    var api_key = "<?php echo get_option("pgrw_api_key"); ?>";
                                
                                    if(api_key != "") {
                                        if(place_id != "") {
                                            if(place_id.startsWith("ChIJ")) {
                                                var grw_api = "<?php echo admin_url('options-general.php'); ?>";
                                                jQuery.get(grw_api + "?pgrw_handler=save&place_id=" + place_id + "&api_key=" + api_key, function(data) {
                                                    var parsedData = JSON.parse(data);
                                                    if(parsedData["error_message"]) {
                                                        document.getElementsByName("pgrw-errormessage")[1].textContent = parsedData["error_message"];
                                                    }else if(parsedData["status"] == "INVALID_REQUEST") {
                                                        document.getElementsByName("pgrw-errormessage")[1].textContent = "You have not provided a correct place id.";
                                                    }else if(parsedData["result"]) {
                                                        document.getElementsByName("pgrw-errormessage")[1].textContent = "Fetched the place.";
                                                        return true;
                                                    }else {
                                                        document.getElementsByName("pgrw-errormessage")[1].textContent = "Encountered, a unusual error, please contact support@aldermanis.com";
                                                    }
                                                });
                                            }else {
                                                document.getElementsByName("pgrw-errormessage")[1].textContent = "That is a invalid place id, they begin with 'ChIJ'.";
                                            }
                                        }else {
                                            document.getElementsByName("pgrw-errormessage")[1].textContent = "Please enter your place id.";
                                        }
                                    }else {
                                        document.getElementsByName("pgrw-errormessage")[1].textContent = "You have not configured your api key, go to Settings -> Google Ratings Widget and enter your api key.";
                                    }
                                }
                            </script>
                        </p>
                        <?php
                    }
                    ?>
                </p>
            </div>
            <?php

            // Display //
            ?>
            <br><br>
            <button class="collapsible" type="button">Display Settings</button>
            <div class="content">
                <p>
                    <label for="<?php echo esc_attr($this->get_field_id('design')); ?>">
                        <?php esc_attr_e('Design:', 'text_domain'); ?>
                    </label>
                    <select
                        class="widefat" 
                        id="<?php echo esc_attr($this->get_field_id('design')); ?>" 
                        name="<?php echo esc_attr($this->get_field_name('design')); ?>"> 
                        <option value="rating" <?php echo ($design == 'rating') ? 'selected' : ''; ?>>
                            Rating
                        </option>
                        <option value="badge" disabled>
                            Badge
                        </option>
                    </select>

                    <label>
                        <?php esc_attr_e('Text  Colour:', 'text_domain'); ?>
                    </label>
                    <input
                        class="widefat" 
                        type="text" 
                        value="#8b8b8b"
                        disabled
                    />

                    <label>
                        <?php esc_attr_e('Background:', 'text_domain'); ?>
                    </label>
                    <input
                        class="widefat" 
                        type="text" 
                        value="#fff"
                        disabled
                    />

                    <label>
                        <?php esc_attr_e('Rating Colour:', 'text_domain'); ?>
                    </label>
                    <input
                        class="widefat" 
                        type="text" 
                        value="#ffd055"
                        disabled
                    />

                    <div class="card">
                        <div class="card-body">
                            <p>In the future more display options will be avaliable in the Google Ratings Widget Pro: <a href="https://www.platinumplugins.com/google-ratings-widget">Upgrade</a></p>
                        </div>
                    </div>
                </p>
            </div>
            <?php

            // Reviews //
            ?>
            <br><br>
            <button class="collapsible" type="button">Review Settings</button>  
            <div class="content">
                <p>
                    <input
                        class="checkbox"
                        type="checkbox"
                        disabled
                    />
                    <label>
                        <?php esc_attr_e('Redact User Name', 'text_domain'); ?>
                    </label>

                    <br>

                    <input
                        class="checkbox"
                        type="checkbox"
                        disabled
                    />
                    <label>
                        <?php esc_attr_e('Redact User Avatars', 'text_domain'); ?>
                    </label>

                    <br><br>

                    <label>
                        <?php esc_attr_e('Minimum Review:', 'text_domain'); ?>
                    </label>
                    <input
                        class="widefat" 
                        type="text" 
                        value="5"
                        disabled
                    />

                    <div class="card">
                        <div class="card-body">
                            <p>The reviews and settings for reviews are avaliable in the Google Ratings Widget Pro: <a href="https://www.platinumplugins.com/google-ratings-widget">Upgrade</a></p>
                        </div>
                    </div>
                </p>
            </div>
            <?php

            // Other Features //
            ?>
            <br><br>
            <button class="collapsible" type="button">Other Features</button>
            <div class="content">
                <p>
                    <input
                        class="checkbox"
                        type="checkbox"
                        disabled
                    />
                    <label>
                        <?php esc_attr_e('Rich Snippets', 'text_domain'); ?>
                    </label>
                </p>    
            </div>
            <br><br>  
            <?php

            ?>
            <hr>
            <p>
                <div class="card">
                    <div class="card-body">
                        <p>The locked features and displaying reviews are avaliable in the Google Ratings Widget Pro: <a href="https://www.platinumplugins.com/google-ratings-widget">Upgrade</a></p>
                    </div>
                </div>
            </p>
            <?php

            ?>
            <script>
              var coll = document.getElementsByClassName("collapsible");
              var i;

              for (i = 0; i < coll.length; i++) {
                coll[i].addEventListener("click", function() {
                  this.classList.toggle("active");
                  var content = this.nextElementSibling;
                  if (content.style.display === "block") {
                    content.style.display = "none";
                  } else {
                    content.style.display = "block";
                  }
                });
              }
            </script>
            <?php
        }

        public function update($new_instance, $old_instance) {
            $instance = $old_instance;
            $settings = array(
                'title' => '',
                'place_id' => '',
                'design' => ''
            );

            foreach($settings as $key => $value) {
                $instance[$key] = isset($new_instance[$key]) ? wp_strip_all_tags($new_instance[$key]) : '';
            }

            return $instance;   
        }

        public function widget($args, $instance) {
            // Global Variables //
            global $wpdb;

            // Variables //
            $place_id = isset($instance['place_id']) ? $instance['place_id'] : '';
            $design = isset($instance['design']) ? $instance['design'] : '';
            $results = $wpdb->get_results($wpdb->prepare("SELECT * FROM pgrw WHERE place_id=%s", $place_id), ARRAY_A);
            $rating = $results[0]["rating"];
            $reviews = $wpdb->get_results($wpdb->prepare("SELECT * FROM pgrw_reviews WHERE place_id=%s", $place_id));

            // Display before Widget //
            echo $args['before_widget']; 

            // Widget Content //
            if(($design == "rating") && ($place_id != "")) {
                ?>
                <div class="gr-box-rating">
                    <div class="gr-box-rating-info">
                        <img src=<?php echo plugins_url("/web/img/google.png", __FILE__); ?> class="gr-box-rating-photo">
                        <span class="gr-box-rating-name"><?php echo get_bloginfo('name'); ?></span>
                        <div class="gr-box-rating-stars" style="width:calc(20px * <?php echo $rating; ?>)"></div>
                    </div>
                </div>
                <?php
            }

            // Display after Widget //
            echo $args['after_widget'];
        }
    }
?>