ezrKeyMedia.views.KeyMedia = Backbone.View.extend({
    // Holds current active subview
    view : null,

    _init : false,

    destination : null,

    container : false,

    initialize : function(options)
    {
        _.bindAll(this, 'render', 'search', 'events');

        // DOM node to store selected image id into
        this.destination = options.destination;

        this.el = $(this.el);

        if ('container' in options) {
            this.container = options.container;
        }
        else {
            this.container = new ezrKeyMedia.views.Modal;
            this.container.el.prependTo('body');
        }

        if ('scaler' in options)
            this.scaler = options.scaler;
        if ('search' in options)
            this.search = options.search;

        if (this._init) this._init();

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
        var destination = this.destination;
        this.uploader.bind('FileUploaded', function(up, file, info) {
            var data = {};
            if ('response' in info) data = JSON.parse(info.response);
            var id = data.content.image.id;
            console.log(destination, id);
            destination.val(id);
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
            el : this.container.show().contentEl
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
            el : this.container.show().contentEl
        };
        this.view = new ezrKeyMedia.views.Scaler(settings);

        this.model.scale(settings.imageId, settings.versions);
    }
});

