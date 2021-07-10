<?php

#
#
# Beam Parsedown
# https://github.com/ardissoebrata/beam-parsedown
#
# (c) Emanuil Rusev
# http://erusev.com
#
# (c) Ardi Soebrata
# https://mybeam.me
#
# For the full license information, view the LICENSE file that was distributed
# with this source code.
#
#

namespace ArdiSSoebrata\BeamParsedown;

use ParsedownExtra;

class BeamParsedown extends ParsedownExtra
{
    const version = '0.0.1';
    protected $isUrlRegex = "/(https?|ftp)\:\/\//i";
    protected $regexAttribute = '(?:([#.][\w-]+\s*)|([\w-]+=[\w-]+\s*))+';
	
    function __construct()
    {
        parent::__construct();

        // @codeCoverageIgnoreStart
		if (version_compare(parent::version, '0.8.1') < 0)
		{
			throw new Exception('BeamParsedown requires a later version of ParsedownExtra');
        }
        // @codeCoverageIgnoreEnd

        $this->InlineTypes['['][] = 'Icon';
        $this->InlineTypes['['][] = 'Audio';

        // Identify our blocks before definition list.
        array_unshift($this->BlockTypes[':'], 'Alert');
        array_unshift($this->BlockTypes[':'], 'Mermaid');
        array_unshift($this->BlockTypes[':'], 'Chart');
        // Identify our blocks before Reference.
        array_unshift($this->BlockTypes['['], 'Youtube');
        array_unshift($this->BlockTypes['['], 'Drawio');
    }

    // Base path.

    protected $basePath = '';

    public function setBasePath($url)
    {
        $this->basePath = preg_replace('{/$}', '', $url) . '/';
        return $this;
    }

    protected function inlineImage($excerpt)
    {
        $image = parent::inlineImage($excerpt);

        if ( ! isset($image))
        {
            return null;
        }

        // Add basePath if src is relative.
        $src = $image['element']['attributes']['src'];
        if (!preg_match($this->isUrlRegex, $src, $urlmatch)) {
            $image['element']['attributes']['src'] = $this->basePath . $src;
        }

        return $image;
    }

    // Heading id & attributes.

    protected function blockHeader($Line)
    {
        $Block = parent::blockHeader($Line);

        if (! isset($Block)) {
            return null;
        }

        if (!isset($Block['element']['attributes']['id'])) {
            $text = $Block['element']['text'];
            $text = preg_replace('/(\[.+:.*\]\s)/', '', $text);         // remove [tag: value]. Ex. [icon: fa fa-home].
            $Block['element']['attributes']['id'] = $this->slugify($text);
        }

        return $Block;
    }

    protected function blockSetextHeader($Line, array $Block = null)
    {
        $Block = parent::blockSetextHeader($Line, $Block);

        if (isset($Block['element']) && !isset($Block['element']['attributes']['id'])) {
            $text = $Block['element']['text'];
            $text = preg_replace('/(\[.+:.*\]\s)/', '', $text);         // remove [tag: value]. Ex. [icon: fa fa-home].
            $Block['element']['attributes']['id'] = $this->slugify($text);
        }

        return $Block;
    }

    protected function parseAttributeData($attributeString)
    {
        $Data = array();

        $attributes = preg_split('/[ ]+/', $attributeString, - 1, PREG_SPLIT_NO_EMPTY);

        foreach ($attributes as $attribute)
        {
            if ($attribute[0] === '#')
            {
                $Data['id'] = substr($attribute, 1);
            }
            elseif ($attribute[0] === '.')
            {
                $classes []= substr($attribute, 1);
            }
            elseif (preg_match('/([\w-]+)=([\w-]+)/', $attribute, $match))
            {
                $Data[$match[1]] = $match[2];
            }
        }

        if (isset($classes))
        {
            $Data['class'] = implode(' ', $classes);
        }

        return $Data;
    }

    public static function slugify($text)
    {
        // replace non letter or digits by -
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);

        // transliterate
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

        // remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);

        // trim
        $text = trim($text, '-');

        // remove duplicate -
        $text = preg_replace('~-+~', '-', $text);

