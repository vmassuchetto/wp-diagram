=== WP-Diagram ===

Contributors: viniciusmassuchetto
Donate link: http://vinicius.soylocoporti.org.br
Tags: posts, positioning, scheduling
Requires at least: 3.0
Tested up to: 3.4.2
Stable tag: 0.01

Allow users to schedule custom post loops for different positions of the theme in a widget-like admin interface. Theme developers can display these loops at any place of the theme. It also provides the hability to override the post title and excerpt for each post.

== Description ==

In commercial theme development it's common to have different post listings across several positions of the theme. We usually end up creating post types and specific categories for users to put posts in these positions.

This plugin creates a dedicated interface for post positioning and scheduling in the theme. For example, assume you want two different post listings, one for a top slideshow, and another for featured news:

Register the positions in your `functions.php`:

`wp_diagram_register_positions( array(
    array(
        'id' => 'home_slideshow',
        'name' => 'Home Slideshow'
    ),
    array(
        'id' => 'featured_news',
        'name' => 'Featured News'
    )
) );

Go to the `Positioning` menu in the admin interface and schedule the posts.

Then, in your templates just do:

<?php $slideshow = wp_diagram_get_query( 'home_slideshow' ); ?>
<?php if ( $slideshow->have_posts() ) : ?>
    <?php while( $slideshow->have_posts() ) : $slideshow->the_post(); ?>

        <?php // Post loop like any other ?>

    <?php endwhile; ?>
<?php endif; ?>

== Installation ==

1. Download and activate the plugin.
2. Register the positions in your `functions.php`.
3. Call these positions in your template files.
3. Schedule your posts in the `Positioning` left menu in admin.

== Usage ==

Register the positions in your `functions.php`:

`wp_diagram_register_positions( array(
    array(
        'id' => 'slideshow',  // Only chars and underscores here
        'name' => 'Slideshow' // Any textual name
    ),
    array(
        'id' => 'featured_news',
        'name' => 'Featured News'
    )
) );

Go to the `Positioning` menu in the admin interface and schedule the posts.
1. Click in `Add New Scheduling` and select a date. The posts you will schedule will be displayed only from this date and on.
2. Search the posts you want to add to this scheduling.
3. If desired, change the order by dragging and dropping the posts. You can also change the post title and excerpt for this scheduling.

Then, in your templates just use `wp_diagram_get_query( 'position_id' )` to call the scheduled posts:

<?php $slideshow = wp_diagram_get_query( 'slideshow' ); ?>
<?php if ( $slideshow->have_posts() ) : ?>
    <?php while( $slideshow->have_posts() ) : $slideshow->the_post(); ?>

        <?php // Post loop like any other ?>

    <?php endwhile; ?>
<?php endif; ?>

== Screenshots ==

1. Admin interface.

== Changelog ==

= 0.01 =

* Plugin released.
