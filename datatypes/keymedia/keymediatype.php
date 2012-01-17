<?php

use \ezr_keymedia\models\Backend;
use \ezr_keymedia\models\image\Handler;

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
    function validateObjectAttributeHTTPInput( $http, $base, $contentObjectAttribute )
    {
        return eZInputValidator::STATE_ACCEPTED;
    }

    /*!
    */
    function fetchObjectAttributeHTTPInput( $http, $base, $attribute )
    {
        // Get value of connected image id
        $attributeId = $attribute->attribute('id');
        $id = $http->variable($base . '_image_id_' . $attributeId);
        $host = $http->variable($base . '_host_' . $attributeId);
        $ending = $http->variable($base . '_ending_' . $attributeId);
        $handler = new Handler($attribute);
        return $handler->setImage($id, $host, $ending);
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
        return $handler->hasImage();
    }

    /**
     * Fetch content contained in this attribute when its stored
     * This method is triggered when a template states {$attribute.content}
     *
     * @param object $attribute
     * @return \ezr_keymedia\models\image\Handler
     */
    public function objectAttributeContent($attribute)
    {
        return new Handler($attribute);
    }

    /**
     * Upload new file
     *
     * @param eZHTTPFile|string $file
     * @return bool
     */
    public function uploadFile($file = null)
    {
        $handler = new Handler($contentObjectAttribute);
        if (is_string($file))
            return $handler->uploadUrl($file);
        elseif ($file instanceof eZHTTPFile)
            return $handler->uploadFile($file);
    }
}

eZDataType::register(
    KeyMedia::DATA_TYPE_STRING,
    'KeyMedia'
);
