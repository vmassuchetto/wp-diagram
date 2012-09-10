<?php global $wp_diagram; ?>
<div class="wrap wp-diagram">
    <div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
    <h2><?php _e( 'Post Positions', 'wp_diagram' ); ?></h2>

    <?php if ( ! empty( $wp_diagram->positions ) ) : ?>

        <div id="poststuff">
        <div id="post-body" class="metabox-holder columns-1">
        <div id="post-body-content">

        <?php foreach($wp_diagram->positions as $position) : ?>
            <div id="position-<?php echo $position['id']; ?>-wrap"
                class="position-wrap">
                <?php include( $wp_diagram->plugin_dir_path . '/admin/position.php' ); ?>
            <div>
        <?php endforeach; ?>

        </div><!-- #post-body-content -->
        </div><!-- #post-body -->
        </div><!-- #poststuff -->

    <?php else : ?>

        <div class="updated"><p>
            <?php _e( sprintf( 'No positions declared in your theme. Please read
                the <a href="%s">documentation</a> to see how to
                declare them.', $wp_diagram->plugin_docs_url ), 'wp_diagram' ); ?>
        </p></div>

    <?php endif; ?>

</div>
