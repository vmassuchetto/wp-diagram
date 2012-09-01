<?php global $wp_diagram; ?>
<div class="wrap">
    <div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
    <h2><?php _e( 'Post Positions', 'wp_diagram' ); ?></h2>

    <?php if ( ! empty( $wp_diagram->positions ) ) : ?>

        <div id="poststuff">
        <div id="post-body" class="metabox-holder columns-2">

        <div id="post-body-content">

        <?php foreach($wp_diagram->positions as $position) : ?>
            <div class="stuffbox" id="position-<?php echo $position['id']; ?>">
            <h3><label for="link_name"><?php echo $position['name']; ?></label></h3>
            <div class="inside">
                <div class="drag-here"></div>
            </div>
            </div>
        <?php endforeach; ?>

        </div><!-- #post-body-content -->

        <div class="postbox-container" id="postbox-container-1">
        <div class="meta-box-sortables ui-sortable" id="side-sortables">

            <div class="postbox">
            <h3 class="hndle"><span><?php _e( 'Search Posts', 'wp_diagram' ); ?></span></h3>
            <div class="inside">
                <input type="text" name="post-search" />
            </div>
            </div>

        </div>
        </div>

        </div><!-- #post-body -->
        </div><!-- #poststuff -->

    <?php else : ?>

        <div class="updated"><p>
            <?php _e( 'No positions declared in your theme. Please read
                the documentation to check how to declare them.', 'wp_diagram' ); ?>
        </p></div>

    <?php endif; ?>

</div>
