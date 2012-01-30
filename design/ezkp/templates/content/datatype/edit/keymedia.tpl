{if eq(is_set($attribute_base), false())}
    {def $attribute_base='ContentObjectAttribute'}
{/if}
{def $handler = $attribute.content
    $image = $handler.image
}
{run-once}
{ezcss( array('jquery.jcrop.css', 'keymedia.css') )}
{ezscript( array(
    'libs/handlebars.js',
    'libs/jquery.jcrop.min.js',

    'keymedia/ns.js',
    'keymedia/Attribute.js',
    'keymedia/Image.js',

    'keymedia/views/browser.js',
    'keymedia/views/Keymedia.js',

    'keymedia/views/Modal.js',
    'keymedia/views/Scalebox.js',
    'keymedia/views/Scaler.js',
    'keymedia/views/Upload.js',
    'keymedia/views/Tagger.js',
) )}
{include uri="design:parts/js_templates.tpl"}
{/run-once}
<div class="attribute-base" data-attribute-base='{$attribute_base}' data-id='{$attribute.id}' data-handler='KeyMedia.views.KeyMedia'
    data-bootstrap='{$image.data|json}' data-version='{$attribute.version}'>
    <div class="keymedia-preview">
        {include uri="design:parts/keymedia/preview.tpl" attribute=$attribute}
    </div>
    <div class="keymedia-interactions">
        {include uri="design:parts/keymedia/interactions.tpl" attribute=$attribute base=$attribute_base}
    </div>
</div>
