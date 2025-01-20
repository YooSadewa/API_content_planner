<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Host;
use App\Models\Pembicara;
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
}
