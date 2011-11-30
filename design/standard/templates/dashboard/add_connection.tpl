<h1>{'Add KeyMedia connection'|i18n( 'keymedia/add_connection' )}</h1>

<form action={'/key_media/addConnection'|ezurl} method="post">
    <label>
        <span>{'Hostname'|i18n( 'keymedia/add_connection' )}</span>
        <input name="host" type="text" placeholder={'Hostname'|i18n( 'keymedia/add_connection' )} />
    </label>

    <label>
        <span>{'Username'|i18n( 'keymedia/add_connection' )}</span>
        <input name="username" type="text" placeholder={'Username'|i18n( 'keymedia/add_connection' )} />
    </label>

    <label>
        <span>{'API key'|i18n( 'keymedia/add_connection' )}</span>
        <input name="api_key" type="text" placeholder={'API-key'|i18n( 'keymedia/add_connection' )} />
    </label>

    <input type="hidden" name="redirect_to" value={'/key_media/dashboard'|ezurl} />
    <button type="submit">{'Add'|i18n( 'keymedia' )}</button>
</form>
