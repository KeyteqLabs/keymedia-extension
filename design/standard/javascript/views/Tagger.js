ezrKeyMedia.views.Tagger = Backbone.View.extend({

    initialize : function(options)
    {
        _.bindAll(this, 'render', 'tag', 'save', 'update');
        this.el = $(this.el);
        this.model = 
        this.model.bind('change', this.update);
        return this;
    },

    p : function()
    {
        return this.$('.image-meta .tags p');
    },
    update : function(model) {
        var tags = this.model.get('tags');
        console.log(tags);
        this.p().text(tags.join(', '));
    },

    events : {
        'click .ezr-keymedia-tagger' : 'tag',
        'submit .tagedit' : 'tag'
    },

    render : function(media) {
        return this;
    },

    save : function(tags) {
        if (_.isString(tags))
        {
            tags = _(tags.split(',')).filter(function(item) { return !!item; });
        }

        var prev = this.p().text().split(',');
        tags = _.union(prev, tags);
        if (tags) this.model.tag(tags);
    },

    tag : function(e) {
        e.preventDefault();
        var input = this.$('.tagedit');
        this.save(input.val());
    }
});
