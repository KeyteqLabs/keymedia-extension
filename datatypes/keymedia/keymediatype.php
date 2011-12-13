<?php

use \ezr_keymedia\models\Backend;

use \ezr_keymedia\models\image\Handler;

class KeyMedia extends eZDataType
{
	const DATA_TYPE_STRING = 'keymedia';
    const FIELD_BACKEND = 'data_int1';

    /**
     * Construction of the class, note that the second parameter in eZDataType 
     * is the actual name showed in the datatype dropdown list.
     */
    function __construct()
    {
        parent::__construct(
            self::DATA_TYPE_STRING, 'KeyMedia', array('serialize_supported' => true)
        );
    }

    /**
     * Called when the datatype is added to a content class
     * Make sure we store needed metadata somewhere ...
     *
     * @param eZHTTPTool $http
     * @param string $base
     * @param eZContentClassAttribute $classAttribute
     */
    public function fetchClassAttributeHTTPInput($http, $base, $class)
    {
        $backend = (int) $http->variable($base . 'connection' . $class->attribute('id'), 0);
        $class->setAttribute(self::FIELD_BACKEND, $backend);
        return true;
    }

    /**
     * Called on {$class_attribute.content} in template
     *
     * @return array 
     */
    public function classAttributeContent($class)
    {
        $backends = \eZPersistentObject::fetchObjectList(Backend::definition());
        $selected = $class->attribute(self::FIELD_BACKEND);
        return compact('backends', 'selected');
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

    function deleteStoredObjectAttribute( $contentObjectAttribute, $version = null )
    {
    }

    /*!
    */
    function fetchObjectAttributeHTTPInput( $http, $base, $contentObjectAttribute )
    {
        // Use data_int for storing 'disabled' flag
        //$contentObjectAttribute->setAttribute( 'data_int', $http->hasPostVariable( $base . '_data_srrating_disabled_' . $contentObjectAttribute->attribute( 'id' ) ) );
        return true;
    }

    /*!
     Store the content. Since the content has been stored in function 
     fetchObjectAttributeHTTPInput(), this function is with empty code.
    */
    function storeObjectAttribute( $contentObjectAttribute )
    {
    }

    /*!
     Returns the meta data used for storing search indices.
    */
    function metaData( $contentObjectAttribute )
    {
        return array();
    }

    /*!
     Returns the text.
    */
    function title( $contentObjectAttribute, $name = null)
    {
        return $this->metaData( $contentObjectAttribute );
    }

    function isIndexable()
    {
        return true;
    }

    function sortKey( $contentObjectAttribute )
    {
        return $this->metaData( $contentObjectAttribute );
    }
  
    function sortKeyType()
    {
        return 'integer';
    }

    function hasObjectAttributeContent( $contentObjectAttribute )
    {
        return true;
    }

    /**
     * Notify eZPublish that KeyMedia supports file upload (file insertion)
     *
     * @return bool
     */
    function isHTTPFileInsertionSupported()
    {
        return true;
    }

    /**
     * Callback for when file insertion to this datatype happens?
     *
     * @param eZContentObject $object
     * @param int|eZContentObjectVersion $version
     * @param string $language Current language being worked on
     * @param mixed $attribute The attribute containing the file
     * @param eZHTTPFile $file THe actual uploaded file
     * @param array $mime
     * @param array $result Out-param containing two keys:
     *        - _errors_ Array with errors with key `description`
     *        - _require_storage_ True if the file needs to be stored afterwards
     * @return bool
     */
    function insertHTTPFile($object, $version, $language, $attribute, $file, $mime, &$result)
    {
        eZDebug::writeWarning("File upload for the win");
        return true;
    }

    /**
     * Fetch content contained in this attribute when its stored
     *
     * @param object $attribute
     * @return mixed
     */
    function objectAttributeContent( $contentObjectAttribute )
    {
        return;
        $objectId = $contentObjectAttribute->attribute('contentobject_id');
        $attributeId = $contentObjectAttribute->attribute('id');
        $ratingObj = null;
        if ( $objectId && $attributeId )
        {
            $ratingObj = ezsrRatingObject::fetchByObjectId( $objectId, $attributeId );
    
            // Create empty object if none could be fetched
            if (  !$ratingObj instanceof ezsrRatingObject )
            {
                $ratingObj = ezsrRatingObject::create( array('contentobject_id' => $objectId,
                                                             'contentobject_attribute_id' => $attributeId ) );
            }
        }
        return $ratingObj;
    }
}

eZDataType::register(
    KeyMedia::DATA_TYPE_STRING,
    'KeyMedia'
);
