{def $base='ContentObjectAttribute'}
{run-once}
<style>
{literal}
#ezr-keymedia-modal .backdrop {
    z-index: 1;
    position: absolute;
    top: 0;
    bottom: 0;
    left: 0;
    right: 0;

    background: black;
    opacity: 0.7;
}

#ezr-keymedia-modal .content {
    z-index: 2;
    border: 1px solid #fff;
    background: white;
    position: absolute;
    top: 100px;
    bottom: 100px;
    right: 100px;
    left: 100px;
    opacity: 1;
}

#ezr-keymedia-modal .item {
    float: left;
    position: relative;
    text-align: center;
    border: 1px solid #999;
    width: 160px;
    height: 120px;
    padding: 2px;
    margin: 3px;
    overflow: hidden;
}
#ezr-keymedia-modal .item img {
    margin: 0 auto;
}
#ezr-keymedia-modal .item:hover .meta {
    display: block;
}
#ezr-keymedia-modal .item .meta {
    display: none;
    bottom: 30px;
    left: 0;
    margin: 0 auto;
    background: #fff;
    opacity: 0.7;
    position: absolute;
    width: 160px;
    overflow: hide;
}
{/literal}
</style>
{/run-once}

{*
{$attribute|attribute('show')}
*}

<button type="button" class="ezr-keymedia-remote-file">
    {'Choose from KeyMedia'|i18n( 'content/edit' )}
</button>

<button type="button" class="ezr-keymedia-local-file">
    {'Choose from computer'|i18n( 'content/edit' )}
</button>

<script type="text/javascript">
{literal}
$('button.ezr-keymedia-remote-file').click(function(e) {
    e.preventDefault();
    var parts = ['keymedia', 'browse'];
    parts.push(1);
    var url = '/ezjscore/call/' + parts.join('::'),
        data = {
            skeleton : true,
            modal: true
        };

    var renderer = function(data, tmpl) {
    };

    KeyMediaBrowser = function() {
        this.modal = false;
        this.body = false;

        this.url = function() {
            var parts = ['keymedia', 'browse'];
            // Make dynamic!
            parts.push(1);
            return '/ezjscore/call/' + parts.join('::');
        }
        this.search = function(q) {
            var data = {};
            if (!this.modal)
                data = {skeleton:true, modal: true};

            if (typeof q === 'string')
                data.q = q;
                
            var callback = this.onSearch, context = this;
            $.getJSON(this.url(), data, function(response) {
                callback.call(context, response);
            });
        };

        this.onSearch = function(response) {
            if (!this.modal && response.content.hasOwnProperty('modal')) {
                this.modal = $(response.content.modal).hide().prependTo('body');
                this.modal.find('.content').html(response.content.skeleton);
                this.modal.show(100);
                this.events();
            }

            this.body = this.modal.find('.content .body').html('');

            var i, tmpl = $(response.content.item), item;
            var results = response.content.results.hits;
            for (i = 0; i < results.length; i++) {
                item = this.item(results[i], tmpl);
                this.body.append(item);
            }
        };

        this.item = function(data, tmpl) {
            var node = tmpl.clone();
            node.find('img').attr('src', data.thumb.url);
            node.find('.meta').text(data.filename + ' (' + data.filesize + ')');
            if (data.shared) node.find('.share').addClass('shared');

            return node;
        };

        this.events = function() {
            var context = this;
            $('a', this.body).click(function(e) {
                e.preventDefault();
                confirm('Select');
            });
            $('button.close', this.modal).click(function(e) {
                context.modal.fadeOut(120, function() {
                    $(this).remove();
                });
            });
            $('button.search', this.modal).click(function(e) {
                e.preventDefault();
                var node = $(e.currentTarget);
                var q = node.prev().val();
                context.search(q);
            });
        };
    };
    var browser = new KeyMediaBrowser();
    browser.search();
});
{/literal}
</script>
