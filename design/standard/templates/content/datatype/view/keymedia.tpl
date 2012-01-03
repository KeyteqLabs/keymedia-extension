{def $media = keymedia($attribute,$format)}

{if eq($attribute.content.id, 0)}
    <label>{'There is no image file'|i18n( 'design/standard/content/datatype' )}</label>
{else}
    {if $media.url|is_set()}
        {def $template = 'design:content/datatype/view/'|concat($media.type)|concat('.tpl')}

        {def $params = hash(
            'title', cond($title|is_set(), $title, ''),
            'class', cond($class|is_set(), $class, '')
        )}

        {include uri=$template media=$media param=$params}
    {else}
        <label>{'Could not fetch image.'|i18n( 'design/standard/content/datatype' )}</label>
    {/if}
{/if}
