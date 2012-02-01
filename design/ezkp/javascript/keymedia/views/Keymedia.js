KeyMedia.views.KeyMedia = KP.ContentEditor.Base.extend(
{
    editor : null,
    image : null,

    initialize : function(options)
    {
        options = (options || {});
        this.init(options);
        _.bindAll(this, 'browse', 'upload', 'scale', 'render', 'changeImage');
        var data = this.$el.data();
        this.model = new KeyMedia.models.Attribute({
            id : data.id,
            version : data.version,
            prefix : '/' + KP.urlPrefix + '/ezjscore/call'
        });

        var bootstrap = this.$('.attribute-base').data('bootstrap');
        this.image = new KeyMedia.models.Image(bootstrap);
        this.image.bind('change', this.render);
        this.image.bind('reset', this.render);

        return this;
    },

    events :
    {
        'click button.from-keymedia' : 'browse',
        'click button.upload' : 'upload',
        'click button.scale' : 'scale'
    },

    parseEdited : function()
    {
    },

    browse : function(e)
    {
        e.preventDefault();
        var options = {
            model : this.model,
            collection : this.model.images,
            onSelect : this.changeImage
        };

        this.model.images.search('');
        this.editor.trigger('stack.push', KeyMedia.views.Browser, options);
    },

    // Start render of scaler sub-view
    scale : function(e)
    {
        e.preventDefault();
        var node = $(e.currentTarget);
        var options = {
            model : this.model,
            image : this.image,
            versions : node.data('versions'),
            trueSize : node.data('truesize')
        };

        this.model.image(this.image);
        this.editor.trigger('stack.push', KeyMedia.views.Scaler, options);
        return this;
    },

    changeImage : function(id, host, ending)
    {
        this.$('.image-id').val(id);
        this.$('.image-host').val(host);
        this.$('.image-ending').val(ending);
        // Triggers autosave
        this.editor.onHandlerSave(this.model.id, this.$(':input').serializeArray());
        this.editor.trigger('stack.pop');
        // Reloads image from server
        this.model.image(this.image);
    },

    upload : function(e)
    {
        e.preventDefault();
        // Hmm?
        $(e.currentTarget).parent().remove();
        this.editor.onHandlerSave(this.attributeId, this.parseEdited());
    },

    render : function()
    {
        if (this.image.get('preview')) {
            this.$('.keymedia-preview').html(this.image.get('preview'));
        }
        if (this.image.get('interactions')) {
            this.$('.keymedia-interactions').html(this.image.get('interactions'));
        }
        return this;
    },

    onAdd : function(elements)
    {
        this.render(elements);

        this.editor.trigger('stack.pop');

        this.editor.onHandlerSave(this.attributeId, this.parseEdited());
    },

    removeObjects : function(e)
    {
        e.preventDefault();

        this.$('ul li input:checked').parents('li').remove();

        this.editor.onHandlerSave(this.attributeId, this.parseEdited());
    }
});
