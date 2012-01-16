ezrKeyMedia.models.Image = Backbone.Model.extend({
    prefix : '',

    defaults : function() {
        return {
            id : '',
            host : ''
        };
    },

    initialize : function(options)
    {
        _.bindAll(this, 'thumb', 'domain', 'removeTag', 'addTag');
        if ('prefix' in options)
            this.prefix = options.prefix;
    },

    domain : function()
    {
        return 'http://' + this.get('host');
    },

    url : function(method, extra) {
        return this.prefix + '/' + ['keymedia', 'image', this.id].join('::');
    },

    saveAttr : function()
    {
        var backend = this.get('backend');
        var url = this.get('prefix') + '/' + ['keymedia', 'tag', backend, this.id].join('::'),
            context = this, data = this.attributes;

        $.ajax({
            url : url,
            data : data,
            dataType : 'json',
            type : 'POST',
            success : function(response) {
                context.set(response.content);
            }
        });
    },

    addTag : function(tag) {
        var tags = this.get('tags');
        tags.push(tag);
        this.set({tags : _.uniq(tags)}, {silent:true});
        return this;
    },

    removeTag : function(rmTag) {
        var tags = _(this.get('tags')).filter(function(tag) {
            return tag !== rmTag;
        });
        this.set({tags : tags}, {silent:true});
        return this;
    },

    // Generate thumb url for a given size
    thumb : function(width, height, filetype) {
        filetype = (filetype || 'jpg');
        return this.domain() + '/' + width + 'x' + height + '/' + this.id + '.' + filetype;
    }
});
