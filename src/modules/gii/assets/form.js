$(function() {
    // AJAX request to generator action
    $('.form-control[data-ajax-action][data-ajax-target]').on('change', function () {
        const $field = $(this),
            $target = $($field.data('ajax-target'));
        if ($target.length < 1) {
            throw new Error('Target element not exists');
        }
        const targetIsField = $.inArray($target.tagName, ['INPUT', 'SELECT']) === -1;
        if (!targetIsField || $target.val() === '') {
            $.post(
                $field.data('ajax-action'),
                $field.parents('form').serializeArray(),
                function (response) {
                    if (targetIsField) {
                        $target.val(response).blur();
                    } else {
                        $target.html(response);
                    }
                }
            );
        }
    });
});
