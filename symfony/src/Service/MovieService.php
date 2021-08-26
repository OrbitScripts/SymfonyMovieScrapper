<?php

namespace App\Service;

use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class MovieService
{
    private HttpClientInterface $client;

    /**
     * @param HttpClientInterface $client
     */
    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    public function getMovieLinks(string $link): array
    {
        $response = $this->client->request("GET", $link);

        try {
            $content = $response->getContent();
        } catch (Exception $e) {
            throw new HttpException("Http Response error");
        }

        $arr = [];
        $i = 0;
        $crawler = new Crawler($content);

        $crawlerLinks = $crawler->filter('div.ott_provider > h3');

        foreach ($crawlerLinks as $domElement) {
            $subElements = $crawlerLinks->eq($i++)->siblings()->filter('div > ul > li:not(.hide) > div > a');
            foreach ($subElements as $subElement) {
                $rawLink = $subElement->attributes->getNamedItem("href")->nodeValue;
                $linkTitle = $subElement->attributes->getNamedItem("title")->nodeValue;

                $clearLink = $this->takeClearLinkToResource($rawLink);
                $platformName = $this->takePlatformName($linkTitle);

                $arr[$domElement->nodeValue][$platformName] = $clearLink;
            }
        }

        $pageTitle = $crawler->filter('head > title')->getNode(0)->nodeValue;
        $movieTitle = $this->takeMovieTitle($pageTitle);

        $result = [];
        $result["title"] = $movieTitle;
        $result["links"] = $arr;

        return $result;
    }

    private function takeMovieTitle(string $title): string
    {
        preg_match('/^([^—]+)/', $title, $matches);
        return trim($matches[0]);
    }

    private function takePlatformName(string $title): string
    {
        preg_match('/(?:on |у )(\w.+$)/', $title, $matches);
        return preg_replace('/\s+/', '', lcfirst(ucwords($matches[1])));
    }

    private function takeClearLinkToResource(string $link): string
    {
        preg_match('/(https:\/\/(?!click).+[^&]*)&/', urldecode($link), $matches);
        return $matches[1];
    }
}