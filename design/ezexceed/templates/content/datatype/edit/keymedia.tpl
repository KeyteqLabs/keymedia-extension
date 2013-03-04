{def
    $handler = $attribute.content
    $media = $handler.media
}
{if is_set($attribute_base)|not}
    {def $attribute_base = "ContentObjectAttribute"}
{/if}

<div class="attribute-base"
    data-handler='keymedia/keymedia'
    data-url-root='{"/"|ezurl("no")}'
    {literal}
    data-paths='{
        "keymedia" : "/extension/keymedia/design/ezexceed/javascript/",
        "brightcove" : "http://admin.brightcove.com/js/BrightcoveExperiences"
    }'
    {/literal}
    data-bootstrap='{$media.data|json}'
>

{if $handler.backend|not}
    <p class="error">{'No KeyMedia connection for content class'|i18n('keymedia')}</p>
{else}
    {if and( $media, $handler.mediaFits|not )}
        <p class="error">{'The uploaded media might be too small for this format'|i18n('keymedia')}</p>
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

        <div class="keymedia-tags">
            <div class="input-append add-tag">
                <input type="text" class="tag" placeholder="{'Write tag'|i18n('keymedia')}" data-autosave="off">
                <button class="btn tag" disabled type="button">{'Add tag'|i18n('keymedia')}</button>
            </div>
            <div class="tags"></div>
        </div>
    </div>
{/if}
<div class="eze-no-image">
    <button type="button" class="btn from-keymedia">
        {'Browse media library'|i18n('keymedia')}
    </button>
    <span class="upload-container" id="keymedia-local-file-container-{$attribute.id}">
        <button type="button" class="btn upload-from-disk"
            id="keymedia-local-file-{$attribute.id}">
            {'Upload new media'|i18n('keymedia')}
        </button>
        <div class="upload-progress hide"><div class="progress"></div></div>
    </span>
</div>
</div>
