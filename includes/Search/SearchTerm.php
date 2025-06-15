<?php
namespace PonePaste\Search;

class SearchTerm {
    const FUZZ_OR_BOOST_PATTERN = '/(?:~(?<fuzz>\d+(?:\.\d+)?|\.\d+))|(?:\^(?<boost>[\-+]?\d+(?:\.\d+)?))$/';

    public string $term;
    public array $float_fields;
    public array $literal_fields;
    public array $int_fields;
    public $ngram_fields;
    public $boost;
    public $fuzz;
    public $wildcarded;
    public $ngram_query;
    /**
     * @var array|mixed
     */
    private array $field_aliases;
    private array $field_transforms;
    private array $no_downcase;
    private string $default_field;
    /**
     * @var array|mixed
     */
    private array $date_fields;
    /**
     * @var array|mixed
     */
    private array $boolean_fields;

    public function __construct($term, $default_field, $options = []) {
        $this->term = $term;
        $allowed_fields = $options['allowed_fields'] ?? [];
        $this->literal_fields = $allowed_fields['literal'] ?? [];
        $this->boolean_fields = $allowed_fields['boolean'] ?? [];
        $this->ngram_fields = $allowed_fields['full_text'] ?? [];
        $this->date_fields = $allowed_fields['date'] ?? [];
        $this->float_fields = $allowed_fields['float'] ?? [];
        $this->int_fields = $allowed_fields['integer'] ?? [];
        $this->fuzz = $options['fuzz'] ?? null;
        $this->boost = $options['boost'] ?? null;
        $this->field_aliases = $options['aliases'] ?? [];
        $this->field_transforms = $options['transforms'] ?? [];
        $this->no_downcase = $options['no_downcase'] ?? [];
        $this->default_field = $default_field;
        $this->ngram_query = false;
        $this->wildcarded = false;
    }

    public function append($str) {
        $this->term .= $str;
    }

    public function prepend($str) {
        $this->term = $str . $this->term;
    }

    public function normalize_field_name($field_name) {
        return array_key_exists($field_name, $this->field_aliases)
            ? $this->field_aliases[$field_name]
            : $field_name;
    }

    public function normalize_val($field_name, $val, $range = null) {
        if (in_array($field_name, $this->int_fields)) {
            try {
                $val = intval($val);

                if ($this->fuzz !== null && $range === null) {
                    $val = ['gte' => $val - $this->fuzz, 'lte' => $val + $this->fuzz];
                }
            } catch (Exception $e) {
                throw new SearchParsingError("Values of \"$field_name\" field must be decimal integers; \"$val\" is invalid.");
            }
        } elseif (in_array($field_name, $this->boolean_fields)) {
            if (!in_array($val, ['true', 'false'])) {
                throw new SearchParsingError("Values of \"$field_name\" must be \"true\" or \"false\"; \"$val\" is invalid.");
            }
        } elseif (in_array($field_name, $this->date_fields)) {
            if (empty($val)) {
                throw new SearchParsingError("Field \"$field_name\" missing date/time value.");
            }

            [$higher, $lower] = RelativeDateParser::parse($val) ?? NillableDateTime::parse($val)->range;

            switch ($range) {
                case 'lt':
                case 'gte':
                    return [$range => $lower];
                case 'lte':
                    return ['lt' => $higher];
                case 'gt':
                    return ['gte' => $higher];
                default:
                    return ['gte' => $lower, 'lt' => $higher];
            }
        } elseif (in_array($field_name, $this->float_fields)) {
            try {
                $val = floatval($val);

                if ($this->fuzz !== null && $range === null) {
                    $val = ['gte' => $val - $this->fuzz, 'lte' => $val + $this->fuzz];
                }
            } catch (Exception $e) {
                throw new SearchParsingError("Values of \"$field_name\" field must be decimals.");
            }
        } elseif (!in_array($field_name, $this->no_downcase)) {
            $val = strtolower($val);
        }

        if (in_array($range, ['lt', 'gt', 'gte', 'lte'])) {
            return [$range => $val];
        } else {
            return $val;
        }
    }

