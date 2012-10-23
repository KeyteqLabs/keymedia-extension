var loadKeyMedia =  function(textarea, ed)
{
    require.config({
        shim : {
            jcrop : {
                deps : ['jquery-safe'],
                exports : 'jQuery.fn.Jcrop'
            }
        },
        paths : {
            'jcrop' : '/extension/keymedia/design/standard/javascript/libs/jquery.jcrop.min',
            'keymedia' : '/extension/keymedia/design/ezexceed/javascript'
        }
    });
    require(['keymedia/views/ezoe', 'keymedia/templates'], function(View)
    {
        var view = new View({
            textEl : textarea,
            tinymceEditor : ed
        });
    });
}

tinymce.create('tinymce.plugins.KeymediaPlugin', {
    init : function(ed, url)
    {
        // Register commands
        ed.addCommand('mceKeymedia', function()
        {
            loadKeyMedia(ed.getElement(), ed);
        });

        // Register buttons
        ed.addButton('keymedia', {title : 'Keymedia', cmd : 'mceKeymedia'});
    },

    getInfo : function()
    {
        return {
            longname : 'Keymedia',
            author : 'Keyteq AS',
            authorurl : 'http://www.keyteq.no',
            infourl : 'http://www.keyteq.no',
            version : tinymce.majorVersion + "." + tinymce.minorVersion
        };
    }
});

// Register plugin
tinymce.PluginManager.add('keymedia', tinymce.plugins.KeymediaPlugin);
