@extends('layouts.admin')
@section('title', 'Credit Packages')
@section('content')

<div class="sh-page-wrap">

    @if(session('success'))
        <div class="sh-notice sh-notice--success" style="margin-bottom:1.5rem;">{{ session('success') }}</div>
    @endif
    @if($errors->has('delete'))
        <div class="sh-notice sh-notice--danger" style="margin-bottom:1.5rem;">{{ $errors->first('delete') }}</div>
    @endif

    <div style="display:flex;flex-direction:column;gap:1.5rem;">

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
                            <th>Actions</th>
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
                            <td><div class="admin-packages-actions">
                                <button type="button"
                                    class="sh-btn sh-btn--sm sh-btn--ghost"
                                    onclick="openEdit({{ $package->id }}, '{{ addslashes($package->name) }}', {{ $package->credits }}, {{ $package->price_cents / 100 }}, {{ $package->sort_order }}, '{{ $package->stripe_price_id ?? '' }}')">
                                    Edit
                                </button>
                                <form method="POST" action="{{ route('admin.packages.toggle', $package) }}" style="display:inline;">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="sh-btn sh-btn--sm sh-btn--ghost">
                                        {{ $package->is_active ? 'Disable' : 'Enable' }}
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('admin.packages.destroy', $package) }}"
                                    onsubmit="return confirm('Delete {{ addslashes($package->name) }}? This cannot be undone.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="sh-btn sh-btn--sm sh-btn--danger">Delete</button>
                                </form>
                            </div></td>
                        </tr>
                        @empty
                        <tr><td colspan="6" style="text-align:center;color:var(--sh-text-muted);padding:2rem;">No packages yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Add / Edit panel --}}
        <div class="sh-card" id="package-form-card">
            <div class="sh-card__header" id="package-form-title">Add New Package</div>
            <div class="sh-card__body">

                {{-- Edit form (hidden by default) --}}
                <form method="POST" id="edit-form" style="display:none;">
                    @csrf
                    @method('PATCH')
                    <div class="sh-field">
                        <label class="sh-label">Package name</label>
                        <input type="text" name="name" id="edit-name" class="sh-input" required>
                    </div>
                    <div class="sh-field">
                        <label class="sh-label">Credits</label>
                        <input type="number" name="credits" id="edit-credits" class="sh-input" min="1" required>
                    </div>
                    <div class="sh-field">
                        <label class="sh-label">Price (USD)</label>
                        <input type="number" name="price_dollars" id="edit-price" class="sh-input" min="0.01" step="0.01" placeholder="e.g. 4.99" required>
                    </div>
                    <div class="sh-field">
                        <label class="sh-label">Sort order (1 = first)</label>
                        <input type="number" name="sort_order" id="edit-sort" class="sh-input" required>
                    </div>
                    <div class="sh-field">
                        <label class="sh-label">Stripe Price ID <span style="color:var(--sh-text-muted);font-size:0.8rem;">(optional)</span></label>
                        <input type="text" name="stripe_price_id" id="edit-stripe" class="sh-input" placeholder="price_1ABC...">
                    </div>
                    <div style="display:flex;gap:0.75rem;">
                        <button type="submit" class="sh-btn sh-btn--primary">Save Changes</button>
                        <button type="button" class="sh-btn sh-btn--ghost" onclick="cancelEdit()">Cancel</button>
                    </div>
                </form>

                {{-- Create form --}}
                <form method="POST" action="{{ route('admin.packages.store') }}" id="create-form">
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
                        <label class="sh-label">Price (USD)</label>
                        <input type="number" name="price_dollars" class="sh-input" placeholder="e.g. 4.99" min="0.01" step="0.01" required>
                    </div>
                    <div class="sh-field">
                        <label class="sh-label">Currency</label>
                        <input type="text" name="currency" class="sh-input" value="USD" maxlength="3" required>
                    </div>
                    <div class="sh-field">
                        <label class="sh-label">Sort order (1 = first)</label>
                        <input type="number" name="sort_order" class="sh-input" value="{{ $packages->count() + 1 }}" required>
                    </div>
                    <div class="sh-field">
                        <label class="sh-label">Stripe Price ID <span style="color:var(--sh-text-muted);font-size:0.8rem;">(optional)</span></label>
                        <input type="text" name="stripe_price_id" class="sh-input" placeholder="price_1ABC...">
                    </div>
                    <button type="submit" class="sh-btn sh-btn--primary">Create Package</button>
                </form>

            </div>
        </div>

    </div>
</div>

<script>
function openEdit(id, name, credits, price, sort, stripeId) {
    document.getElementById('create-form').style.display = 'none';
    document.getElementById('edit-form').style.display = 'block';
    document.getElementById('package-form-title').textContent = 'Edit Package';
    document.getElementById('edit-form').action = '/admin/packages/' + id;
    document.getElementById('edit-name').value = name;
    document.getElementById('edit-credits').value = credits;
    document.getElementById('edit-price').value = price;
    document.getElementById('edit-sort').value = sort;
    document.getElementById('edit-stripe').value = stripeId;
    document.getElementById('package-form-card').scrollIntoView({behavior: 'smooth'});
}
function cancelEdit() {
    document.getElementById('edit-form').style.display = 'none';
    document.getElementById('create-form').style.display = 'block';
    document.getElementById('package-form-title').textContent = 'Add New Package';
}
</script>
@endsection
