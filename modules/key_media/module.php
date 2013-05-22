<?php
ezote\Autoloader::register();
if (isset($Params) && isset($Params['FunctionName']))
{
    $router = new \ezote\lib\Router;
    $Result = $router->legacyHandle('keymedia', 'key_media', $Params['FunctionName'], $Params['Parameters'])->run();
}
else
{
    $merge = array(
        'ViewList' => array(
            'default_navigation_part' => 'key_media_navigation'
        )
    );
    $definition = keymedia\modules\key_media\KeyMedia::getDefinition($merge, array('static' => false));
    list($Module, $FunctionList, $ViewList) = $definition;
}
