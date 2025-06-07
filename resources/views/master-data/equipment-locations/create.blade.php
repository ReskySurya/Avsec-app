<!-- @extends('layouts.app')

@section('title', 'Tambah Relasi Equipment-Location')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Tambah Relasi Equipment-Location</h4>
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

                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form action="{{ route('equipment-locations.store') }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="equipment_id" class="form-label">Equipment <span class="text-danger">*</span></label>
                                    <select class="form-select" id="equipment_id" name="equipment_id" required>
                                        <option value="">Pilih Equipment</option>
                                        @foreach($equipments as $equipment)
                                            <option value="{{ $equipment->id }}" {{ old('equipment_id') == $equipment->id ? 'selected' : '' }}>
                                                {{ $equipment->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="form-text">Pilih equipment yang akan ditempatkan</div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="location_id" class="form-label">Location <span class="text-danger">*</span></label>
                                    <select class="form-select" id="location_id" name="location_id" required>
                                        <option value="">Pilih Location</option>
                                        @foreach($locations as $location)
                                            <option value="{{ $location->id }}" {{ old('location_id') == $location->id ? 'selected' : '' }}>
                                                {{ $location->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="form-text">Pilih location untuk penempatan equipment</div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3" 
                                      placeholder="Deskripsi tambahan untuk relasi ini (opsional)">{{ old('description') }}</textarea>
                            <div class="form-text">Informasi tambahan tentang penempatan equipment di location ini</div>
                        </div>


                        <div id="preview-section" class="mb-3" style="display: none;">
                            <div class="card bg-light">
                                <div class="card-header">
                                    <h6 class="mb-0">Preview Relasi</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-5">
                                            <div id="equipment-preview"></div>
                                        </div>
                                        <div class="col-md-2 text-center">
                                            <i class="fas fa-arrow-right fa-2x text-primary mt-3"></i>
                                        </div>
                                        <div class="col-md-5">
                                            <div id="location-preview"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('equipment-locations.index') }}" class="btn btn-secondary me-md-2">
                                <i class="fas fa-times"></i> Batal
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Simpan Relasi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Available Equipment</h5>
                </div>
                <div class="card-body">
                    @if($equipments->count() > 0)
                        <div class="list-group">
                            @foreach($equipments as $equipment)
                                <div class="list-group-item">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1">{{ $equipment->name }}</h6>
                                        <small>{{ $equipment->locations->count() }} locations</small>
                                    </div>
                                    <p class="mb-1">{{ Str::limit($equipment->description, 100) }}</p>
                                    <small class="text-muted">Created by: {{ $equipment->creator->name ?? 'Unknown' }}</small>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-3">
                            <i class="fas fa-exclamation-triangle text-warning fa-2x mb-2"></i>
                            <p class="text-muted">Belum ada equipment yang tersedia</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Available Locations</h5>
                </div>
                <div class="card-body">
                    @if($locations->count() > 0)
                        <div class="list-group">
                            @foreach($locations as $location)
                                <div class="list-group-item">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1">{{ $location->name }}</h6>
                                        <small>{{ $location->equipments->count() }} equipment</small>
                                    </div>
                                    <p class="mb-1">{{ Str::limit($location->description, 100) }}</p>
                                    <small class="text-muted">Created by: {{ $location->creator->name ?? 'Unknown' }}</small>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-3">
                            <i class="fas fa-exclamation-triangle text-warning fa-2x mb-2"></i>
                            <p class="text-muted">Belum ada location yang tersedia</p>
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
    // Initialize Select2 for better dropdown experience
    $('#equipment_id, #location_id').select2({
        theme: 'bootstrap-5',
        placeholder: function() {
            return $(this).data('placeholder');
        },
        allowClear: true
    });

    // Equipment and Location data for preview
    const equipments = @json($equipments);
    const locations = @json($locations);

    // Preview functionality
    function updatePreview() {
        const equipmentId = $('#equipment_id').val();
        const locationId = $('#location_id').val();
        
        if (equipmentId && locationId) {
            const equipment = equipments.find(e => e.id == equipmentId);
            const location = locations.find(l => l.id == locationId);
            
            if (equipment && location) {
                $('#equipment-preview').html(`
                    <div class="card border-primary">
                        <div class="card-header bg-primary text-white">
                            <h6 class="mb-0"><i class="fas fa-cog"></i> Equipment</h6>
                        </div>
                        <div class="card-body">
                            <h6>${equipment.name}</h6>
                            <p class="text-muted small">${equipment.description}</p>
                            <small class="text-muted">Current locations: ${equipment.locations ? equipment.locations.length : 0}</small>
                        </div>
                    </div>
                `);
                
                $('#location-preview').html(`
                    <div class="card border-success">
                        <div class="card-header bg-success text-white">
                            <h6 class="mb-0"><i class="fas fa-map-marker-alt"></i> Location</h6>
                        </div>
                        <div class="card-body">
                            <h6>${location.name}</h6>
                            <p class="text-muted small">${location.description}</p>
                            <small class="text-muted">Current equipment: ${location.equipments ? location.equipments.length : 0}</small>
                        </div>
                    </div>
                `);
                
                $('#preview-section').show();
            }
        } else {
            $('#preview-section').hide();
        }
    }

    // Event listeners for preview
    $('#equipment_id, #location_id').on('change', updatePreview);

    // Check for existing relation
    function checkExistingRelation() {
        const equipmentId = $('#equipment_id').val();
        const locationId = $('#location_id').val();
        
        if (equipmentId && locationId) {
            const equipment = equipments.find(e => e.id == equipmentId);
            if (equipment && equipment.locations) {
                const hasRelation = equipment.locations.some(l => l.id == locationId);
                if (hasRelation) {
                    alert('Peringatan: Equipment ini sudah memiliki relasi dengan location tersebut!');
                }
            }
        }
    }

    $('#equipment_id, #location_id').on('change', checkExistingRelation);

    // Form validation
    $('form').on('submit', function(e) {
        const equipmentId = $('#equipment_id').val();
        const locationId = $('#location_id').val();
        
        if (!equipmentId || !locationId) {
            e.preventDefault();
            alert('Mohon pilih equipment dan location!');
            return false;
        }
    });
});
</script>
@endpush

@push('styles')
<style>
.select2-container--bootstrap-5 .select2-selection {
    min-height: 38px;
}
</style>
@endpush -->