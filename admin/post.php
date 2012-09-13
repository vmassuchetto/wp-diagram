<li id="post-<?php the_ID(); ?>" valign="top"
    class="post<?php echo ( $i % 2 ) ? ' alt' : ''; ?> format-standard">

<div class="thumbnail">
    <?php if ( strlen( $img = get_the_post_thumbnail( get_the_ID(), array( 150, 150 ) ) ) ) : ?>
        <div class="thumbnail-icon">
            <a
                id="post-<?php the_ID(); ?>-thumbnail-icon"
                class="post-thumbnail-icon post-thumbnail-icon-enabled"
                href="javascript:void(0)"></a>
        </div>
        <div class="thumbnail-preview">
            <?php echo $img; ?>
        </div>
    <?php else : ?>
        <div class="thumbnail-icon">
            <a
                id="post-<?php the_ID() ?>-thumbnail-icon"
                class="post-thumbnail-icon post-thumbnail-icon-disabled"
                href="javascript:void(0)"></a>
        </div>
    <?php endif; ?>
</div>
<div class="info post-info">
    <span class="position-post-title"><strong><?php the_title(); ?></strong></span>
    <span class="position-post-excerpt"><?php the_excerpt(); ?></span>
    <div class="row-actions">
        <span class="position-post-edit">
            <a id="position-<?php echo $position['id']; ?>-edit-post-<?php the_ID(); ?>"
                class="position-edit-post"
                href="javascript:void(0);">
                <?php _e( 'Edit Post Position', 'wp_diagram' ); ?></a>&nbsp;|
        </span>
        <span class="edit"><?php edit_post_link( __( 'Edit Post', 'wp_diagram' ) ); ?> | </span>
        <span class="trash">
            <a id="position-<?php echo $position['id']; ?>-delete-post-<?php the_ID(); ?>"
                class="position-delete-post"
                href="javascript:void(0);">
                <?php _e( 'Remove from scheduling', 'wp_diagram' ); ?></a>&nbsp;|
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

<div class="info inline-edit-row">
    <form
        id="position-<?php echo $position['id']; ?>-post-<?php the_ID(); ?>-edit"
        class="position-post-edit">
    <fieldset>

    <label>
        <span class="title"><?php _e( 'Title' ); ?></span>
        <span class="input-text-wrap">
            <input type="text" value="<?php echo $post->post_title; ?>" class="ptitle" name="post_title">
            <?php if ( ! empty( $post->original_post_title ) ) : ?>
                <small><?php _e( 'Original Title', 'wp_diagram' ); ?>:&nbsp;<?php echo apply_filters( 'the_title', $post->original_post_title ); ?></small>
            <?php endif; ?>
        </span>
    </label>

    <label>
        <span class="title"><?php _e( 'Excerpt' ); ?></span>
        <span class="input-text-wrap">
            <textarea name="post_excerpt"><?php echo $post->post_excerpt; ?></textarea>
            <?php if ( ! empty( $post->original_post_excerpt ) ) : ?>
                <small><?php _e( 'Original Excerpt', 'wp_diagram' ); ?>:&nbsp;<?php echo apply_filters( 'the_excerpt', $post->original_post_excerpt ); ?></small>
            <?php endif; ?>
        </span>
    </label>

    <input type="hidden" name="schedule" value="<?php echo $selected_schedule; ?>" />
    <input type="hidden" name="post_id" value="<?php the_ID(); ?>" />
    <input type="hidden" name="action" value="wp_diagram_save_post" />

    <p class="submit inline-edit-save">
        <a class="button-secondary position-post-edit-cancel"
            title="<?php _e( 'Cancel' ); ?>"
            href="javascript:void(0);"><?php _e( 'Cancel' ); ?></a>

        <a
            id="position-<?php echo $position['id']; ?>-post-<?php the_ID(); ?>-edit-save"
            class="button-primary save alignright position-post-edit-save"
            title="<?php _e( 'Update' ); ?>"
            href="javascript:void(0);"><?php _e( 'Update' ); ?></a>
    </p>

    </fieldset>
    </form>
</div>

</li>
