@extends('layouts.admin.app')

@section('title', 'Docs')

@section('content_header')
    <x-admin.title
        text="Docs"
        bcRoute="admin.docs"
    />
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="m-0">1. Game Scrapper</h5>
                </div>
                <div class="card-body">
                    <p>
                        Game Scrapper used to obtain Game information automaticaly.<br>
                        Only Game name is required to start scrappiing.<br><br>
                        Game scrapping is initialized by 3 triggers:
                    </p>
                    <ol>
                        <li>Creating Game Review and selecting non existing Game;</li>
                        <li>Clicking 'Scrape' on the Game edit page;</li>
                        <li>Calendar Scraped found new game.</li>
                    </ol>
                    <p>
                        in order to ensure avialability of information, Game Scrapper uses few different sources under the hood:
                    </p>
                    <ol>
                        <li><b>Metacritic</b> - web site</li>
                        <li><b>RAWG</b> - api</li>
                        <li><b>Steam</b> - web site</li>
                        <li><b>IGBD</b> - api</li>
                        <li><b>Wikipedia</b> - web site</li>
                        <li><b>esrb.com</b> - web site</li>
                        <li><b>PS</b> - web site</li>
                        <li><b>Nintendo</b> - web site</li>
                        <li><b>Xbox</b> - web site</li>
                    </ol>
                    <p>
                        In order to obtain each piece of information few sources in predefined order are used:
                    </p>
                    <ul>
                        <li><b>metaScore</b>: metacritic > rawg</li>
                        <li><b>userScore</b>: metacritic</li>
                        <li><b>releaseDate</b>: rawg > steam > igbd</li>
                        <li><b>description</b>: steam > igbd > rawg</li>
                        <li><b>developer</b>: steam > igbd > rawg</li>
                        <li><b>publisher</b>: steam > igbd > rawg</li>
                        <li><b>platforms</b>: wikipedia > igbd > rawg</li>
                        <li><b>ganres</b>: wikipedia > igbd > rawg</li>
                        <li><b>esrb</b>: esrb.com > ps > rawg</li>
                        <li><b>esrbImage</b>: esrb.com > ps</li>
                        <li><b>screenshots</b>: nintendo > steam</li>
                        <li><b>thumbnail</b>: igbd > rawg > firstScreenshot</li>
                        <li><b>steam_site</b>: steam</li>
                        <li><b>nintendo_site</b>: nintendo > rawg</li>
                        <li><b>xbox_site</b>: xbox > rawg</li>
                        <li><b>ps_site</b>: ps > rawg</li>
                        <li><b>official_site</b>: igbd > rawg</li>
                    </ul>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <h5 class="m-0">2. Calendar Scrapper</h5>
                </div>
                <div class="card-body">
                    <p>
                        Calendar Scrapper is used to automate filling the Calendar.<br>
                        Scrapper is triggered automatically every midnight.<br>
                        Two sources of new game releases are used
                    </p>
                    <ol>
                        <li>IGBD - api</li>
                        <li>gamesradar - web site</li>
                    </ol>
                    <p>
                        After Game names and Release dates are scraped, Calendar Scrapper filters only new Games.<br>
                        The Game is considered new, its scraped game name is unique in the system.<br>
                        For all new games, Game Scrapper is initiated.
                    </p>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <h5 class="m-0">3. Commenting</h5>
                </div>
                <div class="card-body">
                    TODO
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <h5 class="m-0">4. Game Statuses</h5>
                </div>
                <div class="card-body">
                    <ul>
                        <li><b>Draft</b> - Game page is ONLY visible to admins. When game scrapping is active - 'Draft' status is set temporarily.</li>
                        <li><b>Published</b> - Game page is public. Url is included in sitemap and game link is shown in client side where it needed. The game will be visible on Calendar as well</li>
                        <li><b>Calendar Draft</b> - Game is not visible, same as 'Draft' status, but it was created via Calendar Scrapping.</li>
                        <li><b>Calendar Published</b> - Game do not have separate page and only visible on Calendar.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@stop
