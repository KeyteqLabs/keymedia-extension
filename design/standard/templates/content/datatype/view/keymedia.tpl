{if not( is_set( $format ) )}
    {def $format = array(300,200)}
{/if}
{if not( is_set( $fetchinfo ) )}
    {def $fetchinfo = false()}
{/if}

{if is_set($quality)|not}
    {def $quality = false()}
{/if}

{if is_set($silent)|not}
    {def $silent = true()}
{/if}

{def
    $handler = $attribute.content
    $media = keymedia($attribute,$format, $quality, $fetchinfo)
}

{if eq($handler.id, 0)|not}
    {if $media.url|is_set()}
        {if $media.type|is_set()}
        {def $template = 'design:content/datatype/view/'|concat($media.type)|concat('.tpl')}
        {if $silent|not}{debug-log msg='Loading type specific template:' var=$media.type}{/if}
        {else}
        {def $template = 'design:content/datatype/view/'|concat($handler.type)|concat('.tpl')}
        {if $silent|not}{debug-log msg='Loading type specific template:' var=$handler.type}{/if}
        {/if}
        {include uri=$template media=$media}
    {else}
        {if $silent|not}{debug-log msg='Media.url not set'}{/if}
    {/if}
{else}
    {if $silent|not}{debug-log msg='No media id connected to attribute'}{/if}
{/if}
