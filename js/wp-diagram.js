var values;
values = {};
function wp_diagram_add_post(post, position) {
    jQuery.ajax(

    );
}

jQuery(document).ready(function(){

    jQuery('.position-add-post').each(function(){

        var ac = jQuery(this).autocomplete({
            source: ajaxurl + '?action=wp_diagram_post_search',
            select: function(e, ui){
                position = jQuery(this).attr('name').match(/^position-(.+)-add-post$/);
                position_id = position[1];
                wp_diagram_add_post(ui.item.id, position[1]);
            }
        });

        jQuery(this).focus(function(){
            if (!values.hasOwnProperty(jQuery(this).attr('name')))
                values[jQuery(this).attr('name')] = jQuery(this).val();
            if (jQuery(this).val() == values[jQuery(this).attr('name')])
                jQuery(this).val('');
        });

        jQuery(this).blur(function(){
            if (jQuery(this).val() === '')
                jQuery(this).val(values[jQuery(this).attr('name')]);
        });

    });

    jQuery('.position-add-schedule').each(function(){

        jQuery(this).click(function(){
        });

    });

});
