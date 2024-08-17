jQuery(document).ready(function($) {
    var frame;

    $('#upload_images').on('click', function(e) {
        e.preventDefault();

        if (frame) {
            frame.open();
            return;
        }

        frame = wp.media({
            title: 'Select or Upload Images',
            button: {
                text: 'Use these images'
            },
            multiple: true
        });

        frame.on('select', function() {
            var attachments = frame.state().get('selection').toJSON();
            var container = $('#image_container');

            attachments.forEach(function(attachment) {
                var template = $('<div class="image-row">' +
                    '<input type="hidden" name="image_ids[]" class="image-id" value="' + attachment.id + '">' +
                    '<input type="text" name="image_sizes[]" placeholder="Image size (e.g., 300x200)">' +
                    '<span class="preview-image"><img src="' + attachment.url + '" style="max-width:100px;max-height:100px;"></span>' +
                    '<button class="remove-image button">Remove</button>' +
                    '</div>');
                container.append(template);
            });
        });

        frame.open();
    });

    $(document).on('click', '.remove-image', function(e) {
        e.preventDefault();
        $(this).closest('.image-row').remove();
    });
});