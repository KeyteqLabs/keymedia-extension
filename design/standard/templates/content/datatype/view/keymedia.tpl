{def $media = keymedia($attribute,$format)}

{if eq($attribute.content.id, 0)}
    <label>{'There is no image file'|i18n( 'design/standard/content/datatype' )}</label>
{else}
    {if $media.url|is_set()|and($media.type|eq('image'))}
        <img src="{$media.url}" class="{$class}" width="{$media.width}" height="{$media.height}" title="{$title}" />
    {else}
        <label>{'Could not fetch image.'|i18n( 'design/standard/content/datatype' )}</label>
    {/if}
{/if}
