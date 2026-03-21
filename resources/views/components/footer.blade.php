
<footer class="site-footer" aria-label="Site footer">
    <nav class="footer-links" aria-label="Footer navigation">
    <a class="footer-link" href="{{ route('about-us') }}">About</a>
    <a class="footer-link" href="{{ route('privacy') }}">Privacy Policy</a>
    <a class="footer-link" href="{{ route('terms') }}">Terms of Service</a>
    <a class="footer-link" href="{{ route('cookiePolicy') }}">Cookie Policy</a>
    </nav>
    <p class="footer-copy">&copy; {{ date('Y') }} {{ $footer?->show('site-name', 'BSCNews') }}</p>
</footer>
