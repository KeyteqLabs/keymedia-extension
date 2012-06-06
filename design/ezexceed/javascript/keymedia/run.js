$(function() {
    $('.keymedia-buttons').each(function() {
        var container = $(this);
        var model = new KeyMedia.models.model({
            id : container.data('backend'),
            prefix : container.data('prefix'),
            attributeId : container.data('id'),
            contentObjectId : container.data('contentobject-id'),
            version : container.data('version')
        });
        var keymedia = new KeyMedia.views.KeyMedia({
            container : eZExceed.stack,
            search : function(e) {
                var params = {
                    model : this.model,
                    saveTo : this.destination
                };
                this.container.push(KeyMedia.views.Browser, params, {render:false});
                this.model.search('', {skeleton:true});
            },
            el : container,
            model : model,
            destination : container.find('.media-id')
        }).render();
    });
});
