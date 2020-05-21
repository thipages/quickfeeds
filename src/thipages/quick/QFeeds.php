<?php
namespace thipages\quick;
use DOMDocument;
use Error;
class QFeeds {
    const TYPE_RSS='application/rss+xml';
    const TYPE_ATOM='application/atom+xml';
    public static function autoDiscover($url,$sslEnabled=false) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, $sslEnabled);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $sslEnabled);
        $html = curl_exec($ch);
        $rss = [];
        if (!curl_errno($ch)) {
            $doc = new DOMDocument();
            @$doc->loadHTML($html);
            $links = $doc->getElementsByTagName('link');
            for ($i = 0; $i < $links->length; $i++) {
                $meta = $links->item($i);
                $type = $meta->getAttribute('type');
                if ($type === self::TYPE_ATOM || $type === self::TYPE_RSS) {
                    array_push($rss, (object)[
                        'href' => $meta->getAttribute('href'),
                        'type' => $type,
                        'title' => $meta->getAttribute('title2')
                    ]);
                }
            }
        } else {
            throw new Error(curl_error($ch));
        }
        curl_close($ch);
        return $rss;
    }
}
