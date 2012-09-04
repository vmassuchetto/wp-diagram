<div class="postbox" id="position-<?php echo $position['id']; ?>">
<h3><label for="link_name"><?php echo $position['name']; ?></label></h3>

<div class="misc-pub-section">

    <?php _e( 'Add Post', 'wp_diagram' ); ?>:&nbsp;
    <input id="position-<?php echo $position['id']; ?>-add-post"
        type="text"
        class="position-add-post"
        name="position-<?php echo $position['id']; ?>-add-post"
        value="<?php _e( 'Search by post title', 'wp_diagram' ); ?>" />

    <?php _e( 'Schedule', 'wp_diagram' ); ?>&nbsp;
    <select id="position-<?php echo $position['id']; ?>-select-schedule"
        class="position-select-schedule"
        name="position-<?php echo $position['id']; ?>-schedule">
    </select>

    <a id="position-<?php echo $position['id']; ?>-add-schedule"
        class="button position-add-schedule">
        <?php _e( 'Add New Schedule', 'wp_diagram'); ?></a>

    <a id="position-<?php echo $position['id']; ?>-delete-schedule"
        class="deletion position-delete-schedule">
        <?php _e( 'Delete This Schedule', 'wp_diagram'); ?></a>

</div>

<div class="inside">
</div><!-- .inside -->
</div><!-- .stuffbox -->