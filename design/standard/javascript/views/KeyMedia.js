ezrKeyMedia.views.KeyMedia = Backbone.View.extend({
    // Holds current active subview
    view : null,

    _init : false,

    destination : null,
    host : null,

    container : false,

    initialize : function(options)
    {
        _.bindAll(this, 'render', 'search', 'close');

        // DOM node to store selected image id into
        this.destination = options.destination;
        this.host = options.host;

        this.el = $(this.el);

        if ('container' in options) {
            this.container = options.container;
        }
        else {
            this.container = new ezrKeyMedia.views.Modal;
            this.container.el.prependTo('body');
        }

        this.container.bind('close', this.close);

        if ('scaler' in options)
            this.scaler = options.scaler;
        if ('search' in options)
            this.search = options.search;

        if (this._init) this._init();

        return this;
    },

    events : {
        'click .ezr-keymedia-remote-file' : 'search',
        'click .ezr-keymedia-scale' : 'scaler'
    },

    render : function()
    {
        this.container.render();
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
        var destination = this.destination, host = this.host;
        this.uploader.bind('FileUploaded', function(up, file, info) {
            var data = {};
            if ('response' in info) data = JSON.parse(info.response);
            destination.val(data.content.image.id);
            host.val(data.content.image.host);
        });
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
            saveHostTo : this.host,
            el : this.container.show().contentEl
        });

        this.model.search('', {skeleton:true});
    },

    // Open a scaling gui
    scaler : function(e) {
        if (!(this.destination && this.destination.val())) return false;

        var node = $(e.currentTarget);
        settings = {
            imageId : this.destination.val(),
            versions : node.data('versions'),
            trueSize : node.data('size'),
            host : this.host.val(),
            model : this.model,
            el : this.container.show().contentEl
        };
        this.view = new ezrKeyMedia.views.Scaler(settings);

        this.model.scale(settings.imageId, settings.versions);
    },

    close : function() {
        if (this.view && 'close' in this.view)
        {
            this.view.close();
        }
    }
});

