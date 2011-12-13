{def $content = $class_attribute.content}
<div class="block">
    <h6>{'Connected to'|i18n( 'design/standard/class/datatype' )}:</h6>
    {foreach $content.backends as $backend}
        {if eq($content.selected, $backend.id)}
        <p>
        <a href="http://{$backend.host}/">{$backend.host}</a> ({$backend.username})
        </p>
        {/if}
    {/foreach}
</div>
