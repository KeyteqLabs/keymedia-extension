{def $base='ContentObjectAttribute'
    $handler = $attribute.content
    $media = $handler.media
}

{run-once}
{ezcss( array('jquery.jcrop.css', 'keymedia.css') )}
{ezscript_require( array(
    'ezjsc::jquery',
    'libs/underscore-min.js',
    'libs/backbone-min.js',
))}
{ezscript( array(
    'libs/handlebars.js',
    'libs/plupload/plupload.js',
    'libs/plupload/plupload.html4.js',
    'libs/plupload/plupload.html5.js',
    'libs/jquery.jcrop.min.js',

    'keymedia/ns.js',
    'keymedia/Attribute.js',
    'keymedia/Media.js',

    'keymedia/views/Modal.js',
    'keymedia/views/KeyMedia.js',
    'keymedia/views/Scalebox.js',
    'keymedia/views/Scaler.js',
    'keymedia/views/Browser.js',
    'keymedia/views/Upload.js',
    'keymedia/views/Tagger.js',
    'keymedia/views/EzOE.js',

    'keymedia/run.js'
) )}

{include uri="design:parts/js_templates.tpl"}
{/run-once}

<div class="keymedia-type" data-bootstrap-media='{$media.data|json}'>
    {include uri="design:parts/keymedia/preview.tpl" attribute=$attribute}
    {include uri="design:parts/keymedia/interactions.tpl" attribute=$attribute base=$base}
</div>
