$(function() {
    $('.keymedia-type').each(function() {
        var wrapper = $(this);
        var container = wrapper.find('.keymedia-buttons');
        var model = new ezrKeyMedia.models.model({
            id : container.data('backend'),
            prefix : container.data('prefix'),
            attributeId : container.data('id'),
            contentObjectId : container.data('contentobject-id'),
            version : container.data('version')
        });
        var image = new ezrKeyMedia.models.Image({
            id : container.find('.image-id').val(),
            backend : container.data('backend'),
            prefix : container.data('prefix')
        });
        var controller = new ezrKeyMedia.views.KeyMedia({
            el : container,
            model : model,
            destination : container.find('.image-id'),
            host : container.find('.image-host'),
            ending : container.find('.image-ending')
        }).render();
        var tagger = new ezrKeyMedia.views.Tagger({
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
    });
});
