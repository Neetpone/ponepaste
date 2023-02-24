<?php
use Illuminate\Database\Eloquent\Collection;
use PonePaste\Models\Paste;

function tagsToHtml(array | Collection $tags) : string {
    $output = "";
    foreach ($tags as $tagObj) {
        $tag = $tagObj->name;
        if (stripos($tag, 'nsfw') !== false) {
            $tag = strtoupper($tag);
            $tagcolor = "tag is-danger";
        } elseif (stripos($tag, 'SAFE') !== false) {
            $tag = strtoupper($tag);
            $tagcolor = "tag is-success";
        } elseif (str_contains($tag, '/')) {
            $tagcolor = "tag is-primary";
        } else {
            $tagcolor = "tag is-info";
        }
        $output .= '<a href="/archive?q=' . urlencode($tag) . '"><span class="' . $tagcolor . '">' . pp_html_escape(ucfirst($tag)) . '</span></a>';
    }
    return $output;
}

function tagsToHtmlUser(string | array | Collection $tags, $profile_username) : string {
    $output = "";

    if (is_a($tags, Collection::class)) {
        $tags = $tags->toArray();
    }

    if (is_array($tags)) {
        $tagsSplit = array_map(function($tag) { return $tag['name']; }, $tags);
    } else {
        $tagsSplit = explode(",", $tags);
    }

    if (count($tagsSplit) === 0) {
        return '<span class="tag is-warning">No tags</span>';
    }

    foreach ($tagsSplit as $tag) {
        if (stripos($tag, 'nsfw') !== false) {
            $tag = strtoupper($tag);
            $tagcolor = "tag is-danger";
        } elseif (stripos($tag, 'SAFE') !== false) {
            $tag = strtoupper($tag);
            $tagcolor = "tag is-success";
        } elseif (str_contains($tag, '/')) {
            $tagcolor = "tag is-primary";
        } else {
            $tagcolor = "tag is-info";
        }
        $output .= '<a href="/user.php?user=' . $profile_username . '&q=' . urlencode($tag) . '"><span class="' . $tagcolor . '">' . pp_html_escape(ucfirst($tag)) . '</span></a>';
    }
    return $output;
}

function linkify($value, $protocols = array('http', 'mail'), array $attributes = array()) : array|string|null {
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
    foreach ((array) $protocols as $protocol) {
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

function formatBytes($size, $precision = 2) : string {
    $base = log($size, 1024);
    $suffixes = ['B', 'KB', 'MB', 'GB', 'TB'];

    if ($size === 0) {
        return '0 B';
    }

    return round(pow(1024, $base - floor($base)), $precision) . ' ' . $suffixes[floor($base)];
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
            $friendly .= "{$value} {$part}{$pluralizer} ";
        }
    }

    return trim($friendly) . ' ago';
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

function embedView($paste_id, $p_title, $content, $title) : bool {
    $stats = false;
    if ($content) {
        // Build the output
        $output = "<div class='paste_embed_container'>";
        $output .= "<style>"; // Add our own styles
        $output .= "
            .paste_embed_container {
                font-size: 12px;
                color: #333;
                text-align: left;
                margin-bottom: 1em;
                border: 1px solid #ddd;
                background-color: #f7f7f7;
                border-radius: 3px;
            }
            .paste_embed_container a {
                font-weight: bold;
                color: #666;
                text-decoration: none;
                border: 0;
            }
            .paste_embed_container ol {
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
        $output .= $content; // Paste content
        $output .= "<div class='paste_embed_footer'>";
        $output .= "<a href='https://ponepaste.org/$paste_id'>$p_title</a> Hosted by <a href='https://ponepaste.org'>$title</a> | <a href='https://ponepaste.org/raw/$paste_id'>view raw</a>";
        $output .= "</div>";
        $output .= "</div>";

        // Display embed conntent using json_encode since that escapes
        // characters well enough to satisfy javascript. http://stackoverflow.com/a/169035
        header('Content-Type: text/javascript; charset=utf-8;');
        echo 'document.write(' . json_encode($output) . ')';
        $stats = true;
    } else {
        // 404
        header('HTTP/1.1 404 Not Found');
    }
    return $stats;
}

function addToSitemap(Paste $paste, $priority, $changefreq) : void {
    $c_date = date('Y-m-d');
    $site_data = file_get_contents("sitemap.xml");
    $site_data = str_replace("</urlset>", "", $site_data);

    $c_sitemap =
        '	<url>
		<loc>' . urlForPaste($paste) . '</loc>
		<priority>' . $priority . '</priority>
		<changefreq>' . $changefreq . '</changefreq>
		<lastmod>' . $c_date . '</lastmod>
	</url>
</urlset>';

    $full_map = $site_data . $c_sitemap;
    file_put_contents("sitemap.xml", $full_map);
}

function paste_protocol() : string {
    return !empty($_SERVER['HTTPS']) ? 'https://' : 'http://';
}

/* get rid of unintended wildcards in a parameter to LIKE queries; not a security issue, just unexpected behaviour. */
function escapeLikeQuery(string $query) : string {
    return str_replace(['\\', '_', '%'], ['\\\\', '\\_', '\\%'], $query);
}

function paginate(int $current_page, int $per_page, int $total_records) : string {
    $first_page = 0;
    $last_page = floor($total_records / $per_page);
    $window = 2;

    if ($first_page == $last_page) {
        // Do something?
    }

    $_page_button = function(int $page, string $text, bool $disabled = false) use ($current_page) : string {
        /* We need to update the 'page' parameter in the request URI, or add it if it doesn't exist. */
        $request_uri = parse_url($_SERVER['REQUEST_URI']);
        parse_str((string) @$request_uri['query'], $parsed_query);
        $parsed_query['page'] = (string) $page;
        $page_uri = ((string) @$request_uri['path']) . '?' . http_build_query($parsed_query);

        $selected_class = $current_page == $page ? ' paginator__button--selected' : '';

        $disabled_text = $disabled ? ' aria-disabled="true"' : '';
        return sprintf("<a type=\"button\" class=\"paginator__button$selected_class\" href=\"%s\"%s>%s</a>", $page_uri, $disabled_text, $text);
    };

    $html = '';

    /* First and last page the main paginator will show */
    $first_page_show = max(($current_page - $window), $first_page);
    $last_page_show = min(($current_page + $window), $last_page);

    /* Whether to show the first and last pages in existence at the ends of the paginator */
    $show_first_page = (abs($first_page - $current_page)) > ($window);
    $show_last_page = (abs($last_page - $current_page)) > ($window);

    $prev_button_disabled = $current_page == $first_page ? 'disabled' : '';
    $next_button_disabled = $current_page == $last_page ? 'disabled' : '';

    $html .= $_page_button($current_page - 1, 'Previous', $prev_button_disabled);

    if ($show_first_page) {
        $html .= $_page_button($first_page, $first_page);
        $html .= '<span class="ellipsis">…</span>';
    }

    for ($i = $first_page_show; $i <= $last_page_show; $i++) {
        $html .= $_page_button($i, $i);
    }

    if ($show_last_page) {
        $html .= '<span class="ellipsis">…</span>';
        $html .= $_page_button($last_page, $last_page);
    }

    $html .= $_page_button($current_page + 1, 'Next', $next_button_disabled);

    return $html;
}