        // lowercase
        $text = strtolower($text);

        if (empty($text)) {
            return 'n-a';
        }

        return $text;
    }
    
    // Icon

	protected function InlineIcon($excerpt)
	{
        if (preg_match('/\[icon:(.+?)\]/', $excerpt['text'], $matches)) 
        {
            return array(
                // How many characters to advance the Parsedown's
                // cursor after being done processing this tag.
                'extent' => strlen($matches[0]), 
                'element' => array(
                    'name' => 'i',
                    'attributes' => array(
                        'class' => trim($matches[1]),
                    ),
                    'rawHtml' => '',
                ),
            );
        }
    }

    // Audio

    protected function InlineAudio($excerpt)
    {
        if (preg_match('/\[audio:(.+?)\]/', $excerpt['text'], $matches)) 
        {
            // Add basePath if src is relative.
            $src = trim($matches[1]);
            if (!preg_match($this->isUrlRegex, $src, $urlmatch)) {
                $src = $this->basePath . $src;
            }

            return array(
                // How many characters to advance the Parsedown's
                // cursor after being done processing this tag.
                'extent' => strlen($matches[0]), 
                'element' => array(
                    'name' => 'audio',
                    'attributes' => array(
                        'controls' => '',
                        'preload' => 'none',
                    ),
                    'handler' => 'element',
                    'text' => array(
                        'name' => 'source',
                        'attributes' => array(
                            'src' => $src,
                        )
                    ),
                ),
            );
        }
    }

    // Youtube
    
    protected function BlockYoutube($excerpt)
    {
        if (preg_match('/\[youtube:\s*https\:\/\/youtu\.be\/(.+?)\]/', $excerpt['text'], $matches)) 
        {
            $video_id = trim($matches[1]);
            return array(
                // How many characters to advance the Parsedown's
                // cursor after being done processing this tag.
                'extent' => strlen($matches[0]), 
                'element' => array(
                    'name' => 'div',
                    'attributes' => array(
                        'class' => 'overflow-hidden relative h-0',
                        'style' => 'padding-bottom: 56.25%',
                    ),
                    'handler' => 'element',
                    'text' => array(
                        'name' => 'iframe',
                        'attributes' => array(
                            'src' => 'https://www.youtube.com/embed/' . $video_id,
                            'frameborder' => '0',
                            'allow' => 'accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture',
                            'allowfullscreen' => '',
                            'class' => 'left-0 top-0 h-full w-full absolute',
                        ),
                        'rawHtml' => '',
                    ),
                ),
            );
        }
    }

    // Alerts

    protected $alert_types = array(
        'info' => array(
            'container-class' => 'bg-indigo-100 rounded shadow-sm flex overflow-hidden',
            'icon-bg-class' => 'bg-indigo-500 w-20 flex justify-center items-center',
            'icon-class' => 'fa fa-info-circle fa-2x text-white',
        ),
        'warning' => array(
            'container-class' => 'bg-yellow-50 rounded shadow-sm flex overflow-hidden',
            'icon-bg-class' => 'bg-yellow-300 w-20 flex justify-center items-center',
            'icon-class' => 'fa fa-exclamation-triangle fa-2x',
        )
    );

    protected function BlockAlert($line, $block)
    {
        $types = implode('|', array_keys($this->alert_types));
        if (preg_match('/^:::(' . $types . ')/', $line['text'], $matches))
        {
            $type = trim($matches[1]);
            return array(
                'char' => $line['text'][0],
                'element' => array(
                    'name' => 'div',
                    'attributes' => array(
                        'class' => $this->alert_types[$type]['container-class'],
                        'role' => 'alert',
                    ),
                    'handler' => 'elements',
                    'text' => array(
                        array(
                            'name' => 'div',
                            'attributes' => array(
                                'class' => $this->alert_types[$type]['icon-bg-class']
                            ),
                            'handler' => 'element',
                            'text' => array(
                                'name' => 'i',
                                'attributes' => array(
                                    'class' => $this->alert_types[$type]['icon-class'],
                                ),
                                'rawHtml' => ''
                            ),
                        ),
                        array(
                            'name' => 'div',
                            'attributes' => array(
                                'class' => 'flex-1 px-4',
                            ),
                            'handler' => 'lines',
                            'text' => array(),
                        )
                    ),
                ),
            );
        }
    }

    protected function BlockAlertContinue($line, $block)
    {
        if (isset($block['complete']))
        {
            return;
        }

        // A blank newline has occurred.
        if (isset($block['interrupted']))
        {
            unset($block['interrupted']);
        }

        // Check for end of the block. 
        if (preg_match('/^:::/', $line['text']))
        {
            $block['complete'] = true;
            return $block;
        }
        
        $block['element']['text'][1]['text'][] = $line['body'];
        
        return $block;
    }

    protected function BlockAlertComplete($block)
    {
        return $block;
    }
    
    // draw.io
    
    protected function BlockDrawio($excerpt)
    {
        if (preg_match('/\[drawio:\s*(.+?)\]/', $excerpt['text'], $matches)) 
        {
            $file = trim($matches[1]);
            if (!preg_match($this->isUrlRegex, $file, $urlmatch)) {
                $file = $this->basePath . $file;
            }
            return array(
                // How many characters to advance the Parsedown's
                // cursor after being done processing this tag.
                'extent' => strlen($matches[0]), 
                'element' => array(
                    'name' => 'div',
                    'handler' => 'elements',
                    'text' => array(
                        array(
                            'name' => 'div',
                            'attributes' => array(
                                'class' => 'mxgraph w-full border',
                                'data-mxgraph' => json_encode(array(
                                    'highlight' => '#0000ff',
                                    'target' => 'blank',
                                    'nav' => true,
                                    'resize' => true,
                                    'toolbar' => 'zoom layers lightbox',
                                    'url' => $file,
                                ))
                            ),
                            'rawHtml' => '',
                        ),
                        array(
                            'name' => 'script',
                            'attributes' => array(
                                'type' => 'text/javascript',
                                'src' => 'https://viewer.diagrams.net/js/viewer-static.min.js'
                            ),
                            'rawHtml' => '',
                        ),
                    ),
                ),
            );
        }
    }

    // Mermaid

    protected function BlockMermaid($line, $block)
    {
        if (preg_match('/^:::\s*mermaid/', $line['text'], $matches))
        {
            return array(
                'char' => $line['text'][0],
                'element' => array(
                    'name' => 'div',
                    'attributes' => array(
                        'class' => 'mermaid',
                    ),
                    'rawHtml' => "\n",
                ),
            );
        }
    }

    protected function BlockMermaidContinue($line, $block)
    {
        if (isset($block['complete']))
        {
            return;
        }

        // A blank newline has occurred.
        if (isset($block['interrupted']))
        {
            unset($block['interrupted']);
        }

        // Check for end of the block. 
        if (preg_match('/^:::/', $line['text']))
        {
            $block['complete'] = true;
            return $block;
        }
        
        $block['element']['rawHtml'] .= $line['body'] . "\n";
        
        return $block;
    }
    
    protected function BlockMermaidComplete($block)
    {
        return $block;
    }
    
    // Chart JS

    protected function BlockChart($line, $block)
    {
        if (preg_match('/^:::\s*chart/', $line['text'], $matches))
        {
            return array(
                'char' => $line['text'][0],
                'element' => array(
                    'name' => 'canvas',
                    'attributes' => array(
                        'class' => 'chartjs',
                    ),
                    'rawHtml' => "\n",
                ),
            );
        }
    }

    protected function BlockChartContinue($line, $block)
    {
        if (isset($block['complete']))
        {
            return;
        }

        // A blank newline has occurred.
        if (isset($block['interrupted']))
        {
            unset($block['interrupted']);
        }

        // Check for end of the block. 
        if (preg_match('/^:::/', $line['text']))
        {
            $block['complete'] = true;
            return $block;
        }
        
        $block['element']['rawHtml'] .= $line['body'] . "\n";
        
        return $block;
    }
    
    protected function BlockChartComplete($block)
    {
        return $block;
    }
}