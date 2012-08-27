{run-once}
    {ezscript( array(
    'libs/handlebars.js',
    'libs/jquery.jcrop.min.js',

    'keymedia/ns.js',
    'keymedia/Media.js',
    'keymedia/Attribute.js',

    'keymedia/views/scaled_version.js',
    'keymedia/views/scaler.js',
    'keymedia/views/browser.js',
    'keymedia/views/keymedia.js',

    'keymedia/views/Modal.js',
    'keymedia/views/Upload.js',
    'keymedia/views/Tagger.js',

    'keymedia/views/EzOE.js'
    ) )}
    {include uri="design:parts/js_templates.tpl"}
{/run-once}