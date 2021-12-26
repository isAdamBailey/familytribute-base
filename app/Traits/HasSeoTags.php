<?php

namespace App\Traits;

use Butschster\Head\Contracts\MetaTags\MetaInterface;
use Butschster\Head\Packages\Entities\OpenGraphPackage;
use Butschster\Head\Packages\Entities\TwitterCardPackage;

trait HasSeoTags
{
    protected ?string $ogType = null;

    protected ?string $twitterType = null;

    protected ?string $title = null;

    protected ?string $siteName = null;

    protected ?string $description = null;

    protected ?string $ogImage = null;

    protected string $twitterImage = '';

    protected ?string $titleStart = null;

    protected MetaInterface $meta;

    public function __construct(MetaInterface $meta)
    {
        $this->meta = $meta;
    }

    public function renderSeo()
    {
        $this->renderTags();
        $this->renderOg();
        $this->renderTwitter();
    }

    public function renderTags()
    {
        $this->meta->setTitle($this->getTitle());
        if ($this->getTitleStart()) {
            $this->meta->prependTitle($this->getTitleStart());
        }
    }

    public function renderOg()
    {
        $openGraph = new OpenGraphPackage('og');

        $openGraph->setType($this->getOgType())
            ->setSiteName($this->getSiteName())
            ->setTitle($this->getTitle())
            ->setDescription($this->getDescription())
            ->addImage('/images/'.$this->getOgImage(), [
                'alt' => $this->getTitle(),
                'height' => '630',
                'width' => '1200',
            ])
            ->setUrl(request()->url());

        $this->meta->registerPackage($openGraph);
    }

    public function renderTwitter()
    {
        $twitter = new TwitterCardPackage('twitter');

        $twitter->setType($this->getTwitterType())
            ->setSite(config('meta_tags.twitter.site'))
            ->setCreator(config('meta_tags.twitter.creator'))
            ->setTitle($this->getTitle())
            ->setDescription($this->getDescription())
            ->setImage($this->getTwitterImage());

        $this->meta->registerPackage($twitter);
    }

    public function ogSetType(string $ogType)
    {
        $this->ogType = $ogType;
    }

    public function getOgType(): string
    {
        return $this->ogType ?: 'website';
    }

    public function twitterSetType(string $twitterType)
    {
        $this->twitterType = $twitterType;
    }

    public function getTwitterType(): string
    {
        return $this->twitterType ?: 'summary_large_image';
    }

    public function ogSetTitle(string $title)
    {
        $this->title = $title;
    }

    public function getTitle(): string
    {
        return $this->title ?: config('meta_tags.title.default');
    }

    public function ogSetSiteName(string $siteName)
    {
        $this->siteName = $siteName;
    }

    public function getSiteName(): string
    {
        return $this->siteName ?: config('meta_tags.title.default');
    }

    public function setDescription(string $description)
    {
        $this->description = $description;
    }

    public function getDescription(): string
    {
        return $this->description ?: config('meta_tags.description.default');
    }

    public function setOgImage(string $ogImage)
    {
        $this->ogImage = $ogImage;
    }

    public function getOgImage(): string
    {
        return $this->ogImage ?: config('meta_tags.image.default');
    }

    public function setTwitterImage(string $twitterImage)
    {
        $this->twitterImage = $twitterImage;
    }

    public function getTwitterImage(): string
    {
        return $this->twitterImage;
    }

    public function setTitleStart(string $titleStart)
    {
        $this->titleStart = $titleStart;
    }

    public function getTitleStart(): ?string
    {
        return $this->titleStart;
    }
}
