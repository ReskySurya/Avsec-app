<!-- @extends('layouts.app')

@section('title', 'Detail Location - ' . $location->name)

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="mb-0">Detail Location: {{ $location->name }}</h3>
                    <a href="{{ route('equipment-locations.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <h4>Informasi Location</h4>
                            <table class="table table-borderless">
                                <tr>
                                    <td width="150"><strong>Nama:</strong></td>
                                    <td>{{ $location->name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Deskripsi:</strong></td>
                                    <td>{{ $location->description ?: '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Dibuat oleh:</strong></td>
                                    <td>{{ $location->creator->name ?? 'Unknown' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Tanggal dibuat:</strong></td>
                                    <td>{{ $location->created_at->format('d F Y, H:i') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Update terakhir:</strong></td>
                                    <td>{{ $location->updated_at->format('d F Y, H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <i class="fas fa-map-marker-alt fa-4x text-success mb-3"></i>
                                    <h5>{{ $location->name }}</h5>
                                    <span class="badge bg-success">{{ $location->equipments->count() }} Equipment</span>
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
                    <h4 class="mb-0">Equipment di Location ini</h4>
                </div>
                <div class="card-body">
                    @if($location->equipments->count() > 0)
                        <div class="row">
                            @foreach($location->equipments as $equipment)
                                <div class="col-md-6 col-lg-4 mb-3">
                                    <div class="card border-primary">
                                        <div class="card-header bg-primary text-white">
                                            <h6 class="mb-0">
                                                <i class="fas fa-cog"></i> {{ $equipment->name }}
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            <p class="card-text">{{ Str::limit($equipment->description, 100) }}</p>
                                            
                                            @if($equipment->pivot->description)
                                                <div class="alert alert-info">
                                                    <small><strong>Catatan:</strong> {{ $equipment->pivot->description }}</small>
                                                </div>
                                            @endif
                                            
                                            <div class="text-muted small">
                                                <div><strong>Dibuat oleh:</strong> {{ $equipment->creator->name ?? 'Unknown' }}</div>
                                                <div><strong>Relasi dibuat:</strong> {{ $equipment->pivot->created_at->format('d/m/Y H:i') }}</div>
                                                @if($equipment->pivot->updated_at != $equipment->pivot->created_at)
                                                    <div><strong>Relasi diupdate:</strong> {{ $equipment->pivot->updated_at->format('d/m/Y H:i') }}</div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="card-footer">
                                            <div class="btn-group w-100" role="group">
                                                <a href="{{ route('equipment-locations.show-equipment', $equipment->id) }}" 
                                                   class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i> Detail
                                                </a>
                                                <a href="{{ route('equipment-locations.edit', [$equipment->id, $location->id]) }}" 
                                                   class="btn btn-sm btn-warning">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                                <form action="{{ route('equipment-locations.destroy', [$equipment->id, $location->id]) }}" 
                                                      method="POST" style="display: inline;"
                                                      onsubmit="return confirm('Yakin ingin menghapus {{ $equipment->name }} dari location ini?')">
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
                            <i class="fas fa-cog fa-4x text-muted mb-3"></i>
                            <h5 class="text-muted">Belum ada equipment di location ini</h5>
                            <p class="text-muted">Tambahkan equipment ke location ini dengan membuat relasi baru.</p>
                            <a href="{{ route('equipment-locations.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Tambah Equipment
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    
    @if($location->equipments->count() > 0)
        <div class="row mt-4">
            <div class="col-12">
                <div class="card"></div> -->