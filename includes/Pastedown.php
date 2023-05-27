<?php
namespace PonePaste;

use ParsedownExtra;

class Pastedown extends ParsedownExtra {
    public function __construct() {
        unset($this->BlockTypes['>']);
        $this->InlineTypes['>'] = ['Greentext'];
        array_unshift($this->InlineTypes['<'], 'Redtext');
        $this->InlineTypes['@'] = ['Purpletext'];
    }

    protected function inlineGreentext($Line)
    {
        if (preg_match('/^>[ ]?(.*)/', $Line['text'], $matches))
        {
            $Block = array(
                'markup' => "<span class=\"greentext\">" . pp_html_escape($matches[0]) . "</span>",
                'extent' => strlen($matches[0])
            );

            return $Block;
        }
    }

    protected function inlineRedtext($Line)
    {
        if (preg_match('/^<[ ]?(.*)/', $Line['text'], $matches))
        {
            $Block = array(
                'markup' => "<span class=\"redtext\">" . pp_html_escape($matches[0]) . "</span>",
                'extent' => strlen($matches[0])
            );

            return $Block;
        }
    }

    protected function inlinePurpletext($Line)
    {
        if (preg_match('/^@[ ]?(.*)/', $Line['text'], $matches))
        {
            $Block = array(
                'markup' => "<span class=\"purpletext\">" . pp_html_escape($matches[0]) . "</span>",
                'extent' => strlen($matches[0])
            );

            return $Block;
        }
    }
}