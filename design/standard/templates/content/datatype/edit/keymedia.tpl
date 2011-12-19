{def $base='ContentObjectAttribute'
    $backend = $attribute.content.backend
    $class_attribute = $attribute.content.class
}

{ezscript_require( array(
    'ezjsc::jquery',
    'libs/underscore-min.js',
    'libs/backbone-min.js',
    'plupload/plupload.js',
    'plupload/plupload.html4.js',
    'plupload/plupload.html5.js',

    'jquery.jcrop.min.js',
    'keymedia.scaler.js',
    'keymedia.browser.js',
    'keymedia.js',
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
        data-versions='{$attribute.content.toscale|json}'>
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
        var destination = container.find('.image-id');
        var model = new window.KeyMedia({
            id : container.data('backend'),
            prefix : container.data('prefix'),
            contentObjectId : container.data('contentobject-id'),
            version : container.data('version')
        });
        var keymedia = new window.KeyMediaView({
            model : model,
            destination : destination
        }).render();
        keymedia.el.prependTo('body');

        container.data('browser', keymedia);

        $('.ezr-keymedia-remote-file', container).click(function(e) {
            keymedia.search();
            //model.search();
        });

        $('.ezr-keymedia-scale', container).click(function(e) {
            e.preventDefault();
            var data = {
                image : destination.val(),
                versions : $(this).data('versions')
            };
            keymedia.scaler(data);
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
