{def $backend = $handler.backend
    $size = ezini( 'KeyMedia', 'EditSize', 'keymedia.ini' )
}
<div id="keymedia-buttons-{$attribute.id}" class="keymedia-buttons"
    data-prefix={'/ezjscore/call'|ezurl}
    data-id={$attribute.id}
    data-contentobject-id={$attribute.contentobject_id}
    data-backend={$backend.id}
    data-version={$attribute.version}>

    <input type="hidden" name="{$base}_image_id_{$attribute.id}" value="{$image.id}" class="image-id" />
    <input type="hidden" name="{$base}_host_{$attribute.id}" value="{$image.host}" class="image-host" />
    <input type="hidden" name="{$base}_ending_{$attribute.id}" value="{$image.ending}" class="image-ending" />

    {if $image}
    <input type="button" class="keymedia-scale hid button"
        data-truesize='{$image.size|json}'
        {if $handler.imageFits}
        value="{'Scale'|i18n( 'content/edit' )}"
        {else}
        disabled="disabled"
        value="{'Requires a bigger image'|i18n( 'content/edit' )}"
        {/if}
        data-versions='{$handler.toscale|json}'>
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
