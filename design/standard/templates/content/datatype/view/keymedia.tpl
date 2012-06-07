{if not( is_set( $format ) )}
    {def $format = array(300,200)}
{/if}
{if not( is_set( $fetchinfo ) )}
    {def $fetchinfo = false()}
{/if}

{if is_set($quality)|not}
    {def $quality = false()}
{/if}

{def $media = keymedia($attribute,$format, $quality, $fetchinfo)}

{if eq($attribute.content.id, 0)|not}
    {if $media.url|is_set()}
        {def $template = 'design:content/datatype/view/'|concat($media.type)|concat('.tpl')}

        {include uri=$template media=$media}
    {/if}
{/if}
