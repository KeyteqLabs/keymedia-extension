KeyMedia.views.Scaler = Backbone.View.extend({
    // constants
    TRANSLATIONS : null,

    // size of cropping media
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

    singleVersion : false,

    selectedVersion : null,

    initialize : function(options)
    {
        options = (options || {});
        _.bindAll(this);
        this.versionViews = [];
        this.tpl = {
            scaler : Handlebars.compile($('#tpl-keymedia-scaler').html())
        };
        this.TRANSLATIONS = _KeyMediaTranslations;

        _(this).extend(options);

        if (!_(options).has('media')) {
            this.media = new KeyMedia.models.Media({
                id : options.mediaId,
                host : options.host,
                type : options.type
            });
        }

        // Model is an instance of Attribute
        this.model.bind('scale', this.render);
        this.model.bind('version.create', this.versionCreated);

        // When I get popped from stack
        // i save my current scale
        this.on('destruct', _.bind(function() {
            this.saveCrop();
        }, this));

        return this;
    },

    events : {
        'click .header li' : 'changeScale',
        'mouseenter .header li.cropped' : 'overlay'
    },

    overlay : function(e) {
        var node = $(e.currentTarget);
        var overlay = node.find('div');
        if (node !== this.current) {
            this.createOverlay(overlay, node.data('scale'));
        }
    },

    // render the overlay div for a scaled versions
    // menu item. Happens on mouseenter on the li or after saving
    // new crop information
    createOverlay : function(node, data) {
        if (this.cropper && 'coords' in data && data.coords.length === 4) {

            var scale = this.cropper.getScaleFactor(),
                container = node.parent(),
                coords = data.coords;

            var x = parseInt(coords[0] / scale[0], 10),
                y = parseInt(coords[1] / scale[1], 10),
                x2 = parseInt(coords[2] / scale[0], 10),
                y2 = parseInt(coords[3] / scale[1], 10);

            node.css({
                'top' : y + container.outerHeight(true),
                left : x,
                width : parseInt(x2 - x, 10),
                height : parseInt(y2 - y, 10)
            });
        }
    },

    render : function() {
        var content = this.tpl.scaler({
            tr : this.TRANSLATIONS,
            media : this.media.thumb(this.SIZE.w, this.SIZE.h, 'jpg')
        });
        this.$el.append(content);

        var outerBounds = this.outerBounds(this.versions, 4, 40);
        var _view, _container = this.$('.header ul'),
            className;
        this.versionViews = _(this.versions).map(function(version) {
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
        var selectedEl;

        if (this.selectedVersion) {
            var scale;
            var _this = this;
            selectedEl = this.$('.header ul li').filter(function(){
                scale = $(this).data('scale');
                if (scale && _(scale).has('name') && scale.name == _this.selectedVersion)
                    return true;
                return false;
            });

            selectedEl.find('a').click();
        }

        if (!selectedEl) {
            // Enable the first scaling by simulating a click
            this.$('.header ul li:first-child a').click();
        }

        return this;
    },

    // Calculate outer bounds for preview boxes
    outerBounds : function(versions, gt, lt)
    {
        var i, w, h, min = {w:0,h:0}, max = {w:0,h:0};
        for (i = 0; i < versions.length; i++) {
            if (_(versions[i]).has('size') && _(versions[i].size).isArray())
            {
                w = parseInt(versions[i].size[0], 10);
                h = parseInt(versions[i].size[1], 10);
            }
            else
            {
                w = parseInt(this.media.get('width'), 10);
                h = parseInt(this.media.get('height'), 10);
            }

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

        if (!size)
            size = [selection.w, selection.h];

        // Must store scale coords back onto object
        scale.coords = [selection.x, selection.y, selection.x2, selection.y2];

        return this.model.addVanityUrl(vanityName, scale.coords, size, {media : this.media});
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
            if (version.name === name)
            {
                scaleButtonVersions[key].coords = coords;
            }
        });
        if (this.app)
            this.app.versions = scaleButtonVersions;

        var menuElement = this.$('#scaled-' + data.name.toLowerCase());
        menuElement.data('scale', data);
        this.createOverlay(menuElement, data);
    },

    saveCrop : function()
    {
        var scale = this.current.data('scale');
        if (this.cropper && scale)
        {
            if (!scale.size)
            {
                //Use the actually viewed size
                var tellScaled = this.cropper.tellScaled();
                scale.size = [tellScaled.w, tellScaled.h];
            }

            this.storeVersion(this.cropper.tellSelect(), scale);
            this.current.removeClass('uncropped').addClass('cropped');
        }
    },

    changeScale : function(e) {
        if (e) e.preventDefault();
        var scale;

        if (this.current !== null)
        {
            this.current.removeClass('active');
            if (!this.singleVersion)
                this.saveCrop();
        }

        // If method is triggered without click we
        // should return after saving the current scale
        if (!e) return;

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

        var ratio = null,
            minSize = null;

        if (scale && scale.size)
        {
            ratio = (scale.size[0] / scale.size[1]);
            minSize = scale.size;
        }

        // If an API exists we dont need to build Jcrop
        // but can just change crop
        var context = this, size = this.trueSize;
        var cropperOptions = {
            setSelect : select
        };
        if (ratio)
            cropperOptions.aspectRatio = ratio;
        if (minSize)
            cropperOptions.minSize = minSize;

        if (this.cropper)
        {
            // Change selection to new selection
            this.cropper.setOptions(cropperOptions);
        }
        else
        {
            this.$('.image-wrap img').Jcrop({
                trueSize : size
            }, function(a) {
                // Store reference to API
                context.cropper = this;
                // Set true size of media
                this.setOptions(cropperOptions);
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
