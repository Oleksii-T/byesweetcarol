<?php

namespace App\Services;

use Illuminate\Support\Str;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Simple Scrapping of game info using few sources.
 * The web scrapping is represented via simple HTTP GET request.
 * Thus, js based pages will not be scraped.
 *
 * Order of sources for each piece of data:
 * metaScore: metacritic > rawg
 * userScore: metacritic
 * releaseDate: rawg > steam > igbd
 * description: steam > igbd > rawg
 * developer: steam > igbd > rawg
 * publisher: steam > igbd > rawg
 * platforms: wikipedia > igbd > rawg
 * ganres: wikipedia > igbd > rawg
 * esrb: esrb.com > ps > rawg
 * esrbImage: esrb.com > ps
 * screenshots: nintendo > steam
 * thumbnail: igbd > rawg > firstScreenshot
 * steam_site: steam
 * nintendo_site: nintendo > rawg
 * xbox_site: xbox > rawg
 * ps_site: ps > rawg
 * official_site: igbd > rawg
 */
class GameScraperService
{
    private string $gameName;

    private array $nameVersions = [];

    private array $sources = [];

    private array $data = [];

    public function __construct(string $gameName)
    {
        $this->gameName = $gameName;
    }

    public function scrape()
    {
        $this->info("Start game scraping $this->gameName");
        $this->makeNameVersion();
        $this->makeSources();

        $valuesToScrape = [
            'metaScore',
            'userScore',
            'releaseDate',
            'description',
            'developer',
            'publisher',
            'platforms',
            'ganres',
            'esrb',
            'esrbImage',
            'screenshots',
            'thumbnail',
            'steam_site',
            'nintendo_site',
            'xbox_site',
            'ps_site',
            'official_site',
        ];

        foreach ($valuesToScrape as $value) {
            $this->info("_Scrapping $value...");
            // try {
            $result = $this->$value();
            // } catch (\Throwable $th) {
            //     $this->error("__" . exceptionAsString($th));
            //     $result = null;
            // }
            $this->data[$value] = $result;
        }

        $this->info('_Result'.json_encode($this->data));

        return $this->data;
    }

    private function makeNameVersion()
    {
        $this->nameVersions[] = $this->gameName;
        $this->nameVersions[] = Str::slug($this->gameName);
        $this->nameVersions[] = Str::of($this->gameName)
            ->replaceMatches('/[^A-Za-z0-9]+/', '')
            ->trim('_')
            ->title()
            ->replace(' ', '_')
            ->__toString();
        $this->nameVersions[] = Str::slug($this->gameName).'-switch';

        $this->info('_Names: '.json_encode($this->nameVersions));
        /*
            Apex Legends
            Apex_Legends
            apex-legends
            apex-legends-switch
        */

        /*
            Horizon Forbidden West: Complete Edition
            horizon-forbidden-west-complete-edition
            Horizon_Forbidden_West_Complete_Edition
        */
    }

    private function makeSources()
    {
        $this->info('_Add sources... ');

        // api
        $this->addSourceIgbd();
        $this->addSourceRawg();

        // stores
        $this->addSourceSteam();
        $this->addSourcePS();
        $this->addSourceXbox();
        $this->addSourceNintendo();

        // additional sources
        $this->addSourceWiki();
        $this->addSourceMetacritic();
        $this->addSourceEsrb();

        // file_put_contents('test2.html', $wikiSearchPage);

        $this->info('__Sources: '.json_encode($this->sources));
    }

    private function addSourceIgbd()
    {
        try {
            $this->sources['igbd'] = (new IgdbService())->game($this->gameName);
        } catch (\Throwable $th) {
            $this->error('__Can not add igbd source. '.exceptionAsString($th));
        }
    }

    private function addSourceRawg()
    {
        try {
            $this->sources['rawg'] = (new RawgService())->game($this->gameName);
        } catch (\Throwable $th) {
            $this->error('__Can not add igbd source. '.exceptionAsString($th));
        }
    }

