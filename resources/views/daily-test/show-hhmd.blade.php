@extends('layouts.app')

@section('title', 'Detail Laporan HHMD')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-6 lg:mt-20">
    <div class="bg-white rounded-lg shadow-md p-6">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-900">
                Detail Laporan HHMD
            </h1>
            <div>
                <a href="{{ url()->previous() }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                    <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Kembali
                </a>
            </div>
        </div>

        <!-- Status Badge -->
        <div class="mb-6">
            @if($report->status->name == 'approved')
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                    <svg class="-ml-1 mr-1.5 h-4 w-4 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    Disetujui
                </span>
            @elseif($report->status->name == 'rejected')
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                    <svg class="-ml-1 mr-1.5 h-4 w-4 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    Ditolak
                </span>
            @else
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                    <svg class="-ml-1 mr-1.5 h-4 w-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                    </svg>
                    Menunggu
                </span>
            @endif
        </div>

        <!-- Report ID and Date -->
        <div class="mb-6 grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="bg-gray-50 p-4 rounded-lg">
                <h3 class="text-sm font-medium text-gray-500">ID Laporan</h3>
                <p class="mt-1 text-lg font-semibold text-gray-900">{{ $report->reportID }}</p>
            </div>
            <div class="bg-gray-50 p-4 rounded-lg">
                <h3 class="text-sm font-medium text-gray-500">Tanggal Pengujian</h3>
                <p class="mt-1 text-lg font-semibold text-gray-900">{{ $report->testDate->format('d/m/Y H:i') }} WIB</p>
            </div>
        </div>

        <!-- Main Content -->
        <div class="mb-6">
            <div class="border rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/3">Nama Operator Penerbangan</th>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Bandar Udara Adisutjipto Yogyakarta</td>
                        </tr>
                        <tr>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lokasi Penempatan</th>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $location->name }}</td>
                        </tr>
                        <tr>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Merk/Tipe/Nomor Seri</th>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $report->deviceInfo }}</td>
                        </tr>
                        <tr>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nomor Sertifikat</th>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $report->certificateInfo }}</td>
                        </tr>
                        <tr>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hasil</th>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                @if($report->result == 'pass')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">PASS</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">FAIL</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Catatan</th>
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $report->note ?: 'Tidak ada catatan' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Test Details -->
        <div class="mb-6">
            <h2 class="text-lg font-medium text-gray-900 mb-3">Detail Pengujian</h2>
            <div class="border rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Terpenuhi</th>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                @if($report->reportDetails->terpenuhi)
                                    <svg class="h-5 w-5 text-green-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                @else
                                    <svg class="h-5 w-5 text-red-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                    </svg>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tidak Terpenuhi</th>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                @if($report->reportDetails->tidakTerpenuhi)
                                    <svg class="h-5 w-5 text-green-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                @else
                                    <svg class="h-5 w-5 text-red-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                    </svg>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">TEST 1</th>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                @if($report->reportDetails->test1)
                                    <svg class="h-5 w-5 text-green-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                @else
                                    <svg class="h-5 w-5 text-red-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                    </svg>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Test Condition 1</th>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                @if($report->reportDetails->testCondition1)
                                    <svg class="h-5 w-5 text-green-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                @else
                                    <svg class="h-5 w-5 text-red-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                    </svg>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Test Condition 2</th>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                @if($report->reportDetails->testCondition2)
                                    <svg class="h-5 w-5 text-green-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                @else
                                    <svg class="h-5 w-5 text-red-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                    </svg>
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Rejection Details (if rejected) -->
        @if($report->status->name == 'rejected')
        <div class="mb-6 bg-red-50 p-4 rounded-lg border border-red-200">
            <h2 class="text-lg font-medium text-red-800 mb-3">Alasan Penolakan</h2>
            <p class="text-red-700">{{ $report->approvalNote }}</p>
        </div>
        @endif

        <!-- Signatures -->
        <div class="mb-6">
            <h2 class="text-lg font-medium text-gray-900 mb-3">Tanda Tangan</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="border rounded-lg p-4">
                    <h3 class="text-sm font-medium text-gray-500 mb-2">Petugas</h3>
                    @if($report->submitterSignature)
                        <div class="mb-2">
                            <img src="{{ $report->submitterSignature }}" alt="Tanda Tangan Petugas" class="max-w-full h-auto max-h-32">
                        </div>
                    @else
                        <p class="text-sm text-gray-500 italic">Tidak ada tanda tangan</p>
                    @endif
                    <p class="text-sm font-medium">{{ $report->submittedBy->name }}</p>
                    <p class="text-xs text-gray-500">{{ $report->created_at->format('d/m/Y H:i') }}</p>
                </div>

                <div class="border rounded-lg p-4">
                    <h3 class="text-sm font-medium text-gray-500 mb-2">Supervisor</h3>
                    @if($report->approverSignature)
                        <div class="mb-2">
                            <img src="{{ $report->approverSignature }}" alt="Tanda Tangan Supervisor" class="max-w-full h-auto max-h-32">
                        </div>
                    @else
                        <p class="text-sm text-gray-500 italic">Tidak ada tanda tangan</p>
                    @endif
                    @if($report->approvedBy)
                        <p class="text-sm font-medium">{{ $report->approvedBy->name }}</p>
                    @else
                        <p class="text-sm text-gray-500 italic">Belum disetujui</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection