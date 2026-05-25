@extends('layouts.admin')
@section('title', 'Credit Packages')
@section('content')

<div class="sh-grid" style="grid-template-columns:1fr 380px;gap:1.5rem;align-items:start;">

    {{-- Package list --}}
    <div class="sh-card">
        <div class="sh-card__header">
            Credit Packages
            <span class="sh-badge">{{ $packages->count() }}</span>
        </div>
        <div class="sh-card__body" style="padding:0;">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Credits</th>
                        <th>Price</th>
                        <th>Order</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($packages as $package)
                    <tr>
                        <td style="font-weight:500;">{{ $package->name }}</td>
                        <td>{{ number_format($package->credits) }}</td>
                        <td>${{ number_format($package->price_cents / 100, 2) }}</td>
                        <td>{{ $package->sort_order }}</td>
                        <td>
                            <span class="sh-badge {{ $package->is_active ? 'sh-badge--status-ready' : 'sh-badge--status-failed' }}">
                                {{ $package->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td>
                            <form method="POST" action="{{ route('admin.packages.toggle', $package) }}" style="display:inline;">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="sh-btn sh-btn--sm sh-btn--ghost">
                                    {{ $package->is_active ? 'Disable' : 'Enable' }}
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" style="text-align:center;color:var(--sh-text-muted);padding:2rem;">No packages yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Add new package --}}
    <div class="sh-card">
        <div class="sh-card__header">Add New Package</div>
        <div class="sh-card__body">
            <form method="POST" action="{{ route('admin.packages.store') }}">
                @csrf
                <div class="sh-field">
                    <label class="sh-label">Package name</label>
                    <input type="text" name="name" class="sh-input" placeholder="e.g. Starter" required>
                </div>
                <div class="sh-field">
                    <label class="sh-label">Credits</label>
                    <input type="number" name="credits" class="sh-input" placeholder="e.g. 50" min="1" required>
                </div>
                <div class="sh-field">
                    <label class="sh-label">Price in cents (e.g. 499 = $4.99)</label>
                    <input type="number" name="price_cents" class="sh-input" placeholder="e.g. 499" min="1" required>
                </div>
                <div class="sh-field">
                    <label class="sh-label">Currency</label>
                    <input type="text" name="currency" class="sh-input" value="USD" maxlength="3" required>
                </div>
                <div class="sh-field">
                    <label class="sh-label">Sort order (1 = first)</label>
                    <input type="number" name="sort_order" class="sh-input" value="{{ $packages->count() + 1 }}" required>
                </div>
                <button type="submit" class="sh-btn sh-btn--primary">Create Package</button>
            </form>
        </div>
    </div>

</div>
@endsection
