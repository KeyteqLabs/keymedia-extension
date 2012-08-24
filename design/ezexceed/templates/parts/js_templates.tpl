{def $searchPlaceholder = 'Search for media'|i18n( 'content/keymedia' )
    $search = 'Search for medias'|i18n( 'content/keymedia' )
    $next = 'Next 25 &gt;'
    $prev = '&lt; Previous 25'
    $upload = 'Upload new media'|i18n( 'content/edit' )
}

<script type="text/javascript">
{literal} _KeyMediaTranslations = { {/literal}
    searchPlaceholder : '{$searchPlaceholder}',
    search : '{$search}',
    next : '{$next}',
    prev : '{$prev}',
    upload : '{$upload}'
{literal} }; {/literal}
</script>

<script type="text/x-handlebars-template" id="tpl-keymedia-browser">
{literal}
    <div class="keymedia" id="keymedia-browser">
        <div class="header">
            <form onsubmit="javascript: return false;" class="search">
                <div class="search-field">
                    <input class="q" type="text" name="keymedia-search" placeholder="{{tr.searchPlaceholder}}" />
                    <span class="kp-icon16 search-icon"></span>
                </div>
            </form>
            <div class="upload-container" id="keymedia-local-file-container-{{attribute.id}}">
                <button type="button" class="upload" id="keymedia-local-file-{{attribute.id}}">{{tr.upload}}
                </button>
            </div>
        </div>

        <div class="body">
        {{body}}
        </div>
    </div>
{/literal}
</script>

<script type="text/x-handlebars-template" id="tpl-keymedia-scaledversion">
{literal}
    <a>
        <p class="box"></p>
        <h2>{{name}}<br />
            <span>{{width}}x{{height}}</span>
        </h2>
    </a>
    <div class="overlay"></div>
{/literal}
</script>

<script type="text/x-handlebars-template" id="tpl-keymedia-scaler">
{literal}
    <div class="keymedia" id="keymedia-scaler">
        <div class="header">
            <ul></ul>
        </div>
        <div class="body">
            <div class="image-wrap">
                <img src="{{media}}" />
            </div>
            <div id="keymedia-scaler-controls">
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
            <p class="meta">{{filename}}<br /><span class="details">{{width}} x {{height}}</span></p>
            <span class="share{{#if shared}} shared{{/if}}"></span>
        </a>
    </div>
{/literal}
</script>
