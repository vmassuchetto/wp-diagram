var green = '#D2FFCE';
var yellow = '#FFE49A';
var red = '#FF9AA3';
var grey = '#DDDDDD';
var values;
values = {};

function wp_diagram_add_post(post, position) {
    schedule = jQuery('#position-' + position + '-select-schedule').val();
    jQuery.ajax({
        url: ajaxurl,
        type: 'POST',
        data: {
            action: 'wp_diagram_add_post',
            post: post,
            position: position,
            schedule: schedule
        },
        success: function() {
            wp_diagram_update_position(schedule, position, green);
        }
    });
}

function wp_diagram_add_schedule(date, position) {
    jQuery.ajax({
        url: ajaxurl,
        type: 'POST',
        data: {
            action: 'wp_diagram_add_schedule',
            date: date,
            position: position
        },
        success: function(schedule) {
            if (schedule)
                wp_diagram_update_position(schedule, position, green);
        }
    });
}

function wp_diagram_copy_schedule(date, schedule, position) {
    jQuery.ajax({
        url: ajaxurl,
        type: 'POST',
        data: {
            action: 'wp_diagram_copy_schedule',
            date: date,
            position: position,
            schedule: schedule
        },
        success: function(new_schedule) {
            if (new_schedule)
                wp_diagram_update_position(new_schedule, position, green);
        }
    });
}

function wp_diagram_delete_schedule(schedule, position) {
    jQuery.ajax({
        url: ajaxurl,
        type: 'POST',
        data: {
            action: 'wp_diagram_delete_schedule',
            schedule: schedule
        },
        success: function() {
            wp_diagram_update_position(false, position, red);
        }
    });
}

function wp_diagram_delete_post(schedule, post, position) {
    jQuery.ajax({
        url: ajaxurl,
        type: 'POST',
        data: {
            action: 'wp_diagram_delete_post',
            schedule: schedule,
            post: post
        },
        success: function() {
            wp_diagram_update_position(schedule, position, red);
        }
    });
}

function wp_diagram_update_position(schedule, position, color) {
    jQuery.ajax({
        url: ajaxurl,
        type: 'POST',
        data: {
            action: 'wp_diagram_update_position',
            schedule: schedule,
            position: position
        },
        success: function(data){
            obj = jQuery('#position-' + position + '-wrap');
            obj.html(data);
            wp_diagram_blink_position(position, color);
        }
    });
}

function wp_diagram_blink_position(position, color) {
    if (!color)
        color = grey;
    jQuery('#position-' + position + ' .misc-pub-section').effect(
        'highlight',
        { color: color},
        1000
    );
}

function wp_diagram_change_order(position) {
    var order = [];
    schedule = jQuery('#position-' + position + '-select-schedule').val();
    item = jQuery('#position-' + position + ' li:first');
    for (i = 0; item.length; i++) {
        matches = item.attr('id').match(/^post-(.+)$/);
        order[i] = matches[1];
        item = item.next('li');
    }
    order = order.join(',');
    jQuery.ajax({
        url: ajaxurl,
        type: 'POST',
        data: {
            action: 'wp_diagram_change_order',
            schedule: schedule,
            order: order
        }
    });
}

function wp_diagram_position_triggers(position) {
    wp_diagram_position_autocomplete(position);
    jQuery('#position-' + position + ' .post').hover(function(){
        jQuery(this).find('.row-actions').css('visibility', 'visible');
    },function(){
        jQuery(this).find('.row-actions').css('visibility', 'hidden');
    });
    jQuery('#position-' + position).sortable({
        items: 'li',
        placeholder: 'ui-state-highlight',
        update: function(e, ui) {
            wp_diagram_change_order(position);
        }
    });
}

function wp_diagram_position_autocomplete(position) {
    var e = jQuery('#position-' + position + '-add-post');
    var ac = jQuery(e).autocomplete({
        source: ajaxurl + '?action=wp_diagram_search_post',
        select: function(e, ui){
            position = jQuery(this).attr('name').match(/^position-(.+)-add-post$/);
            position_id = position[1];
            wp_diagram_add_post(ui.item.id, position[1]);
        }
    });
    jQuery(e).focus(function(){
        if (!values.hasOwnProperty(jQuery(this).attr('name')))
            values[jQuery(this).attr('name')] = jQuery(this).val();
        if (jQuery(this).val() == values[jQuery(this).attr('name')])
            jQuery(this).val('');
    });
    jQuery(e).blur(function(){
        if (jQuery(this).val() === '')
            jQuery(this).val(values[jQuery(this).attr('name')]);
    });
}

