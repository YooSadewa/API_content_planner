<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Host;
use Illuminate\Support\Facades\Validator;

class ApiController extends Controller
{
    //Module Host
    public function getHost() {
        $host = Host::all();
        return response()->json([
            'success' => true,
            'message' => 'Data Host berhasil diambil',
            'data' => [
                'host' => $host
            ]
        ], 200);
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
        } catch (\Exception) {
            return response()->json([
                'status' => false,
                'message' => 'Host gagal diupdate',
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

    //Module pembicara
}
