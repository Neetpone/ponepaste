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
                'markup' => "<div class=\"greentext\">" . pp_html_escape($matches[0]) . "</div>",
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
                'markup' => "<div class=\"redtext\">" . pp_html_escape($matches[0]) . "</div>",
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
                'markup' => "<div class=\"purpletext\">" . pp_html_escape($matches[0]) . "</div>",
                'extent' => strlen($matches[0])
            );

            return $Block;
        }
    }
}