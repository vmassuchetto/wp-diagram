<?php
/*
* Plugin Name: WP-Diagram
* Plugin URI:
* Description:
* Version: 0.01
* Author: Vinicius Massuchetto
* Author URI: http://vinicius.soylocoporti.org.br/wp-diagram-wordpress-plugin
*/

class WP_Diagram {

    var $plugin_basename;
    var $plugin_dir_path;
    var $errors;
    var $pages;

    function WP_Diagram() {

        $this->plugin_basename = plugin_basename( __FILE__ );
        $this->plugin_dir_path = plugin_dir_path( __FILE__ );
        $this->errors = array();
        $this->pages = array();

        if ( is_admin() ) {
            add_action( 'admin_menu', array( $this, 'admin_menu' ) );
        }

    }

    function admin_menu() {
        add_menu_page( __( 'Diagramming', 'wp_diagram' ), __( 'Diagramming', 'wp_diagram' ),
            'add_users', 'wp_diagram', array( $this, 'admin_post_positions' ), false, 6);
        //add_submenu_page( 'wp_diagram', __( 'Post Positions', 'wp_diagram' ), __( 'Templates', 'wp_diagram' ),
            //'add_users', 'wp_diagram_templates', array( $this, 'admin_templates' ) );
    }

    function admin_post_positions() {
        include( $this->plugin_dir_path . 'admin/post-positions.php' );
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

function wp_diagram_register_pages( $pages = array() ) {
    global $wp_diagram;

    if ( ! class_exists( 'WP_Diagram' )
        || 'WP_Diagram' !== get_class( $wp_diagram ) )
        WP_Diagram::error_fatal( __( 'Plugin instantiation error.', 'wp_diagram' ) );

    if ( empty( $pages ) || ! is_array( $pages ) )
        $wp_diagram->error_fatal( __( 'No parameter set.', 'wp_diagram' ) );

    $required = array( 'id', 'name', 'front_templates', 'admin_templates' );

    foreach( $pages as $page ) {

        foreach ( $required as $r )
            if ( empty ( $page[ $r ] ) )
                $wp_diagram->error_fatal( sprintf( __( 'Missing "%s" argument on page registration.', 'wp_diagram' ), $r ) );

        if ( count( $page['front_templates']) != count( $page['admin_templates'] ) )
            $wp_diagram->error_fatal( __( 'Wrong array count for "front_templates"
                and "admin_templates on page registration. Please register arrays
                from same sizes in both options.', 'wp_diagram' ) );

        foreach( array_merge( $page['front_templates'], $page['admin_templates'] ) as $template )
            if ( ! locate_template( $template ) )
                $wp_diagram->error_fatal( sprintf( __('Could not find the "%s.php" template file.', 'wp_diagram' ), $template ) );

    }

    // Valid pages declaration
    $wp_diagram->pages = $pages;
}
