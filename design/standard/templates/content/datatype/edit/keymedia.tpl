{def $base='ContentObjectAttribute'
    $backend = $attribute.content.backend
}

{ezscript_require( array(
    'ezjsc::jquery',
    'plupload/plupload.js',
    'plupload/plupload.html4.js',
    'plupload/plupload.html5.js',

    'keymedia.js'
) )}
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
#ezr-keymedia-modal .item a {
    display: block;
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

{* Current image. *}
<div class="keymedia-image">
    {attribute_view_gui image_class=ezini( 'ImageSettings', 'DefaultEditAlias', 'content.ini' ) attribute=$attribute}

    <p>
    {$attribute.content.original.mime_type|wash( xhtml )}
    {$attribute.content.original.filesize|si( byte )}
    </p>

</div>

<div id="keymedia-buttons-{$attribute.id}" data-prefix={'/ezjscore/call'|ezurl}
    data-contentobject-id={$attribute.contentobject_id}
    data-backend={$backend.id}
    data-version={$attribute.version}>

    <input type="hidden" name="{$base}_image_id_{$attribute.id}" value="0" class="image-id" />
    <input type="hidden" name="{$base}_host_{$attribute.id}" value="{$backend.host}" />

    <button type="button" class="ezr-keymedia-remote-file">
        {'Choose from KeyMedia'|i18n( 'content/edit' )}
    </button>

    <button type="button" class="ezr-keymedia-local-file" id="ezr-keymedia-local-file-{$attribute.id}">
        {'Choose from computer'|i18n( 'content/edit' )}
    </button>

    <div id="ezr-keymedia-progress-{$attribute.id}"></div>
</div>

<script type="text/javascript">
    var id = {$attribute.id};
    {literal}
    (function(data) {
        var container = $('#keymedia-buttons-' + id);
        var value = container.find('.image-id');
        container.data('browser', new window.KeyMediaBrowser({
            prefix : container.data('prefix'),
            value : value,
            backend : container.data('backend'),
            contentObjectId : container.data('contentobject-id'),
            version : container.data('version'),
            id : id
        }));

        $('.ezr-keymedia-remote-file', container).click(function(e) {
            container.data('browser').search();
        });

        var uploader = new plupload.Uploader({
            runtimes : 'html5,html4',
            browse_button : 'ezr-keymedia-local-file-' + id,
            container : 'ezr-keymedia-progress-' + id,
            max_file_size : '10mb',
            url : container.data('prefix') + '/keymedia::upload',
            multipart_params : {
                'AttributeID' : id,
                'ContentObjectVersion' : container.data('version'),
                'ContentObjectID' : container.data('contentobject-id')
            },
            headers : {
                'Accept' : 'application/json, text/javascript, */*; q=0.01'
            }
        });


        uploader.init();
        uploader.bind('FilesAdded', function(up, files)
        {
            up.start();
        });
    }(id));
    {/literal}
</script>
