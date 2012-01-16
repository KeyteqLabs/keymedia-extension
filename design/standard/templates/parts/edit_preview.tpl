{def $handler = $attribute.content
    $image = $handler.image
    $size = ezini( 'KeyMedia', 'EditSize', 'keymedia.ini' )
}

<div class="keymedia-image">
    <div class="image-wrap">
        {attribute_view_gui format=array($size,$size) attribute=$attribute}
    </div>

    <div class="image-meta">
        <ul>
            <li>{'Title'|i18n( 'content/edit' )}: {$image.name|wash}</li>
            <li>{'Tags'|i18n( 'content/edit' )}: {$image.tags|implode(',')}</li>
            <li>{'Modified'|i18n( 'content/edit' )}: {$image.modified|datetime('iso8601')}</li>
            <li>{'Size'|i18n( 'content/edit' )}: {$handler.filesize|si( byte )}</li>
        </ul>
    </div>
</div>
