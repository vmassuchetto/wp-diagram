var values;
values = {};

function wp_diagram_add_post(post, position) {
    jQuery.ajax({

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
        success: function() {
            wp_diagram_update_position(date, position);
        }
    });
}

function wp_diagram_update_position(date, position) {
    jQuery.ajax({
        url: ajaxurl,
        type: 'POST',
        data: {
            action: 'wp_diagram_update_position',
            date: date,
            position: position
        },
        success: function(data){
            obj = jQuery('#position-' + position + '-wrap');
            obj.fadeOut(100).delay(100).html(data).fadeIn(100);
            wp_diagram_position_autocomplete(position);
        }
    });
}

function wp_diagram_position_autocomplete(position) {
    var e = jQuery('#position-' + position + '-add-post');
    var ac = jQuery(e).autocomplete({
        source: ajaxurl + '?action=wp_diagram_post_search',
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

    jQuery('.position-add-post').each(function(){
        matches = jQuery(this).attr('id').match(/^position-(.*)-add-post$/);
        wp_diagram_position_autocomplete(matches[1]);
    });

    jQuery('.position-add-schedule').live('click', function(){
        matches = jQuery(this).attr('id').match(/^position-(.+)-add-schedule$/);
        jQuery('#position-' + matches[1] + '-datetime').fadeIn(200);
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
        wrap.find('.position-datetime').fadeOut(100);
    });

    jQuery('.position-select-schedule').live('change', function(){
        matches = jQuery(this).attr('id').match(/^position-(.+)-select-schedule$/);
        date = jQuery(this).val();
        wp_diagram_update_position(date, matches[1]);
    });

});
