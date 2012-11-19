require.config({
    shim : {
        jcrop : {
            deps : ['jquery-safe'],
            exports : 'jQuery.fn.Jcrop',
            init : function(jQuery)
            {
                jQuery.fn.Jcrop = this.jQuery.fn.Jcrop;
            }
        },
        brigthcove : {
            exports : 'brightcove'
        }
    },
    paths : {
        'keymedia' : '/extension/keymedia/design/ezexceed/javascript',
        'brightcove' : '//admin.brightcove.com/js/BrightcoveExperiences',
        'jcrop' : '/extension/keymedia/design/standard/javascript/libs/jquery.jcrop.min'
    }
});
