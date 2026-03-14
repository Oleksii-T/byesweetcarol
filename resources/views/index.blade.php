@extends('layouts.app')

@section('content')
    <div class="page">
      <section class="hero" aria-label="Introduction">
        <h1 class="hero-title">
          <span class="primary">Fresh game news for players across PC, PlayStation, Xbox, and Switch.</span>
          <span class="muted">
            Daily coverage on launches, patches, studio moves, showcases, and hardware updates.
            Top stories below.
          </span>
        </h1>
      </section>

      <main class="content-grid">
        <section class="work-columns" aria-label="Top stories">
          <article class="category-column">
            <h2 class="section-label">Latest Releases</h2>
            <div class="project-list">
              <a class="project-card" href="#" aria-label="Ghostwire Tokyo DLC launch story">
                <div class="project-media-wrap">
                  <img
                    class="project-media"
                    src="https://images.unsplash.com/photo-1518770660439-4636190af475?auto=format&fit=crop&w=1280&q=80"
                    alt="Stylized promotional artwork for a game release"
                    loading="lazy"
                  />
                </div>
                <div class="project-body">
                  <h3 class="project-title">Ghostwire: Tokyo Story DLC Launches</h3>
                  <p class="project-description">The new chapter adds side quests, challenge arenas, and expanded photo mode tools.</p>
                </div>
              </a>

              <a class="project-card" href="#" aria-label="Half Light Chronicle indie spotlight story">
                <div class="project-media-wrap">
                  <img
                    class="project-media"
                    src="https://images.unsplash.com/photo-1519682577862-22b62b24e493?auto=format&fit=crop&w=1280&q=80"
                    alt="Concept art boards and game visual development materials"
                    loading="lazy"
                  />
                </div>
                <div class="project-body">
                  <h3 class="project-title">Indie Spotlight: Half-Light Chronicle</h3>
                  <p class="project-description">A hand-drawn metroidvania climbs the charts after strong launch week player reviews.</p>
                </div>
              </a>

              <a class="project-card" href="#" aria-label="Apex Legends season patch notes story">
                <div class="project-media-wrap">
                  <img
                    class="project-media"
                    src="https://images.unsplash.com/photo-1475721027785-f74eccf877e2?auto=format&fit=crop&w=1280&q=80"
                    alt="Stage lights and esports event atmosphere"
                    loading="lazy"
                  />
                </div>
                <div class="project-body">
                  <h3 class="project-title">Apex Legends Season 25 Patch Notes</h3>
                  <p class="project-description">Respawn tunes ranked progression, legend balance, and controller aim-response settings.</p>
                </div>
              </a>
            </div>
          </article>

          <article class="category-column">
            <h2 class="section-label">Industry &amp; Studios</h2>
            <div class="project-list">
              <a class="project-card" href="#" aria-label="Bandai Namco studio expansion story">
                <div class="project-media-wrap">
                  <img
                    class="project-media"
                    src="https://images.unsplash.com/photo-1570459027562-4a916cc6113f?auto=format&fit=crop&w=1280&q=80"
                    alt="Monochrome design visual used for an industry report"
                    loading="lazy"
                  />
                </div>
                <div class="project-body">
                  <h3 class="project-title">Bandai Namco Expands Tokyo Studio</h3>
                  <p class="project-description">A new cross-platform division will focus on live service operations and tooling.</p>
                </div>
              </a>

              <a class="project-card" href="#" aria-label="Sony showcase announcement story">
                <div class="project-media-wrap">
                  <img
                    class="project-media"
                    src="https://images.unsplash.com/photo-1515405295579-ba7b45403062?auto=format&fit=crop&w=1280&q=80"
                    alt="Abstract red scene used in game event announcement coverage"
                    loading="lazy"
                  />
                </div>
                <div class="project-body">
                  <h3 class="project-title">Sony Confirms Spring Showcase Date</h3>
                  <p class="project-description">PlayStation teases first-party reveals and a slate of major partner updates.</p>
                </div>
              </a>

              <a class="project-card" href="#" aria-label="Square Enix mobile reboot strategy story">
                <div class="project-media-wrap">
                  <img
                    class="project-media"
                    src="https://images.unsplash.com/photo-1460661419201-fd4cecdf8a8b?auto=format&fit=crop&w=1280&q=80"
                    alt="Atmospheric image used to illustrate studio strategy news"
                    loading="lazy"
                  />
                </div>
                <div class="project-body">
                  <h3 class="project-title">Square Enix Details Mobile Reboot Plan</h3>
                  <p class="project-description">Leadership targets fewer launches, stronger retention, and longer live support.</p>
                </div>
              </a>
            </div>
          </article>

          <article class="category-column">
            <h2 class="section-label">Hardware &amp; Platform</h2>
            <div class="project-list">
              <a class="project-card" href="#" aria-label="Next gen handheld benchmark roundup story">
                <div class="project-media-wrap">
                  <img
                    class="project-media"
                    src="https://images.unsplash.com/photo-1512941937669-90a1b58e7e9c?auto=format&fit=crop&w=1280&q=80"
                    alt="Gaming handheld or phone screen showing interface"
                    loading="lazy"
                  />
                </div>
                <div class="project-body">
                  <h3 class="project-title">Next-Gen Handheld Leak Roundup</h3>
                  <p class="project-description">Early benchmark listings hint at better battery life and stable 1080p performance.</p>
                </div>
              </a>

              <a class="project-card" href="#" aria-label="Xbox dashboard update story">
                <div class="project-media-wrap">
                  <img
                    class="project-media"
                    src="https://images.unsplash.com/photo-1545239351-1141bd82e8a6?auto=format&fit=crop&w=1280&q=80"
                    alt="Vibrant UI-inspired abstract image for platform update article"
                    loading="lazy"
                  />
                </div>
                <div class="project-body">
                  <h3 class="project-title">Xbox Dashboard Update Rolls Out</h3>
                  <p class="project-description">Microsoft adds cleaner library filters, quick resume cards, and faster cloud access.</p>
                </div>
              </a>

              <a class="project-card" href="#" aria-label="PS5 Pro rumor credibility analysis story">
                <div class="project-media-wrap">
                  <img
                    class="project-media"
                    src="https://images.unsplash.com/photo-1461749280684-dccba630e2f6?auto=format&fit=crop&w=1280&q=80"
                    alt="Code and hardware screen used for analysis article"
                    loading="lazy"
                  />
                </div>
                <div class="project-body">
                  <h3 class="project-title">PS5 Pro Rumors: What Looks Credible</h3>
                  <p class="project-description">We break down confirmed signals versus speculation around specs and timing.</p>
                </div>
              </a>
            </div>
          </article>
        </section>

        <aside class="recognition" aria-label="Trending topics">
          <h2 class="section-label">Trending Topics</h2>
          <div class="award-list">
            <span class="award-badge" tabindex="0">Elden Ring Nightreign</span>
            <span class="award-badge" tabindex="0">GTA VI</span>
            <span class="award-badge" tabindex="0">Monster Hunter Wilds</span>
            <span class="award-badge" tabindex="0">Switch 2</span>
            <span class="award-badge" tabindex="0">Steam Deck OLED</span>
            <span class="award-badge" tabindex="0">PlayStation Showcase</span>
            <span class="award-badge" tabindex="0">Game Pass</span>
            <span class="award-badge" tabindex="0">Unreal Engine 5</span>
            <span class="award-badge" tabindex="0">Tokyo Game Show</span>
          </div>
        </aside>
      </main>
    </div>
@endsection