    private function addSourceSteam()
    {
        try {
            if ($this->sources['igbd'] ?? null) {
                $steamUrl = collect($this->sources['igbd']['websites'])->where('category', 'steam')->first()['url'] ?? null;
                if ($steamUrl) {
                    $steamSite = $this->html($steamUrl);

                    if (! str_contains($steamSite, 'An error was encountered while processing your request')) {
                        $this->sources['steam'] = $steamUrl;

                        return;
                    }
                }
            }

            $gameName = str_replace(' ', '+', $this->gameName);
            $url = "https://store.steampowered.com/search/?term=$gameName";
            $steamSearchPage = $this->html($url);
            $selector = '#search_resultsRows .search_result_row';
            $crawler = new Crawler($steamSearchPage);
            $firstResult = $crawler->filter($selector)->first()->link()->getUri();
            $this->sources['steam'] = $this->removeQueryString($firstResult);
        } catch (\Throwable $th) {
            $this->error('__Can not add steam source. '.exceptionAsString($th));
        }
    }

    private function addSourcePS()
    {
        try {
            $url = "https://store.playstation.com/search/$this->gameName";
            $psSearchPage = $this->html($url);
            $selector = '.psw-grid-list li a';
            $crawler = new Crawler($psSearchPage, null, 'https://store.playstation.com');
            $firstResult = $crawler->filter($selector)->first()->link()->getUri();
            $gameUrl = $this->removeQueryString($firstResult);
            $gamePage = $this->html($gameUrl);
            $gamePage = strtolower($gamePage);

            if (! str_contains($gamePage, strtolower($this->gameName))) {
                abort(500, "Wrong game page found: $gameUrl");
            }

            $this->sources['ps'] = $gameUrl;
        } catch (\Throwable $th) {
            $this->error('__Can not add ps source. '.exceptionAsString($th));
        }
    }

    //! xbox can not be scraped
    private function addSourceXbox()
    {
        try {
            $gameName = str_replace(' ', '+', $this->gameName);
            $url = "https://www.xbox.com/en-us/Search/Results?q=$gameName";
            $xboxSearchPage = $this->html($url);
            $selector = '.SearchProductGrid-module__container___jew-i li a';
            $crawler = new Crawler($xboxSearchPage);
            $firstResult = $crawler->filter($selector)->first()->link()->getUri();
            $this->sources['xbox'] = $this->removeQueryString($firstResult);
        } catch (\Throwable $th) {
            $this->error('__Can not add xbox source. '.exceptionAsString($th));
        }
    }

    private function addSourceNintendo()
    {
        try {
            foreach ($this->nameVersions as $name) {
                $url = "https://www.nintendo.com/us/store/products/$name/";
                $html = $this->html($url);

                if (str_contains($html, 'Whoops!')) {
                    continue;
                }

                $this->sources['nintendo'] = $url;

                return;
            }
        } catch (\Throwable $th) {
            $this->error('__Can not add nintendo source. '.exceptionAsString($th));
        }
    }

    private function addSourceWiki()
    {
        try {
            if ($this->sources['igbd'] ?? null) {
                foreach ($this->sources['igbd']['websites'] as $relativeWebsite) {
                    if ($relativeWebsite['category'] == 'wikipedia') {
                        $this->sources['wikipedia'] = $relativeWebsite['url'];

                        return;
                    }
                }
            }

            if (! isset($this->sources['wikipedia'])) {
                foreach ($this->nameVersions as $name) {
                    if (! isset($this->sources['wikipedia'])) {
                        $url = "https://en.wikipedia.org/wiki/$name";
                        $html = $this->html($url);

                        if (str_contains($html, 'Wikipedia does not have an article with this exact name')) {
                            continue;
                        }

                        $this->sources['wikipedia'] = $url;
                    }
                }
            }

            if (! isset($this->sources['wikipedia'])) {
                $gameName = str_replace(' ', '+', $this->gameName);
                $url = "https://en.wikipedia.org/w/index.php?search=$gameName";
                $wikiSearchPage = $this->html($url);
                if (str_contains($wikiSearchPage, 'Search results')) {
                    $crawler = new Crawler($wikiSearchPage);
                    $firstResult = $crawler->filter('.mw-search-results .mw-search-result a')->first()->link()->getUri();
                    $this->sources['wikipedia'] = $firstResult;
                } else {
                    // wiki redirected to game page
                    $this->sources['wikipedia'] = $url;
                }
            }
        } catch (\Throwable $th) {
            $this->error('__Can not add wikipedia source. '.exceptionAsString($th));
        }
    }

