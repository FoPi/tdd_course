<?php

/**
 * Created by PhpStorm.
 * User: peterfodor
 * Date: 2016.11.06.
 * Time: 9:34
 */
class MessageParser
{
    protected $tagReplacePairs = array(
        "!\[([^\]]+)\]\(([^\)]+)\)" => '<img src="$1" alt="$2" />',
        "\[([^\]]+)\]\(([^\)]+)\)" => '<a href="$1">$2</a>',
        "\*\*([^*]+)\*\*" => "<strong>$1</strong>",
        "_([^_]+)_" => "<i>$1</i>",
        "`([^`]+)`" => "<pre>$1</pre>",
    );

    protected $patternDelimiter = "/";

    protected $reservedTokenName = "十十十myReservedTokenPrefix十十十";

    /**
     * @param $rawText
     *
     * @return string
     */
    public function parse($rawText)
    {
        $tokenizedText = $this->replacePatternsWithTokens($rawText);

        $convertedText = $this->replaceTokensWithRealOccurrences($tokenizedText);

        return $convertedText;
    }

    protected function replacePatternsWithTokens($rawText)
    {
        $counter = 0;
        $tokenizedTextArray = array('text' => $rawText);

        $patterns = array_keys($this->tagReplacePairs);

        foreach ($patterns as $pattern) {
            $wrapPatternWithDelimiters = $this->wrapPatternWithDelimiters($pattern);
            preg_match_all($wrapPatternWithDelimiters, $tokenizedTextArray['text'], $matches);

            if (count($matches[0])) {
                foreach ($matches[0] as $index => $foundString) {
                    $tokenizedTextArray[$this->reservedTokenName . $counter] = array(
                        'foundString' => $foundString,
                        'pattern' => $wrapPatternWithDelimiters,
                        'replacePattern' => $this->tagReplacePairs[$pattern]
                    );

                    $tokenizedTextArray['text'] = str_replace(
                        $foundString,
                        $this->reservedTokenName . $counter,
                        $tokenizedTextArray['text']
                    );

                    $counter++;
                }
            }
        }

        return $tokenizedTextArray;
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