KeyMedia.views.Browser = Backbone.View.extend({
    tpl : null,

    name : '',

    input : null,

    initialize : function(options)
    {
        _.bindAll(this, 'render', 'select', 'renderItems');

        this.collection.bind('reset', this.renderItems);
        this.el = $(this.el);

        this.tpl = {
            browser : Handlebars.compile($('#tpl-keymedia-browser').html()),
            item : Handlebars.compile($('#tpl-keymedia-item').html())
        };

        if ('onSelect' in options)
            this.onSelect = options.onSelect;

        if ('parentName' in options)
            this.name = options.parentName;

        return this;
    },

    events : {
        'click button.search' : 'search',
        'submit form.search' : 'search',
        'click .item a' : 'select'
    },

    select : function(e) {
        e.preventDefault();
        var node = $(e.currentTarget), id = node.data('id');
        var model = this.model.images.get(id);
        if (this.onSelect)
            this.onSelect(id, model.get('host'), model.get('scalesTo').ending);
        this.$('.close').click();
    },

    search : function(e)
    {
        e.preventDefault();
        var q = '';
        if (this.input)
            q = this.input.val();
        this.model.images.search(this.input.val());
    },

    render : function(response) {
        var content = $(this.tpl.browser({
            tr : _KeyMediaTranslations,
            icon : '/extension/ezkp/design/ezkp/images/kp/32x32/Pictures.png',
            heading : 'Select image'
        }));

        this.el.append(content);
        this.renderItems();
        this.input = this.$('.q');
        return this;
    },

    renderItems : function()
    {
        var html = '';
        this.collection.each(function(item) {
            html += this.tpl.item(item.attributes);
        }, this);
        this.$('.body').html(html);
        return this;
    }
});
