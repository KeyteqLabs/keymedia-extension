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
        _.bindAll(this, 'thumb', 'domain', 'tag');
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

    tag : function(tags)
    {
        var backend = this.get('backend');
        var url = this.get('prefix') + '/' + ['keymedia', 'tag', backend, this.id].join('::'),
            data = {tags:tags},
            context = this;

        $.ajax({
            url : url,
            data : data,
            dataType : 'json',
            type : 'POST',
            success : function(response) {
                console.log(response.content);
                context.set(response.content);
            }
        });
    },

    // Generate thumb url for a given size
    thumb : function(width, height, filetype) {
        filetype = (filetype || 'jpg');
        return this.domain() + '/' + width + 'x' + height + '/' + this.id + '.' + filetype;
    }
});
