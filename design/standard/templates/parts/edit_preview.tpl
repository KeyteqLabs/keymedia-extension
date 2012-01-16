{if not( is_set( $handler ) ) }
    {def $handler = $attribute.content}
{/if}
{if not( is_set( $image ) ) }
    {def $image = $handler.image}
{/if}
{if not( is_set( $size ) ) }
    {def $size = ezini( 'KeyMedia', 'EditSize', 'keymedia.ini' )}
{/if}

<div class="keymedia-image">
    <div class="image-wrap">
        {attribute_view_gui format=array($size,$size) attribute=$attribute}
    </div>

    {if $image}
    <div class="image-meta">
        <ul>
            <li>{'Title'|i18n( 'content/edit' )}: {$image.name|wash}</li>
            <li class="tags">
                <span>{'Tags'|i18n( 'content/edit' )}:</span>
                <p>{$image.tags|implode(',')}</p>

                <input type="text" class="hid tagedit" />
                <input type="button" class="button ezr-keymedia-tagger" id="ezr-keymedia-tagger-{$attribute.id}"
                    value="{'Add tags'|i18n( 'content/edit' )}">
            </li>
            <li>{'Modified'|i18n( 'content/edit' )}: {$image.modified|datetime('iso8601')}</li>
            <li>{'Size'|i18n( 'content/edit' )}: {$handler.filesize|si( byte )}</li>
        </ul>
    </div>
    {/if}
</div>
