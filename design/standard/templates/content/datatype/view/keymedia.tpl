{def $media = keymedia($attribute,$format)}

{if eq($attribute.content.id, 0)}
    <label>{'There is no image file'|i18n( 'design/standard/content/datatype' )}</label>
{else}
    {if $media.url|is_set()}
        {include uri='design:standard/content/datatype/view/'|concat($media.type)|concat('.tpl') media=$media}
    {else}
        <label>{'Could not fetch image.'|i18n( 'design/standard/content/datatype' )}</label>
    {/if}
{/if}
