{def $base='ContentObjectAttribute'
    $backend = $attribute.content.backend
    $class_attribute = $attribute.content.class
}

{ezscript_require( array(
    'ezjsc::jquery',
    'plupload/plupload.js',
    'plupload/plupload.html4.js',
    'plupload/plupload.html5.js',

    'jquery.jcrop.min.js',
    'keymedia.js'
) )}
{ezcss_require( array(
    'jquery.jcrop.css',
    'keymedia.css'
) )}

{* Current image. *}
<div class="keymedia-image">
    {attribute_view_gui image_class=ezini( 'ImageSettings', 'DefaultEditAlias', 'content.ini' ) attribute=$attribute}

    <p>
    {$attribute.content.mime_type|wash( xhtml )}
    {$attribute.content.filesize|si( byte )}
    </p>

</div>

<div id="keymedia-buttons-{$attribute.id}" data-prefix={'/ezjscore/call'|ezurl}
    data-contentobject-id={$attribute.contentobject_id}
    data-backend={$backend.id}
    data-version={$attribute.version}>

    <input type="hidden" name="{$base}_image_id_{$attribute.id}" value="{$attribute.content.image.id}" class="image-id" />
    <input type="hidden" name="{$base}_host_{$attribute.id}" value="{$backend.host}" />

    <button type="button" class="ezr-keymedia-scale"
        data-versions={$attribute.content.toscale|json}>
        {'Scale'|i18n( 'content/edit' )}
    </button>

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

        $('.ezr-keymedia-scale', container).click(function(e) {
            e.preventDefault();
            var data = {
                image : value.val(),
                versions : $(this).data('versions')
            };
            container.data('browser').scaler(data);
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
