// A Keymedia attributes model
KeyMedia.models.Attribute = Backbone.Model.extend({
    prefix : null,
    medias : null,
    controller : null,

    initialize : function(options)
    {
        _.bindAll(this);
        this.medias = new KeyMedia.models.MediaCollection();
        this.medias.attr = this;
    },

    url : function(method, extra) {
        extra = (extra || [this.id,this.version()]);
        return this.get('prefix') + '/' + ['keymedia', method].concat(extra).join('::');
    },

    media : function(media, extra) {
        var _this = this;
        $.getJSON(this.url('media', extra), function(resp) {
            var content = resp.content, data = content.media;
            if (_(content).has('toScale'))
                _this.set('toScale', content.toScale);

            if ('content' in content)
                data.content = content.content;
            media.set(data);

        });
        return media;
    },

    scale : function(media) {
        $.getJSON(this.url('scaler', [media]), this.onScale);
    },

    onScale : function(response) {
        this.trigger('scale', response);
    },

    // Create a new vanity url for a version
    // name should be a string to put on the back of the object name
    // coords should be an array [x,y,x2,y2]
    // size shoudl be an array [width,height]
    addVanityUrl : function(name, coords, size, options)
    {
        options = (options || {});
        var data = {
            name : name,
            coords : coords,
            size : size,
            keymediaId : this.medias.keymediaId
        };
        if (_(options).has('media'))
            data.mediaId = options.media.id;

        var url = ['keymedia', 'saveVersion', this.get('id'), this.version()].join('::'),
            context = this;
        $.ez(url, data, function(response)
        {
            context.trigger('version.create', response.content);
        });
        return this;
    },

    version : function()
    {
        if (this.controller && this.controller.getVersion())
        {
            return this.controller.getVersion();
        }
        return this.get('version');
    }
});
