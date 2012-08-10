KeyMedia.models.Media = Backbone.Model.extend({
    prefix : '',

    defaults : function() {
        return {
            id : '',
            host : '',
            type : ''
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
        return '//' + this.get('host');
    },

    url : function(method, extra) {
        return this.prefix + '/' + ['keymedia', 'media', this.id].join('::');
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

KeyMedia.models.MediaCollection = Backbone.Collection.extend({
    model : KeyMedia.models.Media,

    // Must end in trailing slash
    prefix : '/',

    attr : null,

    total : 0,

    limit : 25,

    keymediaId : null,

    initialize : function(options)
    {
        _.bindAll(this, 'search', 'onSearch', 'page', 'onPage');
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
        data.limit = this.limit;
        return $.getJSON(this.url('browse'), data, this.onSearch);
    },

    onSearch : function(resp)
    {
        if (resp && 'content' in resp && 'results' in resp.content)
        {
            this.keymediaId = resp.content.keymediaId;
            this.total = resp.content.results.total;
            this.reset(resp.content.results.hits);
            this.trigger('search', resp);
        }
    },

    page : function(q)
    {
        if (this.length < this.total)
        {
            var data = {};
            if (typeof q === 'string')
                data.q = q;

            data.limit = this.limit;

            data.offset = this.length;
            return $.getJSON(this.url('browse'), data, this.onPage);
        }
    },

    onPage : function(resp)
    {
        if (resp && 'content' in resp && 'results' in resp.content)
        {
            this.keymediaId = resp.content.keymediaId;
            this.add(resp.content.results.hits);
            this.trigger('page', resp);
        }
    },
});