    private function addSourceMetacritic()
    {
        try {
            foreach ($this->nameVersions as $name) {
                if (! isset($this->sources['metacritic'])) {
                    $url = "https://www.metacritic.com/game/$name/";
                    $html = $this->html($url);

                    if (str_contains($html, '<h2 class="c-error404_header">Uh-oh</h2>')) {
                        continue;
                    }

                    $this->sources['metacritic'] = $url;
                }
            }
        } catch (\Throwable $th) {
            $this->error('__Can not add metacritic source. '.exceptionAsString($th));
        }
    }

    private function addSourceEsrb()
    {
        try {
            $gameName = str_replace(' ', '+', $this->gameName);
            $url = "https://www.esrb.org/search/?searchKeyword=$gameName";
            $esrbSearchPage = $this->html($url);
            $selector = '#results .game .heading a';
            $crawler = new Crawler($esrbSearchPage);
            $firstResult = $crawler->filter($selector)->first()->link()->getUri();
            $this->sources['esrb'] = $firstResult;
        } catch (\Throwable $th) {
            $this->error('__Can not add esrb source. '.exceptionAsString($th));
        }
    }

    private function html(?string $url, bool $usingBrowser = false): string
    {
        if (! $url) {
            return '';
        }

        return PageFetchService::get($url, $usingBrowser);
    }

    private function metaScore()
    {
        $res = $this->getTextFromSourceBySelector('metacritic', '.c-productScoreInfo .c-productScoreInfo_scoreNumber .c-siteReviewScore span');

        if ($res) {
            $this->info('__Got from metacritic');
        }

        if (! $res && ! empty($this->sources['rawg']['metacritic'])) {
            $res = $this->sources['rawg']['metacritic'];
            $this->info('__Got from rawg');
        }

        if (! $this->isInt($res)) {
            return null;
        }

        return $res;
    }

    private function userScore()
    {
        $res = $this->getTextFromSourceBySelector('metacritic', '.c-productScoreInfo .c-productScoreInfo_scoreNumber .c-siteReviewScore span', true);

        if ($res) {
            $this->info('__Got from metacritic');
        }

        if (! $this->isFloat($res)) {
            return null;
        }

        return $res;
    }

    private function releaseDate()
    {
        $res = $this->sources['rawg']['released'] ?? null;

        if ($res) {
            $this->info('__Got from rawg');
        }

        if (! $res) {
            $res = $this->getTextFromSourceBySelector('steam', '.release_date .date');

            if ($res) {
                $this->info('__Got from steam');
            }
        }

        if (! $res && isset($this->sources['igbd']['first_release_date'])) {
            $res = $this->sources['igbd']['first_release_date'];

            if ($res) {
                $this->info('__Got from igbd');
            }
        }

        return $res;
    }

    private function description()
    {
        $res = $this->getTextFromSourceBySelector('steam', '.game_description_snippet');

        if ($res) {
            $this->info('__Got from steam');
        }

        if (! $res && isset($this->sources['igbd']['summary'])) {
            $res = $this->sources['igbd']['summary'];

            if ($res) {
                $this->info('__Got from igbd');
            }
        }

        if (! $res && ! empty($this->sources['rawg']['summary'])) {
            $res = $this->sources['rawg']['summary'];
            $this->info('__Got from rawg');
        }

        return $res;
    }

    private function developer()
    {
        $res = $this->getTextFromSourceBySelector('steam', '.dev_row .summary');

        if ($res) {
            $this->info('__Got from steam');
        }

        if (! $res && isset($this->sources['igbd']['developer'])) {
            $res = $this->sources['igbd']['developer'];

            if ($res) {
                $this->info('__Got from igbd');
            }
        }

        if (! $res && ! empty($this->sources['rawg']['developer'])) {
            $res = $this->sources['rawg']['developer'];
            $this->info('__Got from rawg');
        }

        return $res;
    }

    private function publisher()
    {
        $res = $this->getTextFromSourceBySelector('steam', '.dev_row .summary', true);

        if ($res) {
            $this->info('__Got from steam');
        }

        if (! $res && isset($this->sources['igbd']['publisher'])) {
            $res = $this->sources['igbd']['publisher'];

            if ($res) {
                $this->info('__Got from igbd');
            }
        }

        if (! $res && ! empty($this->sources['rawg']['publisher'])) {
            $res = $this->sources['rawg']['publisher'];
            $this->info('__Got from rawg');
        }

        return $res;
    }

