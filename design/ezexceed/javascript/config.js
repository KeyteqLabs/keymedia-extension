require.config({
    shim : {
        jcrop : {
            deps : ['jquery-safe'],
            exports : 'jQuery.fn.Jcrop'
        }
    },
    paths : {
        'keymedia' : '/extension/keymedia/design/ezexceed/javascript',
        'jcrop' : '/extension/keymedia/design/standard/javascript/libs/jquery.jcrop.min'
    }
});
