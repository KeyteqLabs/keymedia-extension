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
        var node = tmpl.clone();
        node.find('img').attr('src', data.thumb.url);
        node.find('.meta').text(data.filename + ' (' + data.filesize + ')');
        if (data.shared) node.find('.share').addClass('shared');

        return node;
    };

    $.getJSON(url, data, function(resp) {
        var i, tmpl = $(resp.content.item);

        var modal = $(resp.content.modal).hide().prependTo('body');
        modal.find('.content').html(resp.content.skeleton);
        var container = modal.find('.content .body');

        var results = resp.content.results.hits;
        for (i = 0; i < results.length; i++)
            container.append(renderer(results[i], tmpl));
        modal.show(100);
    });
});
{/literal}
</script>
