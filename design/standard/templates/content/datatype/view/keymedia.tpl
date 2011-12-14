{def $content = $attribute.content}
{if eq($content.id, 0)}
<label>{'There is no image file'|i18n( 'design/standard/content/datatype' )}:</label>
{else}
<img src="{$content.thumb}" />
{/if}