    private function platforms()
    {
        $sourceUrl = $this->sources['wikipedia'] ?? null;

        if (! $sourceUrl) {
            return '';
        }

        $crawler = new Crawler($this->html($sourceUrl));
        $res = $this->findInWikiTable($crawler, 'table.infobox.ib-video-game.hproduct', 'Platform(s)');

        if ($res) {
            $this->info('__Got from wikipedia');
        }

        if (! $res && isset($this->sources['igbd']['platforms'])) {
            $res = implode(', ', $this->sources['igbd']['platforms']);

            if ($res) {
                $this->info('__Got from igbd');
            }
        }

        if (! $res && ! empty($this->sources['rawg']['platforms'])) {
            $res = implode(', ', $this->sources['rawg']['platforms']);

            if ($res) {
                $this->info('__Got from rawg');
            }
        }

        return $res;
    }

    private function ganres()
    {
        $sourceUrl = $this->sources['wikipedia'] ?? null;

        if (! $sourceUrl) {
            return '';
        }

        $crawler = new Crawler($this->html($sourceUrl));
        $res = $this->findInWikiTable($crawler, 'table.infobox.ib-video-game.hproduct', 'Genre(s)');

        if ($res) {
            $this->info('__Got from wikipedia');
        }

        if (! $res && isset($this->sources['igbd']['genres'])) {
            $res = implode(', ', $this->sources['igbd']['genres']);

            if ($res) {
                $this->info('__Got from igbd');
            }
        }

        if (! $res && ! empty($this->sources['rawg']['genres'])) {
            $res = implode(', ', $this->sources['rawg']['genres']);

            if ($res) {
                $this->info('__Got from rawg');
            }
        }

        return $res;
    }

    private function esrb()
    {
        $res = $this->getTextFromSourceBySelector('esrb', '.info-txt .description');

        if ($res) {
            $this->info('__Got from esrb');
        }

        if (! $res) {
            $res = $this->getTextFromSourceBySelector('ps', '.psw-l-switcher.psw-with-dividers');

            if ($res) {
                $this->info('__Got from ps');
            }
        }

        if (! $res && ! empty($this->sources['rawg']['esrb_rating'])) {
            $res = $this->sources['rawg']['esrb_rating'];
            $this->info('__Got from rawg');
        }

        return $res;
    }

    private function esrbImage()
    {
        $res = $this->getTextFromSourceBySelector('esbr', '.info .info-img img', false, true);

        if ($res) {
            $this->info('__Got from esrb');
        }

        if (! $res) {
            $res = $this->getTextFromSourceBySelector('ps', '.psw-c-bg-0.psw-t-subtitle img', false, true);

            if ($res) {
                $this->info('__Got from ps');
            }
        }

        return $res;
    }

    private function screenshots()
    {
        try {
            $screens = [];
            $selector = '.slider-list .slide img';
            $crawler = new Crawler($this->html($this->sources['nintendo']));
            $nodes = $crawler->filter($selector);
            foreach ($nodes as $node) {
                $screens[] = $node->getAttribute('src');
            }

            $res = $this->clearLinks($screens);

            if ($res) {
                $this->info('__Got from nintendo');

                return $res;
            }
        } catch (\Throwable $th) {
        }

        try {
            $screens = [];
            $selector = '#highlight_player_area .highlight_screenshot a';
            $crawler = new Crawler($this->html($this->sources['steam']));
            $nodes = $crawler->filter($selector);
            foreach ($nodes as $node) {
                $screens[] = $node->getAttribute('href');
            }

            $res = $this->clearLinks($screens);

            if ($res) {
                $this->info('__Got from steam');

                return $res;
            }
        } catch (\Throwable $th) {
        }

        try {
            $res = [];
            foreach ($this->sources['igbd']['screenshots'] as $uri) {
                $res[] = str_replace('t_thumb', 't_1080p', $uri);
            }

            if ($res) {
                $this->info('__Got from igbd');
            }

            return $res;
        } catch (\Throwable $th) {
        }
    }

    private function thumbnail()
    {
        $res = '';

        if (! empty($this->sources['igbd']['cover'])) {
            $res = str_replace('t_thumb', 't_1080p', ($this->sources['igbd']['cover']));
            $this->info('__Got from igbd');
        } elseif (! empty($this->sources['rawg']['cover'])) {
            $res = $this->sources['rawg']['cover'];
            $this->info('__Got from rawg');
        } elseif ($screenshots = $this->data['screenshots']) {
            $res = array_shift($screenshots);
            $this->data['screenshots'] = $screenshots;

            $this->info('__Got from screenshots');
        }

        return $res;
    }

