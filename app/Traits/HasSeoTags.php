<?php

namespace App\Traits;

use App\Models\SiteSetting;
use Butschster\Head\Contracts\MetaTags\MetaInterface;
use Butschster\Head\Packages\Entities\OpenGraphPackage;
use Butschster\Head\Packages\Entities\TwitterCardPackage;
use Illuminate\Support\Str;

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

    protected SiteSetting $siteSettings;

    public function __construct(MetaInterface $meta)
    {
        $this->meta = $meta;
        $this->siteSettings = SiteSetting::first();
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
        $this->meta->setDescription($this->getDescription());
    }

    public function renderOg()
    {
        $openGraph = new OpenGraphPackage('og');

        $openGraph->setType($this->getOgType())
            ->setSiteName($this->getSiteName())
            ->setTitle($this->getTitle())
            ->setDescription($this->getDescription())
            ->addImage($this->getOgImage(), [
                'alt' => $this->getTitle(),
                'height' => '630',
                'width' => '1200',
            ])
            ->setUrl(request()->url());

        $this->meta->replacePackage($openGraph);
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

        $this->meta->replacePackage($twitter);
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
        $this->title = strip_tags($title);
    }

    public function getTitle(): string
    {
        return $this->title ?: strip_tags($this->siteSettings->title);
    }

    public function ogSetSiteName(string $siteName)
    {
        $this->siteName = strip_tags($siteName);
    }

    public function getSiteName(): string
    {
        return $this->siteName ?: strip_tags($this->siteSettings->title);
    }

    public function setDescription(string $description)
    {
        $this->description = Str::limit(strip_tags($description));
    }

    public function getDescription(): string
    {
        return $this->description ?: Str::limit(strip_tags($this->siteSettings->description));
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
