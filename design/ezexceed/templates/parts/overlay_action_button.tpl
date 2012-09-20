{if or( is_set($handler.type)|not, eq($handler.type, 'image') )}
    {if $handler.mediaFits}
        <button class="btn btn-inverse scale edit-image"
            data-truesize='{$media.size|json}'
            data-versions='{$handler.toscale|json}'>
            {'Edit image'|i18n('keymedia')}
        </button>
    {/if}
{/if}
