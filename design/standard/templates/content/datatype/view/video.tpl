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

    {* These should not be included here in the long run as they are specific
        for the keyteq brightcove account
    *}
    {if not( is_set( $playerId ) )}
        {def $playerId = 940254825001}
    {/if}
    {if not( is_set( $playerKey ) )}
        {def $playerKey = "AQ~~,AAAAj351Auk~,KVZgH27WO2Jwl4REbCDRHmeGwlyZs_Fv"}
    {/if}

    <object id="brightcove_experience_{$attribute.id}" class="BrightcoveExperience">
        <param name="bgcolor" value="#FFFFFF" />
        <param name="width" value="{$media.original.width}" />
        <param name="height" value="{$media.original.height}" />
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
