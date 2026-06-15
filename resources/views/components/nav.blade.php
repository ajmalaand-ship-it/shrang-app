<nav class="sh-nav" id="sh-nav">
    <div class="sh-nav__inner">
        <a href="/" class="sh-nav__logo">
            <img src="/favicons/logo-mark.png" srcset="/favicons/logo-mark.png 1x, /favicons/logo-mark-2x.png 2x" alt="Shrang" class="sh-nav__logo-icon" width="34" height="34">
            @if(($locale ?? 'en') === 'ps')
                <span class="sh-nav__logo-text sh-nav__logo-text--ar">شرنګ</span>
            @else
                <span class="sh-nav__logo-text sh-nav__logo-text--en">Shrang</span>
            @endif
        </a>
        <ul class="sh-nav__links">
            @auth
                <li><a href="{{ route('create') }}" class="{{ request()->routeIs('create*') ? 'active' : '' }}">Create</a></li>
                <li><a href="{{ route('discover') }}" class="{{ request()->routeIs('discover*') ? 'active' : '' }}">Discover</a></li>
                <li><a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">My Clips</a></li>
            @else
                <li><a href="{{ route('how-it-works') }}" class="{{ request()->routeIs('how-it-works') ? 'active' : '' }}">How it works</a></li>
                <li><a href="{{ route('discover') }}" class="{{ request()->routeIs('discover*') ? 'active' : '' }}">Discover</a></li>
                <li><a href="{{ route('pricing') }}" class="{{ request()->routeIs('pricing') ? 'active' : '' }}">Pricing</a></li>
            @endauth
        </ul>
        <div class="sh-nav__right">
            <div class="sh-nav__lang" id="sh-lang-wrap">
                <button class="sh-nav__lang-btn" id="sh-lang-btn" aria-label="Language">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M2 12h20M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                    <span>@switch($locale ?? 'en')
@case('ps') پښتو @break
@case('fa') دری @break
@case('ur') اردو @break
@case('ar') العربية @break
@case('hi') हिन्दी @break
@default EN
@endswitch</span>
                </button>
                <div class="sh-nav__lang-drop" id="sh-lang-drop">
                    <a href="{{ route('lang.switch', 'en') }}" class="{{ ($locale ?? 'en') === 'en' ? 'active' : '' }}">English</a>
                    <a href="{{ route('lang.switch', 'ps') }}" class="{{ ($locale ?? '') === 'ps' ? 'active' : '' }}">پښتو</a>
                    <a href="{{ route('lang.switch', 'fa') }}" class="{{ ($locale ?? '') === 'fa' ? 'active' : '' }}">دری</a>
                    <a href="{{ route('lang.switch', 'ur') }}" class="{{ ($locale ?? '') === 'ur' ? 'active' : '' }}">اردو</a>
                    <a href="{{ route('lang.switch', 'ar') }}" class="{{ ($locale ?? '') === 'ar' ? 'active' : '' }}">العربية</a>
                    <a href="{{ route('lang.switch', 'hi') }}" class="{{ ($locale ?? '') === 'hi' ? 'active' : '' }}">हिन्दी</a>
                </div>
            </div>
            @auth
                <a href="{{ route('credits') }}" class="sh-nav__credits-pill">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    <strong>{{ auth()->user()->credit_balance }}</strong>
                </a>
                <div class="sh-nav__avatar-wrap" id="sh-avatar-wrap">
                    <button class="sh-nav__avatar-btn" id="sh-avatar-btn" aria-label="Account">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </button>
                    <div class="sh-nav__avatar-drop" id="sh-avatar-drop">
                        <div class="sh-nav__drop-header">
                            <strong>{{ auth()->user()->name }}</strong>
                            <span>{{ auth()->user()->credit_balance }} credits</span>
                        </div>
                        <div class="sh-nav__drop-divider"></div>
                        <a href="{{ route('dashboard') }}">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg>
                            My Clips
                        </a>
                        <a href="{{ route('credits') }}">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                            Credits & Billing
                        </a>
                        <a href="{{ route('account') }}">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                            Account Settings
                        </a>
                        <div class="sh-nav__drop-divider"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                                Log out
                            </button>
                        </form>
                    </div>
                </div>
            @else
                <a href="{{ route('login') }}" class="sh-btn sh-btn--ghost sh-btn--sm sh-nav__desktop-only">Log in</a>
                <a href="{{ route('register') }}" class="sh-btn sh-btn--primary sh-btn--sm sh-nav__desktop-only">Start Free</a>
            @endauth
            <button class="sh-nav__burger" id="sh-burger" aria-label="Menu">
                <span></span><span></span><span></span>
            </button>
        </div>
    </div>
