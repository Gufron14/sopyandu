@php $currentUrl = url()->current() @endphp

<div style="width: 11rem">
    <div class="input-group">
        <label class="input-group-prepend" for="role">
            <span class="input-group-text bg-light">Saring</span>
        </label>
        <select class="custom-select" id="role" onchange="window.location.href = this.value;">
            <option value="{{ url('/officer-data') }}" {{ $currentUrl == url('/officer-data') ? 'selected' : '' }}>Semua
            </option>
            <option value="{{ url('/admin/officer-data') }}"
                {{ $currentUrl == url('/admin/officer-data') ? 'selected' : '' }}>Admin</option>
            <option value="{{ url('/midwife/officer-data') }}"
                {{ $currentUrl == url('/midwife/officer-data') ? 'selected' : '' }}>Bidan</option>
            <option value="{{ url('/officer/officer-data') }}"
                {{ $currentUrl == url('/officer/officer-data') ? 'selected' : '' }}>Petugas</option>
        </select>
    </div>
</div>
