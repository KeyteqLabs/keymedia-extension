{def $viewmode = 'keymedia-standard'}
{if $viewmode}
    {set $viewmode = $viewmode}
{/if}
{def $template = 'design:content/datatype/view/ezxmltags/'|concat($viewmode)|concat('.tpl')}
{include uri=$template}
