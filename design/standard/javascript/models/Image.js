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
        _.bindAll(this, 'thumb', 'domain');
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

    // Generate thumb url for a given size
    thumb : function(width, height, filetype) {
        filetype = (filetype || 'jpg');
        return this.domain() + '/' + width + 'x' + height + '/' + this.id + '.' + filetype;
    }
});
