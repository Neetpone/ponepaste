<?php
namespace PonePaste;

use ParsedownExtra;

class Pastedown extends ParsedownExtra {
    public function __construct() {
        parent::__construct();
        unset($this->BlockTypes['>']);
        unset($this->BlockTypes['<']);
        $this->InlineTypes['>'] = ['Greentext'];
        array_unshift($this->InlineTypes['<'], 'Redtext');
        $this->InlineTypes['@'] = ['Purpletext'];
        $this->inlineMarkerList .= '>@';
    }

    protected function inlineGreentext($Line)
    {
        if ($this->isLineStart($Line) && preg_match('/^>[ ]?(.*?)( {2,})?(?=\n|$)/', $Line['text'], $matches)) {
            return [
                'extent' => strlen($matches[0]) - strlen($matches[2] ?? ''),
                'element' => [
                    'name' => 'span',
                    'rawHtml' => '&gt;' . $this->escapeColoredText($matches[1]),
                    'allowRawHtmlInSafeMode' => true, // safe to do because the content is escaped by escapeColoredText()
                    'attributes' => [
                        'class' => 'greentext'
                    ]
                ]
            ];
        }
    }

    protected function inlineRedtext($Line)
    {
        if ($this->isLineStart($Line) && preg_match('/^<[ ]?(.*?)( {2,})?(?=\n|$)/', $Line['text'], $matches)) {
            return [
                'extent' => strlen($matches[0]) - strlen($matches[2] ?? ''),
                'element' => [
                    'name' => 'span',
                    'rawHtml' => '&lt;' . $this->escapeColoredText($matches[1]),
                    'allowRawHtmlInSafeMode' => true, // safe to do because the content is escaped by escapeColoredText()
                    'attributes' => [
                        'class' => 'redtext'
                    ]
                ]
            ];
        }
    }

    protected function inlinePurpletext($Line)
    {
        if ($this->isLineStart($Line) && preg_match('/^@[ ]?(.*?)( {2,})?(?=\n|$)/', $Line['text'], $matches)) {
            return [
                'extent' => strlen($matches[0]) - strlen($matches[2] ?? ''),
                'element' => [
                    'name' => 'span',
                    'rawHtml' => '&#64;' . $this->escapeColoredText($matches[1]),
                    'allowRawHtmlInSafeMode' => true, // safe to do because the content is escaped by escapeColoredText()
                    'attributes' => [
                        'class' => 'purpletext'
                    ]
                ]
            ];
        }
    }

    private function isLineStart($Line): bool
    {
        $position = strlen($Line['context']) - strlen($Line['text']);

        return $position === 0 || $Line['context'][$position - 1] === "\n";
    }

    private function escapeColoredText(string $text): string
    {
        return str_replace('@', '&#64;', self::escape($text));
    }
}