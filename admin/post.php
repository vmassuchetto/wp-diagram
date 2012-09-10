<li id="post-<?php echo $p->ID; ?>" valign="top"
    class="post<?php echo ( $i % 2 ) ? ' alt' : ''; ?> format-standard">

<div class="thumbnail">
    <?php
        if ( has_post_thumbnail( $p->ID ) )
            the_post_thumbnail( array( 64, 64 ) );
    ?>
</div>

<div>
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

</li>

