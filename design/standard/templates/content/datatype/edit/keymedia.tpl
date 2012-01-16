{ezcss( array('jquery.jcrop.css', 'keymedia.css') )}

{def $base='ContentObjectAttribute'
    $handler = $attribute.content
    $image = $handler.image
    $backend = $handler.backend
    $class_attribute = $handler.class
    $size = ezini( 'KeyMedia', 'EditSize', 'keymedia.ini' )
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

<div class="keymedia-image">
    <div class="image-wrap">
        {attribute_view_gui format=array($size,$size) attribute=$attribute}
    </div>

    <div class="image-meta">
        <ul>
            <li>{'Title'|i18n( 'content/edit' )}: {$image.name|wash}</li>
            <li>{'Tags'|i18n( 'content/edit' )}: {$image.tags|implode(',')}</li>
            <li>{'Modified'|i18n( 'content/edit' )}: {$image.modified|datetime('iso8601')}</li>
            <li>{'Size'|i18n( 'content/edit' )}: {$handler.filesize|si( byte )}</li>
        </ul>
    </div>
</div>

<div id="keymedia-buttons-{$attribute.id}" data-prefix={'/ezjscore/call'|ezurl} class="keymedia-buttons"
    data-id={$attribute.id}
    data-contentobject-id={$attribute.contentobject_id}
    data-backend={$backend.id}
    data-backend-host='{$backend.host}'
    data-version={$attribute.version}>

    <input type="hidden" name="{$base}_image_id_{$attribute.id}" value="{$image.id}" class="image-id" />
    <input type="hidden" name="{$base}_host_{$attribute.id}" value="{$backend.host}" 
        class="image-host" />

    <button type="button" class="ezr-keymedia-scale hid"
        data-size='{$image.size|json}'
        data-versions='{$handler.toscale|json}'>
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
