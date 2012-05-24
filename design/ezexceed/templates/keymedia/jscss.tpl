{run-once}
{ezcss( array('jquery.jcrop.css', 'keymedia.css') )}
{ezscript( array(
'libs/handlebars.js',
'libs/jquery.jcrop.min.js',

'keymedia/ns.js',
'keymedia/Attribute.js',
'keymedia/Image.js',

'keymedia/views/scaled_version.js',
'keymedia/views/scaler.js',
'keymedia/views/browser.js',
'keymedia/views/keymedia.js',

'keymedia/views/Modal.js',
'keymedia/views/Upload.js',
'keymedia/views/Tagger.js',
) )}
{include uri="design:parts/js_templates.tpl"}
{/run-once}