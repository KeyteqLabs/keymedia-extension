<?php

/*!
  \class   TemplateKeymediaOperator templatekeymediaoperator.php
  \ingroup eZTemplateOperators
  \brief   Generates url for keymediaattribute, based on alias, format
  \version 1.0
  \date    Monday January 02 2012 12:46:42 pm
  \author  Kristian Blom

  

  Example:
\code
{$value|keymedia('first',$input2)|wash}
\endcode
*/

/*
If you want to have autoloading of this operator you should create
a eztemplateautoload.php file and add the following code to it.
The autoload file must be placed somewhere specified in AutoloadPath
under the group TemplateSettings in settings/site.ini

$eZTemplateOperatorArray = array();
$eZTemplateOperatorArray[] = array( 'script' => 'templatekeymediaoperator.php',
                                    'class' => 'TemplateKeymediaOperator',
                                    'operator_names' => array( 'keymedia' ) );

If your template operator is in an extension, you need to add the following settings:

To extension/YOUREXTENSION/settings/site.ini.append:
---
[TemplateSettings]
ExtensionAutoloadPath[]=YOUREXTENSION
---

To extension/YOUREXTENSION/autoloads/eztemplateautoload.php:
----
$eZTemplateOperatorArray = array();
$eZTemplateOperatorArray[] = array( 'script' => 'extension/YOUEXTENSION/YOURPATH/templatekeymediaoperator.php',
                                    'class' => 'TemplateKeymediaOperator',
                                    'operator_names' => array( 'keymedia' ) );
---

Create the files if they don't exist, and replace YOUREXTENSION and YOURPATH with the correct values.

*/


class TemplateKeymediaOperator
{
    /*!
      Constructor, does nothing by default.
    */
    function TemplateKeymediaOperator()
    {
    }

    /*!
     \return an array with the template operator name.
    */
    function operatorList()
    {
        return array('keymedia');
    }

    /*!
     \return true to tell the template engine that the parameter list exists per operator type,
             this is needed for operator classes that have multiple operators.
    */
    function namedParameterPerOperator()
    {
        return true;
    }

    /*!
     See eZTemplateOperator::namedParameterList
    */
    function namedParameterList()
    {
        return array(
            'keymedia' => array(

                    'attribute' => array(
                        'type' => 'eZContentObjectAttribute',
                        'required' => true
                    ),
                    'format' => array(
                        'type' => 'mixed',
                        'required' => false,
                        'default' => null
                    )

            )
        );
    }


    /*!
     Executes the PHP function for the operator cleanup and modifies \a $operatorValue.
    */
    function modify($tpl, $operatorName, $operatorParameters, $rootNamespace, $currentNamespace, &$operatorValue, $namedParameters, $placement)
    {
        $attr = $namedParameters['attribute'];
        $format = $namedParameters['format'];

        // Example code. This code must be modified to do what the operator should do. Currently it only trims text.
        switch ($operatorName)
        {
            case 'keymedia':
                {
                // $attr->content()->mediaUrl($format);
                $operatorValue = $attr->content()->media($format);

                }
                break;
        }
    }
}

?>
