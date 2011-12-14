(function($) {
    window.KeyMediaBrowser = function(options) {
        this.modal = false;
        this.body = false;
        this.backend = options.backend;
        // $ node of input field
        this.value = options.value;

        this.prefix = options.prefix;
        this.id = options.id;
        this.version = options.version;
        this.contentObjectId = options.contentObjectId;

        this.url = function(method, extras) {
            extras = (extras || []);
            var parts = ['keymedia', method, this.backend];
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
                
            var callback = this.onSearch, context = this;
            $.getJSON(this.url('browse'), data, function(response) {
                callback.call(context, response);
            });
        };

        this.onSearch = function(response) {
            if (!this.modal && response.content.hasOwnProperty('modal')) {
                this.modal = $(response.content.modal).prependTo('body');
                this.modal.find('.content').html(response.content.skeleton);
            }

            this.body = this.modal.find('.content .body').html('');

            var i, tmpl = $(response.content.item), item;
            var results = response.content.results.hits;
            for (i = 0; i < results.length; i++) {
                item = this.item(results[i], tmpl);
                this.body.append(item);
            }
            this.events();
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

        this.close = function(e) {
            this.modal.remove();
            this.modal = false;
            this.body = false;
        };

        this.events = function() {
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
    };
}(jQuery));
