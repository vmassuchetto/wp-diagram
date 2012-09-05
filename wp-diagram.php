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

            $ajax_actions = array( 'post_search', 'add_schedule', 'update_position' );
            foreach( $ajax_actions as $a )
                add_action( 'wp_ajax_wp_diagram_' . $a, array( $this, $a ) );
        }

    }

    /* Plugin Structure */

    function error_fatal( $error_message ) {
        $error = new WP_Error( 'wp_diagram', 'wp_diagram: ' . $error_message );
        wp_die( $error );
    }

    function register_structure() {
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
            wp_enqueue_script( 'jquery-ui-datepicker' );
            wp_enqueue_script( 'jquery-ui-slider' );
            wp_enqueue_script( 'jquery-ui-timepicker',
                $this->plugin_dir_url . 'js/jquery-ui-timepicker-addon.js',
                array( 'jquery-ui-core', 'jquery-ui-slider', 'jquery-ui-datepicker' ) );
            wp_enqueue_script( 'wp-diagram', $this->plugin_dir_url . 'js/wp-diagram.js' );

            wp_enqueue_style( 'jquery-style', $this->plugin_dir_url . 'css/jquery-ui.css' );
            wp_enqueue_style( 'wp-diagram', $this->plugin_dir_url . 'css/wp-diagram.css' );
        }
    }

    function admin_menu() {
        add_menu_page( __( 'Diagramming', 'wp_diagram' ), __( 'Diagramming', 'wp_diagram' ),
            'add_users', 'wp_diagram', array( $this, 'admin_post_positions' ), false, 6 );
    }

    function admin_post_positions() {
        include( $this->plugin_dir_path . 'admin/post-positions.php' );
    }

    /* General Functions */

    function get_schedule( $position ) {
        if (empty ($position) )
            return false;
        $position = sanitize_text_field( $position );

        global $wpdb;

        $sql = $wpdb->prepare("
            SELECT
                ID AS id,
                post_title AS position,
                post_content AS posts,
                post_date AS date
            FROM
                $wpdb->posts
            WHERE 1=1
                AND post_type = '%s'
                AND post_title = '%s'
                AND post_date > NOW()
            ORDER BY date ASC
        ", $this->type_schedule, $position );
        if ( $schedule = $wpdb->get_results( $sql ) )
            return $schedule;
        return false;
    }

    /* Ajax Actions */

    function post_search() {
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
                'value' => ''
            );
        }
        echo json_encode( $response );

        exit;
    }

    function error_fatal( $error_message ) {
        $error = new WP_Error( 'wp_diagram', 'wp_diagram: ' . $error_message );
        wp_die( $error );
    }

}

function wp_diagram_init() {
    global $wp_diagram;
    $wp_diagram = new WP_Diagram();
}
add_action( 'plugins_loaded', 'wp_diagram_init' );

function wp_diagram_register_positions( $positions = array() ) {
    global $wp_diagram;

    if ( ! class_exists( 'WP_Diagram' )
        || 'WP_Diagram' !== get_class( $wp_diagram ) )
        WP_Diagram::error_fatal( __( 'Plugin instantiation error.', 'wp_diagram' ) );

    if ( empty( $positions ) || ! is_array( $positions ) )
        $wp_diagram->error_fatal( __( 'No parameter set for position declaration.', 'wp_diagram' ) );

    $required = array( 'id', 'name' );

    foreach( $positions as $position ) {
        foreach ( $required as $r )
            if ( empty ( $position[ $r ] ) )
                $wp_diagram->error_fatal( sprintf( __( 'Missing "%s" argument on position registration.', 'wp_diagram' ), $r ) );
    }

    // Valid positions
    $wp_diagram->positions = $positions;

}
