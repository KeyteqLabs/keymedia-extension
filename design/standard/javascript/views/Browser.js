ezrKeyMedia.Browser = Backbone.View.extend({
    initialize : function(options)
    {
        _.bindAll(this, 'render', 'item', 'select');

        this.model.bind('search', this.render);
        this.el = $(this.el);

        this.saveTo = options.saveTo;

        return this;
    },

    events : {
        'click button.search' : 'search',
        'click .item a' : 'select'
    },

    select : function(e) {
        e.preventDefault();
        var node = $(e.currentTarget);
        this.saveTo.val(node.data('id'));
        this.$('.close').click();
    },

    search : function(e)
    {
        e.preventDefault();
        this.model.search(
            this.input.val(),
            {skeleton : undefined}
        );
    },
    render : function(response) {
        if (response.content.hasOwnProperty('skeleton'))
        {
            this.el.html(response.content.skeleton);
            this.input = this.$('[type=text]');
        }
        else
        {
            this.el.find('.body').html('');
        }

        var body = this.el.find('.body');

        var i, tmpl = $(response.content.item), item;
        var results = response.content.results.hits;
        for (i = 0; i < results.length; i++) {
            item = this.item(results[i], tmpl);
            body.append(item);
        }

        this.delegateEvents();
        return this;
    },

    item : function(data, tmpl) {
        var node = tmpl.clone();
        node.find('a').data('id', data.id);
        node.find('img').attr('src', data.thumb.url);
        node.find('.meta').text(data.filename + ' (' + data.filesize + ')');
        if (data.shared) node.find('.share').addClass('shared');

        return node;
    }
});
