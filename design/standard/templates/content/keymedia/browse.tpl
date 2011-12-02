{def $searchPlaceholder = 'Search for ...'|i18n( 'content/keymedia' )
    $search = 'Search'|i18n( 'content/keymedia' )
    $next = '&gt;'
    $prev = '&lt;'
}

<div id="ezr-keymedia-browser">
    <div class="header">
        <input type="text" name="ezr-keymedia-search" placeholder={$searchPlaceholder} />
        <button type="button">{$search}</button>
        <a class="prev">{$prev}</a>
        <a class="next">{$next}</a>
    </div>

    <div class="body">
    </div>
</div>
