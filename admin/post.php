<li id="post-<?php the_ID(); ?>" valign="top"
    class="post<?php echo ( $i % 2 ) ? ' alt' : ''; ?> format-standard">

<div class="thumbnail">
    <?php if ( has_post_thumbnail( get_the_ID() ) ) : ?>
        <div class="thumbnail-icon">
            <a
                id="post-<?php the_ID(); ?>-thumbnail-icon"
                class="post-thumbnail-icon post-thumbnail-icon-enabled"
                href="javascript:void(0)"></a>
        </div>
        <div class="thumbnail-preview">
            <?php the_post_thumbnail( array( 150, 150 ) ); ?>
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

<div class="info">
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

<?php /*
<div class="info inline-edit-row">
    <form
        id="position-<?php echo $position['id']; ?>-post-<?php echo $p->ID; ?>-edit"
        class="position-post-edit">
    <fieldset>

    <label>
        <span class="title"><?php _e( 'Title' ); ?></span>
		<span class="input-text-wrap">
            <input type="text" value="" class="ptitle" name="post_title">
            <small><?php _e( 'Original Title', 'wp_diagram' ); ?>:&nbsp;<?php the_title(); ?></small>
        </span>
	</label>

    <label>
        <span class="title"><?php _e( 'Excerpt' ); ?></span>
		<span class="input-text-wrap">
            <input type="text" value="" name="post_name">
            <small><?php _e( 'Original Excerpt', 'wp_diagram' ); ?>:&nbsp;<?php the_excerpt(); ?></small>
        </span>
	</label>

    </fieldset>
    </form>
</div>
*/ ?>

</li>

