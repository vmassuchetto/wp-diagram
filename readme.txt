=== WP-Diagram ===

Contributors: viniciusmassuchetto
Donate link: http://vinicius.soylocoporti.org.br
Tags: posts, positioning, schedulling
Requires at least: 3.0
Tested up to: 3.4.2
Stable tag: 0.01

Allow users to schedule custom post loops for different positions of the theme in a widget-like admin interface. Theme developers can display these loops at any place of the theme. It also provides the hability to override the post title and excerpt for each post.

== Description ==

In commercial theme development it's common to have different post listings across several positions of the theme. We usually end up creating post types and specific categories for users to put posts in these positions.

This plugin creates a dedicated interface for post positioning and schedulling in the theme. For example, assume you want two different post listings, one for a top slideshow, and another for featured news:

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

Then, in your templates just use `wp_diagram_get_query( 'position_id' )` to call the scheduled posts:

<?php $slideshow = wp_diagram_get_query( 'slideshow' ); ?>
<?php if ( $slideshow->have_posts() ) : ?>
    <?php while( $slideshow->have_posts() ) : $slideshow->the_post(); ?>

        <?php // Post loop like any other ?>

    <?php endwhile; ?>
<?php endif; ?>

== Screenshots ==

1. This is the original image.
2. Resizing to 300x300px: `get_post_image ('w=300&h=300');`
3. Adjusting brightness: `get_post_image ('w=200&zc=1&fltr[]=brit|20');`
4. Flip vertically: `get_post_image ('w=200&zc=1&fltr[]=flip|y');`
5. Text: `get_post_image ('w=200&zc=1&fltr[]=wmt|I\\''m a Camel! &#169;|16|C|FFFFFF|/path/to/liberation-sans.ttf|100|10|0|000000|80|x');`
6. Watermak image: `get_post_image ('w=200&zc=1&fltr[]=wmi|/path/to/camel-logo.png|T|50|5|5|320');`
7. Applying mask: `get_post_image ('w=200&zc=1&fltr[]=mask|/path/to/camel-abstract.png&f=png');`
8. Mask applyed

== Changelog ==

= 0.05 =

* Removing get_the_image dependency
* Converting images on the server side
* Adding the security password
* Improving the calling method

= 0.04 =

* phpThumb update to fix security issues;
* Displaying nice error messages instead of breaking execution.

= 0.03 =

* Minor changes to make it work properly on Windows servers.

= 0.02 =

* Changed the way of calling the plugin, now it's done via an array of options. See the documentation for more info;
* Now it works with multisite. There's no absolute way of make it work. Some servers and websites will need a different parse to get the images path;
* There's a verification to check if "get-the-image" is also installed.

= 0.01 =

* Plugin released. Don't expect multisite to work.
