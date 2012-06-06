{if eq(is_set($attribute_base), false())}
    {def $attribute_base='ContentObjectAttribute'}
{/if}
{def $handler = $attribute.content
     $media = $handler.media }
{run-once}
    {if is_set($excludeJS)|not}
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
        ) )}
    {include uri="design:parts/js_templates.tpl"}
    {/if}
{/run-once}
<div class="attribute-base" data-attribute-base='{$attribute_base}' data-id='{$attribute.id}' data-handler='KeyMedia.views.KeyMedia'
    data-bootstrap='{$media.data|json}' data-version='{$attribute.version}'>
    <section {if $media}class="image-container with-image"{else}class="image-container"{/if}>
    {if $media}
        <div class="keymedia-preview current-image">
                <div class="image-wrap">
                    {if eq($attribute.content.id, 0)|not}
                        <div class="remove-wrap">
                            <span class="kp-icon16 remove-black"></span>
                            <input class="button remove"
                                   type="submit"
                                   name="CustomActionButton[{$attribute.id}_delete_image]"
                                   value="{'Remove current media'|i18n( 'content/edit' )}"/>
                        </div>
                    {/if}
                    <button type="button" class="edit-image scale action"
                            {if not( $handler.mediaFits )}disabled="disabled"{/if}
                            data-truesize='{$media.size|json}'
                            data-versions='{$handler.toscale|json}'>

                        {if $handler.mediaFits}
                        {'Scale variants'|i18n( 'content/edit' )}
                        {else}
                        {'Requires a bigger media'|i18n( 'content/edit' )}
                        {/if}
                    </button>

                    {attribute_view_gui format=array(200,200) attribute=$attribute, fetchinfo=true()}
                </div>
        </div>
    {/if}
    
    
    <div class="keymedia-interactions actions">
        <input type="hidden" name="{$attribute_base}_media_id_{$attribute.id}" value="{$media.id}" class="media-id"/>
        <input type="hidden" name="{$attribute_base}_host_{$attribute.id}" value="{$media.host}" class="media-host"/>
        <input type="hidden" name="{$attribute_base}_type_{$attribute.id}" value="{$media.type}" class="media-type"/>
        <input type="hidden" name="{$attribute_base}_ending_{$attribute.id}" value="{$media.ending}" class="media-ending"/>
        {if $handler.backend}
            <section class="edit-buttons"{if $media} style="display:none"{/if}>
                <span class="kp-icon50 pictures-icon"></span>
                <div class="upload-container" id="keymedia-local-file-container-{$attribute.id}"{if $media} style="display:none"{/if}>
                    <button type="button" class="upload-image upload upload-from-disk" id="keymedia-local-file-{$attribute.id}">{'Click to add'|i18n( 'content/edit' )}</button>
                        <div class="upload-progress hid"><div class="progress"></div></div>
                </div>
            </section>

            
        {else}
            <p class="error">{'No KeyMedia connection for content class'|i18n( 'keymedia' )}</p>
        {/if}
    </div>
    </section>
    {if $handler.backend}
        <button type="button" class="from-keymedia">{'Browse KeyMedia'|i18n( 'content/edit' )}</button>
    
    
    
        {if $media}
            <div class="tagger">
                <input type="text" class="tagedit" placeholder="{'Add a tag'|i18n( 'content/edit' )}"/>
                <button type="button" class="tagit">
                    {'Add tag'|i18n( 'content/edit' )}
                </button>
                <ul>
                </ul>
            </div>
        {/if}
    {/if}
</div>
