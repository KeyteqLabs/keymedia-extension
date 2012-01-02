<h2>{'Connections'|i18n( 'keymedia/dashboard' )}</h2>
<ul>
{if $backends}
{foreach $backends as $backend}
    <li>
        <a href={concat( '/key_media/connection/', $backend.id )|ezurl}>{$backend.host}</a>
    </li>
{/foreach}
{/if}
    <li>
        <a href={'/key_media/connection/'|ezurl}>{'Add'|i18n( 'keymedia' )}</a>
    </li>
</ul>
