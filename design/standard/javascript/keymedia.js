window.KeyMedia = Backbone.Model.extend({
    prefix : null,

    initialize : function(options)
    {
        _.bindAll(this, 'onSearch', 'search', 'scale', 'onScale', 'addVanityUrl');
    },

    url : function(method, extra) {
        extra = (extra || []);
        return this.get('prefix') + '/' + ['keymedia', method, this.id].concat(extra).join('::');
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
    },

    // Create a new vanity url for a version
    // name should be a string to put on the back of the object name
    // coords should be an array [x,y,x2,y2]
    // size shoudl be an array [width,height]
    addVanityUrl : function(name, coords, size)
    {
        var data = {
            name : name,
            coords : coords,
            size : size
        };
        var url = this.url('saveVersion', [this.get('attributeId'), this.get('version')]),
            context = this;
        $.ajax({
            url : url,
            data : data,
            dataType : 'json',
            type : 'POST',
            complete : function(response) {
                console.log(response);
                context.trigger('version.create', response);
            }
        });
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

        this.model.scale(settings.imageId, settings.versions);
    },

    close : function(e) {
        this.el.hide();
        if (this.view !== null)
            this.view.remove();
    }
});
