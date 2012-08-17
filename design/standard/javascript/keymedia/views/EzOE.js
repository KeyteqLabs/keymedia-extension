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

        var values = {
            mediaId : this.media.model.id,
            keymediaId : this.media.keymediaId,
            x1 : data.coords[0],
            y1 : data.coords[1],
            x2 : data.coords[2],
            y2 : data.coords[3],
            width : data.size[0],
            height : data.size[1],
            image_url : '//' + media.get('host') + data.url + '.' + media.get('scalesTo').ending
        };
        var customAttributes = _(values).map(function(value, key){
            return key + '|' + value;
        });
        console.log(customAttributes);
        var customAttributesString = customAttributes.join('attribute_separation');
        console.log(customAttributesString);

        var content = '<img id="__mce_tmp" class="ezoeItemCustomTag keymedia" type="custom" ' +
            'customattributes="' + customAttributesString + '" src="' + values.image_url + '" />';
        this.tinymceEditor.execCommand('mceInsertRawHTML', false, content);
    }
});

