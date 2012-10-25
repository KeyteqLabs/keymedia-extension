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
            this.collection.on('reset add', this.renderItems);
        },

        events : {
            'keyup .q' : 'search',
            'submit .form-search' : 'search',
            'click .item a' : 'select',
            'click .load-more' : 'page'
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
            var html = this.collection.map(function(item) {
                return this.template('keymedia/item', item.attributes);
            }, this);
            
            if (clear)
                this.$body.html(html);
            else
                this.$body.append(html);

            if (this.collection.total > this.collection.length)
                this.$body.append(this.template('keymedia/show-more'));
            else
                this.$('.load-more').hide();

            return this;
        },

        page : function()
        {
            this.collection.page(this.$('.q').val());
        },

        enableUpload : function()
        {
            this.upload = new UploadView({
                model : this.model,
                uploaded : this.uplodedMedia,
                el : this.$el,
                version : this.model.get('version'),
                browseContainer : 'keymedia-browser-local-file-container-' + this.model.id,
                browseButton : 'keymedia-browser-local-file-' + this.model.id
            }).render();
            return this;
        },

        uplodedMedia : function(data)
        {
            eZExceed.stack.pop({
                id : data.id,
                host : data.host,
                type : data.type,
                ending : data.ending,
                keymediaId : this.collection.keymediaId
            });
        }
    });
});
