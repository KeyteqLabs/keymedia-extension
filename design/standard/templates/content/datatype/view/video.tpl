{if is_set($nojs)|not}
    {def $nojs = false()}
{/if}

{def $videoId = false()}
{if is_set($media.original.remotes.brightcove.id)}
    {set $videoId = $media.original.remotes.brightcove.id}
{else}
    {if is_set($media.remotes.brightcove)}
        {set $videoId = $media.remotes.brightcove.id}
    {/if}
{/if}

{if $videoId}
    {if $nojs|not}
        {run-once}
        <script type="text/javascript" src="http://admin.brightcove.com/js/BrightcoveExperiences.js"></script>
        {/run-once}
    {/if}

    {* Set defaults if these values are not passed *}
    {if is_set($playerId)|not}
        {def $playerId = ezini('Brightcove', 'PlayerId', 'keymedia.ini')}
    {/if}
    {if is_set($playerKey)|not}
        {def $playerKey = ezini('Brightcove', 'PlayerKey', 'keymedia.ini')}
    {/if}
    {if is_set($width)|not}
        {def $width = $media.file.width}
    {/if}
    {if is_set($height)|not}
        {def $height = $media.file.height}
    {/if}

    <object id="brightcove_experience_{$attribute.id}" class="BrightcoveExperience">
        <param name="bgcolor" value="#FFFFFF" />
        {if gt( $width, 0 )}
        <param name="width" value="{$width}" />
        {/if}
        {if gt( $height, 0 )}
        <param name="height" value="{$height}" />
        {/if}
        <param name="htmlFallback" value="true" />
        <param name="playerID" value="{$playerId}" />
        <param name="playerKey" value="{$playerKey}" />
        <param name="isVid" value="true" />
        <param name="isUI" value="true" />
        <param name="dynamicStreaming" value="true" />
        <param name="@videoPlayer"  value="{$videoId}" />
    </object>

    {if $nojs|not}
    <script type="text/javascript">
        brightcove.createExperiences();
    </script>
    {/if}
{else}
    <p class="error">{'No video found, or its not properly synced'|i18n('eze')}</p>
{/if}
