{ezcss( array('jquery.jcrop.css', 'keymedia.css') )}

{def $base='ContentObjectAttribute'
    $handler = $attribute.content
    $image = $handler.image
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
    'views/Upload.js',

    'keymedia.js',
) )}

{include uri="design:parts/edit_preview.tpl" attribute=$attribute}

<div id="keymedia-buttons-{$attribute.id}" data-prefix={'/ezjscore/call'|ezurl} class="keymedia-buttons"
    data-id={$attribute.id}
    data-contentobject-id={$attribute.contentobject_id}
    data-backend={$backend.id}
    data-version={$attribute.version}>

    <input type="hidden" name="{$base}_image_id_{$attribute.id}" value="{$image.id}" class="image-id" />
    <input type="hidden" name="{$base}_host_{$attribute.id}" value="{$image.host}" class="image-host" />

    <input type="button" class="ezr-keymedia-scale hid button"
        data-size='{$image.size|json}'
        data-versions='{$handler.toscale|json}'
        value="{'Scale'|i18n( 'content/edit' )}">

    <input type="button" class="ezr-keymedia-remote-file button" value="{'Choose from KeyMedia'|i18n( 'content/edit' )}">

    <div class="ezr-keymedia-local-file-container" id="ezr-keymedia-local-file-container-{$attribute.id}">
        <input type="button" class="ezr-keymedia-local-file button" id="ezr-keymedia-local-file-{$attribute.id}"
            value="{'Choose from computer'|i18n( 'content/edit' )}">
    </div>

    <div class="upload-progress hid" id="ezr-keymedia-progress-{$attribute.id}">
        <div class="progress"></div>
    </div>
</div>
