<?php

namespace PonePaste\Search;

use PonePaste\Helpers\SearchParsingError;

class SearchLexer {
    public static function lex($search_str) {
        $TOKEN_LIST = [
            ['quoted_lit', '/^\s*"(?:(?:[^"]|\\")+)"/'],
            ['lparen',     '/^\(/'],
            ['rparen',     '/^\)/'],
            ['and_op',     '/^(?:&&|AND|,)/'],
            ['or_op',      '/^(?:\|\||OR)/'],
            ['not_op',     '/^NOT(?:\s+|(?>\())/'],
            ['not_op',     '/^[!\-]/'],
            ['space',      '/^\s+/'],
            ['word',       '/^(?:[^\s,()]|\\[\s,()])+/']
        ];

        $ops = [];
        $search_term = null;
        $lparen_in_term = 0;
        $negate = false;
        $group_negate = [];
        $token_stack = [];

        while (!empty($search_str)) {
            $match = null;
            $symbol = null;

            foreach ($TOKEN_LIST as $token) {
                [$sym, $regexp] = $token;
                $matches = [];
                if (preg_match($regexp, $search_str, $matches)) {
                    $symbol = $sym;
                    $match = $matches[0];
                    break;
                }
            }

            if (!$match) {
                throw new SearchParsingError('Failed to match a token');
            }

            if (in_array($symbol, ['and_op', 'or_op']) || ($symbol === 'rparen' && $lparen_in_term === 0)) {
                if ($search_term) {
                    $token_stack[] = trim($search_term);
                    $search_term = null;
                    $lparen_in_term = 0;
                    if ($negate) {
                        $token_stack[] = 'not_op';
                        $negate = false;
                    }
                }
            }

            switch ($symbol) {
                case 'and_op':
                    while (!empty($ops) && $ops[0] === 'and_op') {
                        $token_stack[] = array_shift($ops);
                    }
                    array_unshift($ops, 'and_op');
                    break;
                case 'or_op':
                    while (!empty($ops) && in_array($ops[0], ['and_op', 'or_op'])) {
                        $token_stack[] = array_shift($ops);
                    }
                    array_unshift($ops, 'or_op');
                    break;
                case 'not_op':
                    if ($search_term) {
                        $search_term .= $match;
                    } else {
                        $negate = !$negate;
                    }
                    break;
                case 'lparen':
                    if ($search_term) {
                        $search_term .= $match;
                        $lparen_in_term++;
                    } else {
                        array_unshift($ops, 'lparen');
                        $group_negate[] = $negate;
                        $negate = false;
                    }
                    break;
                case 'rparen':
                    if ($lparen_in_term !== 0) {
                        $search_term .= $match;
                        $lparen_in_term--;
                    } else {
                        $balanced = false;
                        while (!empty($ops)) {
                            $op = array_shift($ops);
                            if ($op === 'lparen') {
                                $balanced = true;
                                break;
                            }
                            $token_stack[] = $op;
                        }
                        if (!$balanced) {
                            throw new SearchParsingError('Imbalanced parentheses.');
                        }
                        if (array_pop($group_negate)) {
                            $token_stack[] = 'not_op';
                        }
                    }
                    break;
                case 'word':
                case 'quoted_lit':
                    if ($search_term) {
                        $search_term .= $match;
                    } else {
                        $search_term = $match;
                    }
                    break;
                default:
                    if ($search_term) {
                        $search_term .= $match;
                    }
                    break;
            }

            $search_str = substr($search_str, strlen($match));
        }

        if ($search_term) {
            $token_stack[] = trim($search_term);
        }

        if ($negate) {
            $token_stack[] = 'not_op';
        }

        if (in_array('lparen', $ops) || in_array('rparen', $ops)) {
            throw new SearchParsingError('Imbalanced parentheses.');
        }

        $token_stack = array_merge($token_stack, $ops);

        return $token_stack;
    }
}