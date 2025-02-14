<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Host;
use App\Models\IdeKontenFoto;
use App\Models\IdeKontenVideo;
use App\Models\InspiringPeople;
use App\Models\Pembicara;
use App\Models\Podcast;
use App\Models\Quotes;
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
            $host = Host::where('host_id', $id)->where('host_isactive', 'Y')->first();

            if (!$host) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Host tidak ditemukan',
                    ], 404);
                }

                $host->update([
                    'host_isactive' => 'N'
                ]);

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
            $speaker = Pembicara::where('pmb_id', $id)->where('pmb_isactive', 'Y')->first();
    
            if (!$speaker) {
                return response()->json([
                    'status' => false,
                    'message' => 'Pembicara tidak ditemukan',
                ], 404);
            }
    
            $speaker->update([
                'pmb_isactive' => 'N'
            ]);
    
            return response()->json([
                'status' => true,
                'message' => 'Pembicara berhasil dinonaktifkan',
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
            // Query untuk mendapatkan podcast dengan sorting sesuai kebutuhan
            $podcasts = Podcast::with('hosts', 'pembicaras')
                ->orderByRaw('pdc_link IS NOT NULL, pdc_jadwal_shoot ASC') // Sorting custom
                ->get();
    
            if ($podcasts->isEmpty()) {
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
                                'pdc_link' => $podcast->pdc_link,
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

    public function uploadPodcast($id, Request $request) {
        $podcast = Podcast::where('pdc_id', $id)->first();
        if (!$podcast) {
            return response()->json([
                'status' => false,
                'message' => 'Podcast tidak ditemukan',
            ], 404);
        }
        $validator = Validator::make($request->all(), [
            'pdc_link' => 'url|required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validator Error',
                'error' => $validator->errors()
            ], 400);
        }

        try {
            $uploadPodcast = $podcast->update([
                'pdc_link' => $request->pdc_link,
            ]);
            if ($uploadPodcast) {
                return response()->json([
                    'status' => true,
                    'message' => 'Link Podcast berhasil diupdate',
                    'data' => [
                        'link' => $podcast
                    ]
                ], 200);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal mengupdate link podcast',
                'error' => $e->getMessage()
            ]);
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
    //End module

    //Module Quote of the Day
    public function getQuote() {
        try {
            $quote = Quotes::all();

            if($quote->isEmpty()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Tidak ada quote',
                    'data' => null
                ], 404);
            } else {
                return response()->json([
                    'status' => true,
                    'message' => 'Berhasil mendapatkan quote',
                    'data' => [
                        'quote' => $quote
                    ]
                ], 200);
            }
        }
        catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal mendapatkan quote',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function createQuote(Request $request) {
        $validator = Validator::make($request->all(), [
            'qotd_link' => 'required|unique:quotes|url',
        ]);

        if($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validator error',
                'error' => $validator->errors()
            ], 400);
        }

        try {
            $quote = Quotes::create([
                'qotd_link' => $request->qotd_link
            ]);
            return response()->json([
                'status' => true,
                'message' => 'Berhasil menambahkan quote',
                'data' => [
                    'quote' => $quote
                ]
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal menambahkan quote',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function updateQuote(Request $request, $id) {
        $quote = Quotes::where('qotd_id', $id)->first();

        if(!$quote) {
            return response()->json([
                'status' => false,
                'message' => 'Quote tidak ditemukan',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'qotd_link' => 'required|url|unique:quotes,qotd_Link,'. $id . ',qotd_id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validator Error',
                'error' => $validator->errors()
            ], 400);
        }

        try {
            $updateQuote = $quote->update([
                'qotd_link' => $request->qotd_link
            ]);

            if($updateQuote) {
                return response()->json([
                    'status' => true,
                    'message' => 'Berhasil mengupdate quote',
                    'data' => [
                        'quote' => $quote
                    ]
                ], 200);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal mengupdate quote',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function deleteQuote($id) {
        try {
            $quote = Quotes::find($id);

            if(!$quote) {
                return response()->json([
                    'status' => false,
                    'message' => 'Quote tidak ditemukan',
                ], 404);
            }
            $quote->delete();
            return response()->json([
                'status' => true,
                'message' => 'Berhasil menghapus quote',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal menghapus quote',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    //End module

    //Module Inspiring People
    public function getInspPeople() {
        try {
            $inspiringPeople = InspiringPeople::all();

            if($inspiringPeople->isEmpty()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Tidak ada data inspiring people',
                ], 404);
            } else {
                return response()->json([
                    'status' => true,
                    'message' => 'Berhasil mendapatkan data inspiring people',
                    'data' => [
                        'inspiringPeople' => $inspiringPeople
                    ]
                ], 200);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal mendapatkan data inspiring people',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function createInspPeople(Request $request) {
        $validator = Validator::make($request->all(), [
            'ins_nama' => 'required|string|unique:inspiring_people',
            'ins_link' => 'required|url|unique:inspiring_people',
        ]);

        if($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validator error',
                'error' => $validator->errors()
            ], 400);
        }

        try {
            $inspiringPeople = InspiringPeople::create([
                'ins_nama' => $request->ins_nama,
                'ins_link' => $request->ins_link,
            ]);
            return response()->json([
                'status' => true,
                'message' => 'Berhasil menambahkan data inspiring people',
                'data' => [
                    'inspiringPeople' => $inspiringPeople
                ]
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal menambahkan data inspiring people',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function updateInspPeople(Request $request, $id) {
        $inspiringPeople = InspiringPeople::where('ins_id', $id)->first();

        if(!$inspiringPeople) {
            return response()->json([
                'status' => false,
                'message' => 'Data inspiring people tidak ditemukan',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'ins_nama' => 'required|string|unique:inspiring_people,ins_nama,'. $id . ',ins_id',
            'ins_link' => 'required|url|unique:inspiring_people,ins_link,'. $id . ',ins_id',
        ]);

        if($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validator error',
                'error' => $validator->errors()
            ], 400);
        }

        try {
            $inspiringPeople->update([
                'ins_nama' => $request->ins_nama,
                'ins_link' => $request->ins_link,
            ]);
            return response()->json([
                'status' => true,
                'message' => 'Berhasil mengupdate data inspiring people',
                'data' => [
                    'inspiringPeople' => $inspiringPeople
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal mengupdate data inspiring people',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function deleteInspPeople($id) {
        try {
            $inspiringPeople = InspiringPeople::find($id);

            if(!$inspiringPeople) {
                return response()->json([
                    'status' => false,
                    'message' => 'Data inspiring people tidak ditemukan',
                ], 404);
            }
            $inspiringPeople->delete();
            return response()->json([
                'status' => true,
                'message' => 'Berhasil menghapus data inspiring people',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal menghapus data inspiring people',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    //End module

    //Module Ide Konten
    public function getIdeKontenFoto() {
        try {
            $idekontenfoto = IdeKontenFoto::all();

            if($idekontenfoto->isEmpty()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Data ide konten foto tidak ditemukan',
                ], 404);
            } else {
                return response()->json([
                    'status' => true,
                    'message' => 'Berhasil mendapatkan data ide konten foto',
                    'data' => [
                        'ide_konten_foto' => $idekontenfoto
                    ]
                ], 200);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal mendapatkan data ide konten foto',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function createIdeKontenFoto(Request $request) {
        $validator = Validator::make($request->all(), [
            'ikf_tgl' => 'date|required',
            'ikf_judul_konten' => 'string|required|max:150|unique:ide_konten_foto',
            'ikf_ringkasan' => 'string|required|max:150',
            'ikf_referensi' => 'url|nullable'
        ]);

        if($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Data tidak valid',
                'error' => $validator->errors()
            ], 400);
        }

        try {
            $idekontenfoto = IdeKontenFoto::create([
                'ikf_tgl' => $request->ikf_tgl,
                'ikf_judul_konten' => $request->ikf_judul_konten,
                'ikf_ringkasan' => $request->ikf_ringkasan,
                'ikf_referensi' => $request->ikf_referensi
            ]);
            
            return response()->json([
                'status' => true,
                'message' => 'Berhasil membuat ide konten foto',
                'data' => [
                    'ide_konten_foto' => $idekontenfoto
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal membuat ide konten foto',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function updateIdeKontenFoto(Request $request, $id) {
        $idekontenfoto = IdeKontenFoto::where('ikf_id', $id)->first();
        if(!$idekontenfoto) {
            return response()->json([
                'status' => false,
                'message' => 'Data tidak ditemukan',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'ikf_tgl' => 'date|required',
            'ikf_judul_konten' => 'string|required|max:150|unique:ide_konten_foto,ikf_judul_konten,'. $id . ',ikf_id',
            'ikf_ringkasan' => 'string|required|max:150',
            'ikf_referensi' => 'url|nullable'
        ]);

        if($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Data tidak valid',
                'error' => $validator->errors()
            ], 400);
        }

        try {
            $idekontenfoto->update([
                'ikf_tgl' => $request->ikf_tgl,
                'ikf_judul_konten' => $request->ikf_judul_konten,
                'ikf_ringkasan' => $request->ikf_ringkasan,
                'ikf_referensi' => $request->ikf_referensi
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Berhasil mengupdate ide konten foto',
                'data' => [
                    'ide_konten_foto' => $idekontenfoto
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal mengupdate ide konten foto',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function deleteIdeKontenFoto($id) {
        try {
            $idekontenfoto = IdeKontenFoto::find($id);

            if(!$idekontenfoto) {
                return response()->json([
                    'status' => false,
                    'message' => 'Data tidak ditemukan',
                ], 404);
            }
            $idekontenfoto->delete();
            return response()->json([
                'status' => true,
                'message' => 'Berhasil menghapus ide konten foto',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal menghapus ide konten foto',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    public function getIdeKontenVideo() {
        try {
            $idekontenvideo = IdeKontenVideo::all();

            if($idekontenvideo->isEmpty()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Data ide konten video tidak ditemukan',
                ], 404);
            } else {
                return response()->json([
                    'status' => true,
                    'message' => 'Berhasil mendapatkan data ide konten video',
                    'data' => [
                        'ide_konten_video' => $idekontenvideo
                    ]
                ], 200);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal mendapatkan data ide konten video',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function createIdeKontenVideo(Request $request) {
        $validator = Validator::make($request->all(), [
            'ikv_tgl' => 'date|required',
            'ikv_judul_konten' => 'string|required|max:150|unique:ide_konten_video',
            'ikv_ringkasan' => 'string|required|max:150',
            'ikv_pic' => 'required|string',
            'ikv_skrip' => 'required|mimes:pdf,doc,docx|max:2048',
            'ikv_upload' => 'date|nullable'
        ]);

        if($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Data tidak valid',
                'error' => $validator->errors()
            ], 400);
        }

        try {
            if ($request->hasFile('ikv_skrip')) {
                $file = $request->file('ikv_skrip');
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('uploads'), $filename);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'File skrip tidak ditemukan'
                ], 400);
            }

            $idekontenvideo = IdeKontenVideo::create([
                'ikv_tgl' => $request->ikv_tgl,
                'ikv_judul_konten' => $request->ikv_judul_konten,
                'ikv_ringkasan' => $request->ikv_ringkasan,
                'ikv_pic' => $request->ikv_pic,
                'ikv_skrip' => $filename,
                'ikv_upload' => $request->ikv_upload
            ]);
            
            return response()->json([
                'status' => true,
                'message' => 'Berhasil membuat ide konten video',
                'data' => [
                    'ide_konten_video' => array_merge($idekontenvideo->toArray(), [
                        'ikv_status' => $idekontenvideo->ikv_status ?? 'scheduled'
                    ])
                ]
            ]);            
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal membuat ide konten video',
                'error' => $e->getMessage()
            ], 500);
        } 
    }

    public function confirmUploadKontenVideo(Request $request, $id) {
        $kontenvideo = IdeKontenVideo::where('ikv_id', $id)->first();
        if (!$kontenvideo) {
            return response()->json([
                'status' => false,
                'message' => 'Konten Video tidak ditemukan',
            ], 404);
        }
        $validator = Validator::make($request->all(), [
            'ikv_upload' => 'date|required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validator Error',
                'error' => $validator->errors()
            ], 400);
        }

        try {
            $uploadKontenVideo = $kontenvideo->update([
                'ikv_upload' => $request->ikv_upload,
            ]);
            if ($uploadKontenVideo) {
                return response()->json([
                    'status' => true,
                    'message' => 'Tanggal Upload sudah dikonfirmasi',
                    'data' => [
                        'ikv_upload' => $kontenvideo
                    ]
                ], 200);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal menkonfirmasi konten video',
                'error' => $e->getMessage()
            ]);
        }
    }
}