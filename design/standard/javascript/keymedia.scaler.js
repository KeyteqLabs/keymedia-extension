window.KeyMediaScaler = Backbone.View.extend({
    // Holds reference to current selected scale li
    current : null,

    // Will hold the Jcrop API
    cropper : null,

    trueSize : [],

    // size of cropping image
    size : {
        w : 830,
        h : 580
    },

    initialize : function(options)
    {
        _.bindAll(this, 'render', 'changeScale', 'versionCreated');

        this.el = $(this.el);

        this.image = new KeyMediaImage({
            id : options.imageId,
            host : options.host
        });

        this.versions = options.versions;
        this.trueSize = options.trueSize;

        this.model.bind('scale', this.render);
        this.model.bind('version.create', this.versionCreated);

        return this;
    },

    events : {
        'click .header li' : 'changeScale'
    },

    render : function(response) {
        if (response.content.hasOwnProperty('skeleton'))
        {
            this.el.html(response.content.skeleton);
        }

        this.$('img').attr({
            src : this.image.thumb(this.size.w, this.size.h, 'jpg')
        });

        var i, scale = $(response.content.scale), item, r;
        var ul = this.$('.header ul');
        for (i = 0; i < this.versions.length; i++) {
            r = this.versions[i];
            item = scale.clone();
            item.attr('id', this.scaledId(r));
            item.find('h2').text(r.name);
            item.find('span').text(r.size.join('x'));
            item.data('scale', r);
            ul.append(item);
        }

        // Enable the first scaling by simulating a click
        this.$('.header ul').find('a').first().click();

        return this;
    },

    versionName : function(item)
    {
        return [item.name, item.size.join('x')].join('-').toLowerCase();
    },

    scaledId : function(item)
    {
        return 'scaled-' + item.name;
    },

    storeVersion : function(selection, scale)
    {
        var vanityName = scale.name,
            coords = [selection.x, selection.y, selection.x2, selection.y2],
            size = scale.size;
        console.log('storeVersion', vanityName, coords, size);

        return this.model.addVanityUrl(vanityName, coords, size);
    },

    versionCreated : function(data)
    {
        var menuElement = this.$('#scaled-' + data.name.toLowerCase());
        menuElement.data('scale', data);
    },

    changeScale : function(e) {
        e.preventDefault();

        if (this.current !== null)
        {
            this.current.removeClass('active');
            // If a previous crop exists, save the coordinates as a new vanity url
            var scale = this.current.data('scale'), selection = this.cropper.tellSelect();
            if (this.cropper)
                this.storeVersion(selection, scale);
        }

        this.current = $(e.currentTarget);
        this.current.addClass('active');

        var scale = this.current.data('scale');

        var w = this.size.w, h = this.size.h;
        var x = parseInt((w - scale.size[0]) / 2, 10);
        var y = parseInt((h - scale.size[1]) / 2, 10);

        // Find initial placement of crop
        // x,y,x2,y2
        var select;
        if ('coords' in scale)
        {
            select = scale.coords;
        }
        else
        {
            select = [
                x,
                y,
                w - x,
                h - x
            ];
        }
        var ratio = (scale.size[0] / scale.size[1]);

        // If an API exists we dont need to build Jcrop
        // but can just change crop
        if (this.cropper)
        {
            // Change selection to new selection
            this.cropper.setOptions({
                setSelect : select,
                aspectRatio : ratio,
                minSize : scale.size
            });
        }
        else
        {
            var context = this, size = this.trueSize;
            $('#ezr-keymedia-scaler-crop').Jcrop({
                aspectRatio : ratio,
                setSelect : select,
                minSize : scale.size
            }, function(a) {
                // Store reference to API
                context.cropper = this;
                // Set true size of image
                this.setOptions({trueSize : size});
            });
        }
    }
});
