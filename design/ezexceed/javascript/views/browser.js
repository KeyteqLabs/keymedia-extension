define(['shared/view', './upload'], function(View, UploadView)
{
    return View.extend({
        tpl : null,
        input : null,

        initialize : function(options)
        {
            options = (options || {});
            _.bindAll(this);

            _.extend(this, _.pick(options, ['onSelect']));
            this.collection.bind('reset', this.renderItems);
            this.collection.bind('add', this.renderItems);
        },

        events : {
            'keyup .q' : 'search',
            'submit .form-search' : 'search',
            'click .item a' : 'select',
            'click .more-hits' : 'page'
        },

        select : function(e) {
            e.preventDefault();
            var id = this.$(e.currentTarget).data('id');
            var model = this.collection.get(id);
            eZExceed.stack.pop({
                id : id,
                host : model.get('host'),
                type : model.get('type'),
                ending : model.get('scalesTo').ending,
                keymediaId : this.collection.keymediaId,
                refresh : true,
                model : model.toJSON()
            });
        },

        search : function(e)
        {
            e.preventDefault();
            var q = '';
            if (this.input) {
                q = this.input.val();
            }
            this.collection.search(q);
        },

        render : function() {
            var context = {
                icon : '/extension/ezexceed/design/ezexceed/images/kp/32x32/Pictures.png',
                heading : 'Select media',
                id : this.model.id,
                attribute : this.model.attributes
            };
            var html = this.template('keymedia/browser', context);

            this.$el.append(html);
            this.$body = this.$('.keymedia-thumbs');
            this.renderItems(true);
            this.input = this.$('.q');
            this.enableUpload();
            return this;
        },

        renderItems : function(clear)
        {
            var html = '';
            this.collection.each(function(item) {
                html += this.template('keymedia/item', item.attributes);
            }, this);
            
            if (clear)
                this.$body.html(html);
            else
                this.$body.append(html);

            if (this.collection.total > this.collection.length)
                this.$body.append('<a class="more-hits button">Show more</a>');
            else
                this.$('.more-hits').hide();

            return this;
        },

        page : function()
        {
            this.collection.page(this.$('.q').val());
        },

        enableUpload : function()
        {
            var version = this.model.get('version');
            this.upload = new UploadView({
                model : this.model,
                uploaded : this.uplodedMedia,
                el : this.$el,
                prefix : this.model.get('prefix'),
                version : version,
                browseContainer : 'keymedia-browser-local-file-container-' + this.model.id,
                browseButton : 'keymedia-browser-local-file-' + this.model.id
            }).render();
            return this;
        },

        uplodedMedia : function(data)
        {
            var model = new KeyMedia.models.Media(data.media);
            var options = {
                id : data.id,
                host : data.host,
                type : data.type,
                ending : data.ending,
                keymediaId : this.model.medias.keymediaId,
                model : model
            };
            if (this.onSelect)
                this.onSelect(options, true);
            this.$('.close').click();
        }
    });
});
