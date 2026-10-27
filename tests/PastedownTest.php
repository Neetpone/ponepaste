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

    public function testFootnotesRenderInSafeMode(): void
    {
        $parser = new Pastedown();
        $parser->setSafeMode(true);
        $html = $parser->text(<<<'MARKDOWN'
Here's a simple footnote,[^1] and here's a longer one.[^bignote]

[^1]: This is the first footnote.

[^bignote]: Here's one with multiple paragraphs and code.

    Indent paragraphs to include them in the footnote.

    `{ my code }`

    Add as many paragraphs as you like.
MARKDOWN);

        self::assertStringContainsString('<a href="#fn%3A1" class="footnote-ref">1</a>', $html);
        self::assertStringContainsString('<div class="footnotes">', $html);
        self::assertStringContainsString('<p>1. This is the first footnote.', $html);
        self::assertStringContainsString('<p>2. Here\'s one with multiple paragraphs and code.', $html);
        self::assertStringContainsString(
            '<a href="#fnref1:1" rev="footnote" class="footnote-backref">',
            $html
        );
        self::assertStringContainsString('<code>{ my code }</code>', $html);
        self::assertStringNotContainsString('&lt;a href="#fn%3A1"', $html);
    }

    public function testFootnoteRawHtmlRemainsEscapedInSafeMode(): void
    {
        $parser = new Pastedown();
        $parser->setSafeMode(true);
        $html = $parser->text('[^1]' . "\n\n" . '[^1]: <script>alert(1)</script>');

        self::assertStringNotContainsString('<script>', $html);
        self::assertStringContainsString('&lt;script&gt;alert(1)&lt;/script&gt;', $html);
    }

    public function testHeaderIdsMatchMarkdownLinkAnchors(): void
    {
        $html = (new Pastedown())->text(<<<'MARKDOWN'
[1. Header One](#1-header-one)
[2.2. Header Two](#22-header-two)

# 1. Header One

Some text

# 2.2. Header Two

Some other text
MARKDOWN);

        self::assertStringContainsString('<h1 id="1-header-one">1. Header One</h1>', $html);
        self::assertStringContainsString('<h1 id="22-header-two">2.2. Header Two</h1>', $html);
        self::assertStringContainsString('href="#1-header-one"', $html);
        self::assertStringContainsString('href="#22-header-two"', $html);
    }

    public function testHeaderIdsAreUniqueAndRespectExplicitIds(): void
    {
        $html = (new Pastedown())->text(<<<'MARKDOWN'
# Repeated Heading

# Repeated Heading

Setext Heading
---

# Explicit Heading {#custom-heading}
MARKDOWN);

        self::assertStringContainsString('<h1 id="repeated-heading">Repeated Heading</h1>', $html);
        self::assertStringContainsString('<h1 id="repeated-heading-1">Repeated Heading</h1>', $html);
        self::assertStringContainsString('<h2 id="setext-heading">Setext Heading</h2>', $html);
        self::assertStringContainsString('<h1 id="custom-heading">Explicit Heading</h1>', $html);
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