{def $base='ContentObjectAttribute'
    $handler = $attribute.content
    $image = $handler.image
}

{run-once}
{ezcss( array('jquery.jcrop.css', 'keymedia.css') )}
{ezscript_require( array(
    'ezjsc::jquery',
    'libs/underscore-min.js',
    'libs/backbone-min.js',
))}
{ezscript( array(
    'plupload/plupload.js',
    'plupload/plupload.html4.js',
    'plupload/plupload.html5.js',

    'libs/jquery.jcrop.min.js',
    'ns.js',
    'models/Attribute.js',
    'models/Image.js',

    'views/Modal.js',
    'views/KeyMedia.js',
    'views/Scalebox.js',
    'views/Scaler.js',
    'views/Browser.js',
    'views/Upload.js',
    'views/Tagger.js',

    'keymedia.js',
) )}
{/run-once}

<div class="keymedia-type" data-bootstrap-image='{$image.data|json}'>
    {include uri="design:parts/edit_preview.tpl" attribute=$attribute}
    {include uri="design:parts/edit_buttons.tpl" attribute=$attribute base=$base}
</div>
