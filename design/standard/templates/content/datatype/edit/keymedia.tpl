{def $base='ContentObjectAttribute'
    $handler = $attribute.content
    $backend = $handler.backend
    $class_attribute = $handler.class
}

{ezscript_require( array(
    'ezjsc::jquery',
    'libs/underscore-min.js',
    'libs/backbone-min.js',
))}
{ezscript( array(
    'plupload/plupload.js',
    'plupload/plupload.html4.js',
    'plupload/plupload.html5.js',

    'jquery.jcrop.min.js',
    'ns.js',
    'models/KeyMedia.js',
    'models/Image.js',

    'views/Modal.js',
    'views/KeyMedia.js',
    'views/Scalebox.js',
    'views/Scaler.js',
    'views/Browser.js',

    'keymedia.js',
) )}
{ezcss( array(
    'jquery.jcrop.css',
    'keymedia.css'
) )}

{* Current image. *}
<div class="keymedia-image">
    {def $size=ezini( 'KeyMedia', 'EditSize', 'keymedia.ini' )}
    {attribute_view_gui format=array($size,$size) attribute=$attribute}

    <p>
    {$attribute.content.mime_type|wash( xhtml )}
    {$attribute.content.filesize|si( byte )}
    </p>

</div>

<div id="keymedia-buttons-{$attribute.id}" data-prefix={'/ezjscore/call'|ezurl} class="keymedia-buttons"
    data-id={$attribute.id}
    data-contentobject-id={$attribute.contentobject_id}
    data-backend={$backend.id}
    data-backend-host='{$backend.host}'
    data-version={$attribute.version}>

    <input type="hidden" name="{$base}_image_id_{$attribute.id}" value="{$attribute.content.image.id}" class="image-id" />
    <input type="hidden" name="{$base}_host_{$attribute.id}" value="{$backend.host}" />

    <button type="button" class="ezr-keymedia-scale hid"
        data-size='{$attribute.content.image.size|json}'
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
