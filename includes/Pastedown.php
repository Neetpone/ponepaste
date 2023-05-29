<?php
namespace PonePaste;

use ParsedownExtra;

class Pastedown extends ParsedownExtra {
    public function __construct() {
        parent::__construct();
        unset($this->BlockTypes['>']);
        $this->BlockTypes['>'] = ['Greentext'];
        $this->InlineTypes['>'] = ['Greentext'];
        array_unshift($this->InlineTypes['<'], 'Redtext');
        $this->BlockTypes['<'] = ['Redtext'];
        $this->InlineTypes['@'] = ['Purpletext'];
        $this->BlockTypes['@'] = ['Purpletext'];
    }

    protected function inlineGreentext($Line)
    {
        if (preg_match('/^>[ ]?(.*)/', $Line['text'], $matches)) {
            return [
                'extent' => strlen($matches[0]),
                'element' => [
                    'name' => 'span',
                    'handler' => 'line',
                    'text' => '&gt;' . $matches[1], // This is a huge hack to prevent recursive parsing
                    'attributes' => [
                        'class' => 'greentext'
                    ]
                ]
            ];
        }
    }

    protected function blockGreentext($Line)
    {
        if (preg_match('/^>[ ]?(.*)/', $Line['text'], $matches)) {
            return [
                'extent' => strlen($matches[0]),
                'element' => [
                    'name' => 'div',
                    'handler' => 'line',
                    'text' => '&gt;' . $matches[1], // This is a huge hack to prevent recursive parsing
                    'attributes' => [
                        'class' => 'greentext'
                    ]
                ]
            ];
        }
    }

    protected function inlineRedtext($Line)
    {
        if (preg_match('/^<[ ]?(.*)/', $Line['text'], $matches)) {
            return [
                'extent' => strlen($matches[0]),
                'element' => [
                    'name' => 'span',
                    'handler' => 'line',
                    'text' => '&lt;' . $matches[1], // This is a huge hack to prevent recursive parsing
                    'attributes' => [
                        'class' => 'redtext'
                    ]
                ]
            ];
        }
    }

    protected function blockRedtext($Line)
    {
        if (preg_match('/^<[ ]?(.*)/', $Line['text'], $matches)) {
            return [
                'extent' => strlen($matches[0]),
                'element' => [
                    'name' => 'div',
                    'handler' => 'line',
                    'text' => '&lt;' . $matches[1], // This is a huge hack to prevent recursive parsing
                    'attributes' => [
                        'class' => 'redtext'
                    ]
                ]
            ];
        }
    }

    protected function inlinePurpletext($Line)
    {
        throw new \Exception("Calling the functor");
        if (preg_match('/^@[ ]?(.*)/', $Line['text'], $matches))
        {
            return [
                'markup' => "<div class=\"purpletext\">" . pp_html_escape($matches[0]) . "</div>",
                'extent' => strlen($matches[0]),
                'text' => $matches[1]
            ];
        }
    }

    protected function blockPurpletext($Line)
    {
        if (preg_match('/^@[ ]?(.*)/', $Line['text'], $matches)) {
            return [
                'extent' => strlen($matches[0]),
                'element' => [
                    'name' => 'div',
                    'handler' => 'line',
                    'text' => '&#64;' . $matches[1], // This is a huge hack to prevent recursive parsing
                    'attributes' => [
                        'class' => 'purpletext'
                    ]
                ]
            ];
        }
    }
}