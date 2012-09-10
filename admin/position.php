<?php
    $posts = false;
    $schedule = $wp_diagram->get_schedule( array( 'position' => $position['id'] ) );
    if ( empty( $selected_schedule ) && ! empty( $schedule ) && ! empty( $schedule[ key( $schedule ) ] ) )
        $selected_schedule = $schedule[ key( $schedule ) ]->id;
    if ( ! empty( $selected_schedule ) && ! empty( $schedule[ $selected_schedule ]->posts ) )
        $posts = json_decode( $schedule[ $selected_schedule ]->posts );
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
                    <?php echo mysql2date('j M Y H:i', $s->date ); ?>
                </option>
            <?php endforeach; ?>
        </select>

    <?php endif; ?>

    <a id="position-<?php echo $position['id']; ?>-add-schedule"
        class="button position-add-schedule">
        <?php _e( 'Add New Scheduling', 'wp_diagram'); ?></a>
    <div id="position-<?php echo $position['id']; ?>-datetime-wrap"
        class="position-datetime-wrap">
    <div id="position-<?php echo $position['id']; ?>-datetime"
        class="position-datetime">
        <?php
            global $post;
            $post = (object) array(
                'post_status' => 'publish',
                'post_date' => date( 'Y-m-d H:i:s' ),
                'post_date_gmt' => date( 'Y-m-d H:i:s' )
            );
            touch_time(true, true);
        ?>
    </div><!-- .position-datetime -->
    </div><!-- .position-datetime-wrap -->

    <a id="position-<?php echo $position['id']; ?>-delete-schedule"
        class="deletion position-delete-schedule"
        href="javascript:void(0);">
        <?php _e( 'Delete This Scheduling', 'wp_diagram'); ?></a>

</div><!-- .misc-pub-section -->

<?php if ( $posts ) : ?>
    <ul class="posts">
        <?php $i = 0; foreach( $posts as $p ) : $i++; ?>
            <?php if ( ! $post = get_post( $p->ID ) ) continue; ?>
            <li id="post-<?php echo $p->ID; ?>" valign="top"
                class="post<?php echo ( $i % 2 ) ? ' alt' : ''; ?> format-standard">

            <div class="thumbnail">
                <?php
                    if ( has_post_thumbnail( $p->ID ) )
                        the_post_thumbnail( array( 64, 64 ) );
                ?>
            </div>

            <div>
                <strong><?php the_title(); ?></strong>
                <div class="row-actions">
                    <span class="edit"><?php edit_post_link(); ?> | </span>
                    <span class="trash">
                        <a id="position-<?php echo $position['id']; ?>-delete-post-<?php the_ID(); ?>"
                            class="position-delete-post"
                            href="javascript:void(0);">
                            <?php _e( 'Remove from scheduling', 'wp_diagram' ); ?>
                        </a>&nbsp;|&nbsp;
                    </span>
                    <span class="view">
                        <a rel="permalink"
                            title="<?php the_title(); ?>"
                            href="<?php the_permalink(); ?>">
                            <?php _e( 'View post', 'wp_diagram' ); ?>
                        </a>
                    </span>
                </div>
            </div>

            </li>
        <?php endforeach; ?>
    </table>
<?php endif; ?>
</div><!-- .postbox -->
<script type="text/javascript">wp_diagram_position_triggers('<?php echo $position['id']; ?>');</script>
