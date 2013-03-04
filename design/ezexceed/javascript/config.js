require.config({
    map : {
        jcrop : {
            'jquery' : 'jquery-safe'
        }
    },
    shim : {
        jcrop : {
            exports : 'jQuery.fn.Jcrop'
        },
        brigthcove : {
            exports : 'brightcove'
        }
    },
    paths : {
        'keymedia' : '/extension/keymedia/design/ezexceed/javascript',
        'brightcove' : 'http://admin.brightcove.com/js/BrightcoveExperiences',
        'jcrop' : '/extension/keymedia/design/standard/javascript/libs/jquery.jcrop.min'
    }
});
