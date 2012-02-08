KeyMedia.views.KeyMedia = KP.ContentEditor.Base.extend(
{
    editor : null,
    image : null,

    initialize : function(options)
    {
        options = (options || {});
        this.init(options);
        _.bindAll(this, 'browse', 'scale', 'render', 'changeImage', 'enableUpload');
        var data = this.$el.data();
        this.model = new KeyMedia.models.Attribute({
            id : data.id,
            version : data.version,
            prefix : '/' + KP.urlPrefix + '/ezjscore/call'
        });

        this.image = new KeyMedia.models.Image(this.$('.attribute-base').data('bootstrap'));
        this.image.attr = this.model;
        this.image.bind('change', this.render);
        this.image.bind('reset', this.render);

        return this;
    },

    events :
    {
        'click button.from-keymedia' : 'browse',
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
            trueSize : node.data('truesize'),
            className : 'keymedia-scaler'
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

    render : function()
    {
        if (this.image.get('preview')) {
            this.$('.keymedia-preview').html(this.image.get('preview'));
        }
        if (this.image.get('interactions')) {
            this.$('.keymedia-interactions').html(this.image.get('interactions'));
        }

        this.taggerView = new KeyMedia.views.Tagger({
            el : this.$('.tagger'),
            model : this.image
        }).render();

        this.enableUpload();

        return this;
    },

    enableUpload : function() {
        this.upload = new KeyMedia.views.Upload({
            model : this.model,
            uploaded : this.changeImage,
            el : this.$el,
            prefix : this.model.get('prefix'),
            version : this.model.get('version')
        }).render();
        return this;
    }
});
