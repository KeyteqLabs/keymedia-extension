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
        <h3>{$image.name|wash}</h3>
        <div class="tagger">
            <h4>{'Tags'|i18n( 'content/edit' )}</h4>
            <input type="text" class="tagedit" />
            <input type="button" class="button tagit" id="keymedia-tagger-{$attribute.id}"
                value="{'Add tag'|i18n( 'content/edit' )}">
            <ul>
            </ul>
        </div>
        <p>
        {'Modified'|i18n( 'content/edit' )}: {$image.modified|datetime('iso8601')},
        {'Size'|i18n( 'content/edit' )}: {$handler.filesize|si( byte )}
        </p>
    </div>
    {/if}
</div>
