<!-- @extends('layouts.app')

@section('title', 'Detail Equipment - ' . $equipment->name)

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="mb-0">Detail Equipment: {{ $equipment->name }}</h3>
                    <a href="{{ route('equipment-locations.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <h4>Informasi Equipment</h4>
                            <table class="table table-borderless">
                                <tr>
                                    <td width="150"><strong>Nama:</strong></td>
                                    <td>{{ $equipment->name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Deskripsi:</strong></td>
                                    <td>{{ $equipment->description ?: '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Dibuat oleh:</strong></td>
                                    <td>{{ $equipment->creator->name ?? 'Unknown' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Tanggal dibuat:</strong></td>
                                    <td>{{ $equipment->created_at->format('d F Y, H:i') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Update terakhir:</strong></td>
                                    <td>{{ $equipment->updated_at->format('d F Y, H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <i class="fas fa-cog fa-4x text-primary mb-3"></i>
                                    <h5>{{ $equipment->name }}</h5>
                                    <span class="badge bg-info">{{ $equipment->locations->count() }} Locations</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Locations untuk Equipment ini</h4>
                </div>
                <div class="card-body">
                    @if($equipment->locations->count() > 0)
                        <div class="row">
                            @foreach($equipment->locations as $location)
                                <div class="col-md-6 col-lg-4 mb-3">
                                    <div class="card border-success">
                                        <div class="card-header bg-success text-white">
                                            <h6 class="mb-0">
                                                <i class="fas fa-map-marker-alt"></i> {{ $location->name }}
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            <p class="card-text">{{ Str::limit($location->description, 100) }}</p>
                                            
                                            @if($location->pivot->description)
                                                <div class="alert alert-info">
                                                    <small><strong>Catatan:</strong> {{ $location->pivot->description }}</small>
                                                </div>
                                            @endif
                                            
                                            <div class="text-muted small">
                                                <div><strong>Dibuat oleh:</strong> {{ $location->creator->name ?? 'Unknown' }}</div>
                                                <div><strong>Relasi dibuat:</strong> {{ $location->pivot->created_at->format('d/m/Y H:i') }}</div>
                                                @if($location->pivot->updated_at != $location->pivot->created_at)
                                                    <div><strong>Relasi diupdate:</strong> {{ $location->pivot->updated_at->format('d/m/Y H:i') }}</div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="card-footer">
                                            <div class="btn-group w-100" role="group">
                                                <a href="{{ route('equipment-locations.show-location', $location->id) }}" 
                                                   class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i> Detail
                                                </a>
                                                <a href="{{ route('equipment-locations.edit', [$equipment->id, $location->id]) }}" 
                                                   class="btn btn-sm btn-warning">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                                <form action="{{ route('equipment-locations.destroy', [$equipment->id, $location->id]) }}" 
                                                      method="POST" style="display: inline;"
                                                      onsubmit="return confirm('Yakin ingin menghapus relasi dengan {{ $location->name }}?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger">
                                                        <i class="fas fa-unlink"></i> Hapus
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-map-marker-alt fa-4x text-muted mb-3"></i>
                            <h5 class="text-muted">Equipment ini belum ditempatkan di location manapun</h5>
                            <p class="text-muted">Tambahkan location untuk equipment ini dengan membuat relasi baru.</p>
                            <a href="{{ route('equipment-locations.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Tambah ke Location
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4>{{ $equipment->locations->count() }}</h4>
                            <p class="mb-0">Total Locations</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-map-marker-alt fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4>{{ $equipment->locations->sum(function($loc) { return $loc->equipments->count(); }) }}</h4>
                            <p class="mb-0">Total Equipment di Locations ini</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-cogs fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Confirmation for delete actions
    $('form[method="POST"]').on('submit', function(e) {
        const form = $(this);
        if (form.find('input[name="_method"][value="DELETE"]').length > 0) {
            if (!confirm('Apakah Anda yakin ingin menghapus relasi ini?')) {
                e.preventDefault();
                return false;
            }
        }
    });
});
</script>
@endpush -->