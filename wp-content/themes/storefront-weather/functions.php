<?php
function my_child_theme_enqueue_styles() {
    wp_enqueue_style('parent-style', get_template_directory_uri() . '/style.css');
}
add_action('wp_enqueue_scripts', 'my_child_theme_enqueue_styles');

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

// Widget
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

// adding list of cities to page with search feature

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

add_action('before_cities_table', function() {
    echo '<h2>Custom Hook 1</h2>';
});

add_action('after_cities_table', function() {
    echo '<h2>Custom Hook 2</h2>';
});

?>