<h1>KeyMedia dashboard</h1>

{if $backends}
<h2>{'Connected KeyMedia sites'|i18n( 'keymedia/dashboard' )}</h2>
<ul>
{foreach $backends as $backend}
    <li>
        <span>{$backend.username}</span>
        @ <a href="http://{$backend.host}">{$backend.host}</a>

        <button data-href={'/key_media/deleteConnection'|ezurl}>{'Delete'|i18n( 'keymedia' )}</button>
    </li>
{/foreach}
</ul>
{else}
<h2>{'No connected KeyMedia sites'|i18n( 'keymedia/dashboard' )}</h2>
{/if}
