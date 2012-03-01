{if not( is_set( $handler ) ) }
    {def $handler = $attribute.content}
{/if}
{if not( is_set( $image ) ) }
    {def $image = $handler.image}
{/if}
{if eq(is_set($base), false())}
    {def $base='ContentObjectAttribute'}
{/if}
<input type="hidden" name="{$base}_image_id_{$attribute.id}" value="{$image.id}" class="image-id" />
<input type="hidden" name="{$base}_host_{$attribute.id}" value="{$image.host}" class="image-host" />
<input type="hidden" name="{$base}_ending_{$attribute.id}" value="{$image.ending}" class="image-ending" />
{if $handler.backend}
    <section class="edit-buttons">
        {if $image}
        <button type="button" class="scale action"
            {if not( $handler.imageFits )}disabled="disabled"{/if}
            data-truesize='{$image.size|json}'
            data-versions='{$handler.toscale|json}'>
    
            {if $handler.imageFits}
            {'Scale variants'|i18n( 'content/edit' )}
            {else}
            {'Requires a bigger image'|i18n( 'content/edit' )}
            {/if}
        </button>
        {/if}
        <button type="button" class="from-keymedia">{'Fetch from KeyMedia'|i18n( 'content/edit' )}</button>
    
        <div class="upload-container" id="keymedia-local-file-container-{$attribute.id}">
            <button type="button" class="upload" id="keymedia-local-file-{$attribute.id}">{'Upload new image'|i18n( 'content/edit' )}</button>
            <div class="upload-progress hid"><div class="progress"></div></div>
        </div>
    </section>

    {if $image}
    <div class="meta">
        <p>
        {$image.name|wash}
        </p>
        <div class="tagger">
            <input type="text" class="tagedit" placeholder="{'Add a tag'|i18n( 'content/edit' )}" />
            <button type="button" class="tagit">
                {'Add tag'|i18n( 'content/edit' )}
            </button>
            <ul>
            </ul>
        </div>
    </div>
    {/if}
{else}
    <p class="error">{'No KeyMedia connection for content class'|i18n( 'keymedia' )}</p>
{/if}
