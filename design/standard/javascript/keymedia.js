window.KeyMedia = Backbone.Model.extend({
    prefix : null,

    initialize : function(options)
    {
        _.bindAll(this, 'onSearch', 'search', 'scale', 'onScale');
        this.prefix = options.prefix;
    },

    url : function(method, extra) {
        extra = (extra || []);
        return this.prefix + '/' + ['keymedia', method, this.id].concat(extra).join('::');
    },

    search : function(q, include) {
        var data = (include || {skeleton:true});

        if (typeof q === 'string')
            data.q = q;
            
        $.getJSON(this.url('browse'), data, this.onSearch);
    },

    scale : function(image, versions) {
        versions = (versions || {});
        $.getJSON(this.url('scaler', [image]), {versions:versions}, this.onScale);
    },

    onScale : function(response) {
        this.trigger('scale', response);
    },

    onSearch : function(response)
    {
        this.trigger('search', response);
    }
});

window.KeyMediaView = Backbone.View.extend({
    // Holds current active subview
    view : null,

    // el construction information
    tagName : 'div',
    id : 'ezr-keymedia-modal',

    initialize : function(options)
    {
        _.bindAll(this, 'render');

        // DOM node to store selected image id into
        this.destination = options.destination;

        //this.version = options.version,
        //this.contentObjectId = options.contentObjectId,

        return this;
    },

    events : {
        'click .close' : 'close',
    },

    render : function()
    {
        this.el = $(this.el);

        this.el.html('<div class="backdrop"/><div class="content"/>');
        this.el.hide();

        this.delegateEvents();

        return this;
    },

    search : function(q)
    {
        // Render modal
        var el = $('<div class="browser"/>');
        this.el.find('.content').html(el);
        this.el.show();

        this.view = new KeyMediaBrowser({
            model : this.model,
            saveTo : this.destination,
            el : el
        });

        this.model.search('', {skeleton:true});
    },

    // Open a scaling gui
    scaler : function(settings) {
        // Render modal
        var el = $('<div class="scaler"/>');
        this.el.find('.content').html(el);
        this.el.show();

        settings.el = el;
        settings.model = this.model;
        this.view = new KeyMediaScaler(settings);

        var data = {
            versions : settings.versions
        };

        this.model.scale(settings.image, settings.versions);
    },

    close : function(e) {
        this.el.hide();
        if (this.view !== null)
            this.view.remove();
    }
});
