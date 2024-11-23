# WordPress List of Cities Temperature App

## Introduction
This project is a WordPress-based assignment application showcasing the integration of custom post types, fields, and taxonomies with a public API to display city temperatures. It demonstrates how to manage custom content and dynamically fetch data using OpenWeatherMap.

## Demo Version
Experience the app's features and functionality by visiting [Demo Site](https://abelohost.efriel.com).

### Demo Admin Credentials
- **Username:** efriel  
- **Password:** efriel123  

---

## Features
- **Manage Countries**: Create, update, and delete country taxonomies.
- **City Management**: Add cities with custom fields for latitude and longitude.
- **Temperature Widget**: A custom widget displays city temperatures using OpenWeatherMap API.
- **Custom Template**: Displays a list of cities and temperatures.
- **Export & Print**: Export the city list to CSV or print directly from the page.

---

## Prerequisites
- **PHP**: 8.3.3  
- **MariaDB**: 10.11.9  

---

## Installation
1. **Clone the Repository**:  
   ```bash
   git clone https://github.com/efriel/abelohost.git
   ```

2. **Database Setup**:
   Import the .sql file from the folder root, 
   Update the `wp-config.php` file with your database details:  
   ```php
   define( 'DB_NAME', 'abelodb' );
   define( 'DB_USER', 'your_db_username' );
   define( 'DB_PASSWORD', 'your_db_password' );
   define( 'DB_HOST', 'localhost' );
   ```

