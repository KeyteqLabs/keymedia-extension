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
            var image = new KeyMedia.models.Image({
                id : container.find('.image-id').val(),
                prefix : container.data('prefix')
            });
            image.attr = model;
            var controller = new KeyMedia.views.KeyMedia({
                el : container,
                model : model,
                destination : container.find('.image-id'),
                host : container.find('.image-host'),
                ending : container.find('.image-ending')
            }).render();
            var tagger = new KeyMedia.views.Tagger({
                el : wrapper.find('.tagger'),
                model : image
            }).render();
            if (wrapper.data('bootstrap-image'))
                image.set(wrapper.data('bootstrap-image'));
            wrapper.data('objects', {
                image : image,
                tagger : tagger,
                model : model,
                controller : controller
            });
        }
    });
});
