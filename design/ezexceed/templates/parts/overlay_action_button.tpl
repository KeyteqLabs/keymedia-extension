{if eq($handler.type, 'image')}
<button type="button" class="scale action edit-image"
    {if not( $handler.mediaFits )}disabled="disabled"{/if}
    data-truesize='{$media.size|json}'
    data-versions='{$handler.toscale|json}'>

    {if $handler.mediaFits}
    {'Scale variants'|i18n( 'content/edit' )}
    {else}
    {'Requires a bigger media'|i18n( 'content/edit' )}
    {/if}
</button>
{/if}
