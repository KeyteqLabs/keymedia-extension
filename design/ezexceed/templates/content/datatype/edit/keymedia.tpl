{if eq(is_set($attribute_base), false())}
    {def $attribute_base='ContentObjectAttribute'}
{/if}
{def $handler = $attribute.content
     $media = $handler.media }
{run-once}
    {if is_set($excludeJS)|not}
        {include uri="design:keymedia/js.tpl"}
    {/if}
{/run-once}
<div class="attribute-base" data-attribute-base='{$attribute_base}' data-id='{$attribute.id}' data-handler='KeyMedia.views.KeyMedia'
    data-bootstrap='{$media.data|json}' data-version='{$attribute.version}'>
    {if and( $media, $handler.mediaFits|not )}
        <p class="error">{'The uploaded image might be too small for this format'|i18n( 'content/edit' )}</p>
    {/if}
    <section {if $media}class="image-container with-image"{else}class="image-container"{/if}>
    {if $media}
        <div class="keymedia-preview current-image">
            {include uri="design:parts/keymedia/preview.tpl"
                attribute=$attribute
                media=$media
                handler=$handler}
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
                <div class="kp-icon32 loading-icon loader hide"></div>
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
