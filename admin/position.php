<?php
    $posts = false;
    $schedule = $wp_diagram->get_schedule( array( 'position' => $position['id'] ) );
    $first = ( ! empty( $schedule[ key( $schedule ) ]->id ) ) ? $schedule[ key( $schedule ) ]->id : false;

    if ( empty( $selected_schedule ) && $first )
        $selected_schedule = $first;
    elseif (empty( $selected_schedule ) )
        $selected_schedule = false;

    if ( ! empty( $selected_schedule ) && ! empty( $schedule[ $selected_schedule ]->posts ) )
        $posts = $schedule[ $selected_schedule ]->posts;
?>
<div id="position-<?php echo $position['id']; ?>" class="postbox position">
<h3><label for="link_name"><?php echo $position['name']; ?></label></h3>

<div class="misc-pub-section">

    <?php if ( $schedule ) : ?>

        <?php _e( 'Add Post', 'wp_diagram' ); ?>:&nbsp;
        <input id="position-<?php echo $position['id']; ?>-add-post"
            type="text"
            class="position-add-post"
            name="position-<?php echo $position['id']; ?>-add-post"
            value="<?php _e( 'Search by post title', 'wp_diagram' ); ?>" />

        <?php _e( 'Scheduling', 'wp_diagram' ); ?>:&nbsp;
        <select id="position-<?php echo $position['id']; ?>-select-schedule"
            class="position-select-schedule"
            name="position-<?php echo $position['id']; ?>-schedule">
            <?php foreach( $schedule as $s ) : ?>
                <option value="<?php echo $s->id; ?>"
                    <?php echo ( $selected_schedule == $s->id ) ? ' selected="selected"' : ''; ?>>
                    <?php echo ( ! empty( $s->current ) ) ? _e( 'Displaying Now', 'wp_diagram' ) : mysql2date('j M Y H:i', $s->date ); ?>
                </option>
            <?php endforeach; ?>
        </select>

    <?php endif; ?>

    <a id="position-<?php echo $position['id']; ?>-add-schedule"
        class="button position-add-schedule">
        <?php _e( 'Add New Scheduling', 'wp_diagram'); ?></a>

    <?php if ( $schedule ) : ?>

        <a id="position-<?php echo $position['id']; ?>-delete-schedule"
            class="deletion position-delete-schedule"
            href="javascript:void(0);">
            <?php _e( 'Delete This Scheduling', 'wp_diagram'); ?></a>

    <?php endif; ?>

    <div id="position-<?php echo $position['id']; ?>-datetime-wrap"
        class="position-datetime-wrap">
    <div id="position-<?php echo $position['id']; ?>-datetime"
        class="position-datetime">
        <?php _e( 'Schedule a post position for', 'wp_diagram' ); ?>:&nbsp;
        <?php
            global $post;
            $post = (object) array(
                'post_status' => 'publish',
                'post_date' => date_i18n( 'Y-m-d H:i:s' ),
                'post_date_gmt' => date_i18n( 'Y-m-d H:i:s' )
            );
            touch_time(true, true);
        ?>

        <?php if ( $schedule ) : ?>

            <a id="position-<?php echo $position['id']; ?>-copy-schedule"
                class="position-copy-schedule button-secondary">
                <?php _e( 'Create Copying Current Posts', 'wp_diagram' ); ?></a>

        <?php endif; ?>

    </div><!-- .position-datetime -->
    </div><!-- .position-datetime-wrap -->

</div><!-- .misc-pub-section -->

<?php if ( $posts ) : ?>
    <ul class="posts">
        <?php
            $i = 0;
            foreach( $posts as $post ) {
                setup_postdata( $post );
                include( $this->plugin_dir_path . 'admin/post.php' );
            }
        ?>
    </table>
<?php endif; ?>
</div><!-- .postbox -->
<script type="text/javascript">wp_diagram_position_triggers('<?php echo $position['id']; ?>');</script>
</div><!-- .position -->
<?php unset( $selected_schedule ); ?>
