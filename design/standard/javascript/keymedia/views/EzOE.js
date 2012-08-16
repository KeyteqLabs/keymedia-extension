KeyMedia.views.EzOE = Backbone.View.extend({
    attributeEl : null,

    initialize : function(options)
    {
        options = (options || {});

        if (_(options).has('textEl'))
            this.attributeEl = $(options.textEl).closest('.attribute');

        _.bindAll(this);

        /**
         * Fetch info about DAM's from server
         * Show media from DAM
         */

        var prefix = eZExceed.urlPrefix ? '/' + eZExceed.urlPrefix : '';
        prefix = prefix + '/ezjscore/call';
        this.model = new KeyMedia.models.Attribute({
            id : this.attributeEl.data('id'),
            version : this.attributeEl.data('version'),
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

        this.model.bind('version.create', this.insertVersion);

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

        var _this = this;
        this.media.model.on('change', function(){
            var media = _this.media.model;
            var options = {
                model : _this.model,
                media : media,
                versions : [{name : 'Test'}],
                trueSize : [media.get('width'), media.get('height')],
                className : 'keymedia-scaler'
                //app : this
            };
            console.log(options);
            var headingOptions =
            {
                name : 'Select crops',
                icon : '/extension/ezexceed/design/ezexceed/images/kp/32x32/Pictures-alt-2b.png',
                quotes : true
            };

            eZExceed.stack.push(KeyMedia.views.Scaler, options, {headingOptions : headingOptions});
        });

    },

    insertVersion : function(data)
    {
        console.log(data);
        var media = this.media.model;
        var fileUrl = '//' + media.get('host') + data.url + '.' + media.get('scalesTo').ending;
        var mediaId = media.id,
            keymediaId = this.media.keymediaId;
        //var content = '<div class="ezoeItemCustomTag keymedia" type="custom" customattributes="mediaid|324"><p><img src="' + fileUrl + '" /></p></div>';
        var content = '<div class="ezoeItemCustomTag keymedia" type="custom" ' +
            'customattributes="mediaId|' + mediaId + 'attribute_separationkeymediaId|' + keymediaId +
            'attribute_separationurl|' + fileUrl + '"><p>Keymedia</p></div>';
        tinyMCE.execCommand('mceInsertContent', false, content);

    }
});

