KeyMedia.views.KeyMedia = eZExceed.ContentEditor.Base.extend(
{
    editor : null,
    image : null,
    versions : null,

    initialize : function(options)
    {
        options = (options || {});
        _.bindAll(this);
        this.init(options);
        var data = this.$el.data();
        var prefix = eZExceed.urlPrefix ? '/' + eZExceed.urlPrefix : '';
        prefix = prefix + '/ezjscore/call';
        this.model = new KeyMedia.models.Attribute({
            id : data.id,
            version : data.version,
            prefix : prefix
        });
        this.model.controller = this;
        this.model.on('version.create', this.versionCreated);

        this.image = new KeyMedia.models.Image(this.$('.attribute-base').data('bootstrap'));
        this.image.attr = this.model;
        this.image.on('change', this.render);
        this.image.on('reset', this.render);

        return this;
    },

    events :
    {
        'click button.from-keymedia' : 'browse',
        'click button.scale' : 'scale',
        'click .image-wrap .remove' : 'removeImage'
    },

    parseEdited : function()
    {
    },

    removeImage : function(e)
    {
        e.preventDefault();
        this.$('.image-id').val('');
        this.$('.image-host').val('');
        this.$('.image-ending').val('');
        var data = this.$(':input').serializeArray();
        data.push({
            name : 'imageRemove',
            value : 1
        });

        var version = this.getVersion();

        if (version)
        {
            // Triggers autosave
            this.editor.onHandlerSave(this.model.id, version, data);
        }
        else
        {
            // Triggers autosave
            this.editor.onHandlerSave(this.model.id, data);
        }
    },

    browse : function(e)
    {
        e.preventDefault();
        var options = {
            model : this.model,
            collection : this.model.images,
            onSelect : this.changeImage
        };
        var headingOptions =
        {
            icon : '/extension/ezexceed/design/ezexceed/images/kp/32x32/Pictures.png',
            name : 'Select image',
            quotes : true,
        };
        this.model.images.search('');
        this.editor.trigger('stack.push', KeyMedia.views.Browser, options, {headingOptions : headingOptions});
    },

    // Start render of scaler sub-view
    scale : function(e)
    {
        e.preventDefault();
        var node = $(e.currentTarget);
        var options = {
            model : this.model,
            image : this.image,
            versions : this.versions,
            trueSize : node.data('truesize'),
            className : 'keymedia-scaler',
            app : this
        };
        var headingOptions =
        {
            name : 'Select crops',
            icon : '/extension/ezexceed/design/ezexceed/images/kp/32x32/Pictures-alt-2b.png',
            quotes : true
        };

        this.model.image(this.image);
        this.editor.trigger('stack.push', KeyMedia.views.Scaler, options, {headingOptions : headingOptions});
        return this;
    },

    changeImage : function(id, host, ending, pop)
    {
        this.$('.image-id').val(id);
        this.$('.image-host').val(host);
        this.$('.image-ending').val(ending);

        var data = this.$(':input').serializeArray();
        data.push({
            name : 'changeImage',
            value : 1
        });

        var version = this.getVersion();

        if (version)
        {
            // Triggers autosave
            this.editor.onHandlerSave(this.model.id, version, data);
        }
        else
        {
            // Triggers autosave
            this.editor.onHandlerSave(this.model.id, data);
        }
        if (pop)
            this.editor.trigger('stack.pop');
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

        this.versions = this.$('button.scale').data('versions');
        this.enableUpload();

        return this;
    },

    enableUpload : function() {
        var version = this.getVersion() ? this.getVersion() : this.model.get('version');
        this.upload = new KeyMedia.views.Upload({
            model : this.model,
            uploaded : this.changeImage,
            el : this.$el,
            prefix : this.model.get('prefix'),
            version : version
        }).render();
        return this;
    },

    versionCreated : function()
    {
        this.editor.trigger('autosave.saved');
        this.editor.model.trigger('autosave.saved');
    },

    getVersion : function()
    {
        var language = this.$el.data('language');
        if (language && _.isFunction(this.editor.getVersion))
            return this.editor.getVersion(language);
        return false;
    },

    success : function(response)
    {
        var remove = _(response).find(function(el){
            if (_(el).has('imageRemove') || (_(el).has('name') && el.name == 'imageRemove'))
                return true;
        });
        var changeImage = _(response).find(function(el)
        {
            if (_(el).has('changeImage') || (_(el).has('name') && el.name == 'changeImage'))
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
        else if(changeImage)
        {
            // Reloads image from server
            this.model.image(this.image);
        }
    }
});
