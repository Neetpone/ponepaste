<?php

namespace PonePaste\Helpers;

class DnsblHelper {
    private const HOSTS = [
        'dnsbl.dronebl.org',
        'sbl.spamhaus.org'
    ];

    // returns false if not blacklisted, otherwise returns the host that blacklisted the IP
    public static function isBlacklisted(string $ip) : false | string {
        // IPv6 addresses are not supported
        if (!str_contains($ip, '.')) {
            return false;
        }

        if (RedisHelper::exists('dnsbl/' . $ip)) {
            return RedisHelper::get('dnsbl/' . $ip) === '1';
        }

        $reverse_ip = implode('.', array_reverse(explode('.', $ip)));

        foreach (self::HOSTS as $host) {
            $lookup = $reverse_ip . '.' . $host;

            if (checkdnsrr($lookup, 'A')) {
                RedisHelper::setex('dnsbl/' . $ip, 3600, '1');
                return $host;
            }
        }

        RedisHelper::setex('dnsbl/' . $ip, 3600, '0');
        return false;
    }
}