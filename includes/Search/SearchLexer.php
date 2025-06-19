<?php

namespace PonePaste\Search;

use PonePaste\Helpers\SearchParsingError;

class SearchLexer {
    public static function lex($search_str) {
        SearchToken::init();
        $TOKEN_LIST = [
            [SearchToken::$QUOTED_LIT, '/^\s*"(?:(?:[^"]|\\")+)"/'],
            [SearchToken::$LPAREN,     '/^\(/'],
            [SearchToken::$RPAREN,     '/^\)/'],
            [SearchToken::$AND_OP,     '/^(?:&&|AND|,)/'],
            [SearchToken::$OR_OP,      '/^(?:\|\||OR)/'],
            [SearchToken::$NOT_OP,     '/^NOT(?:\s+|(?>\())/'],
            [SearchToken::$NOT_OP,     '/^[!\-]/'],
            [SearchToken::$SPACE,      '/^\s+/'],
            [SearchToken::$WORD,       '/^(?:[^\s,()]|\\[\s,()])+/']
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

            if (in_array($symbol, [SearchToken::$AND_OP, SearchToken::$OR_OP]) || ($symbol === SearchToken::$RPAREN && $lparen_in_term === 0)) {
                if ($search_term) {
                    $token_stack[] = trim($search_term);
                    $search_term = null;
                    $lparen_in_term = 0;
                    if ($negate) {
                        $token_stack[] = SearchToken::$NOT_OP;
                        $negate = false;
                    }
                }
            }

            switch ($symbol) {
                case SearchToken::$AND_OP:
                    while (!empty($ops) && $ops[0] === SearchToken::$AND_OP) {
                        $token_stack[] = array_shift($ops);
                    }
                    array_unshift($ops, SearchToken::$AND_OP);
                    break;
                case SearchToken::$OR_OP:
                    while (!empty($ops) && in_array($ops[0], [SearchToken::$AND_OP, SearchToken::$OR_OP])) {
                        $token_stack[] = array_shift($ops);
                    }
                    array_unshift($ops, SearchToken::$OR_OP);
                    break;
                case SearchToken::$NOT_OP:
                    if ($search_term) {
                        $search_term .= $match;
                    } else {
                        $negate = !$negate;
                    }
                    break;
                case SearchToken::$LPAREN:
                    if ($search_term) {
                        $search_term .= $match;
                        $lparen_in_term++;
                    } else {
                        array_unshift($ops, SearchToken::$LPAREN);
                        $group_negate[] = $negate;
                        $negate = false;
                    }
                    break;
                case SearchToken::$RPAREN:
                    if ($lparen_in_term !== 0) {
                        $search_term .= $match;
                        $lparen_in_term--;
                    } else {
                        $balanced = false;
                        while (!empty($ops)) {
                            $op = array_shift($ops);
                            if ($op === SearchToken::$LPAREN) {
                                $balanced = true;
                                break;
                            }
                            $token_stack[] = $op;
                        }
                        if (!$balanced) {
                            throw new SearchParsingError('Imbalanced parentheses.');
                        }
                        if (array_pop($group_negate)) {
                            $token_stack[] = SearchToken::$NOT_OP;
                        }
                    }
                    break;
                case SearchToken::$WORD:
                case SearchToken::$QUOTED_LIT:
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
            $token_stack[] = SearchToken::$NOT_OP;
        }

        if (in_array(SearchToken::$LPAREN, $ops) || in_array(SearchToken::$RPAREN, $ops)) {
            throw new SearchParsingError('Imbalanced parentheses.');
        }

        $token_stack = array_merge($token_stack, $ops);

        return $token_stack;
    }
}