<?php
namespace PonePaste;

use ParsedownExtra;

class Pastedown extends ParsedownExtra {
    public function __construct() {
        $this->BlockTypes['>'] = ['Greentext'];
        $this->BlockTypes['<'] []= ['Redtext'];
        $this->BlockTypes['@'] = ['Purpletext'];
    }


    protected function blockGreentext($Line)
    {
        if (preg_match('/^>[ ]?(.*)/', $Line['text'], $matches))
        {
            $Block = array(
                'element' => array(
                    'name' => 'span',
                    'attributes' => [
                        'class' => 'greentext'
                    ],
                    'handler' => 'line',
                    'text' => $matches[0],
                ),
            );

            return $Block;
        }
    }

    protected function blockRedtext($Line)
    {
        if (preg_match('/^<[ ]?(.*)/', $Line['text'], $matches))
        {
            $Block = array(
                'element' => array(
                    'name' => 'span',
                    'handler' => 'line',
                    'attributes' => [
                        'class' => 'redtext'
                    ],
                    'text' => $matches[0],
                ),
            );

            return $Block;
        }
    }

    protected function blockPurpletext($Line)
    {
        if (preg_match('/^@[ ]?(.*)/', $Line['text'], $matches))
        {
            $Block = array(
                'element' => array(
                    'name' => 'span',
                    'handler' => 'line',
                    'attributes' => [
                        'class' => 'purpletext'
                    ],
                    'text' => $matches[0],
                ),
            );

            return $Block;
        }
    }
}