window.KeyMediaScaler = Backbone.View.extend({
    current : null,
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

    changeScale : function(e) {
        e.preventDefault();

        if (this.current !== null)
        {
            this.current.removeClass('active');
        }

        this.current = $(e.currentTarget);
        this.current.addClass('active');

        var scale = this.current.data('scale');

        var w = this.size.w, h = this.size.h;
        var x = parseInt((w - scale.dimension[0]) / 2, 10);
        var y = parseInt((h - scale.dimension[1]) / 2, 10);

        // Find initial placement of crop
        // x,y,x2,y2
        var initial = [
            x,
            y,
            w - x,
            h - x
        ];
        var ratio = (scale.dimension[0] / scale.dimension[1]);
        console.log(ratio);
        $('#ezr-keymedia-scaler-crop').Jcrop({
            aspectRatio : ratio,
            setSelect : initial
        });
    }
});
