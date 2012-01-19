ezrKeyMedia.models.model = Backbone.Model.extend({
    prefix : null,

    initialize : function(options)
    {
        _.bindAll(this, 'onSearch', 'search', 'scale', 'onScale', 'addVanityUrl');
    },

    url : function(method, extra) {
        extra = (extra || []);
        return this.get('prefix') + '/' + ['keymedia', method, this.id].concat(extra).join('::');
    },

    search : function(q, include) {
        var data = (include || {skeleton:true});

        if (typeof q === 'string')
            data.q = q;
            
        $.getJSON(this.url('browse'), data, this.onSearch);
    },

    scale : function(image) {
        $.getJSON(this.url('scaler', [image]), this.onScale);
    },

    onScale : function(response) {
        this.trigger('scale', response);
    },

    onSearch : function(response)
    {
        this.trigger('search', response);
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
        var url = this.url('saveVersion', [this.get('attributeId'), this.get('version')]),
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
