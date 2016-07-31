<?php
namespace MichalKoval\SEO;

class SEOBuilder implements SEOBuilderInterface {

    protected $keywords = "";
    protected $description = "";
    protected $canonical = "";
    protected $metas = [];

    protected $url = "";
    protected $path = "";
    protected $sitemapItems = [];

    protected $agents = [];
    protected $disallow = [];
    protected $sitemap = [];
    protected $noIndex = [];

    const DEFAULT_PROTOCOL = "http://www.sitemaps.org/schemas/sitemap/0.9";
    const DEFAULT_LAST_MOD = 'now';
    const DEFAULT_PRIORITY = '0.5';
    const DEFAULT_CHANGE_FREQ = "weekly";

    const DEFAULT_AGENTS = "*";
    const DEFAULT_DISALLOW = "*";
    const DEFAULT_NO_INDEX = "";

    /**
     * @param string|array $name
     * @param string|null $content
     * @param string|null $type
     * @return string
     */
    protected function getMetaHTML($name, $content = null, $type = 'content') {
        $output = "";

        if(is_array($name)) {
            foreach($name as $k => $v) {
                $output .= "<meta $type=\"$k\" content=\"$v\" />\n";
            }
        }else {
            if($name == null ){
                $output = "<meta $type=\"$content\" />";
            }else {
                $output = "<meta name=\"$name\" content=\"$content\" />";
            }
        }

        return $output;
    }

    public function charset($charset) {
        return $this->getMetaHTML(null, $charset, "charset");
    }

    /**
     * @param string $canonical
     */
    public function setCanonical($canonical) {
        $this->canonical = $canonical;
    }

    /**
     * @return string
     */
    public function getCanonical () {
        return '<link rel="canonical" href="'.$this->canonical.'" />';
    }

    /**
     * @param array|string $name
     * @param null|string $value
     * @param string $type
     * @return string
     */
    public function addMeta($name, $value = null, $type = "content") {
        if(is_array($name)) {
            $this->metas['type'] = $value;
            foreach($name as $k => $v) {
                $this->metas['values'][$k] = $v;
            }
        }else {
            $this->metas['type'] = $type;
            $this->metas['values'][$name] = $value;
        }
    }

    /**
     * @return string
     */
    public function enableRobots() {
        return $this->getMetaHTML("robots", "FOLLOW, INDEX");
    }

    /**
     * @return string
     */
    public function disableRobots() {
        return $this->getMetaHTML("robots", "NOFOLLOW, NOINDEX");
    }

    /**
     * @return string
     */
    public function getMeta() {
        if(!isset($this->metas['values'])) return "";

        return $this->getMetaHTML($this->metas['values'], null, $this->metas['type']);
    }

    /**
     * @param string $site_name
     * @param string $title
     * @param string $desc
     * @param string $type
     * @param string $image
     * @param string $link
     * @return string
     */
    public function getFacebookTags($site_name = "", $title = "", $desc = "", $type = "website", $image = "", $link = "") {
        $link = empty($link) ? $actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]" : $link;

