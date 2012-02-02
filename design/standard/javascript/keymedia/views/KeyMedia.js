KeyMedia.views.KeyMedia = Backbone.View.extend({
    // Holds current active subview
    view : null,
    destination : null,
    host : null,

    container : false,

    initialize : function(options)
    {
        options = (options || {});
        _.bindAll(this, 'render', 'search', 'close', 'enableUpload', 'changeImage');

        // DOM node to store selected image id into
        this.destination = options.destination;
        this.host = options.host;
        this.ending = options.ending;

        if ('container' in options) {
            this.container = options.container;
        }
        else {
            this.container = new KeyMedia.views.Modal().render();
            this.container.$el.prependTo('body');
        }
        this.container.bind('close', this.close);
        return this;
    },

    events : {
        'click .keymedia-remote-file' : 'search',
        'click .keymedia-scale' : 'scaler'
    },

    render : function()
    {
        this.container.render();
        this.enableUpload();
        this.delegateEvents();
        return this;
    },

    changeImage : function(id, host, ending) {
        this.destination.val(id);
        this.host.val(host);
        this.ending.val(ending);
        return this;
    },

    enableUpload : function() {
        this.upload = new KeyMedia.views.Upload({
            model : this.model,
            uploaded : this.changeImage,
            el : this.$el.parent(),
            prefix : this.$el.data('prefix'),
            version : this.$el.data('version')
        });
        this.upload.render();
        return this;
    },

    search : function()
    {
        this.view = new KeyMedia.views.Browser({
            collection : this.model.images,
            onSelect : this.changeImage,
            el : this.container.show().contentEl
        }).render();

        this.model.images.search('');
    },

    // Open a scaling gui
    scaler : function(e) {
        if (!(this.destination && this.destination.val())) {
            return false;
        }

        var node = $(e.currentTarget);
        settings = {
            imageId : this.destination.val(),
            versions : node.data('versions'),
            trueSize : node.data('truesize'),
            host : this.host.val(),
            model : this.model,
            el : this.container.show().contentEl
        };
        this.view = new KeyMedia.views.Scaler(settings);
        this.model.scale(settings.imageId);
    },

    close : function() {
        if (this.view && ('close' in this.view)) {
            this.view.close();
        }
    }
});

