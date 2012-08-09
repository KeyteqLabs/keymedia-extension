KeyMedia.views.EzOE = Backbone.View.extend({

    initialize : function(options)
    {
        options = (options || {});
        _.extend(this, options);
        _.bindAll(this);

        /**
         * Fetch info about DAM's from server
         * Show media from DAM
         */

        var prefix = eZExceed.urlPrefix ? '/' + eZExceed.urlPrefix : '';
        prefix = prefix + '/ezjscore/call';
        this.model = new KeyMedia.models.Attribute({
            id : '',
            version : '',
            prefix : prefix
        });

        var options = {
            model : this.model,
            collection : this.model.medias,
            onSelect : this.changeMedia
        };
        var headingOptions =
        {
            icon : '/extension/ezexceed/design/ezexceed/images/kp/32x32/Pictures.png',
            name : 'Select media',
            quotes : true
        };
        this.model.medias.search('');

        eZExceed.stack.push(KeyMedia.views.Browser, options, {headingOptions : headingOptions});

        //this.editor.trigger('stack.push', KeyMedia.views.Browser, options, {headingOptions : headingOptions});

        //tinyMCE.execCommand('mceInsertContent', false, '<b>Hello world!!</b>');

        return this;
    },

    changeMedia : function(params)
    {
        /**
         * Not cool to have this here
         */
        eZExceed.stack.pop();

        /**
         * Show the editor
         */
        console.log(params);
        console.log('Changed');
    }
});

