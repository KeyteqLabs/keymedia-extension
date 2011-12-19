(function($) {
    window.KeyMediaBrowser = function(options) {
        this.modal = false;
        this.body = false;
        this.backend = options.backend;
        // $ node of input field
        this.value = options.value;

        this.scalerSize = {
            w : 830,
            h : 580
        };

        this.prefix = options.prefix;
        this.id = options.id;
        this.version = options.version;
        this.contentObjectId = options.contentObjectId;

        this.url = function(method, extras) {
            extras = (extras || []);
            var parts = ['keymedia', method];
            for (var i = 0; i < extras.length; i++)
                parts.push(extras[i]);
            return this.prefix + '/' + parts.join('::');
        }
        this.search = function(q) {
            var data = {};
            if (!this.modal)
                data = {skeleton:true, modal: true};

            if (typeof q === 'string')
                data.q = q;
                
            var url = this.url('browse', [this.backend]);
            var callback = this.onSearch, context = this;
            $.getJSON(url, data, function(response) {
                callback.call(context, response);
            });
        };

        this.onSearch = function(response) {
            this.body = this.assureModal(response, '').html('');

            var i, tmpl = $(response.content.item), item;
            var results = response.content.results.hits;
            for (i = 0; i < results.length; i++) {
                item = this.item(results[i], tmpl);
                this.body.append(item);
            }
            this.eventsBrowse();
        };

        this.item = function(data, tmpl) {
            var node = tmpl.clone();
            node.find('a').data('id', data.id);
            node.find('img').attr('src', data.thumb.url);
            node.find('.meta').text(data.filename + ' (' + data.filesize + ')');
            if (data.shared) node.find('.share').addClass('shared');

            return node;
        };

        this.select = function(e) {
            e.preventDefault();
            var node = $(e.currentTarget);
            this.value.val(node.data('id'));
            this.close();
        };

        // Open a scaling gui
        this.scaler = function(settings) {
            if (typeof settings !== 'object')
                throw 'Missing scaler settings';

            var data = {
                versions : settings.versions
            };
            if (!this.modal) {
                data.modal = true;
                data.skeleton = true;
            }

            var url = this.url('scaler', [this.backend, settings.image]);
            var callback = this.renderScaler, context = this;
            $.getJSON(url, data, function(response) {
                callback.call(context, response, settings);
            });
        };

        this.renderScaler = function(response, settings) {
            this.body = this.assureModal(response, '');

            var i, scale = $(response.content.scale), item, r;
            var scales = this.modal.find('.header ul');
            for (i = 0; i < settings.versions.length; i++) {
                r = settings.versions[i];
                item = scale.clone();
                item.find('em').text(r.name);
                item.find('span').text(r.dimension.join('x'));
                item.find('a').data('scale', r);
                scales.append(item);
            }

            var scaler = this.scalerSize;
            this.body.find('img').attr({
                src : 'http://keymediarevived.raymond.keyteq.no/' + scaler.w + 'x' + scaler.h + '/' + settings.image + '.jpg'
            });
            this.eventsScale();
            return;
        };

        this.changeScale = function(e, scale) {
            e.preventDefault();
            var w = this.scalerSize.w, h = this.scalerSize.h;
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
        };
        this.eventsScale = function() {
            var context = this;
            this.modal.find('.header a').click(function(e) {
                context.changeScale.call(context, e, $(this).data('scale'));
            });
        };
        this.eventsBrowse = function() {
            var context = this;
            $('a', this.body).click(function(e) {
                context.select.call(context, e);
            });
            $('button.close', this.modal).click(function(e) {
                context.close.call(context, e);
            });
            $('button.search', this.modal).click(function(e) {
                e.preventDefault();
                var node = $(e.currentTarget);
                var q = node.prev().val();
                context.search(q);
            });
        };
        // private helper
        this.assureModal = function(response) {
            if (!this.modal && response.content.hasOwnProperty('modal')) {
                this.modal = $(response.content.modal).prependTo('body');
            }
            if (this.modal && response.content.hasOwnProperty('skeleton')) {
                this.modal.find('.content').html(response.content.skeleton);
            }
            if (!this.body) {
                this.body = this.modal.find('.content .body');
            }

            return this.body;
        };

        this.close = function(e) {
            this.modal.remove();
            this.modal = false;
            this.body = false;
        };
    };
}(jQuery));