</nav>

<div class="sh-nav__drawer" id="sh-drawer">
    <div class="sh-nav__drawer-inner">
        @auth
            <div class="sh-nav__drawer-user">
                <span class="sh-nav__drawer-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                <div class="sh-nav__drawer-user-info">
                    <strong>{{ auth()->user()->name }}</strong>
                    <span>{{ auth()->user()->credit_balance }} credits</span>
                </div>
            </div>
            <div class="sh-nav__drop-divider"></div>
            <a href="{{ route('create') }}" class="sh-nav__drawer-link {{ request()->routeIs('create*') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
                Create
            </a>
            <a href="{{ route('discover') }}" class="sh-nav__drawer-link {{ request()->routeIs('discover*') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                Discover
            </a>
            <a href="{{ route('dashboard') }}" class="sh-nav__drawer-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg>
                My Clips
            </a>
            <a href="{{ route('credits') }}" class="sh-nav__drawer-link {{ request()->routeIs('credits*') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                Credits & Billing
            </a>
            <a href="{{ route('account') }}" class="sh-nav__drawer-link {{ request()->routeIs('account*') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                Account
            </a>
            <div class="sh-nav__drop-divider"></div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="sh-nav__drawer-logout">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                    Log out
                </button>
            </form>
        @else
            <a href="{{ route('how-it-works') }}" class="sh-nav__drawer-link">How it works</a>
            <a href="{{ route('discover') }}" class="sh-nav__drawer-link">Discover</a>
            <a href="{{ route('pricing') }}" class="sh-nav__drawer-link">Pricing</a>
            <a href="{{ route('faq') }}" class="sh-nav__drawer-link">FAQ</a>
            <div class="sh-nav__drop-divider"></div>
            <a href="{{ route('login') }}" class="sh-nav__drawer-link">Log in</a>
            <a href="{{ route('register') }}" class="sh-btn sh-btn--primary sh-btn--sm" style="margin-top:0.5rem;">Start Free</a>
        @endauth
        <div class="sh-nav__drop-divider"></div>
        <div class="sh-nav__drawer-langs">
            <a href="{{ route('lang.switch', 'en') }}" class="{{ ($locale ?? 'en') === 'en' ? 'active' : '' }}">English</a>
            <a href="{{ route('lang.switch', 'ps') }}" class="{{ ($locale ?? '') === 'ps' ? 'active' : '' }}">پښتو</a>
            <a href="{{ route('lang.switch', 'fa') }}" class="{{ ($locale ?? '') === 'fa' ? 'active' : '' }}">دری</a>
            <a href="{{ route('lang.switch', 'ur') }}" class="{{ ($locale ?? '') === 'ur' ? 'active' : '' }}">اردو</a>
            <a href="{{ route('lang.switch', 'ar') }}" class="{{ ($locale ?? '') === 'ar' ? 'active' : '' }}">العربية</a>
            <a href="{{ route('lang.switch', 'hi') }}" class="{{ ($locale ?? '') === 'hi' ? 'active' : '' }}">हिन्दी</a>
        </div>
    </div>
</div>
<div class="sh-nav__overlay" id="sh-overlay"></div>

<script>
(function(){
    var burger=document.getElementById('sh-burger');
    var drawer=document.getElementById('sh-drawer');
    var overlay=document.getElementById('sh-overlay');
    var langBtn=document.getElementById('sh-lang-btn');
    var langDrop=document.getElementById('sh-lang-drop');
    var avatarBtn=document.getElementById('sh-avatar-btn');
    var avatarDrop=document.getElementById('sh-avatar-drop');
    function closeAll(){
        if(langDrop) langDrop.classList.remove('open');
        if(avatarDrop) avatarDrop.classList.remove('open');
    }
    function closeDrawer(){
        if(drawer) drawer.classList.remove('open');
        if(overlay) overlay.classList.remove('open');
        if(burger) burger.classList.remove('open');
    }
    if(burger) burger.addEventListener('click',function(e){
        e.stopPropagation();
        drawer.classList.toggle('open');
        overlay.classList.toggle('open');
        burger.classList.toggle('open');
    });
    if(overlay) overlay.addEventListener('click', closeDrawer);
    if(langBtn) langBtn.addEventListener('click',function(e){
        e.stopPropagation();
        closeAll();
        langDrop.classList.toggle('open');
    });
    if(avatarBtn) avatarBtn.addEventListener('click',function(e){
        e.stopPropagation();
        closeAll();
        avatarDrop.classList.toggle('open');
    });
    document.addEventListener('click', closeAll);
})();
</script>
