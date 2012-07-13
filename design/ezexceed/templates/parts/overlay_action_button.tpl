{if eq($handler.type, 'image')}
    {if $handler.mediaFits}
        <button type="button" class="scale action edit-image"
            data-truesize='{$media.size|json}'
            data-versions='{$handler.toscale|json}'>
            {'Scale variants'|i18n( 'content/edit' )}
        </button>
    {/if}
{/if}
