{def $content = $class_attribute.content}
<div class="block">
    <label>{'Select KeyMedia'|i18n( 'design/standard/class/datatype' )}:</label>
    <select name="ContentClass_connection_{$class_attribute.id}">
        <option value=0>---</option>
    {foreach $content.backends as $backend}
        <option value="{$backend.id}"{if eq($backend.id, $content.selected)} selected{/if}>
        {$backend.host}
        </option>
    {/foreach}
    </select>
</div>
<div class="block">
    <label>{'Scaled versions'|i18n( 'design/standard/class/datatype' )}:</label>
    <textarea rows="5" cols="45" name="ContentClass_versions_{$class_attribute.id}">{$content.versions}</textarea>
    <p>
    {'1 row = 1 version: 500x500 => vanity-url.
    Use #slug# to add the generated slug url for the object into the name: #slug#-vanity-url'|i18n( 'design/standard/class/datatype' )}
    </p>
</div>
