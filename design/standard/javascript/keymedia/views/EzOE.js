KeyMedia.views.EzOE = Backbone.View.extend({
    attributeEl : null,
    tinymceEditor : null,

    initialize : function(options)
    {
        options = (options || {});

        if (_(options).has('textEl'))
            this.attributeEl = $(options.textEl).closest('.attribute');
        if (_(options).has('tinymceEditor'))
            this.tinymceEditor = options.tinymceEditor;

        _.bindAll(this);

        var prefix = (eZExceed && _(eZExceed).has('urlPrefix')) ? '/' + eZExceed.urlPrefix : '';
        prefix = prefix + '/ezjscore/call';

        /**
         * TODO: The attributeEl is eZExceed spesific so this won't work in vanilla version
         */
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

        this.model.bind('version.create', this.updateEditor);

        this.browser = eZExceed.stack.push(KeyMedia.views.Browser, options, {headingOptions : headingOptions});
        this.browser.on('destruct', this.showScaler);

        return this;
    },

    changeMedia : function(params)
    {
        this.media = params;
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
            };
            var headingOptions =
            {
                name : 'Select crops',
                icon : '/extension/ezexceed/design/ezexceed/images/kp/32x32/Pictures-alt-2b.png',
                quotes : true
            };

            eZExceed.stack.push(KeyMedia.views.Scaler, options, {headingOptions : headingOptions});
        });
    },

    updateEditor : function(data)
    {
        var media = this.media.model;
        var fileUrl = '//' + media.get('host') + data.url + '.' + media.get('scalesTo').ending;
        var mediaId = media.id,
            keymediaId = this.media.keymediaId;

        var content = '<img id="__mce_tmp" class="ezoeItemCustomTag keymedia" type="custom" ' +
            'customattributes="mediaId|' + mediaId + 'attribute_separationkeymediaId|' + keymediaId +
            'attribute_separationimage_url|' + fileUrl + '" src="' + fileUrl + '" />';
       // eZOEPopupUtils.insertHTMLCleanly(this.tinymceEditor, '<img id="__mce_tmp" type="custom" src="' + fileUrl + '" \/>', '__mce_tmp');
        this.tinymceEditor.execCommand('mceInsertRawHTML', false, content);
        //tinyMCE.execCommand('mceInsertRawHTML', false, content);

    }
});

