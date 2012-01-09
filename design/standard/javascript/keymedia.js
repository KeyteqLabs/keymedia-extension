$(function() {
    $('.keymedia-buttons').each(function() {
    var container = $(this);
    console.log(container);
    var id = container.data('id');
    var destination = container.find('.image-id');
    var model = new ezrKeyMedia.models.model({
        id : container.data('backend'),
        prefix : container.data('prefix'),
        attributeId : id,
        contentObjectId : container.data('contentobject-id'),
        version : container.data('version')
    });
    var keymedia = new ezrKeyMedia.views.KeyMedia({
        model : model,
        destination : destination
    }).render();
    keymedia.el.prependTo('body');

    if (destination.val())
    {
        $('.ezr-keymedia-scale', container).click(function(e) {
            e.preventDefault();
            var data = {
                imageId : destination.val(),
                host : container.data('backend-host'),
                versions : $(this).data('versions'),
                trueSize : $(e.currentTarget).data('size')
            };
            keymedia.scaler(data);
        }).show();
    }

    $('.ezr-keymedia-remote-file', container).click(function(e) {
        keymedia.search();
        //model.search();
    });

    var uploader = new plupload.Uploader({
        runtimes : 'html5,html4',
        browse_button : 'ezr-keymedia-local-file-' + id,
        container : 'ezr-keymedia-progress-' + id,
        max_file_size : '10mb',
        url : container.data('prefix') + '/keymedia::upload',
        multipart_params : {
            'AttributeID' : id,
            'ContentObjectVersion' : container.data('version'),
            'ContentObjectID' : container.data('contentobject-id')
        },
        headers : {
            'Accept' : 'application/json, text/javascript, */*; q=0.01'
        }
    });


    uploader.init();
    uploader.bind('FilesAdded', function(up, files)
    {
        up.start();
    });
    });
});
