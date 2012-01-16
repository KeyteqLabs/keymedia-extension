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
        var keymedia = new ezrKeyMedia.views.KeyMedia({
            el : container,
            model : model,
            destination : container.find('.image-id'),
            host : container.find('.image-host')
        }).render();
        var tagger = new ezrKeyMedia.views.Tagger({
            el : wrapper,
            model : new ezrKeyMedia.models.Image({
                id : container.find('.image-id').val(),
                backend : container.data('backend'),
                prefix : container.data('prefix')
            })
        }).render();
    });
});
