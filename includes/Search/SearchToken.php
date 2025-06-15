<?php

namespace PonePaste\Search;

class SearchToken {
    public static $NOT_OP;
    public static $AND_OP;
    public static $OR_OP;
    public static $LPAREN;
    public static $RPAREN;
    public static $QUOTED_LIT;
    public static $WORD;
    public static $SPACE;

    public string $type;

    public function __construct(string $type) {
        $this->type = $type;
    }

    public static function init() {
        if (self::$NOT_OP) {
            return;
        }

        self::$NOT_OP = new SearchToken('not_op');
        self::$AND_OP = new SearchToken('and_op');
        self::$OR_OP = new SearchToken('or_op');
        self::$LPAREN = new SearchToken('lparen');
        self::$RPAREN = new SearchToken('rparen');
        self::$QUOTED_LIT = new SearchToken('quoted_lit');
        self::$WORD = new SearchToken('word');
        self::$SPACE = new SearchToken('space');
    }
}