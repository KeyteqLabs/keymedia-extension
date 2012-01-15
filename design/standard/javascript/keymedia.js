$(function() {
    $('.keymedia-buttons').each(function() {
        var container = $(this);
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
    });
});
