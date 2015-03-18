<?php

use \keymedia\models\Backend;
use \keymedia\models\media\Handler;

class KeyMedia extends eZDataType
{
	const DATA_TYPE_STRING = 'keymedia';
    const FIELD_BACKEND = 'data_int1';
    const FIELD_JSON = 'data_text5';
    const FIELD_VALUE = 'data_text';

    /**
     * Construction of the class, note that the second parameter in eZDataType 
     * is the actual name showed in the datatype dropdown list.
     */
    function __construct()
    {
        parent::__construct(self::DATA_TYPE_STRING, 'KeyMedia', array('serialize_supported' => true));
    }

    /**
     * Called when the datatype is added to a content class
     *
     * @param eZHTTPTool $http
     * @param string $base
     * @param eZContentClassAttribute $classAttribute
     */
    public function fetchClassAttributeHTTPInput($http, $base, $class)
    {
        $backendKey = $base . '_connection_' . $class->attribute('id'); 
        $versionsKey = $base . '_versions_' . $class->attribute('id'); 
        if ($http->hasPostVariable($backendKey))
        {
            $backend = (int) $http->variable($backendKey, 0);
            $class->setAttribute(self::FIELD_BACKEND, $backend);
        }
        if ($http->hasPostVariable($versionsKey))
        {
            $versions = explode("\n", $http->variable($versionsKey, ''));
            $versions = array_filter($versions);
            $versions = array_map('trim', $versions);
            $json = json_encode(compact('versions'));
            $class->setAttribute(self::FIELD_JSON, $json);
        }
        return true;
    }

    /**
     * Called on {$class_attribute.content} in content class template
     *
     * @return array 
     */
    public function classAttributeContent($class)
    {
        $backends = \eZPersistentObject::fetchObjectList(Backend::definition());
        $selected = $class->attribute(self::FIELD_BACKEND);
        $json = json_decode($class->attribute(self::FIELD_JSON));
        if ($json && $json->versions)
            $versions = join("\n", $json->versions);
        return compact('backends', 'selected', 'versions');
    }

    /**
     * Validations for when a ContentObject containing this datatype
     * as an attribute is saved
     *
     * @param eZHTTPTool $http
     * @param mixed $base
     * @param object $contentObjectAttribute
     *
     * @return bool
     */
    public function validateObjectAttributeHTTPInput( $http, $base, $contentObjectAttribute )
    {
        return eZInputValidator::STATE_ACCEPTED;
    }

    /*!
    */
    public function fetchObjectAttributeHTTPInput( $http, $base, $attribute )
    {
        // Get value of connected media id
        $attributeId = $attribute->attribute('id');
        $data = array(
            'id' =>  $http->variable($base . '_media_id_' . $attributeId)
        );

        $extras = $http->variable($base . '_data_' . $attributeId);
        if ($extras) {
            $data += json_decode($extras, true);
        }
        $data['alttext'] = $http->variable($base . '_alttext_' . $attributeId, '');

        $handler = new Handler($attribute);

        return $data['id'] ? $handler->setMedia($data) : $handler->remove();
    }

    /**
     * Check if attribute has content
     * Called before {$attribute.content} in templates delegates to
     * `objectAttributeContent` to actuall fetch the content
     *
     * @param object $attribute
     * @return bool
     */
    public function hasObjectAttributeContent($attribute)
    {
        $handler = $this->objectAttributeContent($attribute);
        return $handler->hasMedia();
    }

    /**
     * Fetch content contained in this attribute when its stored
     * This method is triggered when a template states {$attribute.content}
     *
     * @param object $attribute
     * @return \keymedia\models\media\Handler
     */
    public function objectAttributeContent($attribute)
    {
        return new Handler($attribute);
    }

    public function onPublish($attribute, $contentObject, $publishedNodes)
    {
        $handler = $this->objectAttributeContent($attribute);
        $handler->reportUsage($contentObject);
    }
}

eZDataType::register(
    KeyMedia::DATA_TYPE_STRING,
    'KeyMedia'
);
