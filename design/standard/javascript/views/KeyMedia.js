ezrKeyMedia.views.KeyMedia = Backbone.View.extend({
    // Holds current active subview
    view : null,

    initialize : function(options)
    {
        _.bindAll(this, 'render', 'search');

        // DOM node to store selected image id into
        this.destination = options.destination;

        this.modal = new ezrKeyMedia.views.Modal;

        return this;
    },

    render : function()
    {
        this.el = $(this.el);
        this.el.append(this.modal.el);
        this.modal.render();

        return this;
    },

    search : function(q)
    {
        this.view = new ezrKeyMedia.views.Browser({
            model : this.model,
            saveTo : this.destination,
            el : this.modal.show().contentEl
        });

        this.model.search('', {skeleton:true});
    },

    // Open a scaling gui
    scaler : function(settings) {
        settings = _.extend({
            model : this.model,
            el : this.modal.show().contentEl
        }, settings);
        this.view = new ezrKeyMedia.views.Scaler(settings);

        this.model.scale(settings.imageId, settings.versions);
    }
});

