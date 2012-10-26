<?php

/**
 * @author Kristian Blom
 * @since 2012-01-02
 */
class TemplateKeymediaOperator
{

    /**
     * @return array
     */
    function operatorList()
    {
        return array('keymedia');
    }

    /**
     * @return bool
     */
    function namedParameterPerOperator()
    {
        return true;
    }

    /**
     * @return array
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
                    ),
                'quality' => array(
                    'type' => 'mixed',
                    'required' => false,
                    'default' => null
                ),
                'fetchInfo' => array(
                    'type' => 'mixed',
                    'required' => false,
                    'default' => null
                )
            )
        );
    }


    /**
     * @param $tpl
     * @param $operatorName
     * @param $operatorParameters
     * @param $rootNamespace
     * @param $currentNamespace
     * @param $operatorValue
     * @param $namedParameters
     * @param $placement
     * @return void
     */
    function modify($tpl, $operatorName, $operatorParameters, $rootNamespace, $currentNamespace, &$operatorValue, $namedParameters, $placement)
    {
        $attr = $namedParameters['attribute'];
        if (!$attr) {
            $operatorValue = null;
            return;
        }
        $format = $namedParameters['format'];
        $quality = $namedParameters['quality'];
        $fetchInfo = $namedParameters['fetchInfo'];
        $format = $format ?: array(300, 200);

        switch ($operatorName) {
            case 'keymedia':
                $handler = $attr->content();
                $operatorValue = $handler->media($format, $quality, $fetchInfo);
                break;
        }
    }
}
