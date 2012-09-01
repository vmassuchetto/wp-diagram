<?php global $wp_diagram; ?>
<div class="wrap">
    <div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
    <h2><?php _e( 'Post Positions', 'wp_diagram' ); ?></h2>

    <p>

    <?php if ( ! empty( $wp_diagram->pages ) ) : ?>

        <?php _e( 'Pages', 'wp_diagram' ); ?> <select name="pages">
        <?php foreach( $wp_diagram->pages as $page ) : ?>
            <option value="<?php echo $page['id']; ?>"><?php echo $page['name']; ?></option>
        <?php endforeach; ?>
        </select>

    <?php else : ?>

        <div class="updated"><p>
            <?php _e( 'No pages declared in your template. Please read
                the documentation to check how to declare them.', 'wp_diagram' ); ?>
        </p></div>

    <?php endif; ?>

    </p>

</div>
