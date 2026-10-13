<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use PonePaste\Pastedown;

final class PastedownTest extends TestCase
{
    public function testGreentext(): void
    {
        $html = (new Pastedown())->text('>hello');

        self::assertStringContainsString(
            '<p><span class="greentext">&gt;hello</span></p>',
            $html
        );
    }

    public function testRedtext(): void
    {
        $html = (new Pastedown())->text('<warning');

        self::assertStringContainsString(
            '<p><span class="redtext">&lt;warning</span></p>',
            $html
        );
    }

    public function testPurpletext(): void
    {
        $html = (new Pastedown())->text('@note');

        self::assertStringContainsString(
            '<p><span class="purpletext">&#64;note</span></p>',
            $html
        );
    }

    public function testColoredTextInSafeMode(): void
    {
        $parser = new Pastedown();
        $parser->setSafeMode(true);
        $html = $parser->text('>hello & world <div></div>');

        self::assertStringContainsString(
            '<p><span class="greentext">&gt;hello &amp; world &lt;div&gt;&lt;/div&gt;</span></p>',
            $html
        );
    }

    public function testGreentextInline(): void
    {
        $html = (new Pastedown())->text('inline is not greentext >hello');

        self::assertStringContainsString(
            '<p>inline is not greentext &gt;hello</p>',
            $html
        );
    }

    public function testRedtextInline(): void
    {
        $html = (new Pastedown())->text('inline not redtext <warning');

        self::assertStringContainsString(
            '<p>inline not redtext &lt;warning</p>',
            $html
        );
    }

    public function testPurpletextInline(): void
    {
        $html = (new Pastedown())->text('inline is not purpletext @note');

        self::assertStringContainsString(
            '<p>inline is not purpletext @note</p>',
            $html
        );
    }

    public function testComplex1(): void
    {
        $html = (new Pastedown())->text('## Green, Red, and Purple Text
Greentext is started exactly like you would expect it to be.  
>Just like so.  
Redtext is started with a backwards angle bracket.  
<Red text example  
And finally, purpletext is initiated with an at symbol.  
@What uses you put this colour to is entirely up to you.  

**Be warned:** coloured text is currently bugged, causing the stuttered newlines you see above every time normal text ends and coloured text starts.');

        # strip away newlines and whitespace for easier comparison
        $html = preg_replace('/\s+/', '', $html);

        $expected = '
<h2>Green, Red, and Purple Text</h2>
<p>Greentext is started exactly like you would expect it to be.<br/>
<span class="greentext">&gt;Just like so.</span><br/>
Redtext is started with a backwards angle bracket.<br/>
<span class="redtext">&lt;Red text example</span><br/>
And finally, purpletext is initiated with an at symbol.<br/>
<span class="purpletext">&#64;What uses you put this colour to is entirely up to you.</span></p>
<p><strong>Be warned:</strong> coloured text is currently bugged, causing the stuttered newlines you see above every time normal text ends and coloured text starts.</p>';

        $expected = preg_replace('/\s+/', '', $expected);

        self::assertStringContainsString(
            $expected,
            $html
        );
    }

    public function testComplex2(): void
    {
        $html = (new Pastedown())->text('>testing text colour  
<testing text colour  
@testing text colour  
><testing text colour  
>@testing text colour  
<>testing text colour  
<@testing text colour  
@>testing text colour  
@<testing text colour
');

        # strip away newlines and whitespace for easier comparison
        $html = preg_replace('/\s+/', '', $html);

        $expected = '
<p>
<span class="greentext">&gt;testing text colour</span><br/>
<span class="redtext">&lt;testing text colour</span><br/>
<span class="purpletext">&#64;testing text colour</span><br/>
<span class="greentext">&gt;&lt;testing text colour</span><br/>
<span class="greentext">&gt;&#64;testing text colour</span><br/>
<span class="redtext">&lt;&gt;testing text colour</span><br/>
<span class="redtext">&lt;&#64;testing text colour</span><br/>
<span class="purpletext">&#64;&gt;testing text colour</span><br/>
<span class="purpletext">&#64;&lt;testing text colour</span>
</p>';
        $expected = preg_replace('/\s+/', '', $expected);

        self::assertStringContainsString(
            $expected,
            $html
        );
    }
}