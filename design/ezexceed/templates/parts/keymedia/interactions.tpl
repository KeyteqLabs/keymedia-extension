{if not( is_set( $handler ) ) }
    {def $handler = $attribute.content}
{/if}
{if not( is_set( $media ) ) }
    {def $media = $handler.media}
{/if}
{if eq(is_set($base), false())}
    {def $base='ContentObjectAttribute'}
{/if}
<input type="hidden" name="{$base}_media_id_{$attribute.id}" value="{$media.id}" class="media-id" />
<input type="hidden" name="{$base}_host_{$attribute.id}" value="{$media.host}" class="media-host" />
<input type="hidden" name="{$base}_ending_{$attribute.id}" value="{$media.ending}" class="media-ending" />
{if $handler.backend}<!--
    <section class="edit-buttons">
        {if $media}
        <button type="button" class="scale action"
            {if not( $handler.mediaFits )}disabled="disabled"{/if}
            data-truesize='{$media.size|json}'
            data-versions='{$handler.toscale|json}'>

            {if $handler.mediaFits}
            {'Scale variants'|i18n( 'content/edit' )}
            {else}
            {'Requires a bigger media'|i18n( 'content/edit' )}
            {/if}
        </button>
        {/if}
        <button type="button" class="from-keymedia">{'Fetch from KeyMedia'|i18n( 'content/edit' )}</button>

        <div class="upload-container" id="keymedia-local-file-container-{$attribute.id}">
            <button type="button" class="upload" id="keymedia-local-file-{$attribute.id}">{'Upload new media'|i18n( 'content/edit' )}</button>
            <div class="upload-progress hid"><div class="progress"></div></div>
        </div>
    </section>

    {if $media}
    <div class="meta">
        <p>
        {$media.name|wash}
        </p>
        <div class="tagger">
            <input type="text" class="tagedit" placeholder="{'Add a tag'|i18n( 'content/edit' )}" />
            <button type="button" class="tagit">
                {'Add tag'|i18n( 'content/edit' )}
            </button>
            <ul>
            </ul>
        </div>
    </div> -->


    <div class="new-wrap"{if $hasMedia} style="display:none"{/if}>
        <span class="kp-icon50 pictures-icon"></span>

        <div class="upload-container" id="keymedia-local-file-container-{$attribute.id}">
            <button type="button" class="upload" id="keymedia-local-file-{$attribute.id}">{'Upload new media'|i18n( 'content/edit' )}</button>
        </div>
    </div>

    </section>


    {if $media}
    <div class="meta">
        <p>
        {$media.name|wash}
        </p>
        <div class="tagger">
            <input type="text" class="tagedit" placeholder="{'Add a tag'|i18n( 'content/edit' )}" />
            <button type="button" class="tagit">
                {'Add tag'|i18n( 'content/edit' )}
            </button>
            <ul>
            </ul>
        </div>
    </div>
    {/if}

{else}
    <p class="error">{'No KeyMedia connection for content class'|i18n( 'keymedia' )}</p>
{/if}
