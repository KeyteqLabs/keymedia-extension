{def $searchPlaceholder = '…'|i18n( 'content/keymedia' )
    $search = 'Search for images'|i18n( 'content/keymedia' )
    $next = 'Next 25 &gt;'
    $prev = '&lt; Previous 25'
}

<script type="text/javascript">
{literal} _KeyMediaTranslations = { {/literal}
    searchPlaceholder : '{$searchPlaceholder}',
    search : '{$search}',
    next : '{$next}',
    prev : '{$prev}'
{literal} }; {/literal}
</script>

<script type="text/x-handlebars-template" id="tpl-keymedia-browser">
    {include uri="design:handlebars/stack/item/header.tpl" noWrap=true()}
{literal}
    <div class="stack-item-content">
        <div class="keymedia" id="keymedia-browser">
            <div class="header">
                <form onsubmit="javascript: return false;" class="search">
                    <input class="q" type="text" name="keymedia-search" placeholder="{{tr.searchPlaceholder}}" />
                    <button type="button" class="search">{{tr.search}}</button>
                    <a class="prev">{{tr.prev}}</a>
                    <a class="next">{{tr.next}}</a>
                </form>
            </div>
    
            <div class="body">
            {{body}}
            </div>
        </div>
    </div>
{/literal}
</script>

<script type="text/x-handlebars-template" id="tpl-keymedia-scaledversion">
{literal}
    <a>
        <h2>{{name}}</h2>
        <p>{{width}}x{{height}}</p>
        <span class="box"></span>
    </a>
    <div class="overlay"></div>
{/literal}
</script>

<script type="text/x-handlebars-template" id="tpl-keymedia-scaler">
    {include uri="design:handlebars/stack/item/header.tpl" noWrap=true()}
{literal}
    <div class="stack-item-content">
        <div class="keymedia" id="keymedia-scaler">
            <div class="header">
                <ul></ul>
            </div>
            <div class="body">
                <div class="image-wrap">
                    <img src="{{image}}" />
                </div>
                <div id="keymedia-scaler-controls">
                </div>
            </div>
        </div>
    </div>
{/literal}
</script>

<script type="text/x-handlebars-template" id="tpl-keymedia-item">
{literal}
    <div class="item">
        <a class="pick" data-id="{{id}}">
            <img src="{{thumb.url}}" />
            <span class="meta">{{filename}} ({{width}}x{{height}})</span>
            <span class="share{{#if shared}} shared{{/if}}"></span>
        </a>
    </div>
{/literal}
</script>
