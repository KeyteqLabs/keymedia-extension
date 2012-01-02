{def $url = '/key_media/connection/'}
{if $backend}
<h1>{'Edit'|i18n( 'keymedia/add_connection' )} {$backend.host}</h1>
    {set $url = concat($url, $backend.id)}
{else}
<h1>{'Add KeyMedia connection'|i18n( 'keymedia/add_connection' )}</h1>
{/if}

<form action={$url|ezurl} method="post">
    <label>
        <span>{'Hostname'|i18n( 'keymedia/add_connection' )}</span>
        <input name="host" type="text" placeholder={'Hostname'|i18n( 'keymedia/add_connection' )}
            {if $backend}value="{$backend.host}"{/if} />
    </label>

    <label>
        <span>{'Username'|i18n( 'keymedia/add_connection' )}</span>
        <input name="username" type="text" placeholder={'Username'|i18n( 'keymedia/add_connection' )}
            {if $backend}value="{$backend.username}"{/if} />
    </label>

    <label>
        <span>{'API key'|i18n( 'keymedia/add_connection' )}</span>
        <input name="api_key" type="text" placeholder={'API-key'|i18n( 'keymedia/add_connection' )}
            {if $backend}value="{$backend.api_key}"{/if} />
    </label>

    <label>
        <span>{'API version'|i18n( 'keymedia/add_connection' )}</span>
        {def $version = 2}
        {if $backend}
            {set $version = $backend.api_version}
        {/if}
        <select name="api_version">
            <option value="1"{if eq($version, 1)} selected="selected"{/if}>Version 1</option>
            <option value="2"{if eq($version, 2)} selected="selected"{/if}>Version 2</option>
        </select>
    </label>

    <input type="hidden" name="redirect_to" value={'/key_media/connection'|ezurl} />
    <button type="submit">{'Save'|i18n( 'keymedia' )}</button>
</form>
