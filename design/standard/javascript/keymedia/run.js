$(function() {
    $('.keymedia-type').each(function() {
        var wrapper = $(this);
        var container = wrapper.find('.keymedia-buttons');
        if (container.length) {
            var model = new KeyMedia.models.Attribute({
                id : container.data('id'),
                prefix : container.data('prefix'),
                version : container.data('version')
            });
            var media = new KeyMedia.models.Media({
                id : container.find('.media-id').val(),
                prefix : container.data('prefix')
            });
            media.attr = model;
            var controller = new KeyMedia.views.KeyMedia({
                el : container,
                wrapper : wrapper,
                model : model,
                destination : container.find('.media-id'),
                host : container.find('.media-host'),
                ending : container.find('.media-ending')
            }).render();
            var tagger = new KeyMedia.views.Tagger({
                el : wrapper.find('.tagger'),
                model : media
            }).render();
            if (wrapper.data('bootstrap-media'))
                media.set(wrapper.data('bootstrap-media'));
            wrapper.data('objects', {
                media : media,
                tagger : tagger,
                model : model,
                controller : controller
            });
        }
    });
});
