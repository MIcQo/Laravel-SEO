<?php
namespace MichalKoval\SEO;


interface SEOBuilderInterface {

    /**
     * @param string $canonical
     */
    public function setCanonical($canonical);

    /**
     * @return string
     */
    public function getCanonical ();

    /**
     * @param $name
     * @param null $value
     * @param string $type
     * @return mixed
     */
    public function addMeta($name, $value = null, $type = "content");

    /**
     * @return string
     */
    public function enableRobots();

    /**
     * @return string
     */
    public function disableRobots();

    /**
     * @return string
     */
    public function getMeta();

    /**
     * @param string $site_name
     * @param string $title
     * @param string $desc
     * @param string $type
     * @param string $image
     * @param string $link
     * @return mixed
     */
    public function getFacebookTags($site_name = "", $title = "", $desc = "", $type = "website", $image = "", $link = "");

    /**
     * @return string
     */
    public function getDescription();

    /**
     * @param string $desc
     */
    public function setDescription($desc);

    /**
     * @return mixed
     */
    public function getKeywords();

    /**
     * @param string|null $keywords
     */
    public function setKeywords($keywords = null);

    /**
     * @param string $url
     */
    public function setURL($url);

    /**
     * @param string $path
     */
    public function setPath($path);

    /**
     * @param $location
     * @param string|null $lastmod
     * @param float|string|null $priority
     * @param integer|string|null $changeFreq
     */
    public function addItem($location, $lastmod = null, $priority = null, $changeFreq = null);

    /**
     * @return string
     */
    public function render();

    /**
     * @return string
     */
    public function renderIndex();

    /**
     * @param null $agent
     */
    public function addUserAgent($agent = null);

    /**
     * @param null $path
     */
    public function addDisallow($path = null);

    /**
     * @param null $sitemap
     */
    public function addSitemap($sitemap = null);

    /**
     * @param null $noIndex
     */
    public function addNoIndex($noIndex = null);

    /**
     * @return string
     */
    public function getRobot();
}
