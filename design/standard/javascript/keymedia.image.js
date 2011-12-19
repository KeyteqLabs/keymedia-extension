window.KeyMediaImage = Backbone.Model.extend({
    defaults : function() {
        return {
            id : '',
            host : ''
        };
    },

    initialize : function(options)
    {
        _.bindAll(this, 'thumb', 'addVanityUrl', 'domain');
    },

    domain : function()
    {
        return 'http://' + this.get('host');
    },

    url : function(method, extra) {
        return this.domain() + '/media/' + this.id + '.json';
    },

    // Generate thumb url for a given size
    thumb : function(width, height, filetype) {
        filetype = (filetype || 'jpg');
        return this.domain() + '/' + width + 'x' + height + '/' + this.id + '.' + filetype;
    },

    addVanityUrl : function(image, versions) {
    }
});
