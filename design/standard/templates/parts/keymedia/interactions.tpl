{if $handler.backend}
    {if eq(is_set($base), false())}
        {def $base='ContentObjectAttribute'}
    {/if}
<div id="keymedia-buttons-{$attribute.id}" class="keymedia-buttons"
    data-prefix={'/ezjscore/call'|ezurl}
    data-id="{$attribute.id}"
    data-contentobject-id="{$attribute.contentobject_id}"
    data-version="{$attribute.version}">

    <input type="hidden" name="{$base}_media_id_{$attribute.id}" value="{$media.id}" class="media-id" />
    <input type="hidden" name="{$base}_host_{$attribute.id}" value="{$media.host}" class="media-host" />
    <input type="hidden" name="{$base}_ending_{$attribute.id}" value="{$media.ending}" class="media-ending" />

    {if $media}
    <input type="button" class="keymedia-scale hid button"
        data-truesize='{$media.size|json}'
        {if $handler.mediaFits}
        value="{'Scale'|i18n( 'content/edit' )}"
        {else}
        disabled="disabled"
        value="{'Requires a bigger media'|i18n( 'content/edit' )}"
        {/if}
        data-versions='{$handler.toscale|json}'>
        <input type="button" class="keymedia-remove-file button" value="{'Remove media'|i18n( 'content/edit' )}">
    {/if}

    <input type="button" class="keymedia-remote-file button" value="{'Choose from KeyMedia'|i18n( 'content/edit' )}">

    <div class="keymedia-local-file-container" id="keymedia-local-file-container-{$attribute.id}">
        <input type="button" class="keymedia-local-file button" id="keymedia-local-file-{$attribute.id}"
            value="{'Choose from computer'|i18n( 'content/edit' )}">
    </div>

    <div class="upload-progress hid" id="keymedia-progress-{$attribute.id}">
        <div class="progress"></div>
    </div>
</div>
{else}
<h2 class="error">{'No KeyMedia connection for content class'|i18n( 'keymedia' )}</h2>
{/if}
