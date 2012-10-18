require.config({
    shim : {
        jcrop : {
            deps : ['jquery'],
            exports : 'jQuery.Jcrop'
        }
    },
    paths : {
        'keymedia' : '/extension/keymedia/design/ezexceed/javascript',
        'jcrop' : '/extension/keymedia/design/standard/javascript/libs/jquery.jcrop.min'
    }
});
