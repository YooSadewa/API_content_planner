<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Host;
use App\Models\Pembicara;
use App\Models\Podcast;
use Illuminate\Support\Facades\Validator;

class ApiController extends Controller
{
    //Module Host
    public function getHost() {
        try {
            $host = Host::all();
        
            if ($host->isEmpty()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Data Host tidak ditemukan',
                    'data' => null
                ], 404);
            }
        
            return response()->json([
                'status' => true,
                'message' => 'Data Host berhasil diambil',
                'data' => [
                    'host' => $host
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan saat mengambil data host',
                'error' => $e->getMessage()
            ], 500);
        }
        
    }

    public function createHost(Request $request) {
        $validator = Validator::make($request->all(), [
            'host_nama' => 'required|string|max:255|unique:hosts'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validator Error',
                'data' => $validator->errors(),
            ], 422);
        }

        try {
            $host = Host::create([
                'host_nama' => $request->host_nama
            ]);
    
            return response()->json([
                'status' => true,
                'message' => 'Host berhasil dibuat',
                'data' => [
                    'host' => $host,
                ]
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500); 
        }
    }

    public function updateHost(Request $request, $id) {
        $host = Host::where('host_id', $id)->first();

        if (!$host) {
            return response()->json([
                'status' => false,
                'message' => 'Host tidak ditemukan',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'host_nama' => 'sometimes|required|string|max:255|unique:hosts,host_nama,' . $id . ',host_id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validator Error',
                'data' => $validator->errors(),
            ], 422);
        }

        try {
            $updateStatus = $host->update([
                'host_nama' => $request->host_nama
            ]);
                if ($updateStatus) {
                    return response()->json([
                        'status' => true,
                        'message' => 'Host updated successfully',
                        'data' => [
                            'host' => $host,
                        ],
                    ], 200);
                }
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Host gagal diupdate',
                'data' => $e->getMessage(),
            ], 500); 
        }
    }

    public function deleteHost($id) {
        try {
            $host = Host::find($id);

            if(!$host) {
                return response()->json([
                    'status' => false,
                    'message' => 'Host tidak ditemukan',
                ], 404);
            }

            $host->delete();

            return response()->json([
                'status' => true,
                'message' => 'Host berhasil dihapus',
            ], 200);
        } catch (\Exception) {
            return response()->json([
                'status' => false,
                'message' => 'Host gagal dihapus',
            ], 500);
        }
    }
    //End module

    //Module Pembicara
    public function getSpeaker() {
        try {
            $speaker = Pembicara::all();

            if($speaker->isEmpty()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Pembicara tidak ditemukan',
                    'data' => null
                ], 404);
            } else {
                return response()->json([
                    'status' => true,
                    'message' => 'Pembicara berhasil ditemukan',
                    'data' => [
                        'pembicara' => $speaker
                    ]
                ], 200);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan saat mengambil data pembicara',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function createSpeaker(Request $request) {
        $validator = Validator::make($request->all(), [
            'pmb_nama' => 'required|string|max:255|unique:pembicaras'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validator Error',
                'data' => $validator->errors()
            ], 422);
        }

        try {
            $speaker = Pembicara::create([
                'pmb_nama' => $request->pmb_nama,
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Pembicara berhasil dibuat',
                'data' => [
                    'pembicara' => $speaker
                ]
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan saat membuat pembicara',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function updateSpeaker($id, Request $request) {
        $speaker = Pembicara::where('pmb_id', $id)->first();

        if(!$speaker) {
            return response()->json([
                'status' => false,
                'message' => 'Pembicara tidak ditemukan',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'pmb_nama' => 'required|string|max:255|unique:pembicaras,pmb_nama,' . $id . ',pmb_id'
        ]);

        if($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validator Error',
                'data' => $validator->errors()
            ], 422);
        }

        try {
            $updateSpeaker = $speaker->update([
                'pmb_nama' => $request->pmb_nama,
            ]);

            if ($updateSpeaker) {
                return response()->json([
                    'status' => true,
                    'message' => 'Pembicara berhasil diupdate',
                    'data' => [
                        'pembicara' => $speaker
                    ]
                ], 200);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan saat mengupdate pembicara',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function deleteSpeaker($id) {
        try {
            $speaker = Pembicara::find($id);

            if(!$speaker) {
                return response()->json([
                    'status' => false,
                    'message' => 'Pembicara tidak ditemukan',
                ], 404);
            }

            $speaker->delete();

            return response()->json([
                'status' => true,
                'message' => 'Pembicara berhasil dihapus',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan saat menghapus pembicara',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    //End module

    //Module podcast
    public function getPodcast() {
        try {
            $podcasts = Podcast::with('hosts', 'pembicaras')->get();

            if($podcasts->isEmpty()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Tidak ada podcast',
                ], 404);
            } else {
                return response()->json([
                    'status' => true,
                    'message' => 'Berhasil mendapatkan podcast',
                    'data' => [
                        'podcast' => $podcasts->map(function ($podcast) {
                            return [
                                'pdc_id' => $podcast->pdc_id,
                                'pdc_jadwal_shoot' => $podcast->pdc_jadwal_shoot,
                                'pdc_jadwal_upload' => $podcast->pdc_jadwal_upload,
                                'pdc_tema' => $podcast->pdc_tema,
                                'pdc_abstrak' => $podcast->pdc_abstrak,
                                'host_id' => $podcast->host_id,
                                'host_nama' => $podcast->hosts ? $podcast->hosts->host_nama : null,
                                'pmb_id' => $podcast->pmb_id,
                                'pdc_nama' => $podcast->pembicaras ? $podcast->pembicaras->pmb_nama : null,
                                'pdc_catatan' => $podcast->pdc_catatan,
                            ];
                        }) 
                    ]
                ], 200);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan saat mendapatkan podcast',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function createPodcast(Request $request) {
        $validator = Validator::make($request->all(), [
            'pdc_jadwal_shoot' => 'required|date',
            'pdc_jadwal_upload' => 'nullable|date',
            'pdc_tema' => 'required|string|max:150|unique:podcasts',
            'pdc_abstrak' => 'nullable|string|max:150',
            'pmb_id' => 'required|integer|exists:pembicaras,pmb_id',
            'host_id' => 'required|integer|exists:hosts,host_id',
            'pdc_catatan' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validator Error',
                'error' => $validator->errors()
            ], 400);
        }

        try {
            $podcast = Podcast::create([
                'pdc_jadwal_shoot' => $request->pdc_jadwal_shoot,
                'pdc_jadwal_upload' => $request->pdc_jadwal_upload,
                'pdc_tema' => $request->pdc_tema,
                'pdc_abstrak' => $request->pdc_abstrak, 
                'pmb_id' => $request->pmb_id,
                'host_id' => $request->host_id,
                'pdc_catatan' => $request->pdc_catatan,
            ]);

            $podcast->load('hosts', 'pembicaras');
            $podcast->makeHidden(['pdc_id']);
            $podcast->hosts->makeHidden(['created_at', 'updated_at']);
            $podcast->pembicaras->makeHidden(['created_at', 'updated_at']);

            return response()->json([
                'status' => true,
                'message' => 'Podcast berhasil dibuat',
                'data' => [
                    'podcast' => $podcast,
                ]
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan saat membuat podcast',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function updatePodcast($id, Request $request) {
        $podcast = Podcast::where('pdc_id', $id)->first();

        if (!$podcast) {
            return response()->json([
                'status' => false,
                'message' => 'Podcast tidak ditemukan',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'pdc_jadwal_shoot' => 'required|date',
            'pdc_jadwal_upload' => 'nullable|date',
            'pdc_tema' => 'required|string|max:150|unique:podcasts,pdc_tema,'. $id . ',pdc_id',
            'pdc_abstrak' => 'nullable|string|max:150',
            'pmb_id' => 'required|integer|exists:pembicaras,pmb_id',
            'host_id' => 'required|integer|exists:hosts,host_id',
            'pdc_catatan' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validator Error',
                'error' => $validator->errors()
            ], 400);
        }

        try {
            $updatePodcast = $podcast->update([
                'pdc_jadwal_shoot' => $request->pdc_jadwal_shoot,
                'pdc_jadwal_upload' => $request->pdc_jadwal_upload,
                'pdc_tema' => $request->pdc_tema,
                'pdc_abstrak' => $request->pdc_abstrak,
                'pmb_id' => $request->pmb_id,
                'host_id' => $request->host_id,
                'pdc_catatan' => $request->pdc_catatan,
            ]);

            $podcast->load('hosts', 'pembicaras');
            $podcast->makeHidden(['pdc_id']);
            $podcast->hosts->makeHidden(['created_at', 'updated_at']);
            $podcast->pembicaras->makeHidden(['created_at', 'updated_at']);

            if ($updatePodcast) {
                return response()->json([
                    'status' => true,
                    'message' => 'Podcast berhasil diupdate',
                    'data' => [
                        'podcast' => $podcast
                    ]
                ], 200);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal mengupdate podcast',
                'error' => $e->getMessage()
            ]);
        }
    }

    public function deletePodcast($id) {
        try {
            $podcast = Podcast::find($id);

            if(!$podcast) {
                return response()->json([
                    'status' => false,
                    'message' => 'Podcast tidak ditemukan',
                ], 404);
            }

            $podcast->delete();

            return response()->json([
                'status' => true,
                'message' => 'Podcast berhasil dihapus',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal menghapus podcast',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
