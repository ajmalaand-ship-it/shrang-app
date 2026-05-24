@extends("layouts.app")

@section("title", __("ui.credits.balance"))

@section("content")
<div class="sh-page-wrap credits-page">

    <header class="sh-section credits-page__header">
        <h1 class="sh-heading">{{ __("ui.credits.balance") }}</h1>
        <p class="credits-page__balance">
            <span class="credits-page__balance-number">{{ $balance }}</span>
            <span class="sh-text-muted">{{ __("ui.credits.unit") }}</span>
        </p>
    </header>

    @if (session("success"))
        <div class="sh-notice sh-notice--success">{{ session("success") }}</div>
    @endif

    <section class="sh-section">
        <h2 class="sh-subheading">{{ __("ui.credits.buy") }}</h2>

        @if ($packages->isEmpty())
            <p class="sh-text-muted">No credit packages available yet.</p>
        @else
            <div class="credits-page__packages">
                @foreach ($packages as $package)
                    <div class="sh-card credits-page__package">
                        <div class="sh-card__body">
                            <h3 class="credits-page__package-name">{{ $package->name }}</h3>
                            <p class="credits-page__package-credits">
                                {{ number_format($package->credits) }}
                                <span class="sh-text-muted">{{ __("ui.credits.unit") }}</span>
                            </p>
                            <p class="credits-page__package-price">
                                ${{ number_format($package->price_cents / 100, 2) }}
                                {{ $package->currency }}
                            </p>
                            <button class="sh-btn sh-btn--primary"
                                    onclick="startCheckout('{{ $package->id }}')">
                                {{ __("ui.credits.buy") }}
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </section>

    <section class="sh-section">
        <h2 class="sh-subheading">{{ __("ui.credits.history") }}</h2>
        <p class="sh-text-muted">Credit history coming in Phase 9.</p>
    </section>

</div>
@endsection

@section("page_js")
<script>
const csrfToken = document.querySelector("meta[name=csrf-token]").content;

async function startCheckout(packageId) {
    try {
        const response = await fetch("/credits/checkout", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": csrfToken,
                "Accept": "application/json",
            },
            body: JSON.stringify({ package_id: packageId }),
        });
        const data = await response.json();
        if (!response.ok) {
            alert(data.error || "Something went wrong.");
            return;
        }
        alert("Stripe checkout coming soon. Client secret: " + data.client_secret);
    } catch (err) {
        alert("Connection error. Please try again.");
    }
}
</script>
@endsection
