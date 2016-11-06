<?php
/**
 * Class MessageParserTest
 */
class MessageParserTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var MessageParser
     */
    protected $messageParser;

    public function setUp()
    {
        $this->messageParser = new MessageParser();
    }

    public function testEmptyTextConversion()
    {
        $this->assertEquals("", $this->messageParser->parse(""));
    }

    public function testBoldTextConversion()
    {
        $this->assertEquals("<strong>text</strong>", $this->messageParser->parse("**text**"));
    }

    public function testMultipleBoldTextConversion()
    {
        $this->assertEquals(
            "<strong>text</strong><strong>text2</strong>",
            $this->messageParser->parse("**text****text2**")
        );
    }

    public function testNotMatchingBoldTextConversion()
    {
        $this->assertEquals(
            "*<strong>text</strong>**<strong>text2</strong>",
            $this->messageParser->parse("***text******text2**")
        );
    }

    public function testItalicTextConversion()
    {
        $this->assertEquals("<i>text</i>", $this->messageParser->parse("_text_"));
    }

    public function testPreFormattedTextConversion()
    {
        $this->assertEquals("<pre>text</pre>", $this->messageParser->parse("`text`"));
    }

    //<editor-fold desc="Given test cases">
    /**
     * @group given test case
     */
    public function testBoldExampleTextConversion()
    {
        $this->assertEquals("text1<strong>text2</strong>text3", $this->messageParser->parse("text1**text2**text3"));
    }

    /**
     * @group given test case
     */
    public function testItalicExampleTextConversion()
    {
        $this->assertEquals("text1<i>text2</i>text3", $this->messageParser->parse("text1_text2_text3"));
    }

    /**
     * @group given test case
     */
    public function testPreFormattedExampleTextConversion()
    {
        $this->assertEquals("text1<pre>text2</pre>text3", $this->messageParser->parse("text1`text2`text3"));
    }

    /**
     * @group given test case
     */
    public function testLinkExampleTextConversion()
    {
        $this->assertEquals(
            'text1<a href="text2">text3</a>text4',
            $this->messageParser->parse("text1[text2](text3)text4")
        );
    }

    /**
     * @group given test case
     */
    public function testImageExampleTextConversion()
    {
        $this->assertEquals(
            'text1<img src="text2" alt="text3" />text4',
            $this->messageParser->parse("text1![text2](text3)text4")
        );
    }
}
