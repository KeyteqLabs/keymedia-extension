KeyMedia.views.KeyMedia = KP.ContentEditor.Base.extend(
{
    initialize : function(options)
    {
        this.init(options);
        _.bindAll(this, 'browse', 'upload', 'scale', 'render', 'changeImage');
        var data = this.el.data();
        this.model = new KeyMedia.models.Attribute({
            id : data.id,
            version : data.version,
            prefix : '/' + KP.urlPrefix + '/ezjscore/call'
        });

        return this;
    },

    events :
    {
        'click button.from-keymedia' : 'browse',
        'click button.upload' : 'upload',
        'click button.scale' : 'scale'
    },

    parseEdited : function()
    {
        var values = [],
            node,
            elements = this.$('ul li'),
            value,
            inputName = this.base + '_data_object_relation_list_' + this.attributeId;

        if (elements.length)
        {
            elements.each(function()
            {
                node = $(this);
                value = {};
                value[inputName] = node.data('id');
                values.push(value);
            });
        }
        else
        {
            value = {};
            value[inputName] = 'no_relation';
            values.push(value);
        }
        return values;
    },

    browse : function(e)
    {
        e.preventDefault();
        var options = {
            model : this.model,
            collection : this.model.images,
            onSelect : this.changeImage,
            parentName : this.editor.model.get('metadata').objectName
        };

        this.model.images.search('');
        this.editor.trigger('stack.push', KeyMedia.views.Browser, options);
    },

    changeImage : function(id, host, ending)
    {
        this.editor.trigger('stack.pop');
        // Store image on attribute and reload it
    },

    upload : function(e)
    {
        e.preventDefault();

        $(e.currentTarget).parent().remove();
        this.editor.onHandlerSave(this.attributeId, this.parseEdited());
    },

    render : function(elements)
    {
        return this;
    },

    scale : function(elements)
    {
        var parsedElements = [], parsedElement = null, order = null, even = null;

        var existingCount = this.$('tr').length;

        _(elements).each(function(element, i)
        {
            order = existingCount + i;

             parsedElement =
             {
                 id : element.get('id'),
                 name : element.get('name'),
                 className : KP.getClass(element.get('contentClassId')),
                 sectionName : KP.getSection(element.get('sectionId')),
                 published : element.get('status'),
                 icon : element.get('classIcon')
             };

            parsedElements[i] = parsedElement;
        }, this);

        return parsedElements;
    },

    onAdd : function(elements)
    {
        this.render(elements);

        this.editor.trigger('stack.pop');

        this.editor.onHandlerSave(this.attributeId, this.parseEdited());
    },

    removeObjects : function(e)
    {
        e.preventDefault();

        this.$('ul li input:checked').parents('li').remove();

        this.editor.onHandlerSave(this.attributeId, this.parseEdited());
    }
});