        return $this->getMetaHTML([
            'og:url' => $link,
            'og:type' => $type,
            'og:title' => $title,
            'og:description' => $desc,
            'og:image' => $image,
            'og:site_name' => $site_name
        ], null, 'property');
    }

    /**
     * @return string
     */
    public function getDescription() {
        return $this->getMetaHTML("description", $this->description);
    }

    /**
     * @param string $desc
     */
    public function setDescription($desc) {
        $this->description = $desc;
    }

    /**
     * @return string
     */
    public function getKeywords() {
        return $this->getMetaHTML("keywords", $this->keywords);
    }

    /**
     * @param string $keywords
     */
    public function setKeywords($keywords = null) {
        if(empty($this->keywords)) {
            $this->keywords = $keywords;
        } else {
            $this->keywords .= ", $keywords";
        }
    }

    /**
     * @param $location
     * @param $lastmod
     * @param $priority
     * @param $changeFreq
     */
    protected function addXMLItem($location, $lastmod, $priority, $changeFreq) {
        $this->sitemapItems[] = [
            'loc' => $location,
            'lastmod' => $lastmod,
            'priority' => $priority,
            'changefreq' => $changeFreq
        ];
    }
    /**
     * @param $url
     */
    public function setURL($url) {
        $this->url = $url;
    }

    /**
     * @param string $path
     */
    public function setPath($path) {
        $this->path = $path;
    }

    /**
     * @param $location
     * @param string|null $lastmod
     * @param float|string|null $priority
     * @param integer|string|null $changeFreq
     */
    public function addItem($location, $lastmod = null, $priority = null, $changeFreq = null) {
        $date = new \DateTime;
        $lastmod = $lastmod != null ? $lastmod : self::DEFAULT_LAST_MOD;
        $priority = $priority != null ? $priority : self::DEFAULT_PRIORITY;
        $changeFreq = $changeFreq != null ? $changeFreq : self::DEFAULT_CHANGE_FREQ;

        $lastmod = $date->modify($lastmod)->getTimestamp();

        $this->addXMLItem($location, $lastmod, $priority, $changeFreq);
    }

    /**
     * @return string
     */
    public function render() {
        $doc = new \DomDocument('1.0', "UTF-8");
        $doc->preserveWhiteSpace = false;
        $doc->formatOutput = true;

        $root = $doc->createElement('urlset');

        $attr = $doc->createAttribute("xmlns");
        $attr->value = self::DEFAULT_PROTOCOL;
        $root->appendChild($attr);

        $root = $doc->appendChild($root);

        foreach($this->sitemapItems as $map) {
            $occ = $doc->createElement('url');
            $occ = $root->appendChild($occ);
            foreach($map as $name => $url) {
                $child = $doc->createElement($name);
                $child = $occ->appendChild($child);
                $value = $doc->createTextNode($name == 'loc' ? $this->url."".$url : ($name == "lastmod" ? date("Y-m-d", $url) : $url ));
                $child->appendChild($value);
            }
        }

        $xml_string = $doc->saveXML() ;
        return $xml_string;

    }

    /**
     * @return string
     */
    public function renderIndex() {
        $doc = new \DomDocument("1.0", "UTF-8");
        $doc->preserveWhiteSpace = false;
        $doc->formatOutput = true;

        // <sitemapindex xmlns="...">

        $sitemapindex = $doc->createElement("sitemapindex");

        $xmlns = $doc->createAttribute("xmlns");
        $xmlns->value = self::DEFAULT_PROTOCOL;
        $sitemapindex->appendChild($xmlns);

        $sitemapindex = $doc->appendChild($sitemapindex);

            // <sitemap>

            $sitemap = $doc->createElement("sitemap");
            $sitemap = $sitemapindex->appendChild($sitemap);

                // <loc>
                $loc = $doc->createElement("loc");
                $loc = $sitemap->appendChild($loc);
                $locVal = $doc->createTextNode($this->path);
                $loc->appendChild($locVal);
                // </loc>

                // <lastmod>
                $lm = $doc->createElement("lastmod");
                $lm = $sitemap->appendChild($lm);
                $lmVal = $doc->createTextNode((new \DateTime())->format(DATE_W3C));
                $lm->appendChild($lmVal);
                // </lastmod>

            // </sitemap>

        // </sitemapindex>

        return $doc->saveXML();

    }

    /**
     * @param null $agent
     */
    public function addUserAgent($agent = null) {
        $agent = $agent == null ? self::DEFAULT_AGENTS : $agent;
        $this->agents[] = $agent;
    }

    /**
     * @param null $path
     */
    public function addDisallow($path = null) {
        $path = $path == null ? self::DEFAULT_DISALLOW : $path;
        $this->disallow[] = $path;
    }

    /**
     * @param null $sitemap
     */
    public function addSitemap($sitemap = null) {
        $sitemap = $sitemap == null ? false : $sitemap;
        $this->sitemap[] = $sitemap;
    }

    /**
     * @param null $noIndex
     */
    public function addNoIndex($noIndex = null) {
        $noIndex = $noIndex == null ? self::DEFAULT_NO_INDEX : $noIndex;
        $this->noIndex[] = $noIndex;
    }

    /**
     * @return string
     */
    public function getRobot() {
        //        Sitemap: http://www.example.com/sitemap.xml
        //        User-agent: *
        //        Disallow: *

        $robots = "";

        foreach($this->sitemap as $sitemap) {
            if($sitemap !== false) $robots .= "Sitemap: $sitemap\n";
        }

        foreach($this->agents as $agent) {
            $robots .= "User-agent: $agent\n";
        }

        foreach($this->disallow as $disallow) {
            $robots .= "Disallow: $disallow \n";
        }

        foreach($this->noIndex as $noIndex) {
            $robots .= "Noindex: $noIndex \n";
        }

        return $robots;
    }

}
