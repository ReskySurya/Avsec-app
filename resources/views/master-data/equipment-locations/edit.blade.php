@extends('layouts.app')

@section('title', 'Edit Relasi Equipment-Location')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Edit Relasi Equipment-Location</h4>
                    <a href="{{ route('equipment-locations.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>
                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Current Relation Info -->
                    <div class="card bg-light mb-4">
                        <div class="card-header">
                            <h6 class="mb-0">Relasi yang akan diedit</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-5">
                                    <div class="card border-primary">
                                        <div class="card-header bg-primary text-white">
                                            <h6 class="mb-0"><i class="fas fa-cog"></i> Equipment</h6>
                                        </div>
                                        <div class="card-body">
                                            <h6>{{ $equipment->name }}</h6>
                                            <p class="text-muted small mb-1">{{ $equipment->description }}</p>
                                            <small class="text-muted">Created by: {{ $equipment->creator->name ?? 'Unknown' }}</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2 text-center">
                                    <i class="fas fa-link fa-3x text-success mt-4"></i>
                                </div>
                                <div class="col-md-5">
                                    <div class="card border-success">
                                        <div class="card-header bg-success text-white">
                                            <h6 class="mb-0"><i class="fas fa-map-marker-alt"></i> Location</h6>
                                        </div>
                                        <div class="card-body">
                                            <h6>{{ $location->name }}</h6>
                                            <p class="text-muted small mb-1">{{ $location->description }}</p>
                                            <small class="text-muted">Created by: {{ $location->creator->name ?? 'Unknown' }}</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Edit Form -->
                    <form action="{{ route('equipment-locations.update', [$equipment->id, $location->id]) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-4">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="4" 
                                      placeholder="Masukkan deskripsi untuk relasi ini...">{{ old('description', $pivotData->pivot->description) }}</textarea>
                            <div class="form-text">
                                Deskripsi ini akan menjelaskan detail tentang penempatan equipment di location tersebut.
                            </div>
                        </div>

                        <!-- Relation History -->
                        <div class="card border-info mb-4">
                            <div class="card-header bg-info text-white">
                                <h6 class="mb-0"><i class="fas fa-history"></i> Riwayat Relasi</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <strong>Relasi dibuat:</strong><br>
                                        <span class="text-muted">{{ $pivotData->pivot->created_at->format('d F Y, H:i:s') }}</span>
                                    </div>
                                    <div class="col-md-6">
                                        <strong>Terakhir diupdate:</strong><br>
                                        <span class="text-muted">{{ $pivotData->pivot->updated_at->format('d F Y, H:i:s') }}</span>
                                    </div>
                                </div>
                                @if($pivotData->pivot->description)
                                    <hr>
                                    <div>
                                        <strong>Deskripsi saat ini:</strong><br>
                                        <div class="alert alert-secondary">
                                            {{ $pivotData->pivot->description }}
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('equipment-locations.show-equipment', $equipment->id) }}" class="btn btn-info me-md-2">
                                <i class="fas fa-eye"></i> Lihat Equipment
                            </a>
                            <a href="{{ route('equipment-locations.show-location', $location->id) }}" class="btn btn-success me-md-2">
                                <i class="fas fa-eye"></i> Lihat Location
                            </a>
                            <a href="{{ route('equipment-locations.index') }}" class="btn btn-secondary me-md-2">
                                <i class="fas fa-times"></i> Batal
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Relasi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Information -->
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Equipment Locations Lainnya</h5>
                </div>
                <div class="card-body">
                    @php
                        $otherLocations = $equipment->locations->where('id', '!=', $location->id);
                    @endphp
                    
                    @if($otherLocations->count() > 0)
                        <div class="list-group">
                            @foreach($otherLocations as $otherLocation)
                                <div class="list-group-item">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1">{{ $otherLocation->name }}</h6>
                                        <small>{{ $otherLocation->pivot->created_at->format('d/m/Y') }}</small>
                                    </div>
                                    @if($otherLocation->pivot->description)
                                        <p class="mb-1">{{ $otherLocation->pivot->description }}</p>
                                    @endif
                                    <small class="text-muted">{{ Str::limit($otherLocation->description, 60) }}</small>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-3">
                            <i class="fas fa-info-circle text-info fa-2x mb-2"></i>
                            <p class="text-muted">Equipment ini hanya ada di location yang sedang diedit</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Equipment Lain di Location ini</h5>
                </div>
                <div class="card-body">
                    @php
                        $otherEquipments = $location->equipments->where('id', '!=', $equipment->id);
                    @endphp
                    
                    @if($otherEquipments->count() > 0)
                        <div class="list-group">
                            @foreach($otherEquipments as $otherEquipment)
                                <div class="list-group-item">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1">{{ $otherEquipment->name }}</h6>
                                        <small>{{ $otherEquipment->pivot->created_at->format('d/m/Y') }}</small>
                                    </div>
                                    @if($otherEquipment->pivot->description)
                                        <p class="mb-1">{{ $otherEquipment->pivot->description }}</p>
                                    @endif
                                    <small class="text-muted">{{ Str::limit($otherEquipment->description, 60) }}</small>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-3">
                            <i class="fas fa-info-circle text-info fa-2x mb-2"></i>
                            <p class="text-muted">Hanya equipment ini yang ada di location tersebut</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Auto-save draft functionality
    let saveTimeout;
    const originalDescription = $('#description').val();
    
    $('#description').on('input', function() {
        clearTimeout(saveTimeout);
        const currentVal = $(this).val();
        
        // Show indicator that changes are being made
        if (currentVal !== originalDescription) {
            if (!$('.draft-indicator').length) {
                $(this).after('<small class="draft-indicator text-warning"><i class="fas fa-edit"></i> Unsaved changes</small>');
            }
        } else {
            $('.draft-indicator').remove();
        }
        
        // Auto-save after 2 seconds of no typing
        saveTimeout = setTimeout(function() {
            // Here you could implement auto-save to temporary storage
            console.log('Auto-save triggered');
        }, 2000);
    });

    // Character counter
    const maxLength = 500;
    $('#description').after(`<div class="form-text" id="char-counter">0 / ${maxLength} characters</div>`);
    
    $('#description').on('input', function() {
        const length = $(this).val().length;
        $('#char-counter').text(`${length} / ${maxLength} characters`);
        
        if (length > maxLength * 0.9) {
            $('#char-counter').removeClass('text-muted').addClass('text-warning');
        } else if (length > maxLength) {
            $('#char-counter').removeClass('text-warning').addClass('text-danger');
        } else {
            $('#char-counter').removeClass('text-warning text-danger').addClass('text-muted');
        }
    }).trigger('input');

    // Form validation
    $('form').on('submit', function(e) {
        const description = $('#description').val().trim();
        
        if (description.length > maxLength) {
            e.preventDefault();
            alert('Deskripsi terlalu panjang! Maksimal ' + maxLength + ' karakter.');
            $('#description').focus();
            return false;
        }
        
        // Remove draft indicator
        $('.draft-indicator').remove();
    });

    // Warn about unsaved changes
    window.addEventListener('beforeunload', function(e) {
        if ($('.draft-indicator').length > 0) {
            e.preventDefault();
            e.returnValue = '';
        }
    });
});
</script>
@endpush