3. **Source Files**:  
   Download additional source files from:  
   - [Assignment Test App](https://drive.google.com/file/d/1SKjT-p95IPobZ8Zm2U3HqLjK1eN8HcCB/view?usp=sharing)  
   - [Storefront Child Theme](https://drive.google.com/file/d/1d8W4Rhza2zAWzQPNnnwCd9T1PPzgwwKC/view?usp=sharing)  

4. **Video Tutorial**:  
   Learn how to set up and use the app with this [Loom Video Tutorial](https://www.loom.com/share/9760260b399a4f83b58cfbf1f861feb0?sid=67889c6c-cd7b-4e6e-9111-41effb551c22).

---

## Usage
After installation:
- **Create Cities**: Add cities with location details via the admin panel.
- **Add Widget**: Place the temperature widget on your WordPress sidebar.
- **List Cities**: Use the custom page template to display city data dynamically.

### Custom Hooks
- **Before Table**: Insert features like "Export to CSV" above the city list.
- **After Table**: Add "Print Table" functionality below the city list.

---

## License
Efriel Elyasa License +628111006011 efriel@ymail.com

### Code Description

## Create Custom Post "City"
```php
    // Cities
    function register_cities_post_type() {
        $labels = array(
            'name'               => 'Cities',
            'singular_name'      => 'City',
            'menu_name'          => 'Cities',
            'add_new'            => 'Add New',
            'add_new_item'       => 'Add New City',
            'edit_item'          => 'Edit City',
            'new_item'           => 'New City',
            'view_item'          => 'View City',
            'all_items'          => 'All Cities',
            'search_items'       => 'Search Cities',
            'not_found'          => 'No Cities found',
            'not_found_in_trash' => 'No Cities found in Trash',
        );

        $args = array(
            'labels'             => $labels,
            'public'             => true,
            'has_archive'        => true,
            'rewrite'            => array('slug' => 'cities'),
            'supports'           => array('title', 'editor'),
            'menu_icon'          => 'dashicons-location-alt',
            'show_in_rest'       => true,
        );

        register_post_type('cities', $args);
    }
    add_action('init', 'register_cities_post_type');


    function add_cities_meta_box() {
        add_meta_box(
            'city_meta_box',
            'City Details',
            'display_cities_meta_box',
            'cities',
            'normal',
            'high'
        );
    }
    add_action('add_meta_boxes', 'add_cities_meta_box');

    function display_cities_meta_box($post) {
        $latitude = get_post_meta($post->ID, 'city_latitude', true);
        $longitude = get_post_meta($post->ID, 'city_longitude', true);

        echo '<label for="city_latitude">Latitude:</label>';
        echo '<input type="text" name="city_latitude" id="city_latitude" value="' . esc_attr($latitude) . '" class="widefat">';
        echo '<label for="city_longitude">Longitude:</label>';
        echo '<input type="text" name="city_longitude" id="city_longitude" value="' . esc_attr($longitude) . '" class="widefat">';
    }

    function save_city_meta_box($post_id) {
        if (array_key_exists('city_latitude', $_POST)) {
            update_post_meta($post_id, 'city_latitude', sanitize_text_field($_POST['city_latitude']));
        }
        if (array_key_exists('city_longitude', $_POST)) {
            update_post_meta($post_id, 'city_longitude', sanitize_text_field($_POST['city_longitude']));
        }
    }
    add_action('save_post', 'save_city_meta_box');

    function add_cities_columns($columns) {
        $columns['latitude'] = 'Latitude';
        $columns['longitude'] = 'Longitude';
        return $columns;
    }
    add_filter('manage_edit-cities_columns', 'add_cities_columns');

    function fill_cities_columns($column, $post_id) {
        if ($column === 'latitude') {
            echo esc_html(get_post_meta($post_id, 'city_latitude', true));
        }
        if ($column === 'longitude') {
            echo esc_html(get_post_meta($post_id, 'city_longitude', true));
        }
    }
    add_action('manage_cities_posts_custom_column', 'fill_cities_columns', 10, 2);
```

## Create Taxonomy "Countries"
```php
// Countries
function register_countries_taxonomy() {
    $labels = array(
        'name'              => 'Countries',
        'singular_name'     => 'Country',
        'search_items'      => 'Search Countries',
        'all_items'         => 'All Countries',
        'edit_item'         => 'Edit Country',
        'update_item'       => 'Update Country',
        'add_new_item'      => 'Add New Country',
        'new_item_name'     => 'New Country Name',
        'menu_name'         => 'Countries',
    );

    $args = array(
        'labels'            => $labels,
        'hierarchical'      => true,
        'public'            => true,
        'show_ui'           => true,
        'show_in_rest'      => true,
        'rewrite'           => array('slug' => 'countries'),
    );

    register_taxonomy('countries', 'cities', $args);
}
add_action('init', 'register_countries_taxonomy');
```

## Create Widget
    ```php
    class CityWeatherWidget extends WP_Widget {
        public function __construct() {
            parent::__construct(
                'city_weather_widget',
                'City Weather Widget',
                array('description' => 'Displays a city name and its current temperature.')
            );
        }

        // Admin Dashboard Widget
        public function form($instance) {
            $city_id = !empty($instance['city_id']) ? $instance['city_id'] : '';
            ?>
            <p>
                <label for="<?php echo esc_attr($this->get_field_id('city_id')); ?>">City:</label>
                <select id="<?php echo esc_attr($this->get_field_id('city_id')); ?>" name="<?php echo esc_attr($this->get_field_name('city_id')); ?>">
                    <option value="">Select a City</option>
                    <?php
                    $cities = get_posts(array('post_type' => 'cities', 'numberposts' => -1));
                    foreach ($cities as $city) {
                        echo '<option value="' . esc_attr($city->ID) . '" ' . selected($city_id, $city->ID, false) . '>' . esc_html($city->post_title) . '</option>';
                    }
                    ?>
                </select>
            </p>
            <?php
        }

        // Save widget
        public function update($new_instance, $old_instance) {
            $instance = array();
            $instance['city_id'] = !empty($new_instance['city_id']) ? sanitize_text_field($new_instance['city_id']) : '';
            return $instance;
        }

        // Front-end widget
        public function widget($args, $instance) {
            $city_id = !empty($instance['city_id']) ? $instance['city_id'] : '';
            if (!$city_id) {
                return;
            }

            $city_name = get_the_title($city_id);
            $latitude = get_post_meta($city_id, 'city_latitude', true);
            $longitude = get_post_meta($city_id, 'city_longitude', true);

            if (!$latitude || !$longitude) {
                echo $args['before_widget'] . 'Location data missing for this city.' . $args['after_widget'];
                return;
            }

            $api_key = '95b24ac3e41b2946fc4c916ca2d08a8a';
            $response = wp_remote_get("https://api.openweathermap.org/data/2.5/weather?lat=$latitude&lon=$longitude&units=metric&appid=$api_key");
            
            if (is_wp_error($response)) {
                echo $args['before_widget'] . 'Unable to fetch weather data.' . $args['after_widget'];
                return;
            }

            $weather_data = json_decode(wp_remote_retrieve_body($response), true);

            if (!isset($weather_data['main']['temp'])) {
                echo $args['before_widget'] . 'Weather data not available.' . $args['after_widget'];
                return;
            }

            $temperature = $weather_data['main']['temp'];

            echo $args['before_widget'];
            echo $args['before_title'] . esc_html($city_name) . $args['after_title'];
            echo '<p>Current Temperature: ' . esc_html($temperature) . ' °C</p>';
            echo $args['after_widget'];
        }
    }

    function register_city_weather_widget() {
        register_widget('CityWeatherWidget');
    }
    add_action('widgets_init', 'register_city_weather_widget');
    ```

## Add functions for list of city page
    ```php
    add_action('wp_ajax_search_cities', 'search_cities_handler');
    add_action('wp_ajax_nopriv_search_cities', 'search_cities_handler');

    function search_cities_handler() {
        global $wpdb;

        $api_key = '95b24ac3e41b2946fc4c916ca2d08a8a'; // Replace with your API key
        $search = isset($_POST['search']) ? sanitize_text_field($_POST['search']) : '';

        $query = "
            SELECT p.ID, p.post_title AS city_name, t.name AS country_name, 
            lat.meta_value AS latitude, long.meta_value AS longitude
            FROM {$wpdb->posts} p
            LEFT JOIN {$wpdb->term_relationships} tr ON (p.ID = tr.object_id)
            LEFT JOIN {$wpdb->term_taxonomy} tt ON (tr.term_taxonomy_id = tt.term_taxonomy_id)
            LEFT JOIN {$wpdb->terms} t ON (tt.term_id = t.term_id)
            LEFT JOIN {$wpdb->postmeta} lat ON (p.ID = lat.post_id AND lat.meta_key = 'city_latitude')
            LEFT JOIN {$wpdb->postmeta} `long`  ON (p.ID = long.post_id AND long.meta_key = 'city_longitude')
            WHERE p.post_type = 'cities' AND p.post_status = 'publish'
            AND (p.post_title LIKE %s OR t.name LIKE %s)
        ";

        $cities = $wpdb->get_results($wpdb->prepare($query, "%$search%", "%$search%"));

        if ($cities) {
            echo '<table><thead><tr><th>Country</th><th>City</th><th>Temperature</th></tr></thead><tbody>';
            foreach ($cities as $city) {
                $temperature = 'N/A';
                if (!empty($city->latitude) && !empty($city->longitude)) {
                    $api_url = "https://api.openweathermap.org/data/2.5/weather?lat={$city->latitude}&lon={$city->longitude}&units=metric&appid={$api_key}";
                    $response = wp_remote_get($api_url);

                    if (!is_wp_error($response)) {
                        $data = json_decode(wp_remote_retrieve_body($response), true);
                        $temperature = isset($data['main']['temp']) ? $data['main']['temp'] . ' °C' : 'N/A';
                    }
                }

                echo '<tr>';
                echo '<td>' . esc_html($city->country_name) . '</td>';
                echo '<td>' . esc_html($city->city_name) . '</td>';
                echo '<td>' . esc_html($temperature) . '</td>';
                echo '</tr>';
            }
            echo '</tbody></table>';
        } else {
            echo 'No results found.';
        }

        wp_die();
    }


    function enqueue_city_search_script() {
        wp_enqueue_script('city-search', get_stylesheet_directory_uri() . '/js/city-search.js', array('jquery'), null, true);
        wp_localize_script('city-search', 'ajax_object', array('ajax_url' => admin_url('admin-ajax.php')));
    }
    add_action('wp_enqueue_scripts', 'enqueue_city_search_script');

    function enqueue_export_print_script() {
        wp_enqueue_script('export-print', get_stylesheet_directory_uri() . '/js/export-print.js', array('jquery'), null, true);
    }
    add_action('wp_enqueue_scripts', 'enqueue_export_print_script');

    add_action('before_cities_table', function() {
        echo '<button id="export-csv">Export to CSV</button>';
    });

    add_action('after_cities_table', function() {
        echo '<button id="print-table">Print Table</button>';
    });

```