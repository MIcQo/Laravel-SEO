# Laravel-SEO

## Install

Run the following command and provide the last version:
```
composer require michal-koval/laravel-seo
```

or

just add to composer.json (require-dev)
```
"michal-koval/laravel-seo": "dev-master"
```


config\app.php
```php
'providers' => [
  // another providers
  MichalKoval\SEO\SEOServiceProvider::class,
];
```
and
```php
'aliases' => [
  // another aliases
  'SEO' => MichalKoval\SEO\SEOFacade::class
];
```

If you got it, then you can try if it works.

## Documentation

### Meta keywords
In your controller you can set keywords, description or another custom meta tags
```php
namespace App\Http\Controllers;

use SEO;

classHomeController extends Controller {
  public function index() {
    SEO::setKeywords("foo, bar, foobar");
  }
}
```

and in template

```blade
{!! SEO::getKeywords() !!} 
{{# result is <meta name="keywords" content="foo, bar, foobar" /> #}}
```


you can append keywords with using a same command
```php
SEO::addKeywords("append, to, the, keywords");
{{# result in template is <meta name="keywords" content="foo, bar, foobar, append, to, the, keywords" /> #}}
```

### Sitemap

This generate sitemap instantly without files

routes.php
```php
Route::get("/sitemap.xml", function() {
    $seo = App::make("seo");
    $seo->setURL("http://example.com");
    $seo->addItem("/home");
    $seo->addItem("/some", '+1 day');
    $seo->addItem("/blue");
    $output = $seo->render();

    return (new Response($output))->header("Content-type", "text/xml");

})->name('sitemap');
```

output is:
```xml

<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
  <url>
    <loc>http://example.com/home</loc>
    <lastmod>2016-07-31</lastmod>
    <priority>0.5</priority>
    <changefreq>weekly</changefreq>
  </url>
  <url>
    <loc>http://example.com/some</loc>
    <lastmod>2016-08-01</lastmod>
    <priority>0.5</priority>
    <changefreq>weekly</changefreq>
  </url>
  <url>
    <loc>http://example.com/blue</loc>
    <lastmod>2016-07-31</lastmod>
    <priority>0.5</priority>
    <changefreq>weekly</changefreq>
  </url>
</urlset>
```

#### Sitemap index

again routes.php
```php
Route::get("/sitemap-index.xml", function() {
    $seo = App::make("seo");
    $seo->setPath(route("sitemap"));
    $output = $seo->renderIndex();

    return (new Response($output))->header("Content-type", "text/xml");
});
```
and output is: 
```xml
<?xml version="1.0" encoding="UTF-8"?>
<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
  <sitemap>
    <loc>http://example.com/public/sitemap.xml</loc>
    <lastmod>2016-07-31T03:42:38+00:00</lastmod>
  </sitemap>
</sitemapindex>
```
### Robots.txt
```php
Route::get("/robots.txt", function() {
    $seo = App::make("seo");
    $seo->addUserAgent();
    $seo->addDisallow("/assets");
    $seo->addSitemap(route("sitemap"));
    $seo->addNoIndex("/path");
    $output = $seo->getRobot();
    return (new Response($output))->header("Content-type", "text/plain");
});
```

and the magic output is:
```
Sitemap: http://example.com/public/sitemap.xml
User-agent: *
Disallow: /assets 
No-Index: /path 
```

### Full commands list
```php
/**
 * @param string $canonical
 */
public function setCanonical($canonical);

/**
 * <link rel="canonical" href="http://example.com/" />
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
 * <meta name="robots" content="FOLLOW, INDEX" />
 * @return string
 */
public function enableRobots();

/**
 * <meta name="robots" content="NOFOLLOW, NOINDEX" />
 * @return string
 */
public function disableRobots();

/**
 * <meta ... />
 * @return string
 */
public function getMeta();

/**
 * Return OpenGraph params (Social)
 * <meta property="og:image" />
 * <meta property="og:url" />
 * ...
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
 * <meta name="description" content="..." />
 * @return string
 */
public function getDescription();

/**
 * @param string $desc
 */
public function setDescription($desc);

/**
 * <meta name="keywords" content="..." />
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
 * Return XML Formated Sitemap
 * @return string
 */
public function render();

/**
 * Return XML Formated Sitemap Index
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
 * Return robots.txt formatted string
 * @return string
 */
public function getRobot();
```
