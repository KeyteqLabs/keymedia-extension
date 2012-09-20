define(['shared/view', 'keymedia/models', './tagger', './upload'], function(View, Models, TaggerView, UploadView)
{
    return View.extend({
        media : null,
        versions : null,
        version : null,

        initialize : function(options)
        {
            options = (options || {});
            _.bindAll(this);
            _.extend(this, _.pick(options, ['id', 'version']));

            var data = this.$el.data();
            _.extend(data, this.$('.attribute-base').data());

            var urlRoot = '/ezjscore/call';
            if (data.urlRoot !== '/') urlRoot = data.urlRoot + urlRoot;

            this.model = new Models.attribute({
                id : data.id,
                version : this.version,
                media : data.bootstrap
            });
            this.model.urlRoot = urlRoot;
            this.model.on('change', this.render);
            this.model.on('version.create', this.versionCreated);

            this.collection = new Models.collection();
            this.collection.urlRoot = urlRoot;
            this.collection.id = data.id;
            this.collection.version = this.version;

            this.on('saved', this.update, this);

            /*
            this.media = new Models.media(data.bootstrap);
            this.media.on('reset change', this.render);
            */

            return this;
        },

        events : {
            'click button.from-keymedia' : function(e)
            {
                e.preventDefault();
                require(['keymedia/views/browser'], this.browse);
            },
            'click button.scale' : 'scale',
            'click .remove' : 'removeMedia'
        },

        removeMedia : function(e)
        {
            e.preventDefault();
            var data = this.$('.data').val('').serializeArray();

            data.push({
                name : 'mediaRemove',
                value : 1
            });

            this.trigger('save', this.model.id, data);
            this.show(this.$('.eze-no-image'));
            this.hide(this.$('.eze-image'));
        },

        browse : function(BrowseView)
        {
            var options = {
                model : this.model,
                collection : this.collection,
                onSelect : this.changeMedia
            };

            var context = {
                icon : '/extension/ezexceed/design/ezexceed/images/kp/32x32/Pictures.png',
                heading : 'Select media',
                render : true
            };
            eZExceed.stack.push(
                BrowseView,
                options,
                context
            ).on('destruct', this.changeMedia);
            this.collection.search('');
        },

        // Start render of scaler sub-view
        scale : function(e)
        {
            e.preventDefault();
            var node = $(e.currentTarget);
            var options = {
                model : this.model,
                //media : this.media,
                versions : this.versions,
                trueSize : node.data('truesize'),
                className : 'keymedia-scaler',
                app : this
            };

            var headingOptions = {
                heading : 'Select crops',
                icon : '/extension/ezexceed/design/ezexceed/images/kp/32x32/Pictures-alt-2b.png'
            };

            this.model.fetch();
            this.editor.trigger('stack.push', KeyMedia.views.Scaler, options, {headingOptions : headingOptions});
            return this;
        },

        changeMedia : function(data, pop)
        {
            var refresh = data.refresh;
            this.$('.media-id').val(data.id);
            this.$('.media-host').val(data.host);
            this.$('.media-type').val(data.type);
            this.$('.media-ending').val(data.ending);

            data = this.$(':input').serializeArray();
            data.push({
                name : 'changeMedia',
                value : 1
            });

            this.trigger('save', this.model.id, data);
        },

        update : function()
        {
            this.model.fetch();
        },

        render : function()
        {
            var content = this.model.get('content');
            var media = this.model.get('media');
            var upload = !content || !media;
            if (content) {
                this.$el.html(content);
            }

            this.taggerView = new TaggerView({
                el : this.$('.tagger'),
                model : this.model
            }).render();

            this.versions = this.$('button.scale').data('versions');

            if (upload) {
                this.enableUpload();
            }
            else {
                this.hide(this.$('.eze-no-image'));
            }

            return this;
        },

        enableUpload : function() {
            this.upload = new UploadView({
                model : this.model,
                uploaded : this.changeMedia,
                el : this.$el,
                prefix : this.model.get('prefix'),
                version : this.version
            }).render();
            return this;
        },

        versionCreated : function()
        {
            this.editor.trigger('autosave.saved');
            this.editor.model.trigger('autosave.saved');
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
            else if(changeMedia) {
                // Reloads media from server
                this.model.fetch();
            }
        }
    });
});
