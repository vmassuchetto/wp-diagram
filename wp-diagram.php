<?php
/*
* Plugin Name: WP-Diagram
* Plugin URI: http://vinicius.soylocoporti.org.br/wp-diagram-wordpress-plugin
* Description: Posts diagramming and schedulling across independent positions.
* Version: 0.01
* Author: Vinicius Massuchetto
* Author URI: http://vinicius.soylocoporti.org.br
*/

class WP_Diagram {

    var $plugin_docs_url;
    var $plugin_basename;
    var $plugin_dir_path;
    var $errors;
    var $positions;
    var $type_schedule;

    function WP_Diagram() {

        $this->plugin_docs_url = 'http://vinicius.soylocoporti.org.br/wp-diagram-wordpress-plugin';
        $this->plugin_basename = plugin_basename( __FILE__ );
        $this->plugin_dir_path = plugin_dir_path( __FILE__ );
        $this->plugin_dir_url = plugin_dir_url( __FILE__ );
        $this->errors = array();
        $this->positions = array();
        $this->type_schedule = 'wp_diagram_schedule';

        $this->register_structure();

        if ( is_admin() ) {
            add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
            add_action( 'admin_menu', array( $this, 'admin_menu' ) );
            add_action( 'admin_head', array( $this, 'admin_head' ) );

            $ajax_actions = array( 'add_schedule', 'copy_schedule', 'delete_schedule',
                'search_post', 'add_post', 'delete_post', 'save_post',
                'change_order', 'update_position' );
            foreach( $ajax_actions as $a )
                add_action( 'wp_ajax_wp_diagram_' . $a, array( $this, $a ) );
        }

    }

    /* Plugin Structure */

    function error_fatal( $error_message ) {
        $error = new WP_Error( 'wp_diagram', 'wp_diagram fatal error: ' . $error_message );
        wp_die( $error );
    }

    function error_warning( $error_message ) {
        echo 'wp_diagram warning: ' . $error_message;
    }

    function register_structure() {
        add_theme_support( 'post-thumbnails' );
        register_post_type( $this->type_schedule, array(
            'public' => false,
            'publicly_queryable' => false,
            'exclude_from_search' => true,
            'show_ui' => false,
            'rewrite' => false
        ) );
    }

    /* Admin */

    function admin_enqueue_scripts() {
        if ( ! empty( $_GET['page'] ) && 'wp_diagram' == $_GET['page'] ) {
            wp_enqueue_script( 'jquery' );
            wp_enqueue_script( 'jquery-ui-core' );
            wp_enqueue_script( 'jquery-ui-autocomplete' );
            wp_enqueue_script( 'jquery-ui-draggable' );
            wp_enqueue_script( 'jquery-ui-datepicker' );
            wp_enqueue_script( 'jquery-ui-slider' );
            wp_enqueue_script( 'jquery-ui-effects-core',
                $this->plugin_dir_url . 'js/jquery-ui-effects-core.js',
                array( 'jquery-ui-core') );
            wp_enqueue_script( 'jquery-ui-effects-highlight',
                $this->plugin_dir_url . 'js/jquery-ui-effects-highlight.js',
                array( 'jquery-ui-effects-core') );
            wp_enqueue_script( 'jquery-ui-timepicker',
                $this->plugin_dir_url . 'js/jquery-ui-timepicker-addon.js',
                array( 'jquery-ui-core', 'jquery-ui-slider', 'jquery-ui-autocomplete',
                    'jquery-ui-sortable', 'jquery-ui-datepicker' ) );
            wp_enqueue_script( 'wp-diagram', $this->plugin_dir_url . 'js/wp-diagram.js' );

            wp_enqueue_style( 'jquery-style', $this->plugin_dir_url . 'css/jquery-ui.css' );
            wp_enqueue_style( 'wp-diagram', $this->plugin_dir_url . 'css/wp-diagram.css' );
        }
    }

