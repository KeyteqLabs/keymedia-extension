define(['shared/view', 'jquery-safe', '../models', './browser', './scaler'],
    function(View, $, Models, BrowserView, ScalerView)
{
    return View.extend({
        attributeEl : null,
        tinymceEditor : null,
        bookmark : null,
        selectedContent : null,
        editorAttributes : {},

        initialize : function(options)
        {
            options = (options || {});
            _.bindAll(this);

            if (_(options).has('textEl')) {
                this.attributeEl = $(options.textEl).closest('.attribute');
            }
            if (_(options).has('tinymceEditor')) {
                this.tinymceEditor = options.tinymceEditor;
                this.bookmark = this.tinymceEditor.selection.getBookmark();
                this.selectedContent = $(this.tinymceEditor.selection.getContent());
            }

            // TODO: The attributeEl is eZExceed spesific so this won't work in vanilla version
            var data = this.attributeEl.data();
            this.model = new Models.attribute({
                id : 'ezoe',
                attributeId : data.id,
                version : data.version
            }).on('version.create', this.updateEditor);

            var urlRoot = '/ezjscore/call';
            if (data.urlRoot !== '/') urlRoot = data.urlRoot + urlRoot;

            this.collection = new Models.collection();
            this.collection.urlRoot = urlRoot;
            this.collection.id = data.id;
            this.collection.version = data.version;

            if (this.selectedContent && this.selectedContent.is('img') && this.selectedContent.hasClass('keymedia')) {
                // Preselected image. Show scaler with selected crop
                var customAttributes = this.selectedContent.attr('customattributes');
                var attributes = {};

                _(customAttributes.split('attribute_separation')).each(function(value){
                    var tmpArr = value.split('|');
                    attributes[tmpArr[0]] = tmpArr[1];
                });
                this.editorAttributes = attributes;
                var options = {
                    id : attributes.mediaId,
                    keymediaId : attributes.keymediaId,
                    model : new Models.model()
                };
                this.media = options;
                this.showScaler();
            }
            else {
                var options = {
                    model : this.model,
                    collection : this.collection
                };
                var context = {
                    icon : '/extension/ezexceed/design/ezexceed/images/kp/32x32/Pictures.png',
                    heading : 'Select media',
                    render : true,
                    quotes : true
                };
                eZExceed.stack.push(
                    BrowserView,
                    options,
                    context
                ).on('destruct', this.loadScaler);
                this.collection.search('');
            }
        },

        loadScaler : function(data)
        {
            this.model.fetch({
                url : this.model.url('media', 'ezoe', data.id)
            }).success(this.showScaler);
        },

        changeMedia : function(params)
        {
            this.media = params;
            eZExceed.stack.pop();
        },

        showScaler : function()
        {
            var media = this.model.get('media');
            var options = {
                model : this.model,
                versions : this.model.get('toScale'),
                trueSize : [media.get('file').width, media.get('file').height],
                className : 'keymedia-scaler',
                singleVersion : true,
                editorAttributes : this.editorAttributes
            };

            var context = {
                heading : 'Select crop',
                icon : '/extension/ezexceed/design/ezexceed/images/kp/32x32/Pictures-alt-2b.png',
                className : 'dark',
                render : true
            };

            eZExceed.stack.push(
                ScalerView,
                options,
                context
            );
        },

        updateEditor : function(data)
        {
            var media = this.model.get('media');
            var values = this.editorAttributes;

            values = _(values).extend({
                mediaId : media.id,
                keymediaId : media.get('keymediaId'),
                version : data.name,
                image_width : data.size[0],
                image_height : data.size[1],
                image_url : '//' + media.get('host') + data.url + '.' + media.get('scalesTo').ending
            });
            if (data.coords)
            {
                values.x1 = data.coords[0];
                values.y1 = data.coords[1];
                values.x2 = data.coords[2];
                values.y2 = data.coords[3];
            }
            var customAttributes = _(values).map(function(value, key){
                return key + '|' + value;
            });
            var customAttributesString = customAttributes.join('attribute_separation');

            var imgAttribute = {
                src : values.image_url,
                customattributes : customAttributesString
            }
            this.updateTinyMCE(imgAttribute);
        },

        updateTinyMCE : function(attributes)
        {
            var ed = this.tinymceEditor,
                args = {
                    src : '',
                    alt : '',
                    style : '',
                    'class' : '',
                    width : '',
                    height : '',
                    onmouseover : '',
                    onmouseout : '',
                    type : 'custom'
                };

            _(args).extend(attributes);

            if (args.class.length)
                args.class += ' ';
            args.class = args.class.concat('ezoeItemCustomTag keymedia');

            // Fixes crash in Safari
            if (tinymce.isWebKit)
                ed.getWin().focus();

            if (this.bookmark)
                ed.selection.moveToBookmark(this.bookmark);

            var el = ed.selection.getNode();

            if (el && el.nodeName == 'IMG')
                ed.dom.setAttribs(el, args);
            else
            {
                ed.execCommand('mceInsertContent', false, '<img id="__mce_tmp" />', {skip_undo : 1});
                ed.dom.setAttribs('__mce_tmp', args);
                ed.dom.setAttrib('__mce_tmp', 'id', '');
                ed.undoManager.add();
            }
            /**
            * Trigger eZExceed autosave
            */
            ed.execCommand('mceRepaint');
            ed.save();
            $(ed.getElement()).trigger('focusout');
            ed.getWin().focus();
        }
    });

});
