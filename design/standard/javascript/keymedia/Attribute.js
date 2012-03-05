// A Keymedia attributes model
KeyMedia.models.Attribute = Backbone.Model.extend({
    prefix : null,
    images : null,

    initialize : function(options)
    {
        _.bindAll(this, 'scale', 'onScale', 'addVanityUrl', 'image');
        this.images = new KeyMedia.models.ImageCollection();
        this.images.attr = this;
    },

    url : function(method, extra) {
        extra = (extra || [this.id,this.get('version')]);
        return this.get('prefix') + '/' + ['keymedia', method].concat(extra).join('::');
    },

    image : function(image) {
        $.getJSON(this.url('image'), function(resp) {
            var content = resp.content, data = content.image;
            if ('content' in content)
                data.content = content.content;
            image.set(data);
        });
        return image;
    },

    scale : function(image) {
        $.getJSON(this.url('scaler', [image]), this.onScale);
    },

    onScale : function(response) {
        this.trigger('scale', response);
    },

    // Create a new vanity url for a version
    // name should be a string to put on the back of the object name
    // coords should be an array [x,y,x2,y2]
    // size shoudl be an array [width,height]
    addVanityUrl : function(name, coords, size)
    {
        var data = {
            name : name,
            coords : coords,
            size : size
        };
        var url = this.url('saveVersion', [this.get('id'), this.get('version')]),
            context = this;
        $.ajax({
            url : url,
            data : data,
            dataType : 'json',
            type : 'POST',
            success : function(response) {
                context.trigger('version.create', response.content);
            }
        });
    }
});
