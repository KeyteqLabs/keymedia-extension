(function(tinymce)
{
    tinymce.create('tinymce.plugins.KeymediaPlugin', {
        init : function(ed, url)
        {
            //var n = ed.dom.getNode();

            // Register commands
            ed.addCommand('mceKeymedia', function()
            {
                ed.execCommand('mceCustom', false, 'Keymedia');
            });

            // Register buttons
            ed.addButton('keymedia', {title : 'Keymedia', cmd : 'mceKeymedia'});

            ed.onNodeChange.add(function(ed, cm, n)
            {
                cm.setActive('keymedia', n.nodeName === 'SPAN');
            });


        },

        getInfo : function()
        {
            return {
                longname : 'Keymedia',
                author : 'Fumaggo',
                authorurl : 'http://www.fumaggo.com',
                infourl : 'http://www.fumaggo.com',
                version : tinymce.majorVersion + "." + tinymce.minorVersion
            };
        }
    });

    // Register plugin
    tinymce.PluginManager.add('keymedia', tinymce.plugins.KeymediaPlugin);
})(tinymce);