jQuery(document).ready(function(){

    /* Schedule */

    jQuery('.position-add-schedule').live('click', function(){
        matches = jQuery(this).attr('id').match(/^position-(.+)-add-schedule$/);
        jQuery('#position-' + matches[1] + '-datetime').slideDown(200);
    });

    jQuery('.position-select-schedule').live('change', function(){
        matches = jQuery(this).attr('id').match(/^position-(.+)-select-schedule$/);
        schedule = jQuery(this).val();
        wp_diagram_update_position(schedule, matches[1]);
    });

    jQuery('.position-datetime-wrap .save-timestamp').live('click', function(){
        wrap = jQuery(this).parents('.position-datetime-wrap');
        matches = wrap.attr('id').match(/^position-(.+)-datetime-wrap$/);
        year = wrap.find('#aa').val();
        month = wrap.find('#mm').val();
        day = wrap.find('#jj').val();
        hour = wrap.find('#hh').val();
        minute = wrap.find('#mn').val();
        date = year + '-' + month + '-' + day + ' ' + hour + ':' + minute + ':00';
        wp_diagram_add_schedule(date, matches[1]);
    });

    jQuery('.position-copy-schedule').live('click', function(){
        matches = jQuery(this).attr('id').match(/^position-(.+)-copy-schedule$/);
        schedule = jQuery('#position-' + matches[1] + '-select-schedule').val();
        wrap = jQuery(this).parents('.position-datetime-wrap');
        year = wrap.find('#aa').val();
        month = wrap.find('#mm').val();
        day = wrap.find('#jj').val();
        hour = wrap.find('#hh').val();
        minute = wrap.find('#mn').val();
        date = year + '-' + month + '-' + day + ' ' + hour + ':' + minute + ':00';
        wp_diagram_copy_schedule(date, schedule, matches[1]);
    });

    jQuery('.position-delete-schedule').live('click', function(){
        matches = jQuery(this).attr('id').match(/^position-(.+)-delete-schedule$/);
        schedule = jQuery('#position-' + matches[1] + '-select-schedule').val();
        wp_diagram_delete_schedule(schedule, matches[1]);
    });

    /* Post */

    jQuery('.position-add-post').each(function(){
        matches = jQuery(this).attr('id').match(/^position-(.*)-add-post$/);
        wp_diagram_position_autocomplete(matches[1]);
    });

    jQuery('.position-delete-post').live('click', function(){
        matches = jQuery(this).attr('id').match(/^position-(.+)-delete-post-(.+)$/);
        schedule = jQuery('#position-' + matches[1] + '-select-schedule').val();
        wp_diagram_delete_post(schedule, matches[2], matches[1]);
    });

    jQuery('.post-thumbnail-icon-enabled').live('mouseover', function(){
        preview = jQuery(this).parent().parent().find('.thumbnail-preview');
        preview.fadeIn(200);
    });

    jQuery('.position-edit-post').live('click', function(){
        post = jQuery(this).parents('.post');
        post.find('.post-info').fadeOut(200);
        post.find('.inline-edit-row').delay(200).fadeIn(200);
    });

    jQuery('.position-post-edit-cancel').live('click', function(){
        post = jQuery(this).parents('.post');
        post.find('.inline-edit-row').fadeOut(200);
        post.find('.post-info').delay(200).fadeIn(200);
    });

    jQuery('.position-post-edit-save').live('click', function(){
        matches = jQuery(this).attr('id').match(/position-(.+)-post-(.+)-edit-save/);
        form = jQuery('#position-' + matches[1] + '-post-' + matches[2] + '-edit');
        schedule = jQuery('#position-' + matches[1] + '-select-schedule').val();
        jQuery.ajax({
            url: ajaxurl,
            type: 'POST',
            data: form.serialize(),
            success: function(){
                wp_diagram_update_position(schedule, matches[1], green);
            }
        });
    });

    jQuery('.thumbnail-preview').live('mouseout', function(){
        jQuery(this).fadeOut(200);
    });

});
