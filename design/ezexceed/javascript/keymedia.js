define(['shared/datatype', 'keymedia/views/main', 'keymedia/config'],
    function(Base, MainView)
{
    return Base.extend({
        initialize : function(options)
        {
            _.bindAll(this);
            this.init(options);
            _.extend(this, _.pick(options, ['version']));
            this.view = new MainView({
                el : options.el,
                id : options.objectId,
                version : options.version
            });

            this.model.on('autosave.saved', this.saved, this);
            this.view.on('save', this.save, this);
        },

        render : function()
        {
            this.view.render();
            return this;
        },

        save : function(id, data)
        {
            this.editor.saveAttribute(this, data);
        },

        saved : function(model, response)
        {
            if (response && _(response).has('attributes') && _(response.attributes).has(this.attributeId))
                this.view.trigger('saved');
        },

        parseEdited : function() {}
    });
});
