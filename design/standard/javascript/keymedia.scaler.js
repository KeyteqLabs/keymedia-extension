window.KeyMediaScaler = Backbone.View.extend({
    size : {
        w : 830,
        h : 580
    },
    initialize : function(options)
    {
        _.bindAll(this, 'render', 'changeScale');

        this.el = $(this.el);

        this.image = options.image;
        this.versions = options.versions;

        this.model.bind('scale', this.render);

        return this;
    },

    events : {
        'click .header a' : 'changeScale'
    },

    render : function(response) {
        if (response.content.hasOwnProperty('skeleton'))
        {
            this.el.html(response.content.skeleton);
        }

        var i, scale = $(response.content.scale), item, r;
        var scales = this.$('.header ul');
        for (i = 0; i < this.versions.length; i++) {
            r = this.versions[i];
            item = scale.clone();
            item.find('h2').text(r.name);
            item.find('span').text(r.dimension.join('x'));
            item.find('a').data('scale', r);
            scales.append(item);
        }

        var scaler = this.size;
        this.$('img').attr({
            src : 'http://keymediarevived.raymond.keyteq.no/' + this.size.w + 'x' + this.size.h + '/' + this.image + '.jpg'
        });
        return this;
    },

    changeScale : function(e) {
        e.preventDefault();
        var scale = $(e.currentTarget).data('scale');

        var w = this.size.w, h = this.size.h;
        var x = parseInt((w - scale.dimension[0]) / 2, 10);
        var y = parseInt((h - scale.dimension[1]) / 2, 10);
        // x,y,x2,y2
        var initial = [
            x,
            y,
            w - x,
            h - x
        ];
        $('#ezr-keymedia-scaler-crop').Jcrop({
            ratio : (scale.dimension[0] / scale.dimension[1]),
            setSelect : initial
        });
    }
});
