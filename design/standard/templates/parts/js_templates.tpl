{def $searchPlaceholder = '…'|i18n( 'content/keymedia' )
    $search = 'Search for images'|i18n( 'content/keymedia' )
    $close = 'Close'|i18n( 'content/keymedia' )
    $next = 'Next 25 &gt;'
    $prev = '&lt; Previous 25'
}

<script type="text/javascript">
{literal} _KeyMediaTranslations = { {/literal}
    searchPlaceholder : '{$searchPlaceholder}',
    search : '{$search}',
    close : '{$close}',
    next : '{$next}',
    prev : '{$prev}'
{literal} }; {/literal}
</script>

<script type="text/x-handlebars-template" id="tpl-keymedia-browser">
{literal}
    <div id="keymedia-browser">
        <div class="header">
            <form onsubmit="javascript: return false;" class="search">
                <input type="text" name="keymedia-search" placeholder="{{tr.searchPlaceholder}}" />
                <button type="button" class="search">{{tr.search}}</button>
                <a class="prev">{{tr.prev}}</a>
                <a class="next">{{tr.next}}</a>

                <button type="button" class="close">{{tr.close}}</button>
            </form>
        </div>

        <div class="body">
        {{body}}
        </div>
    </div>
{/literal}
</script>

<script type="text/x-handlebars-template" id="tpl-keymedia-item">
{literal}
    <div class="item" id="item-{{id}}">
        <a class="pick">
            <img src="{{thumb.url}}" />
            <span class="meta">{{filename}} ({{width}}x{{height}})</span>
            <span class="share{{#if shared}} shared{{/if}}"></span>
        </a>
    </div>
{/literal}
</script>
