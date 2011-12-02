{def $searchPlaceholder = 'Search for ...'|i18n( 'content/keymedia' )
    $search = 'Search'|i18n( 'content/keymedia' )
    $close = 'Close'|i18n( 'content/keymedia' )
    $next = '&gt;'
    $prev = '&lt;'
}

<div id="ezr-keymedia-browser">
    <div class="header">
        <input type="text" name="ezr-keymedia-search" placeholder={$searchPlaceholder} />
        <button type="button" class="search">{$search}</button>
        <a class="prev">{$prev}</a>
        <a class="next">{$next}</a>

        <button type="button" class="close">{$close}</button>
    </div>

    <div class="body">
    </div>
</div>