    private function _escape_colons() {
        if (preg_match('/^(.*?[^\\\\]):(.*)$/', $this->term, $matches)) {
            $field = $matches[1];
            $val = $matches[2];
            $field = strtolower($field);

            if (preg_match('/(.*)\.([gl]te?|eq)$/', $field, $submatches)) {
                $range_field = $submatches[1];
                if (in_array($range_field, $this->date_fields) || in_array($range_field, $this->int_fields) || in_array($range_field, $this->float_fields)) {
                    return [$this->normalize_field_name($range_field), $this->normalize_val($range_field, $val, $submatches[2])];
                }
            }

            $field = $field;

            if (in_array($field, $this->ngram_fields)) {
                $this->ngram_query = true;
            } elseif (!in_array($field, array_merge($this->date_fields, $this->int_fields, $this->float_fields, $this->literal_fields, $this->boolean_fields))) {
                $this->ngram_query = in_array($this->default_field, $this->ngram_fields);
                return [$this->normalize_field_name($this->default_field), $this->normalize_val($this->default_field, "$field:$val")];
            }

            return [$this->normalize_field_name($field), $this->normalize_val($field, $val)];
        }
    }

    public function parse() {
        $this->setup_fuzz_and_boost();

        echo "term: " . $this->term . "\n";

        $wildcardable = !preg_match('/^"([^"]|\\\\")+"$/', $this->term);
        if (!$wildcardable) {
            $this->term = substr($this->term, 1, strlen($this->term) - 2);
        }

        echo "after wildcardable: " . $this->term . "\n";

        $field = null;
        $value = null;
        [$field, $value] = $this->_escape_colons() ?? [$this->default_field, $this->normalize_val($this->default_field, $this->term)];

        if (isset($this->field_transforms[$field])) {
            $value = $this->field_transforms[$field]($value);
        }

        $extra = [];

        if ($this->boost !== null) {
            $extra['boost'] = floatval($this->boost);
        }

        if (is_array($value)) {
            return ['range' => [$field => array_merge($value, $extra)]];
        } elseif ($this->fuzz !== null) {
            $this->normalize_term($value, !$wildcardable);
            return ['fuzzy' => [$field => ['value' => $value, 'fuzziness' => $this->fuzz] + $extra]];
        } elseif ($wildcardable && preg_match('/(?:^|[^\\\\])[\*\?]/', $value)) {
            $value = preg_replace('/\\\\([^\*\?])/', '\1', $value);

            $this->wildcarded = true;
            $this->ngram_query = false;

            if ($value === '*') {
                return ['match_all' => []];
            }

            if (empty($extra)) {
                return ['wildcard' => [$field => $value]];
            } else {
                return ['wildcard' => [$field => ['value' => $value] + $extra]];
            }
        } elseif ($this->ngram_query) {
            if (empty($extra)) {
                return ['match_phrase' => [$field => $value]];
            } else {
                return ['match_phrase' => [$field => ['value' => $value] + $extra]];
            }
        } else {
            $this->normalize_term($value, !$wildcardable);
            if (empty($extra)) {
                return ['term' => [$field => $value]];
            } else {
                return ['term' => [$field => ['value' => $value] + $extra]];
            }
        }
    }

    public function normalize_term(&$match, $quoted) {
        if ($quoted) {
            $match = str_replace('\"', '"', $match);
        } else {
            $match = preg_replace('/\\\\(.)/', '\1', $match);
        }
    }

    public function __toString() {
        return $this->term;
    }

    private function setup_fuzz_and_boost() {
        do {
            $matched = false;
            $this->term = preg_replace_callback(self::FUZZ_OR_BOOST_PATTERN, function ($matches) use (&$matched) {
                $matched = true;
                $captures = $matches['captures'];

                if (isset($captures['fuzz'])) {
                    $this->fuzz = floatval($captures['fuzz']);
                }

                if (isset($captures['boost'])) {
                    $this->boost = floatval($captures['boost']);
                }

                return '';
            }, $this->term);
        } while ($matched);
    }
}
