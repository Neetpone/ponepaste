<?php
/*************************************************************************************
 * example.php
 * --------
 * Author: Benny Baumann (BenBE@geshi.org)
 * Copyright: (c) 2020 Benny Baumann (http://qbnz.com/highlighter/)
 * Release Version: 1.0.8.11-wmf1
 * Date Started: 2020/11/02
 *
 * Example language file for GeSHi.
 *
 * CHANGES
 * -------
 * 2020/11/02 (1.0.8.11-wmf1)
 *  -  First Release
 *
 * TODO (updated 2020/11/02)
 * -------------------------
 * * Complete language file
 *
 *************************************************************************************
 *
 *     This file is part of GeSHi.
 *
 *   GeSHi is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 *   GeSHi is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License
 *   along with GeSHi; if not, write to the Free Software
 *   Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 ************************************************************************************/
$language_data = array(
    'LANG_NAME' => 'Example',
    'COMMENT_SINGLE' => array(
        1 => '>',
        2 => '<',
		3 => '**',
		4 => '###',
		5 => '##',
		6 => '@',
		7 => '++',
		
        ),
    'COMMENT_MULTI' => array(
        '_' => '_'
        ),
    'COMMENT_REGEXP' => array(
        1 => ''
        ),
    'CASE_KEYWORDS' => GESHI_CAPS_NO_CHANGE,
    'QUOTEMARKS' => array(
        1 => '',
        2 => ''
        ),
    'ESCAPE_CHAR' => '',
    'ESCAPE_REGEXP' => array(
        1 => '',
        2 => ''
        ),
    'HARDQUOTE' => array(),
    'HARDESCAPE' => array(),
    'HARDCHAR' => '',
    'NUMBERS' =>
        GESHI_NUMBER_INT_BASIC | GESHI_NUMBER_OCT_PREFIX | GESHI_NUMBER_HEX_PREFIX |
        GESHI_NUMBER_FLT_SCI_ZERO,
    'KEYWORDS' => array(
        1 => array(
            ''
            )
        ),
    'CASE_SENSITIVE' => array(
        1 => false
        ),
    'SYMBOLS' => array(
        0 => array(
            '>'
            )
        ),
    'STYLES' => array(
        'KEYWORDS' => array(
            1 => ''
            ),
        'COMMENTS' => array(
            1 => 'font-style: normal; color: #789922;',
            2 => 'font-weight: normal; color: #991111;',
            3 => 'font-weight: bold; color: #000;',
			4 => 'font-size: 25px; font-weight: bold; color: #000;',
			5 => 'font-size: 35px; font-weight: bold; color: #000;',
			6 => 'color: #440088;',
			7 => 'border: 3px dotted #000;',
            'MULTI' => 'text-decoration: underline;color: #000;'
            ),
        'ESCAPE_CHAR' => array(
            1 => '',
            2 => ''
            ),
        'BRACKETS' => array(),
        'STRINGS' => array(
            1 => '',
            2 => ''
            ),
        'NUMBERS' => array(),
        'METHODS' => array(),
        'SYMBOLS' => array(
            0 => 'font-style: italic; color: #789922;'
            ),
        'REGEXPS' => array(),
        'SCRIPT' => array()
        ),
    'URLS' => array(
        1 => ''
        ),
    'OOLANG' => false,
    'OBJECT_SPLITTERS' => array(),
    'REGEXPS' => array(),
    'STRICT_MODE_APPLIES' => GESHI_MAYBE,
    'SCRIPT_DELIMITERS' => array(),
    'HIGHLIGHT_STRICT_BLOCK' => array(),
    'TAB_WIDTH' => 4
);