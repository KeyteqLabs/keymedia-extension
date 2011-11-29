<?php

//namespace ezr_keymedia\datatypes\keymedia;

//use \ezpI18n;
//use \eZDataType;

class KeyMedia extends eZDataType
{
	const DATA_TYPE_STRING = 'keymedia';

    /*!
     Construction of the class, note that the second parameter in eZDataType 
     is the actual name showed in the datatype dropdown list.
    */
    function __construct()
    {
        parent::__construct( self::DATA_TYPE_STRING, \ezpI18n::tr( 'extension/keymedia/datatype', 'KeyMedia', 'Datatype name' ), array( 'serialize_supported' => true ) );
    }

    /**
     * Validates the input and returns true if the input was
     * valid for this datatype.
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

    /*!
     Returns the content.
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
