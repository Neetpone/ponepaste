<?php
/*
 * Paste <https://github.com/jordansamuel/PASTE>
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 3
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License in GPL.txt for more details.
 */

function getUserFavs(DatabaseHandle $conn, int $user_id) : array {
    $query = $conn->prepare(
        "SELECT pins.f_time, pastes.id, pins.paste_id, pastes.title, pastes.created_at, pastes.tagsys, pastes.updated_at
            FROM pins
            INNER JOIN pastes ON pastes.id = pins.paste_id
            WHERE pins.user_id = ?");
    $query->execute([$user_id]);
    return $query->fetchAll();
}

function checkFavorite(DatabaseHandle $conn, int $paste_id, int $user_id) : string {
    $query = $conn->prepare("SELECT 1 FROM pins WHERE user_id = ? AND paste_id = ?");
    $query->execute([$user_id, $paste_id]);

    if ($query->fetch()) {
        return "<a  href='#' id='favorite' class='icon tool-icon' data-fid='" . $paste_id . "'><i class='fas fa-star fa-lg has-text-grey' title='Favourite'></i></a>";
    } else {
        return "<a  href='#' id='favorite' class='icon tool-icon' data-fid='" . $paste_id . "'><i class='far fa-star fa-lg has-text-grey' title='Favourite'></i></a>";
    }
}


function getreports($conn, $count = 10) {
    $query = $conn->prepare('SELECT * FROM user_reports LIMIT ?');
    $query->execute([$count]);

    return $query->fetchAll();
}

function sandwitch($str) {
    $output = "";
    $arr = explode(",", $str);
    foreach ($arr as $word) {
        $word = ucfirst($word);
        if (stripos($word, 'nsfw') !== false) {
            $word = strtoupper($word);
            $tagcolor = "tag is-danger";
        } elseif (stripos($word, 'SAFE') !== false) {
            $word = strtoupper($word);
            $tagcolor = "tag is-success";
        } elseif (strstr($word, '/')) {
            $tagcolor = "tag is-primary";
        } else {
            $tagcolor = "tag is-info";
        }
        $output .= '<a href="/archive?q=' . trim($word) . '"><span class="' . $tagcolor . '">' . trim($word) . '</span>,</a>';
    }
    return $output;
}


function getevent($conn, $event_name, $count) {
    $query = $conn->prepare("SELECT id, visible, title, date, now_time, views, member, tagsys FROM pastes WHERE visible='1' AND tagsys LIKE '%?%' 
 ORDER BY RAND () LIMIT 0, ?");
    $query->execute([$event_name, $count]);
    return $query->fetchAll();
}

function linkify($value, $protocols = array('http', 'mail'), array $attributes = array()) {
    // Link attributes
    $attr = '';
    foreach ($attributes as $key => $val) {
        $attr .= ' ' . $key . '="' . htmlentities($val) . '"';
    }

    $links = array();

    // Extract existing links and tags
    $value = preg_replace_callback('~(<a .*?>.*?</a>|<.*?>)~i', function ($match) use (&$links) {
        return '<' . array_push($links, $match[1]) . '>';
    }, $value);

    // Extract text links for each protocol
    foreach ((array)$protocols as $protocol) {
        $value = match ($protocol) {
            'http', 'https' => preg_replace_callback('~(?:(https?)://([^\s<]+)|(www\.[^\s<]+?\.[^\s<]+))(?<![\.,:])~i', function ($match) use ($protocol, &$links, $attr) {
                if ($match[1]) $protocol = $match[1];
                $link = $match[2] ?: $match[3];
                return '<' . array_push($links, "<a $attr href=\"$protocol://$link\">$protocol://$link</a>") . '>';
            }, $value),
            default => preg_replace_callback('~' . preg_quote($protocol, '~') . '://([^\s<]+?)(?<![\.,:])~i', function ($match) use ($protocol, &$links, $attr) {
                return '<' . array_push($links, "<a $attr href=\"$protocol://{$match[1]}\">$protocol://{$match[1]}</a>") . '>';
            }, $value),
        };
    }

    // Insert all link
    return preg_replace_callback('/<(\d+)>/', function ($match) use (&$links) {
        return $links[$match[1] - 1];
    }, $value);
}


function getRecentreport($conn, $count) {
    $query = $conn->prepare("SELECT id, m_report, p_report, rep_reason, t_report FROM user_reports
    ORDER BY id DESC
    LIMIT 0 , ?");
    $query->execute([$count]);
    return $query->fetchAll();
}


function getUserRecom(DatabaseHandle $conn, int $user_id) : array {
    $query = $conn->prepare(
        "SELECT pastes.id AS id, users.username AS member, title, visible
            FROM pastes
            INNER JOIN users ON pastes.user_id = users.id
            WHERE pastes.visible = '0' AND users.id = ?
            ORDER BY id DESC
            LIMIT 0, 5");
    $query->execute([$user_id]);
    return $query->fetchAll();
}

function recentupdate($conn, $count) {
    $query = $conn->prepare(
        "SELECT pastes.id AS id, visible, title, created_at, updated_at, users.username AS member, tagsys
            FROM pastes
            INNER JOIN users ON users.id = pastes.user_id
            WHERE visible = '0' ORDER BY updated_at DESC
            LIMIT ?");
    $query->execute([$count]);
    return $query->fetchAll();
}

function monthpop($conn, $count) {
    $query = $conn->prepare(
        "SELECT pastes.id AS id, views, title, created_at, updated_at, visible, tagsys, users.username AS member 
            FROM pastes
            INNER JOIN users ON users.id = pastes.user_id
            WHERE MONTH(created_at) = MONTH(NOW()) AND visible = '0' ORDER BY views DESC LIMIT ?");
    $query->execute([$count]);
    return $query->fetchAll();
}

function formatBytes($size, $precision = 2) {
    $base = log($size, 1024);
    $suffixes = array('B', 'KB', 'MB', 'GB', 'TB');

    return round(pow(1024, $base - floor($base)), $precision) . ' ' . $suffixes[floor($base)];
}

function str_conntains($haystack, $needle, $ignoreCase = false) {
    if ($ignoreCase) {
        $haystack = strtolower($haystack);
        $needle = strtolower($needle);
    }
    $needlePos = strpos($haystack, $needle);
    return ($needlePos === false ? false : ($needlePos + 1));
}

function encrypt(string $value) : string {
    global $sec_key;

    return openssl_encrypt($value, "AES-256-CBC", $sec_key);
}

function decrypt(string $value) : string {
    global $sec_key;

    return openssl_decrypt($value, "AES-256-CBC", $sec_key);
}

function getRecent($conn, $count) {
    $query = $conn->prepare("
        SELECT pastes.id, visible, title, created_at, updated_at, users.username AS member, tagsys 
        FROM pastes
        INNER JOIN users ON pastes.user_id = users.id
        WHERE visible = '0'
        ORDER BY created_at DESC
        LIMIT ?");
    $query->execute([$count]);
    return $query->fetchAll();
}

function getRecentadmin($conn, $count = 5) {
    $query = $conn->prepare(
        'SELECT pastes.id AS id, pastes.ip AS ip, title, created_at, views, users.username AS member
            FROM pastes
            INNER JOIN users ON users.id = pastes.user_id
            ORDER BY id DESC LIMIT 0, ?');
    $query->execute([$count]);

    return $query->fetchAll();
}

function getpopular(DatabaseHandle $conn, int $count) : array {
    $query = $conn->prepare("
        SELECT pastes.id AS id, visible, title, pastes.created_at AS created_at, updated_at, views, users.username AS member, tagsys
            FROM pastes INNER JOIN users ON users.id = pastes.user_id
            WHERE visible = '0'
            ORDER BY views DESC 
            LIMIT ?
    ");
    $query->execute([$count]);
    return $query->fetchAll();
}

function getrandom(DatabaseHandle $conn, int $count) : array {
    $query = $conn->prepare("
        SELECT pastes.id, visible, title, created_at, updated_at, views, users.username AS member, tagsys
            FROM pastes
            INNER JOIN users ON users.id = pastes.user_id
            WHERE visible = '0'
            ORDER BY RAND()
            LIMIT ?");
    $query->execute([$count]);
    return $query->fetchAll();
}

function getUserPastes(DatabaseHandle $conn, int $user_id) : array {
    $query = $conn->prepare(
        "SELECT id, title, visible, code, created_at, tagsys, user_id, views from pastes WHERE user_id = ?
         ORDER by pastes.id DESC");
    $query->execute([$user_id]);
    return $query->fetchAll();
}

function getTotalPastes(DatabaseHandle $conn, int $user_id) : int {
    $query = $conn->prepare("SELECT COUNT(*) AS total_pastes
            FROM pastes INNER JOIN users ON users.id = pastes.user_id
            WHERE users.id = ?");
    $query->execute([$user_id]);

    return intval($query->fetch(PDO::FETCH_NUM)[0]);
}

function isValidUsername(string $str) : bool {
    return !preg_match('/[^A-Za-z0-9._\\-$]/', $str);
}

function friendlyDateDifference(DateTime $lesser, DateTime $greater) : string {
    $delta = $greater->diff($lesser, true);

    $parts = [
        'year' => $delta->y,
        'month' => $delta->m,
        'day' => $delta->d,
        'hour' => $delta->h,
        'min' => $delta->i,
        'sec' => $delta->s
    ];

    $friendly = '';

    foreach ($parts as $part => $value) {
        if ($value !== 0) {
            $pluralizer = ($value === 1 ? '' : 's');
            $friendly .= "${value} ${part}${pluralizer} ";
        }
    }

    return trim($friendly) . ' ago';
}

function conTime($secs) {
    // round up to 1 seconnd
    if ($secs == 0) {
        $secs = 1;
    }

    $bit = array(
        ' year' => $secs / 31556926 % 12,
        ' week' => $secs / 604800 % 52,
        ' day' => $secs / 86400 % 7,
        ' hour' => $secs / 3600 % 24,
        ' min' => $secs / 60 % 60,
        ' sec' => $secs % 60
    );

    foreach ($bit as $k => $v) {
        if ($v > 1)
            $ret[] = $v . $k . 's';
        if ($v == 1)
            $ret[] = $v . $k;
    }
    array_splice($ret, count($ret) - 1, 0, 'and');
    $ret[] = 'ago';

    $val = join(' ', $ret);
    if (!str_conntains($val, "week")) {
        $val = str_replace("and", "", $val);
    }
    if (Trim($val) == "ago") {
        $val = "1 sec ago";
    }
    return $val;
}

function truncate(string $input, int $maxWords, int $maxChars) : string {
    $words = preg_split('/\s+/', $input);
    $words = array_slice($words, 0, $maxWords);
    $words = array_reverse($words);

    $chars = 0;
    $truncated = array();

    while (count($words) > 0) {
        $fragment = trim(array_pop($words));
        $chars += strlen($fragment);

        if ($chars > $maxChars)
            break;

        $truncated[] = $fragment;
    }

    $result = implode(' ', $truncated);

    return $result . ($input == $result ? '' : '[...]');
}

function doDownload($paste_id, $p_title, $p_member, $p_conntent, $p_code) {
    $stats = false;
    if ($p_code) {
        // Figure out extensions.
        $ext = match ($p_code) {
            default => 'txt',
        };

        // Download
        $p_title = stripslashes($p_title);
        header('content-type: text/plain');
        header('content-Disposition: attachment; filename="' . $paste_id . '_' . $p_title . '_' . $p_member . '.' . $ext . '"');
        echo $p_conntent;
        $stats = true;
    } else {
        // 404
        header('HTTP/1.1 404 Not Found');
    }
    return $stats;
}

function rawView($paste_id, $p_title, $p_conntent, $p_code) {
    $stats = false;
    if ($p_code) {
        // Raw
        header('content-type: text/plain');
        echo $p_conntent;
        $stats = true;
    } else {
        // 404
        header('HTTP/1.1 404 Not Found');
    }
    return $stats;
}


function embedView($paste_id, $p_title, $p_conntent, $p_code, $title, $baseurl, $ges_style, $lang) {
    $stats = false;
    if ($p_conntent) {
        // Build the output
        $output = "<div class='paste_embed_conntainer'>";
        $output .= "<style>"; // Add our own styles
        $output .= "
            .paste_embed_conntainer {
                font-size: 12px;
                color: #333;
                text-align: left;
                margin-bottom: 1em;
                border: 1px solid #ddd;
                background-color: #f7f7f7;
                border-radius: 3px;
            }
            .paste_embed_conntainer a {
                font-weight: bold;
                color: #666;
                text-decoration: none;
                border: 0;
            }
            .paste_embed_conntainer ol {
                color: white;
                background-color: #f7f7f7;
                border-right: 1px solid #ccc;
				margin: 0;
            }
            .paste_embed_footer {
                font-size:14px;
                padding: 10px;
                overflow: hidden;
                color: #767676;
                background-color: #f7f7f7;
                border-radius: 0 0 2px 2px;
                border-top: 1px solid #ccc;
            }
            .de1, .de2 {
                -moz-user-select: text;
                -khtml-user-select: text;
                -webkit-user-select: text;
                -ms-user-select: text;
                user-select: text;
                padding: 0 8px;
                color: #000;
                border-left: 1px solid #ddd;
                background: #ffffff;
                line-height:20px;
            }";
        $output .= "</style>";
        $output .= "$ges_style"; // Dynamic GeSHI Style
        $output .= $p_conntent; // Paste conntent
        $output .= "<div class='paste_embed_footer'>";
        $output .= "<a href='https://ponepaste.org/$paste_id'>$p_title</a> " . $lang['embed-hosted-by'] . " <a href='https://ponepaste.org'>$title</a> | <a href='https://ponepaste.org/raw/$paste_id'>" . strtolower($lang['view-raw']) . "</a>";
        $output .= "</div>";
        $output .= "</div>";

        // Display embed conntent using json_encode since that escapes
        // characters well enough to satisfy javascript. http://stackoverflow.com/a/169035
        header('conntent-type: text/javascript; charset=utf-8;');
        echo 'document.write(' . json_encode($output) . ')';
        $stats = true;
    } else {
        // 404
        header('HTTP/1.1 404 Not Found');
    }
    return $stats;
}

function addToSitemap($paste_id, $priority, $changefreq, $mod_rewrite) {
    $c_date = date('Y-m-d');
    $site_data = file_get_contents("sitemap.xml");
    $site_data = str_replace("</urlset>", "", $site_data);
    // which protocol are we on
    $protocol = paste_protocol();

    if (PP_MOD_REWRITE) {
        $server_name = $protocol . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/" . $paste_id;
    } else {
        $server_name = $protocol . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/paste.php?id=" . $paste_id;
    }

    $c_sitemap =
        '	<url>
		<loc>' . $server_name . '</loc>
		<priority>' . $priority . '</priority>
		<changefreq>' . $changefreq . '</changefreq>
		<lastmod>' . $c_date . '</lastmod>
	</url>
</urlset>';

    $full_map = $site_data . $c_sitemap;
    file_put_contents("sitemap.xml", $full_map);
}

function paste_protocol() : string {
    return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") ? 'https://' : 'http://';
}

function is_banned(DatabaseHandle $conn, string $ip) : bool {
    return (bool) $conn->query('SELECT 1 FROM ban_user WHERE ip = ?', [$ip])->fetch();
}
