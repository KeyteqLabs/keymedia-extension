{literal}
<script type="x-jquery-tmpl" id="tpl-keymedia-browser">
    <div class="stack-item-content">
        <div class="header">
            <form onsubmit="javascript: return false;" class="search">
                <input type="text" name="keymedia-search" placeholder="…"{{if q}} value="${q}"{{/if}} />
                <button type="submit" class="search">Search for images</button>
                <a class="prev">&gt;</a>
                <a class="next">&lt;</a>

                <button type="button" class="close">X</button>
            </form>
        </div>

        <div class="body">
            ${body}
        </div>
    </div>
</script>

<script type="x-jquery-tmpl" id="tpl-keymedia-item">
    <div class="item" id="${id}">
        <a class="pick">
            <img src="${src}" />
            <span class="meta">${filename} (${filesize})</span>
            <span class="share{{if shared}} shared{{/if}}"></span>
        </a>
    </div>
</script>
{/literal}
