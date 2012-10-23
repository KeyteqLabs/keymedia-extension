{def $handler = $attribute.content
     $media = $handler.media
}
{if is_set($attribute_base)|not}
    {def $attribute_base = "ContentObjectAttribute"}
{/if}

{run-once}
    {if is_set($excludeJS)|not}
        {include uri="design:keymedia/jscss.tpl"}
    {/if}
{/run-once}

<div class="attribute-base"
    data-handler='keymedia/keymedia'
    data-url-root='{"/"|ezurl("no")}'
    {literal}data-paths='{"keymedia" : "/extension/keymedia/design/ezexceed/javascript/"}'{/literal}
    data-bootstrap='{$media.data|json}'
>

{if $handler.backend|not}
    <p class="error">{'No KeyMedia connection for content class'|i18n( 'keymedia' )}</p>
{else}
    {if and( $media, $handler.mediaFits|not )}
        <p class="error">{'The uploaded image might be too small for this format'|i18n( 'content/edit' )}</p>
    {/if}
{/if}

<input type="hidden" name="{$attribute_base}_media_id_{$attribute.id}" value="{$media.id}" class="media-id data"/>
<input type="hidden" name="{$attribute_base}_host_{$attribute.id}" value="{$media.host}" class="media-host data"/>
<input type="hidden" name="{$attribute_base}_type_{$attribute.id}" value="{$media.type}" class="media-type data"/>
<input type="hidden" name="{$attribute_base}_ending_{$attribute.id}" value="{$media.ending}" class="media-ending data"/>

{if $media}
    <div class="eze-image">
        {include uri="design:parts/keymedia/preview.tpl"
            attribute=$attribute
            media=$media
            handler=$handler}

        <div class="input-append add-tag">
            <input id="appendedInputButton" type="text" placeholder="{'Write tag'|i18n('keymedia')}">
            <button class="btn" type="button">{'Add tag'|i18n('keymedia')}</button>
        </div>
        <div class="tags">
        <!-- <span class="label">Tagname <button class="close">×</button></span> -->
        </div>
    </div>
    <div class="eze-no-image hide">
{else}
<div class="eze-no-image">
{/if}
    <button type="button" class="btn from-keymedia">
        {'Browse media library'|i18n('keymedia')}
    </button>
    <div class="upload-container" id="keymedia-local-file-container-{$attribute.id}">
        <button type="button" class="btn upload-from-disk"
            id="keymedia-local-file-{$attribute.id}">
            {'Upload new image'|i18n('keymedia')}
        </button>
        <div class="upload-progress hide"><div class="progress"></div></div>
    </div>
</div>
</div>
