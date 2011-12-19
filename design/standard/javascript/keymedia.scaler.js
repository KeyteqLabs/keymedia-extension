window.KeyMediaScaler = Backbone.View.extend({
    // Holds reference to current selected scale li
    current : null,

    // Will hold the Jcrop API
    cropper : null,

    trueSize : [],

    // Dimensions of cropping image
    size : {
        w : 830,
        h : 580
    },

    initialize : function(options)
    {
        _.bindAll(this, 'render', 'changeScale');

        this.el = $(this.el);

        this.image = new KeyMediaImage({
            id : options.imageId,
            host : options.host
        });

        this.versions = options.versions;
        this.trueSize = options.trueSize;

        this.model.bind('scale', this.render);

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
            item.find('h2').text(r.name);
            item.find('span').text(r.dimension.join('x'));
            item.data('scale', r);
            ul.append(item);
        }

        // Enable the first scaling by simulating a click
        this.$('.header ul').find('a').first().click();

        return this;
    },

    storeVersion : function(selection, scale)
    {
        var vanityName = [scale.name, scale.dimension.join('x')].join('-'),
            coords = [selection.x, selection.y, selection.x2, selection.y2],
            size = scale.dimension;

        return this.model.addVanityUrl(vanityName, coords, size);
    },

    changeScale : function(e) {
        e.preventDefault();

        if (this.current !== null)
        {
            this.current.removeClass('active');
            // If a previous crop exists, save the coordinates as a new vanity url
            if (this.cropper)
                this.storeVersion(this.cropper.tellSelect(), this.current.data('scale'));
        }

        this.current = $(e.currentTarget);
        this.current.addClass('active');

        var scale = this.current.data('scale');

        var w = this.size.w, h = this.size.h;
        var x = parseInt((w - scale.dimension[0]) / 2, 10);
        var y = parseInt((h - scale.dimension[1]) / 2, 10);

        // Find initial placement of crop
        // x,y,x2,y2
        var select = [
            x,
            y,
            w - x,
            h - x
        ];
        var ratio = (scale.dimension[0] / scale.dimension[1]);

        // If an API exists we dont need to build Jcrop
        // but can just change crop
        if (this.cropper)
        {
            // Change selection to new selection
            this.cropper.setOptions({
                setSelect : select,
                aspectRatio : ratio,
                minSize : scale.dimension
            });
        }
        else
        {
            var context = this, size = this.trueSize;
            $('#ezr-keymedia-scaler-crop').Jcrop({
                aspectRatio : ratio,
                setSelect : select,
                minSize : scale.dimension
            }, function(a) {
                // Store reference to API
                context.cropper = this;
                // Set true size of image
                this.setOptions({trueSize : size});
            });
        }
    }
});
