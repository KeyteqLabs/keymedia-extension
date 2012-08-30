KeyMedia.views.KeyMedia = eZExceed.ContentEditor.Base.extend(
{
    editor : null,
    media : null,
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

        this.media = new KeyMedia.models.Media(this.$('.attribute-base').data('bootstrap'));
        this.media.attr = this.model;
        this.media.on('change', this.render);
        this.media.on('reset', this.render);

        return this;
    },

    events :
    {
        'click button.from-keymedia' : 'browse',
        'click button.scale' : 'scale',
        'click .image-wrap .remove' : 'removeMedia'
    },

    parseEdited : function()
    {
    },

    removeMedia : function(e)
    {
        e.preventDefault();
        this.$('.media-id').val('');
        this.$('.media-host').val('');
        this.$('.media-type').val('');
        this.$('.media-ending').val('');
        var data = this.$(':input').serializeArray();
        data.push({
            name : 'mediaRemove',
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
            collection : this.model.medias,
            onSelect : this.changeMedia
        };
        var headingOptions =
        {
            icon : '/extension/ezexceed/design/ezexceed/images/kp/32x32/Pictures.png',
            name : 'Select media',
            quotes : true,
        };
        this.model.medias.search('');
        this.editor.trigger('stack.push', KeyMedia.views.Browser, options, {headingOptions : headingOptions});
    },

    // Start render of scaler sub-view
    scale : function(e)
    {
        e.preventDefault();
        var node = $(e.currentTarget);
        var options = {
            model : this.model,
            media : this.media,
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

        this.model.media(this.media);
        this.editor.trigger('stack.push', KeyMedia.views.Scaler, options, {headingOptions : headingOptions});
        return this;
    },

    changeMedia : function(data, pop)
    {
        if (data.refresh)
        {
            this.model.media(this.media);
            return;
        }
        this.$('.media-id').val(data.id);
        this.$('.media-host').val(data.host);
        this.$('.media-type').val(data.type);
        this.$('.media-ending').val(data.ending);

        var data = this.$(':input').serializeArray();
        data.push({
            name : 'changeMedia',
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
        if (this.media.get('content'))
        {
            var content = $(this.media.get('content'));
            if (content.first().hasClass('attribute-base'))
                this.$('.attribute-base').replaceWith(this.media.get('content'));
            else
                this.$el.html(this.media.get('content'));
        }

        this.taggerView = new KeyMedia.views.Tagger({
            el : this.$('.tagger'),
            model : this.media
        }).render();

        this.versions = this.$('button.scale').data('versions');
        this.enableUpload();

        return this;
    },

    enableUpload : function() {
        var version = this.getVersion() ? this.getVersion() : this.model.get('version');
        this.upload = new KeyMedia.views.Upload({
            model : this.model,
            uploaded : this.changeMedia,
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
            if (_(el).has('mediaRemove') || (_(el).has('name') && el.name == 'mediaRemove'))
                return true;
        });
        var changeMedia = _(response).find(function(el)
        {
            if (_(el).has('changeMedia') || (_(el).has('name') && el.name == 'changeMedia'))
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
        else if(changeMedia)
        {
            // Reloads media from server
            this.model.media(this.media);
        }
    }
});
