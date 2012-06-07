{run-once}
<script type="text/javascript" src="http://admin.brightcove.com/js/BrightcoveExperiences.js"></script>
{/run-once}

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
    <param name="@videoPlayer"  value="{$media.original.remotes.brightcove.id}" />
</object>
<script type="text/javascript">
    brightcove.createExperiences();
</script>