    public function steam_site()
    {
        $res = $this->sources['steam'] ?? null;

        if ($res) {
            $this->info('__Got from steam');
        }

        if (! empty($this->sources['igbd']['websites'])) {
            foreach ($this->sources['igbd']['websites'] as $relativeWebsite) {
                if ($relativeWebsite['category'] == 'steam') {
                    $res = $relativeWebsite['url'];
                    $this->info('__Got from igbd');

                    break;
                }
            }
        }

        if (! $res && ! empty($this->sources['rawg']['steam_site'])) {
            $res = $this->sources['rawg']['steam_site'];
            $this->info('__Got from rawg');
        }

        return $res;
    }

    public function nintendo_site()
    {
        $res = $this->sources['nintendo'] ?? null;

        if ($res) {
            $this->info('__Got from nintendo');
        }

        if (! $res && ! empty($this->sources['rawg']['nintendo_site'])) {
            $res = $this->sources['rawg']['nintendo_site'];
            $this->info('__Got from rawg');
        }

        return $res;
    }

    public function xbox_site()
    {
        $res = $this->sources['xbox'] ?? null;

        if ($res) {
            $this->info('__Got from xbox');
        }

        if (! $res && ! empty($this->sources['rawg']['xbox_site'])) {
            $res = $this->sources['rawg']['xbox_site'];
            $this->info('__Got from rawg');
        }

        return $res;
    }

    public function ps_site()
    {
        $res = $this->sources['ps'] ?? null;

        if ($res) {
            $this->info('__Got from ps');
        }

        if (! $res && ! empty($this->sources['rawg']['ps_site'])) {
            $res = $this->sources['rawg']['ps_site'];
            $this->info('__Got from rawg');
        }

        return $res;
    }

    public function official_site()
    {
        $res = '';

        if (! empty($this->sources['igbd']['websites'])) {
            foreach ($this->sources['igbd']['websites'] as $relativeWebsite) {
                if ($relativeWebsite['category'] == 'official') {
                    $res = $relativeWebsite['url'];
                    $this->info('__Got from igbd');

                    break;
                }
            }
        }

        if (! $res && ! empty($this->sources['rawg']['official_site'])) {
            $res = $this->sources['rawg']['official_site'];
            $this->info('__Got from rawg');
        }

        return $res;
    }

    private function findInWikiTable($crawler, $selector, $name)
    {
        $headers = $crawler->filter("$selector th");
        $platformsNumber = 0;
        foreach ($headers as $i => $header) {
            // dump($header->nodeValue);
            if ($header->nodeValue == $name) {
                $platformsNumber = $i;

                // dump("platforms found $i");
                break;
            }
        }

        $values = $crawler->filter("$selector td");
        // dd($values);
        if (! $platformsNumber) {
            return null;
        }

        // dump('find vlaue...');
        foreach ($values as $i => $value) {
            if ($i == $platformsNumber) {
                return $value->nodeValue;
            }
        }

        return null;
    }

    private function getTextFromSourceBySelector(string $source, string $selector, bool $last = false, bool $isImage = false)
    {
        try {
            $sourceUrl = $this->sources[$source] ?? null;

            if (! $sourceUrl) {
                return '';
            }

            // dlog("SOURCE HTML $source: " . $this->html($sourceUrl)); //! LOG

            $crawler = new Crawler($this->html($sourceUrl));
            $nodes = $crawler->filter($selector);
            $node = $last ? $nodes->last() : $nodes->first();

            if ($isImage) {
                return $this->removeQueryString($node->image()->getUri());
            }

            return $node->text('');
        } catch (\Throwable $th) {
            report($th);

            return '';
        }
    }

    private function clearLinks($links)
    {
        return collect($links)
            ->unique()
            ->filter()
            ->map(fn ($l) => $this->removeQueryString($l))
            ->toArray();
    }

    public function removeQueryString($link)
    {
        return preg_replace('/\?.*/', '', $link);
    }

    private function info($text)
    {
        \Log::channel('scraper')->info($text);
    }

    private function error($text)
    {
        \Log::channel('scraper')->error($text);
    }

    private function isInt($var)
    {
        return filter_var($var, FILTER_VALIDATE_INT) !== false;
    }

    private function isFloat($var)
    {
        return is_numeric($var);
    }
}
