ezrKeyMedia.views.KeyMedia = Backbone.View.extend({
    // Holds current active subview
    view : null,

    destination : null,

    initialize : function(options)
    {
        _.bindAll(this, 'render', 'search', 'events');

        // DOM node to store selected image id into
        this.destination = options.destination;

        this.el = $(this.el);

        this.modal = new ezrKeyMedia.views.Modal;
        this.modal.el.prependTo('body');

        return this;
    },

    events : function() {
        var events = {
            'click .ezr-keymedia-remote-file' : 'search'
        };
        if (this.destination && this.destination.val())
            events['click .ezr-keymedia-scale'] = 'scaler';
        return events;
    },

    render : function()
    {
        this.modal.render();
        this.enableUpload();
        this.delegateEvents();
        return this;
    },

    enableUpload : function() {
        var attrId = this.model.get('attributeId');
        this.uploader = new plupload.Uploader({
            runtimes : 'html5,html4',
            browse_button : 'ezr-keymedia-local-file-' + attrId,
            container : 'ezr-keymedia-progress-' + attrId,
            max_file_size : '10mb',
            url : this.el.data('prefix') + '/keymedia::upload',
            multipart_params : {
                'AttributeID' : attrId,
                'ContentObjectVersion' : this.el.data('version'),
                'ContentObjectID' : this.el.data('contentobject-id')
            },
            headers : {
                'Accept' : 'application/json, text/javascript, */*; q=0.01'
            }
        });

        this.uploader.init();
        this.uploader.bind('FilesAdded', function(up, files)
        {
            up.start();
        });

        return this;
    },

    search : function()
    {
        this.view = new ezrKeyMedia.views.Browser({
            model : this.model,
            saveTo : this.destination,
            el : this.modal.show().contentEl
        });

        this.model.search('', {skeleton:true});
    },

    // Open a scaling gui
    scaler : function(e) {
        var node = $(e.currentTarget);
        settings = {
            imageId : this.destination.val(),
            versions : node.data('versions'),
            trueSize : node.data('size'),
            host : this.el.data('backend-host'),
            model : this.model,
            el : this.modal.show().contentEl
        };
        this.view = new ezrKeyMedia.views.Scaler(settings);

        this.model.scale(settings.imageId, settings.versions);
    }
});

