{if not( is_set( $handler ) ) }
    {def $handler = $attribute.content}
{/if}
{if not( is_set( $image ) ) }
    {def $image = $handler.image}
{/if}
<input type="hidden" name="{$base}_image_id_{$attribute.id}" value="{$image.id}" class="image-id" />
<input type="hidden" name="{$base}_host_{$attribute.id}" value="{$image.host}" class="image-host" />
<input type="hidden" name="{$base}_ending_{$attribute.id}" value="{$image.ending}" class="image-ending" />
{if $handler.backend}
    {if $image}
    <button type="button" class="scale"
        {if not( $handler.imageFits )}disabled="disabled"{/if}
        data-truesize='{$image.size|json}'
        data-versions='{$handler.toscale|json}'>

        {if $handler.imageFits}
        {'Scale'|i18n( 'content/edit' )}
        {else}
        {'Requires a bigger image'|i18n( 'content/edit' )}
        {/if}
    </button>
    {/if}
    <button type="button" class="from-keymedia">{'Choose from KeyMedia'|i18n( 'content/edit' )}</button>

    <div class="upload-container">
        <button type="button" class="upload">{'Choose from computer'|i18n( 'content/edit' )}</button>
        <div class="upload-progress hid"><div class="progress"></div></div>
    </div>

    {if $image}
    <div class="meta">
        <h3>{$image.name|wash}</h3>
        <div class="tagger">
            <h4>{'Tags'|i18n( 'content/edit' )}</h4>
            <input type="text" class="tagedit" />
            <button type="button">
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
