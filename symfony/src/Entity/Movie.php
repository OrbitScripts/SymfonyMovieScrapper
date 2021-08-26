<?php

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;

class Movie
{
    const SITE_CONSTRAINT = '/https:\/\/www\.themoviedb\.org\/movie\/[\w-]+\/watch+/';

    /**
     * @Assert\Url(message = "The url '{{ value }}' is not a valid url")
     * @Assert\Regex(pattern=self::SITE_CONSTRAINT, message="Only TMDB site scrapping avaliable")
     */
    private string $link;

    /**
     * @return string
     */
    public function getLink(): string
    {
        return $this->link;
    }

    /**
     * @param string $link
     */
    public function setLink(string $link): void
    {
        $this->link = $link;
    }

}