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

        this.view = eZExceed.stack.push(KeyMedia.views.Browser, options, {headingOptions : headingOptions});

        //this.editor.trigger('stack.push', KeyMedia.views.Browser, options, {headingOptions : headingOptions});

        //tinyMCE.execCommand('mceInsertContent', false, '<b>Hello world!!</b>');

        return this;
    },

    changeMedia : function(params)
    {
        /**
         * Not cool to have this here
         */
        this.media = params;
        this.view.on('destruct', this.showScaler);
        eZExceed.stack.pop();
    },

    showScaler : function()
    {
        /**
         * Show the editor
         */
        this.model.media(this.media.model, ['ezoe', this.media.id]);

        console.log(this.media);
        var _this = this;
        this.media.model.on('change', function(){
            var media = _this.media.model;
            var fileUrl = media.get('file').url;
            var mediaId = media.id,
                keymediaId = _this.media.keymediaId;
            //var content = '<div class="ezoeItemCustomTag keymedia" type="custom" customattributes="mediaid|324"><p><img src="' + fileUrl + '" /></p></div>';
            var content = '<div class="ezoeItemCustomTag keymedia" type="custom" ' +
                'customattributes="mediaId|' + mediaId + 'attribute_separationkeymediaId|' + keymediaId +
                'attribute_separationurl|' + fileUrl + '"><p>Keymedia</p></div>';
            tinyMCE.execCommand('mceInsertContent', false, content);
        });
        return;

        var options = {
            model : this.model,
            media : this.media.model,
            versions : {},
            //trueSize : node.data('truesize'),
            className : 'keymedia-scaler',
            app : this
        };
        var headingOptions =
        {
            name : 'Select crops',
            icon : '/extension/ezexceed/design/ezexceed/images/kp/32x32/Pictures-alt-2b.png',
            quotes : true
        };

        eZExceed.stack.push(KeyMedia.views.Scaler, options, {headingOptions : headingOptions});
    }
});

