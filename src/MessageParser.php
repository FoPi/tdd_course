<?php

/**
 * Class MessageParser
 */
class MessageParser
{
    /**
     * The order is important
     *
     *
     * @var array
     */
    protected $orderedReplacePairs = array(
        "!\[([^\]]+)\]\(([^\)]+)\)" => '<img src="$1" alt="$2" />',
        "\[([^\]]+)\]\(([^\)]+)\)" => '<a href="$1">$2</a>',
        "\*\*([^*]+)\*\*" => "<strong>$1</strong>",
        "_([^_]+)_" => "<i>$1</i>",
        "`([^`]+)`" => "<pre>$1</pre>",
    );

    /**
     * @var string
     */
    protected $patternDelimiter = "/";

    /**
     * @var string
     */
    protected $reservedTokenName = "������myReservedTokenPrefix������";

    /**
     * @param $rawText
     *
     * @return string
     */
    public function parse($rawText)
    {
        $tokenizedText = $this->replacePatternsWithTokens($rawText);

        return $this->replaceTokensWithRealOccurrences($tokenizedText);
    }

    /**
     * @param $rawText
     *
     * @return array
     */
    protected function replacePatternsWithTokens($rawText)
    {
        $counter = 0;
        $tokenizeTextArray = array('text' => $rawText);

        $patterns = array_keys($this->orderedReplacePairs);

        foreach ($patterns as $pattern) {
            $wrapPatternWithDelimiters = $this->wrapPatternWithDelimiters($pattern);
            preg_match_all($wrapPatternWithDelimiters, $tokenizeTextArray['text'], $matches);

            if (count($matches[0])) {
                foreach ($matches[0] as $index => $foundString) {
                    $tokenizeTextArray[$this->reservedTokenName . $counter] = array(
                        'foundString' => $foundString,
                        'pattern' => $wrapPatternWithDelimiters,
                        'replacePattern' => $this->orderedReplacePairs[$pattern]
                    );

                    $tokenizeTextArray['text'] = str_replace(
                        $foundString,
                        $this->reservedTokenName . $counter,
                        $tokenizeTextArray['text']
                    );

                    $counter++;
                }
            }
        }

        return $tokenizeTextArray;
    }

    /**
     * @param $pattern
     *
     * @return string
     */
    protected function wrapPatternWithDelimiters($pattern)
    {
        return $this->patternDelimiter . $pattern . $this->patternDelimiter;
    }

    /**
     * @param $tokenizedArray
     *
     * @return string
     */
    protected function replaceTokensWithRealOccurrences($tokenizedArray)
    {
        $tokenizedText = $tokenizedArray['text'];
        $counter = count($tokenizedArray) - 2;

        for (; $counter >= 0; $counter--) {
            $tokenName = $this->reservedTokenName . $counter;
            $actual = $tokenizedArray[$tokenName];

            $tokenizedText = str_replace(
                $tokenName,
                preg_replace($actual['pattern'], $actual['replacePattern'], $actual['foundString']),
                $tokenizedText
            );
        }

        return $tokenizedText;
    }
}