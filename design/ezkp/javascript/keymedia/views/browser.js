KeyMedia.views.Browser = Backbone.View.extend({
    tpl : null,
    input : null,

    initialize : function(options)
    {
        options = (options || {});
        _.bindAll(this, 'render', 'select', 'renderItems', 'page', 'onPage');

        this.collection.bind('reset', this.renderItems);
        this.collection.bind('add', this.renderItems);

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
        var model = this.model.images.get(id);
        if (this.onSelect) {
            this.onSelect(id, model.get('host'), model.get('scalesTo').ending, true);
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
        this.model.images.search(this.input.val());
    },

    render : function(response) {
        var content = $(this.tpl.browser({
            tr : _KeyMediaTranslations,
            icon : '/extension/ezkp/design/ezkp/images/kp/32x32/Pictures.png',
            heading : 'Select image'
        }));

        this.$el.append(content);
        this.renderItems(true);
        this.input = this.$('.q');
        return this;
    },

    renderItems : function(clear)
    {
        var html = '';
        this.collection.each(function(item) {
            html += this.tpl.item(item.attributes);
        }, this);
        
        if (clear)
            this.$('.body').html(html);
        else
            this.$('.body').append(html);

        if (this.collection.total > this.collection.length)
            this.$('.body').append('<a class="more-hits button">Show more</a>');
        else
            this.$('.more-hits').hide();

        return this;
    },

    page : function()
    {
        this.collection.page();
    },

    onPage : function()
    {
        this.renderItems();
    }
});