    function admin_menu() {
        add_menu_page( __( 'Positioning', 'wp_diagram' ), __( 'Positioning', 'wp_diagram' ),
            'add_users', 'wp_diagram', array( $this, 'admin_post_positions' ),
            $this->plugin_dir_url . 'img/blogs-stack.png'
            , 6 );
    }

    function admin_head() {
        ?>
        <style type="text/css" media="screen">
            .wp-diagram .post-thumbnail-icon { background-image:url('<?php echo $this->plugin_dir_url; ?>img/image.png'); }
        </style>
        <?php
    }

    function admin_post_positions() {
        include( $this->plugin_dir_path . 'admin/positions.php' );
    }

    /* General Functions */

    function get_schedule_sql_select() {
        return "
            ID AS id,
            post_title AS position,
            post_content AS posts,
            post_date AS date
        ";
    }

    function get_current_schedule( $args = false ) {
        global $wpdb;

        $defaults = array(
            'position' => false,
            'raw' => false
        );
        $args = wp_parse_args( $args, $defaults );
        if ( ! $args['position'] )
            return false;

        $select = $this->get_schedule_sql_select();
        $sql = $wpdb->prepare( "
            SELECT {$select}
            FROM {$wpdb->posts}
            WHERE 1=1
                AND post_type = '%s'
                AND post_title = '%s'
                AND post_date < '%s'
            ORDER BY date ASC
            LIMIT 1
        ", $this->type_schedule, $args['position'], date_i18n( 'Y-m-d H:i:s' ) );
        $schedule = $wpdb->get_results( $sql );

        if ( ! $args['raw'] ) {
            foreach ( array_keys( $schedule ) as $k ) {
                $schedule[ $k ] = $this->process_schedule( $schedule[ $k ] );
            }
        }

        if ( ! empty( $schedule[0] ) && $schedule[0]->date < date_i18n( 'Y-m-d H:i:s' ) ) {
            $schedule[0]->current = true;
            return $schedule[0];
        }
        return false;
    }

    function get_schedule( $args = false ) {
        global $wpdb;

        $defaults = array(
            'position' => false,
            'schedule' => false,
            'raw' => false
        );
        $args = wp_parse_args( $args, $defaults );

        $schedules = array();
        if ( $s = $this->get_current_schedule( $args + array( 'raw' => true ) ) )
            $schedules[ $s->id ] = $s;

        $select = $this->get_schedule_sql_select();

        if ( ! empty( $args['schedule'] ) ) {
            $where = $wpdb->prepare( "
                AND ID = '%s'
            ", $args['schedule'] );
            $limit = ' LIMIT 1 ';

        } elseif ( ! empty( $args['position'] ) ) {
            $where = $wpdb->prepare( "
                AND post_type = '%s'
                AND post_title = '%s'
                AND post_date > '%s'
            ", $this->type_schedule, $args['position'], date_i18n( 'Y-m-d H:i:s' ) );
            $limit = '';
        }

        if ( ! $where )
            return false;

        $sql = "
            SELECT {$select}
            FROM {$wpdb->posts}
            WHERE 1=1 {$where}
            ORDER BY date ASC
            {$limit}
        ";
        if ( $schedule = $wpdb->get_results( $sql, OBJECT_K ) )
            $schedules = $schedules + $schedule;

        if ( ! $args['raw'] ) {
            foreach ( array_keys( $schedules ) as $k ) {
                $schedules[ $k ] = $this->process_schedule( $schedules[ $k ] );
            }
        }

        if ( ! empty( $args['schedule'] ) )
            return $schedules[ key( $schedules ) ];
        return $schedules;
    }

    function process_schedule( $schedule ) {
        if ( empty( $schedule ) || empty( $schedule->posts ) )
            return false;

        $schedule_posts = json_decode( $schedule->posts );
        $posts = array();
        foreach ( $schedule_posts as $p ) {

            if (! $post = get_post( $p->ID ) )
                continue;

            if ( ! empty( $p->post_title ) ) {
                $post->original_post_title = $post->post_title;
                $post->post_title = $p->post_title;
            }

            if ( ! empty( $p->post_excerpt ) ) {
                $post->original_post_excerpt = $post->post_excerpt;
                $post->post_excerpt = $p->post_excerpt;
            }

            $posts[ $p->ID ] = $post;
        }

        $schedule->posts = $posts;
        return $schedule;
    }

    /* Ajax Actions */

    function search_post() {
        if ( empty( $_GET['term'] ) )
            return 0;
        $query = sanitize_text_field( $_GET['term'] );

        global $post, $wpdb;
        $sql = $wpdb->prepare("
            SELECT ID
            FROM {$wpdb->posts}
            WHERE 1=1
                AND post_title LIKE '%%%s%%'
                AND post_status IN ('publish', 'future')
            LIMIT 15
        ", $query );
        $posts = $wpdb->get_results( $sql );

        $response = array();
        foreach( $posts as $p ) {
            $post = get_post( $p->ID );
            setup_postdata( $post );
            $response[] = array(
                'id' => get_the_ID(),
                'label' => get_the_title(),
                'date' => get_the_date(),
                'value' => ''
            );
        }
        echo json_encode( $response );
        exit;
    }

    function add_schedule( $args = false ) {
        $defaults = array(
            'date' => false,
            'position' => false,
            'echo' => true
        );
        if ( ! empty( $_POST ) )
            $args = $_POST;
        $args = wp_parse_args( $args, $defaults );
        $args['date'] = sanitize_text_field( $args['date'] );
        $args['position'] = sanitize_text_field( $args['position'] );
        if ( ! $args['date'] || ! $args['position']
            || ! $time = strtotime( $args['date'] ) )
            exit;

        $postargs = array(
            'post_type' => $this->type_schedule,
            'post_title' => $args['position'],
            'post_date' => $args['date'],
            'post_status' => 'publish',
            'post_content' => json_encode( array() )
        );
        $post =  wp_insert_post( $postargs );
        if ( $args['echo'] ) {
            echo $post;
            exit;
        }

        return $post;
    }

    function copy_schedule( $args = false ) {
        $defaults = array(
            'date' => false,
            'schedule' => false,
            'position' => false,
            'echo' => true
        );
        if ( ! empty( $_POST ) )
            $args = $_POST;
        $args = wp_parse_args( $args, $defaults );
        $args['date'] = sanitize_text_field( $args['date'] );
        $args['schedule'] = intval( $args['schedule'] );
        $args['position'] = sanitize_text_field( $args['position'] );
        $schedule = $this->get_schedule( array( 'schedule' => $args['schedule'], 'raw' => true ) );
        if ( ! $args['date'] || ! $args['position'] || ! $schedule
            || ! $time = strtotime( $args['date']) )
            exit;

        $postargs = array(
            'post_type' => $this->type_schedule,
            'post_title' => $args['position'],
            'post_date' => $args['date'],
            'post_status' => 'publish',
            'post_content' => $schedule->posts
        );
        $post =  wp_insert_post( $postargs );
        if ( $args['echo'] ) {
            echo $post;
            exit;
        }

        return $post;
    }

    function delete_schedule( $args = false ) {
        $defaults = array(
            'schedule' => false
        );
        if ( empty( $args ) )
            $args = $_POST;
        $args = wp_parse_args( $args, $defaults );
        $args['schedule'] = intval( $args['schedule'] );
        if ( ! $args['schedule']
            || ! $schedule = $this->get_schedule( array( 'schedule' => $args['schedule'], 'raw' => true ) ) )
            exit;

        if ( wp_delete_post( $schedule->id ) )
            echo 1;
        exit;
    }

    function add_post( $args = false ) {
        global $wpdb;

        $defaults = array(
            'post' => false,
            'schedule' => false,
        );
        if ( ! $args )
            $args = $_POST;
        $args = wp_parse_args( $args, $defaults );
        $args['post'] = intval( $args['post'] );
        $args['schedule'] = intval( $args['schedule'] );
        if ( ! $args['post'] || ! $args['schedule']
            || ! $schedule = $this->get_schedule( array( 'schedule' => $args['schedule'], 'raw' => true ) ) )
            exit;
        $posts = json_decode( $schedule->posts, true );
        $new_post = array( $args['post'] => array( 'ID' => $args['post'] ) );
        if ( ! is_array( $posts ) )
            $posts = array();
        $posts = $new_post + $posts;
        $schedule->posts = json_encode( $posts );
        $sql = $wpdb->prepare("
            UPDATE {$wpdb->posts}
            SET post_content = '%s'
            WHERE ID = '%s'
        ", $schedule->posts, $schedule->id );
        if ( $wpdb->query( $sql ) )
            echo 1;
        exit;
    }

    function delete_post( $args = false ) {
        global $wpdb;

        $defaults = array(
            'schedule' => false,
            'post' => false
        );
        if ( ! $args )
            $args = $_POST;
        $args = wp_parse_args( $args, $defaults );
        $args['schedule'] = intval( $args['schedule'] );
        $args['post'] = intval( $args['post'] );
        if ( ! $args['schedule'] || ! $args['post']
            || ! $schedule = $this->get_schedule( array( 'schedule' => $args['schedule'], 'raw' => true ) ) )
            exit;

        $posts = json_decode( $schedule->posts, true );
        unset( $posts[ $args['post'] ] );
        $posts = json_encode( $posts);

        $sql = $wpdb->prepare("
            UPDATE {$wpdb->posts}
            SET post_content = '%s'
            WHERE ID = '%s'
        ", $posts, $schedule->id );
        if ( $wpdb->query( $sql ) )
            echo 1;
        exit;
    }

    function save_post( $args = false ) {
        global $wpdb;

        $defaults = array(
            'schedule' => false,
            'post_id' => false,
            'post_title' => false,
            'post_excerpt' => false
        );
        if ( ! $args )
            $args = $_POST;
        $args = wp_parse_args( $args, $defaults );
        $args['schedule'] = intval( $args['schedule'] );
        $args['post_id'] = intval( $args['post_id'] );
        $args['post_title'] = sanitize_text_field( $args['post_title'] );
        $args['post_excerpt'] = sanitize_text_field( $args['post_excerpt'] );

        if ( ! $args['post_id'] )
            exit;

        $schedule = $this->get_schedule( array( 'schedule' => $args['schedule'], 'raw' => true ) );
        $posts = json_decode( $schedule->posts );
        $posts->{$args['post_id']}->post_title = $args['post_title'];
        $posts->{$args['post_id']}->post_excerpt = $args['post_excerpt'];
        $posts = json_encode( $posts );

        $sql = $wpdb->prepare( "
            UPDATE {$wpdb->posts}
            SET post_content = '%s'
            WHERE ID = '%s'
        ", $posts, $args['schedule'] );
        if ( $wpdb->query( $sql ) )
            echo 1;
        exit;
    }

    function change_order( $args = false ) {
        global $wpdb;

        $defaults = array(
            'order' => false,
            'schedule' => false
        );
        if ( ! $args )
            $args = $_POST;
        $args = wp_parse_args( $args, $defaults );
        $args['schedule'] = intval( $args['schedule'] );
        $args['order'] = array_filter( explode( ',', $args['order'] ), 'ctype_digit');
        if ( ! $args['schedule'] || ! $args['order']
            || ! $schedule = $this->get_schedule( array( 'schedule' => $args['schedule'], 'raw' => true ) ) )
            exit;

        $posts = json_decode( $schedule->posts, true );
        $sorted = array();
        foreach ( $args['order'] as $o ) {
            $sorted[$o] = $posts[$o];
        }

        $posts = json_encode( $sorted );
        $sql = $wpdb->prepare("
            UPDATE {$wpdb->posts}
            SET post_content = '%s'
            WHERE ID = '%s'
        ", $posts, $args['schedule'] );

        if ( $wpdb->query( $sql ) )
            echo 1;
        exit;
    }

    function update_position() {
        if ( empty( $_POST['position'] ) )
            return 0;

        global $selected_schedule, $position, $wp_diagram;

        if ( ! empty( $_POST['schedule'] ) )
            $selected_schedule = intval( $_POST['schedule'] );
        else
            $selected_schedule = false;
        $position = sanitize_text_field( $_POST['position'] );

        if ( $position &&
            ! empty( $wp_diagram->positions[ $position ] ) )
            $position = $wp_diagram->positions[ $position ];

        include( $this->plugin_dir_path . 'admin/position.php' );
        exit;
    }

}

function wp_diagram_init() {
    global $wp_diagram;
    $wp_diagram = new WP_Diagram();
    do_action( 'wp_diagram_init' );
}
add_action( 'plugins_loaded', 'wp_diagram_init' );

function wp_diagram_check_instance() {
    global $wp_diagram;

    $cache_key = 'wp_diagram_check_instance';
    if ( wp_cache_get( $cache_key ) )
        return true;

    if ( ! class_exists( 'WP_Diagram' )
        || 'WP_Diagram' !== get_class( $wp_diagram ) )
        WP_Diagram::error_fatal( __( 'Plugin instantiation error.', 'wp_diagram' ) );

    wp_cache_set( $cache_key, true );
    return true;
}

function wp_diagram_check_position( $position ) {
    global $wp_diagram;

    $cache_key = 'wp_diagram_check_position_' . $position;
    if ( wp_cache_get( $cache_key ) )
        return true;

    wp_diagram_check_instance();

    if ( ! in_array( $position, array_keys( $wp_diagram->positions ) ) ) {
        $wp_diagram->error_warning( sprintf( __( 'Position "%s" not declared in your theme.', 'wp_diagram' ), $position ) );
        return false;
    }

    wp_cache_set( $cache_key, true );
    return true;
}

/* API Functions */

function wp_diagram_register_positions( $positions = array() ) {
    global $wp_diagram;

    wp_diagram_check_instance();

    if ( empty( $positions ) || ! is_array( $positions ) )
        $wp_diagram->error_fatal( __( 'No parameter set for position declaration.', 'wp_diagram' ) );

    $registered_positions = array();
    $required = array( 'id', 'name' );

    foreach( $positions as $position ) {
        foreach ( $required as $r ) {
            if ( empty ( $position[ $r ] ) )
                $wp_diagram->error_fatal(
                    sprintf( __( 'Missing "%s" argument on position registration.', 'wp_diagram' ), $r ) );
        }
        if ( preg_match( '/[^A-Za-z0-9_]/', $position['id'] ) )
            $wp_diagram->error_fatal(
                sprintf( __( 'Please provide position IDs with only characters, numbers and underscores.', 'wp_diagram' ), $r ) );
        if ( in_array( $position['id'], array_keys( $registered_positions ) ) )
            $wp_diagram->error_fatal(
                sprintf( __( 'Position IDs are meant to be unique. You\'re providing duplicated ones.', 'wp_diagram' ), $r ) );

        $registered_positions[ $position['id'] ] = $position;
    }

    // Valid positions
    $wp_diagram->positions = $registered_positions;

}

function wp_diagram_get_posts( $position ) {
    global $wp_diagram;

    if ( ! wp_diagram_check_position( $position ) )
        return false;

    $cache_key = 'wp_diagram_current_posts_' . $position;
    if ( $posts = wp_cache_get( $cache_key ) )
        return $posts;

    if ( ! $schedule = $wp_diagram->get_current_schedule( array( 'position' => $position ) ) ) {
        wp_cache_set( $cache_key, false );
        return false;
    }

    $i = 0;
    $posts = array();
    foreach ( $schedule->posts as $p ) {
        $posts[ $i++ ] = $p;
    }

    wp_cache_set( $cache_key, $posts );
    return $posts;
}

function wp_diagram_get_query( $position ) {
    if ( ! $posts = wp_diagram_get_posts( $position ) )
        return false;

    $query = new WP_Query();
    $query->current_post = -1;
    $query->post_count = count( $posts );
    $query->posts = $posts;

    return $query;
}

function wp_diagram_query( $position ) {
    global $wp_query;

    if ( ! $query = wp_diagram_get_query( $position ) )
        return false;

    $wp_query = $query;
}
