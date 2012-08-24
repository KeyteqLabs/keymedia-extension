KeyMedia.views.Browser = Backbone.View.extend({
    tpl : null,
    input : null,

    initialize : function(options)
    {
        options = (options || {});
        _.bindAll(this);

        this.collection.on('add reset', this.renderItems);

        this.tpl = {
            browser : Handlebars.compile($('#tpl-keymedia-browser').html()),
            item : Handlebars.compile($('#tpl-keymedia-item').html())
        };

        if ('onSelect' in options) {
            this.onSelect = options.onSelect;
        }
        return this;
    },

    events : {
        'click button.search' : 'search',
        'submit form.search' : 'search',
        'click .item a' : 'select',
        'click .more-hits' : 'page'
    },

    select : function(e) {
        e.preventDefault();
        var node = $(e.currentTarget), id = node.data('id');
        var model = this.model.medias.get(id);
        var options = {
            id : id,
            host : model.get('host'),
            type : model.get('type'),
            ending : model.get('scalesTo').ending,
            keymediaId : this.model.medias.keymediaId,
            model : model
        };
        if (this.onSelect) {
            this.onSelect(options, true);
        }
        this.$('.close').click();
    },

    search : function(e)
    {
        e.preventDefault();
        var q = '';
        if (this.input) {
            q = this.input.val();
        }
        this.model.medias.search(this.input.val());
    },

    render : function(response) {
        var content = $(this.tpl.browser({
            tr : _KeyMediaTranslations,
            icon : '/extension/ezexceed/design/ezexceed/images/kp/32x32/Pictures.png',
            heading : 'Select media',
            attribute : this.model.attributes
        }));

        this.$el.append(content);
        this.$body = this.$('.body');
        this.renderItems(true);
        this.input = this.$('.q');
        this.enableUpload();
        return this;
    },

    renderItems : function(clear)
    {
        var html = '';
        this.collection.each(function(item) {
            html += this.tpl.item(item.attributes);
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

    onPage : function()
    {
        this.renderItems();
    },

    enableUpload : function()
    {
        var version = this.model.get('version');
        this.upload = new KeyMedia.views.Upload({
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
        {
            this.onSelect(options, true);
        }
        this.$('.close').click();
    }
});
