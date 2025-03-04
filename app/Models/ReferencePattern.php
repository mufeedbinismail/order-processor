<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class ReferencePattern extends Model
{
    use HasFactory;

    protected $guarded = [];

    const YEAR_TWO_DIGITS = '{YY}';
    const YEAR_FOUR_DIGITS = '{YYYY}';
    const MONTH_TWO_DIGITS = '{MM}';
    const MONTH_THREE_LETTERS = '{MMM}';
    const MONTH_FULL_NAME = '{MMMM}';
    const DAY_TWO_DIGITS = '{DD}';
    const DAY_THREE_LETTERS = '{DDD}';
    const SEQUENCE = '{SEQ:[0-9]}';

    /**
     * Returns the pattern for sequence of given digits
     *
     * @param int $digits
     * @return string
     */
    public static function sequenceOf(int $digits)
    {
        return strtr(self::SEQUENCE, ['[0-9]' => $digits]);
    }

    /**
     * Returns the pattern string from the given components
     *
     * @param array $components
     * @param string $separator
     * @return string
     */
    public static function getPatternString($components, $separator = '/')
    {
        return implode($separator, $components);
    }

    /**
     * Returns the list of all available placeholders
     *
     * @return array
     */
    public static function getPlaceHolders()
    {
        return [
            self::YEAR_TWO_DIGITS => 'date',
            self::YEAR_FOUR_DIGITS => 'date',
            self::MONTH_TWO_DIGITS => 'date',
            self::MONTH_THREE_LETTERS => 'date',
            self::MONTH_FULL_NAME => 'date',
            self::DAY_TWO_DIGITS => 'date',
            self::DAY_THREE_LETTERS => 'date',
            self::SEQUENCE => 'sequence',
        ];
    }

    /**
     * Check if the placeholder is a numeric sequence
     *
     * @param string $placeholder
     * @return bool
     */
    public static function isNumericPlaceholder($placeholder)
    {
        return preg_match('/^\{SEQ:[0-9]\}$/', $placeholder);
    }

    /**
     * Get the number of digits from the sequence placeholder
     *
     * @param string $sequence
     * @return int
     * @throws \InvalidArgumentException
     */
    public static function getDigitsFromSequence($sequence)
    {
        if (!preg_match('/^\{SEQ:([0-9])\}$/', $sequence, $match)) {
            throw new \InvalidArgumentException("Invalid sequence placeholder given.");
        }

        return $match[1];
    }

    /**
     * parse the template pattern and return the constituent parts
     *
     * @param string $pattern
     * @param string|array $context if its a plain string assumed as date for ease of use
     * @return string[] array of 4 elements [template, prefix, numeric sequence, postfix]
     * @throws \UnexpectedValueException
     * @throws \InvalidArgumentException
     * @throws \LogicException
     */
    public static function parsePattern($pattern, $context = null)
    {
        $placeHolders = ReferencePattern::getPlaceHolders();

		if (!isset($context))
            $context = date('Y-m-d');

        if (is_string($context))
            $context = array('date' => $context);

        $processed = '';
        while(($start = strpos($pattern, '{')) !== false) {
            $processed .= substr($pattern, 0, $start);
            
            if (($stop = strpos($pattern, '}')) === false) {
                throw new \UnexpectedValueException("Malformed placeholder in refline definition.");
            }

            $placeholder = substr($pattern, $start + 1, $stop - $start - 1);
            $pattern = substr($pattern, $stop + 1);

            if (isset($placeHolders[$placeholder])) {
                if (!isset($context[$placeHolders[$placeholder]])) {
                    throw new \InvalidArgumentException("Expected key %s in the context array. None given.", $placeHolders[$placeholder]);
                }

                switch ($placeholder) {
                    case self::YEAR_TWO_DIGITS:
                    case self::YEAR_FOUR_DIGITS:
                    case self::MONTH_TWO_DIGITS:
                    case self::MONTH_THREE_LETTERS:
                    case self::MONTH_FULL_NAME:
                    case self::DAY_TWO_DIGITS:
                    case self::DAY_THREE_LETTERS:
                        $date = Carbon::parse($context[$placeHolders[$placeholder]]);
                        $dateFormat = [
                            self::YEAR_TWO_DIGITS => 'y',
                            self::YEAR_FOUR_DIGITS => 'Y',
                            self::MONTH_TWO_DIGITS => 'm',
                            self::MONTH_THREE_LETTERS => 'M',
                            self::MONTH_FULL_NAME => 'F',
                            self::DAY_TWO_DIGITS => 'd',
                            self::DAY_THREE_LETTERS => 'D',
                        ];
                        $processed .= $date->format($dateFormat[$placeholder]);
                        break;

                }
            }
            
            elseif (static::isNumericPlaceholder('{'.$placeholder.'}')) {
                $processed .= '{'.$placeholder.'}';
            }
        }

        $processed .= $pattern;

        // By this time all the place holders have been replaced and only the numeric sequence should be left
        // If in the event the admin forgot to set the numeric sequence, we should throw an error
        if (!preg_match('/^([^\{]*)?\{([^\}]*)\}(.*)/', $processed, $match)) {
            throw new \LogicException("Missing numeric placeholder in refline definition.");
        }

        return $match;
    }
}
