KeyMedia.views.Scaler = Backbone.View.extend({
    // constants
    HEADING : 'Select crops',
    TRANSLATIONS : null,

    // size of cropping image
    SIZE : {
        w : 830,
        h : 580
    },

    // Holds reference to current selected scale li
    current : null,

    // Will hold the Jcrop API
    cropper : null,

    trueSize : [],

    tpl : null,

    $img : null,

    versionViews : null,

    initialize : function(options)
    {
        options = (options || {});
        _.bindAll(this, 'render', 'changeScale', 'versionCreated', 'createOverlay');
        this.versionViews = [];
        this.tpl = {
            scaler : Handlebars.compile($('#tpl-keymedia-scaler').html())
        };
        this.TRANSLATIONS = _KeyMediaTranslations;

        if ('image' in options) {
            this.image = options.image;
        }
        else {
            this.image = new KeyMedia.models.Image({
                id : options.imageId,
                host : options.host
            });
        }

        this.versions = options.versions;
        this.trueSize = options.trueSize;
        this.app = options.app;

        // Model is an instance of Attribute
        this.model.bind('scale', this.render);
        this.model.bind('version.create', this.versionCreated);

        return this;
    },

    events : {
        'click .header li' : 'changeScale',
        'mouseenter .header li' : 'overlay'
    },

    overlay : function(e) {
        var node = $(e.currentTarget),
            overlay = node.find('div');

        if (node !== this.current) {
            this.createOverlay(node.find('div'), node.data('scale'));
        }
    },

    // render the overlay div for a scaled versions
    // menu item. Happens on mouseenter on the li or after saving
    // new crop information
    createOverlay : function(node, data) {
        if (this.cropper && 'coords' in data && data.coords.length === 4) {
            var scale = this.cropper.getScaleFactor(), container = this.$img.parent(), coords = data.coords;

            var x = parseInt(coords[0] / scale[0], 10),
                y = parseInt(coords[1] / scale[1], 10),
                x2 = parseInt(coords[2] / scale[0], 10),
                y2 = parseInt(coords[3] / scale[1], 10),
                offset = container.position();
            node.css({
                'top' : parseInt((offset.top - 0) + y, 10),
                left : parseInt((offset.left - 0) + x, 10),
                width : parseInt(x2 - x, 10),
                height : parseInt(y2 - y, 10)
            });
        }
    },

    render : function() {
        var content = this.tpl.scaler({
            tr : this.TRANSLATIONS,
            heading : this.HEADING,
            icon : '/extension/ezkp/design/ezkp/images/kp/32x32/Pictures-alt-2b.png',
            image : this.image.thumb(this.SIZE.w, this.SIZE.h, 'jpg')
        });
        this.$el.append(content);

        var outerBounds = this.outerBounds(this.versions, 4, 40), el, versions = this.versions;
        var _view, _container = this.$('.header ul'),
            className;
        this.versionViews = this.versions.map(function(version) {
            if ('url' in version)
                className = 'cropped';
            else
                className = 'uncropped';
            _view = new KeyMedia.views.ScaledVersion({
                model : version,
                outerBounds : outerBounds,
                className : className
            }).render();
            _container.append(_view.el);
            return _view;
        });

        this.$img = this.$('img');

        // Enable the first scaling by simulating a click
        this.$('.header ul').find('a').first().click();

        return this;
    },

    // Calculate outer bounds for preview boxes
    outerBounds : function(versions, gt, lt)
    {
        var i, w, h, min = {w:0,h:0}, max = {w:0,h:0};
        for (i = 0; i < versions.length; i++) {
            w = parseInt(versions[i].size[0], 10);
            h = parseInt(versions[i].size[1], 10);

            if (w > max.w) max.w = w;
            if (h > max.h) max.h = h;

            if (min.w === 0 || w < min.w) min.w = w;
            if (min.h === 0 || h < min.h) min.h = h;
        }

        return { max : max, min : min };
    },

    scaledId : function(item)
    {
        return 'scaled-' + item.name;
    },

    storeVersion : function(selection, scale)
    {
        var vanityName = scale.name,
            size = scale.size;

        // Must store scale coords back onto object
        scale.coords = [selection.x, selection.y, selection.x2, selection.y2];

        return this.model.addVanityUrl(vanityName, scale.coords, size);
    },

    versionCreated : function(data)
    {
        /**
         * HACK. Prevent old coords to be used when scaling again
         * Store new coords in scale button
         */
        var name = data.name,
            coords = data.coords;

        var scaleButtonVersions = this.versions;
        _(scaleButtonVersions).each(function(version, key){
            if (version.name == name)
            {
                scaleButtonVersions[key].coords = coords;
            }
        });
        this.app.versions = scaleButtonVersions;

        var menuElement = this.$('#scaled-' + data.name.toLowerCase());
        menuElement.data('scale', data);
        this.createOverlay(menuElement, data);
    },

    changeScale : function(e) {
        e.preventDefault();
        var scale;

        if (this.current !== null)
        {
            this.current.removeClass('active');

            if (this.cropper !== null)
            {
                // If a previous crop exists, save the coordinates as a new vanity url
                scale = this.current.data('scale');
                if (this.cropper && scale)
                {
                    this.storeVersion(this.cropper.tellSelect(), scale);
                    this.current.removeClass('uncropped').addClass('cropped');
                }
            }
        }

        this.current = $(e.currentTarget);
        this.current.addClass('active');
        scale = this.current.data('scale');

        if (typeof scale === 'undefined')
            return this;

        var w = this.SIZE.w, h = this.SIZE.h, x, y, x2, y2;

        // Find initial placement of crop
        // x,y,x2,y2
        if (scale && 'coords' in scale)
        {
            x = scale.coords[0] - 0;
            y = scale.coords[1] - 0;
            x2 = scale.coords[2] - 0;
            y2 = scale.coords[3] - 0;
        }
        else
        {
            x = parseInt((this.trueSize[0] - w) / 2, 10);
            y = parseInt((this.trueSize[1] - h) / 2, 10);
            x2 = parseInt((this.trueSize[0] + w) / 2, 10);
            y2 = parseInt((this.trueSize[1] + h) / 2, 10);
        }
        var select = [x,y,x2,y2];

        var ratio = (scale.size[0] / scale.size[1]);

        // If an API exists we dont need to build Jcrop
        // but can just change crop
        var context = this, size = this.trueSize;
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
            this.$('.image-wrap img').Jcrop({
                trueSize : size
            }, function(a) {
                // Store reference to API
                context.cropper = this;
                // Set true size of image
                this.setOptions({
                    aspectRatio : ratio,
                    setSelect : select,
                    minSize : scale.size
                });
            });
        }
    },

    close : function() {
        if (this.cropper) {
            this.cropper.destroy();
            this.cropper = null;
            this.current = null;
            this.delegateEvents([]);
            this.model.unbind('scale');
            this.model.unbind('version.create');
            this.$el.html('');
        }
    }
});
