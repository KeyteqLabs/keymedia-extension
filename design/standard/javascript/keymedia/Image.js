KeyMedia.models.Image = Backbone.Model.extend({
    prefix : '',

    defaults : function() {
        return {
            id : '',
            host : ''
        };
    },

    initialize : function(options)
    {
        options = (options || {});
        _.bindAll(this, 'thumb', 'domain', 'removeTag', 'addTag', 'saveAttr');
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
        // KeyMedia.models.Attribute instance
        var url = this.attr.url('tag', [this.attr.id, this.attr.get('version')]),
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

KeyMedia.models.ImageCollection = Backbone.Collection.extend({
    model : KeyMedia.models.Image,

    // Must end in trailing slash
    prefix : '/',

    attr : null,

    initialize : function(options)
    {
        _.bindAll(this, 'search', 'onSearch');
        return this;
    },

    url : function()
    {
        return this.attr.url('browse');
    },

    search : function(q, filters)
    {
        var data = (filters || {});
        if (typeof q === 'string')
            data.q = q;
        return $.getJSON(this.url('browse'), data, this.onSearch);
    },

    onSearch : function(resp)
    {
        if (resp && 'content' in resp && 'results' in resp.content)
        {
            this.reset(resp.content.results.hits);
            this.trigger('search', resp);
        }
    }
});
