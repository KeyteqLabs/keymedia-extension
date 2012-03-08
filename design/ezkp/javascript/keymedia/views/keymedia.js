KeyMedia.views.KeyMedia = KP.ContentEditor.Base.extend(
{
    editor : null,
    image : null,

    initialize : function(options)
    {
        options = (options || {});
        this.init(options);
        _.bindAll(this, 'browse', 'scale', 'render', 'changeImage', 'enableUpload', 'remove');
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
        'click button.scale' : 'scale',
        'click .image-wrap .remove' : 'remove'
    },

    parseEdited : function()
    {
    },

    remove : function(e)
    {
        e.preventDefault();
        this.$('.image-id').val('');
        this.$('.image-host').val('');
        this.$('.image-ending').val('');
        var data = this.$(':input').serializeArray();
        data.push({
            name : 'imageRemove',
            value : 1
        })
        // Triggers autosave
        this.editor.onHandlerSave(this.model.id, data);
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
        if (this.image.get('content'))
        {
            var content = $(this.image.get('content'));
            if (content.first().hasClass('attribute-base'))
                this.$('.attribute-base').replaceWith(this.image.get('content'));
            else
                this.$el.html(this.image.get('content'));
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
    },

    success : function(response)
    {
        var remove = _(response).find(function(el){
            if (_(el).has('imageRemove') || (_(el).has('name') && el.name == 'imageRemove'))
                return true;
        });
        if (remove)
        {
            this.$('.current-image').remove();
            this.$('.meta').remove();
            this.$('.tagger').remove();
            this.$('.edit-buttons').show();
            this.$('.image-container').removeClass('with-image');
            this.$('.upload-container').show();
        }
    }
});
