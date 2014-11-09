(function($, window, undefined) { // iffy scope

    $(function() { // docready

        var $form = $("#pwr-form");
        var $toggleAll = $form.find('.toggle-all');

        //WireTabs
        $form.WireTabs({
            items : $form.find('> .Inputfields > .InputfieldWrapper'),
            rememberTabs: 1
        });

        $toggleAll.click(function(evt){
            evt.stopPropagation();
            if ($(this).prop('checked')) {
                $form.find('.toggle').not(":disabled").prop('checked', true);
            } else {
                $form.find('.toggle').not(":disabled").prop('checked', false);
            }
        });

        if ($form.find('.toggle:checked').length === 0) {
            $toggleAll.prop('checked', false);
        }
    });

})(jQuery, window);
