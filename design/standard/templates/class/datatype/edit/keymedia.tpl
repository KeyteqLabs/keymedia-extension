{def $content = $class_attribute.content}
<div class="block">
    <label>{'Select KeyMedia'|i18n( 'design/standard/class/datatype' )}:</label>
    <select name="connection">
        <option value=0>---</option>
    {foreach $content.backends as $backend}
        <option value="{$backend.id}"{if eq($backend.id, $content.selected)} selected{/if}>
        {$backend.host}
        </option>
    {/foreach}
    </select>
</div>
