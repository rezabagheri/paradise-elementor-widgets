/**
 * Paradise User Profile — Media uploader for profile photo field.
 */
(function ($) {
    'use strict';

    var frame;

    $(document).on('click', '#paradise-upload-photo', function (e) {
        e.preventDefault();

        if (frame) {
            frame.open();
            return;
        }

        frame = wp.media({
            title: 'Select Profile Photo',
            button: { text: 'Use this photo' },
            multiple: false,
            library: { type: 'image' }
        });

        frame.on('select', function () {
            var attachment = frame.state().get('selection').first().toJSON();
            $('#paradise-profile-photo-id').val(attachment.id);
            $('#paradise-profile-photo-preview').attr('src', attachment.url).show();
            $('#paradise-upload-photo').text('Change Photo');
            $('#paradise-remove-photo').show();
        });

        frame.open();
    });

    $(document).on('click', '#paradise-remove-photo', function (e) {
        e.preventDefault();
        $('#paradise-profile-photo-id').val('');
        $('#paradise-profile-photo-preview').hide();
        $('#paradise-upload-photo').text('Upload Photo');
        $(this).hide();
    });

})(jQuery);
