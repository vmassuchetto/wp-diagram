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
        include( $this->plugin_dir_path . 'admin/positions.php' );
    }

    /* General Functions */

    function get_schedule( $args = false ) {
        $defaults = array(
            'position' => false,
            'schedule' => false
        );
        $args = wp_parse_args( $args, $defaults );

        global $wpdb;

        if ( ! empty( $args['schedule'] ) ) {
            $where = $wpdb->prepare( "
                AND ID = '%s'
            ", $args['schedule'] );

        } elseif ( ! empty( $args['position'] ) ) {
            $where = $wpdb->prepare( "
                AND post_type = '%s'
                AND post_title = '%s'
                AND post_date > NOW()
            ", $this->type_schedule, $args['position'] );
        }

        if ( ! $where )
            return false;

        $sql = "
            SELECT
                ID AS id,
                post_title AS position,
                post_content AS posts,
                post_date AS date
            FROM
                $wpdb->posts
            WHERE 1=1 {$where}
            ORDER BY date ASC
        ";
        if ( ! $schedule = $wpdb->get_results( $sql, OBJECT_K ) )
            return false;

        if ( ! empty( $args['schedule'] ) )
            return $schedule[ key( $schedule ) ];
        return $schedule;
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
            || ! $time = strtotime($args['date']) )
            exit;

        $postargs = array(
            'post_type' => $this->type_schedule,
            'post_title' => $args['position'],
            'post_date' => $args['date'],
            'post_status' => 'publish',
            'post_content' => json_encode( array() ),
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
            || ! $schedule = $this->get_schedule( array( 'schedule' => $args['schedule'] ) ) )
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
            || ! $schedule = $this->get_schedule( array( 'schedule' => $args['schedule'] ) ) )
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
            || ! $schedule = $this->get_schedule( array( 'schedule' => $args['schedule'] ) ) )
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
}
add_action( 'plugins_loaded', 'wp_diagram_init' );

function wp_diagram_register_positions( $positions = array() ) {
    global $wp_diagram;

    if ( ! class_exists( 'WP_Diagram' )
        || 'WP_Diagram' !== get_class( $wp_diagram ) )
        WP_Diagram::error_fatal( __( 'Plugin instantiation error.', 'wp_diagram' ) );

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
