<?php
namespace PonePaste\Search;

class SearchParser {
    private string $search_str;
    private bool $requires_query;
    private mixed $default_field;
    private array|null $allowed_fields;
    /**
     * @var array|mixed
     */
    private mixed $field_aliases;
    /**
     * @var array|mixed
     */
    private mixed $field_transforms;
    private array|null $no_downcase;
    private array $parsed;
    /**
     * @var array|SearchTerm[]
     */
    private ?array $tokens = null;

    public function __construct($search_str, $default_field, $options = []) {
        $this->search_str = trim($search_str);
        $this->requires_query = false;
        $this->default_field = $default_field;
        $this->allowed_fields = $options['allowed_fields'] ?? null;
        $this->field_aliases = $options['field_aliases'] ?? [];
        $this->field_transforms = $options['field_transforms'] ?? [];
        $this->no_downcase = $options['no_downcase'] ?? [];
        $this->parsed = $this->_parse();
    }

    private function _bool_to_es_op($operator) {
        return $operator === 'and_op'
            ? 'must'
            : 'should';
    }

    private function _flatten_operands($ops, $operator, $negate_result) {
        $bool_op_type = $this->_bool_to_es_op($operator);
        $boolses = [];

        foreach ($ops as [$type, $negate, $op]) {
            if ($type === 'term' && $negate) {
                $op = ['bool' => ['must_not' => [$op]]];
            }

            $bool_exp = isset($op['bool']) ? $op['bool'] : null;

            if ($bool_exp && count($bool_exp) === 1 && array_key_exists($bool_op_type, $bool_exp)) {
                $boolses = array_merge($boolses, $bool_exp[$bool_op_type]);
            } elseif ($bool_exp === null || !empty($bool_exp)) {
                $boolses[] = $op;
            }
        }

        if (empty($boolses)) {
            throw new Exception('What?');
        }

        $query = ['bool' => [$bool_op_type => $boolses]];

        if ($negate_result) {
            if ($bool_op_type === 'must_not') {
                return ['subexp', false, ['bool' => ['must' => $boolses]]];
            }

            return ['subexp', false, ['bool' => ['must_not' => [['bool' => $query]]]]];
        }

        return ['subexp', false, ['bool' => $query]];
    }

    private function _parse() : array {
        $operand_stack = [];
        $tokens = $this->tokens();

        foreach ($tokens as $idx => $token) {
            $is_token_op = $token instanceof SearchToken;
            if ($is_token_op && $token->type === 'not_op') {
                continue;
            }

            $negate = ($idx < count($tokens) - 1) && ($tokens[$idx + 1] instanceof SearchToken && $tokens[$idx + 1]->type === 'not_op');

            if ($token instanceof SearchTerm) {
                $parsed = $token->parse();
                var_dump($parsed);
                if ($token->wildcarded || $token->fuzz || $token->boost || $token->ngram_query) {
                    $this->requires_query = true;
                }
                $operand_stack[] = ['term', $negate, $parsed];
            } else {
                $op_2 = array_pop($operand_stack);
                $op_1 = array_pop($operand_stack);

                if ($op_1 === null || $op_2 === null) {
                    throw new SearchParsingError('Missing operand.');
                }

                $operand_stack[] = $this->_flatten_operands([$op_1, $op_2], $token, $negate);
            }
        }

        if (count($operand_stack) > 1) {
            throw new SearchParsingError('Missing operator.');
        }

        $op = array_pop($operand_stack);

        if ($op === null) {
            return [];
        }

        $negate = $op[1];
        $exp = $op[2];

        return $negate ? ['bool' => ['must_not' => [$exp]]] : $exp;
    }

    public function parsed() {
        return !empty($this->parsed) ? $this->parsed : ['match_none' => []];
    }

    public function tokens() {
        if ($this->tokens === null) {
            $tokenList = SearchLexer::lex($this->search_str);
            $this->tokens = array_map(function ($t) {
                return is_string($t) ? $this->new_search_term($t) : $t;
            }, $tokenList);
        }

        return $this->tokens;
    }

    public function new_search_term($term_str) {
        return new SearchTerm(
            $term_str,
            $this->default_field,
            [
                'allowed_fields' => $this->allowed_fields,
                'aliases' => $this->field_aliases,
                'transforms' => $this->field_transforms,
                'no_downcase' => $this->no_downcase
            ]
        );
    }
}