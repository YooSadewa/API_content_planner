<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\DetailAccount;
use App\Models\DetailPlatform;
use Illuminate\Http\Request;
use App\Models\IdeKontenFoto;
use App\Models\IdeKontenVideo;
use App\Models\InspiringPeople;
use App\Models\LinkUploadPlanner;
use App\Models\OnlinePlanner;
use App\Models\Podcast;
use App\Models\Quotes;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ApiController extends Controller
{
    //Module user
    public function register(Request $request) {
        $validator = Validator::make($request->all(), [
            'user_name' => 'required|string',
            'username' => 'required|string|unique:users',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'data' => $validator->errors(),
            ], 422);
        }

        $user = User::create([
            'user_name' => $request->user_name,
            'username' => $request->username,
            'password' => bcrypt($request->password), // Hash password
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'User registered successfully',
            'data' => [
                'user' => $user,
                'token' => $token,
            ],
        ], 201);
    }

    public function login(Request $request){
        $validator = Validator::make($request->all(), [
            'username' => 'string|required',
            'password' => 'string|required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'data' => $validator->errors(),
            ], 422);
        }

        $admin = User::where('username', $request->username)->first();

        if(!$admin || !Hash::check($request->password, $admin->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials',
            ], 401);
        }

        Auth::login($admin);

        return response()->json([
            'success' => true,
            'message' => 'Admin logged in successfully',
            'data' => [
                'admin' => $admin,
                'token' => $admin->createToken('auth_token')->plainTextToken,
            ],
        ], 200);
    }
    //End module

    //Module podcast
    public function getPodcast() {
        try {
            // Query untuk mendapatkan podcast dengan sorting sesuai kebutuhan
            $podcasts = Podcast::query()
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
                        'podcast' => $podcasts
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
            'pdc_host' => 'required|string',
            'pdc_speaker' => 'required|string',
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
                'pdc_host' => $request->pdc_host,
                'pdc_speaker' => $request->pdc_speaker,
                'pdc_catatan' => $request->pdc_catatan,
            ]);

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
            'pdc_host' => 'required|string',
            'pdc_speaker' => 'required|string',
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
                'pdc_host' => $request->pdc_host,
                'pdc_speaker' => $request->pdc_speaker,
                'pdc_catatan' => $request->pdc_catatan,
            ]);

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
            'ikf_tgl' => 'date|nullable',
            'ikf_judul_konten' => 'string|required|max:150|unique:ide_konten_foto',
            'ikf_ringkasan' => 'string|required|max:150',
            'ikf_pic' => 'required|string',
            'ikf_status' => 'required|string',
            'ikf_skrip' => 'nullable|mimes:pdf,doc,docx|max:2048',
            'ikf_referensi' => 'nullable|url',
            'ikf_upload' => 'date|nullable'
        ]);

        if($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Data tidak valid',
                'error' => $validator->errors()
            ], 400);
        }

        try {
            $filename = null; 
            if ($request->hasFile('ikf_skrip')) {
                $file = $request->file('ikf_skrip');
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('uploads'), $filename);
            }

            $idekontenfoto = IdeKontenFoto::create([
                'ikf_tgl' => $request->ikf_tgl,
                'ikf_judul_konten' => $request->ikf_judul_konten,
                'ikf_ringkasan' => $request->ikf_ringkasan,
                'ikf_pic' => $request->ikf_pic,
                'ikf_status' => $request->ikf_status,
                'ikf_skrip' => $filename,
                'ikf_referensi' => $request->ikf_referensi,
                'ikf_upload' => $request->ikf_upload
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
    
        $rules = [
            'ikf_tgl' => 'date|nullable',
            'ikf_judul_konten' => 'string|required|max:150|unique:ide_konten_foto,ikf_judul_konten,'.$id.',ikf_id',
            'ikf_ringkasan' => 'string|required|max:150',
            'ikf_pic' => 'required|string',
            'ikf_status' => 'required|string',
            'ikf_skrip' => 'nullable|mimes:pdf,doc,docx|max:2048',
            'ikf_referensi' => 'nullable|url',
            'ikf_upload' => 'date|nullable'
        ];
    
        // Only validate file if it's present
        if ($request->hasFile('ikf_skrip')) {
            $rules['ikf_skrip'] = 'required|mimes:pdf,doc,docx|max:2048';
        }
    
        $validator = Validator::make($request->all(), $rules);
    
        if($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Data tidak valid',
                'error' => $validator->errors()
            ], 400);
        }
    
        try {
            $updateData = [
                'ikf_tgl' => $request->ikf_tgl,
                'ikf_judul_konten' => $request->ikf_judul_konten,
                'ikf_ringkasan' => $request->ikf_ringkasan,
                'ikf_pic' => $request->ikf_pic,
                'ikf_status' => $request->ikf_status,
                'ikf_referensi' => $request->ikf_referensi,
                'ikf_upload' => $request->ikf_upload
            ];
    
            // Handle file upload only if new file is provided
            if ($request->hasFile('ikf_skrip')) {
                // Delete old file if exists
                if ($idekontenfoto->ikf_skrip) {
                    $oldFilePath = public_path('uploads/' . $idekontenfoto->ikf_skrip);
                    if (file_exists($oldFilePath)) {
                        unlink($oldFilePath);
                    }
                }
    
                $file = $request->file('ikf_skrip');
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('uploads'), $filename);
                $updateData['ikf_skrip'] = $filename;
            }
    
            $idekontenfoto->update($updateData);
    
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

    public function confirmUploadKontenFoto(Request $request, $id) {
        $kontenfoto = IdeKontenFoto::where('ikf_id', $id)->first();
        if (!$kontenfoto) {
            return response()->json([
                'status' => false,
                'message' => 'Konten Foto tidak ditemukan',
            ], 404);
        }
        $validator = Validator::make($request->all(), [
            'ikf_upload' => 'date|required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validator Error',
                'error' => $validator->errors()
            ], 400);
        }

        try {
            $uploadKontenFoto = $kontenfoto->update([
                'ikf_upload' => $request->ikf_upload,
                'ikf_status' => 'done',
            ]);
            if ($uploadKontenFoto) {
                return response()->json([
                    'status' => true,
                    'message' => 'Tanggal Upload sudah dikonfirmasi',
                    'data' => [
                        'ikf_upload' => $kontenfoto
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
            'ikv_tgl' => 'date|nullable',
            'ikv_judul_konten' => 'string|required|max:150|unique:ide_konten_video',
            'ikv_ringkasan' => 'string|required|max:150',
            'ikv_pic' => 'required|string',
            'ikv_status' => 'required|string',
            'ikv_skrip' => 'nullable|mimes:pdf,doc,docx|max:2048',
            'ikv_referensi' => 'nullable|url',
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
            $filename = null; 
            if ($request->hasFile('ikv_skrip')) {
                $file = $request->file('ikv_skrip');
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('uploads'), $filename);
            }

            $idekontenvideo = IdeKontenVideo::create([
                'ikv_tgl' => $request->ikv_tgl,
                'ikv_judul_konten' => $request->ikv_judul_konten,
                'ikv_ringkasan' => $request->ikv_ringkasan,
                'ikv_pic' => $request->ikv_pic,
                'ikv_status' => $request->ikv_status,
                'ikv_skrip' => $filename,
                'ikv_referensi' => $request->ikv_referensi,
                'ikv_upload' => $request->ikv_upload
            ]);
            
            return response()->json([
                'status' => true,
                'message' => 'Berhasil membuat ide konten video',
                'data' => [
                    'ide_konten_video' => $idekontenvideo
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
            'ikv_upload' => 'date|required',
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
                'ikv_status' => 'done',
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

    public function updateIdeKontenVideo(Request $request, $id) {
        $idekontenvideo = IdeKontenVideo::where('ikv_id', $id)->first();
        if(!$idekontenvideo) {
            return response()->json([
                'status' => false,
                'message' => 'Data tidak ditemukan',
            ], 404);
        }
    
        $rules = [
            'ikv_tgl' => 'date|nullable',
            'ikv_judul_konten' => 'string|required|max:150|unique:ide_konten_video,ikv_judul_konten,'.$id.',ikv_id',
            'ikv_ringkasan' => 'string|required|max:150',
            'ikv_pic' => 'required|string',
            'ikv_status' => 'required|string',
            'ikv_skrip' => 'nullable|mimes:pdf,doc,docx|max:2048',
            'ikv_referensi' => 'nullable|url',
            'ikv_upload' => 'date|nullable'
        ];
    
        // Only validate file if it's present
        if ($request->hasFile('ikv_skrip')) {
            $rules['ikv_skrip'] = 'required|mimes:pdf,doc,docx|max:2048';
        }
    
        $validator = Validator::make($request->all(), $rules);
    
        if($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Data tidak valid',
                'error' => $validator->errors()
            ], 400);
        }
    
        try {
            $updateData = [
                'ikv_tgl' => $request->ikv_tgl,
                'ikv_judul_konten' => $request->ikv_judul_konten,
                'ikv_ringkasan' => $request->ikv_ringkasan,
                'ikv_pic' => $request->ikv_pic,
                'ikv_status' => $request->ikv_status,
                'ikv_referensi' => $request->ikv_referensi,
                'ikv_upload' => $request->ikv_upload
            ];
    
            // Handle file upload only if new file is provided
            if ($request->hasFile('ikv_skrip')) {
                // Delete old file if exists
                if ($idekontenvideo->ikv_skrip) {
                    $oldFilePath = public_path('uploads/' . $idekontenvideo->ikv_skrip);
                    if (file_exists($oldFilePath)) {
                        unlink($oldFilePath);
                    }
                }
    
                $file = $request->file('ikv_skrip');
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('uploads'), $filename);
                $updateData['ikv_skrip'] = $filename;
            }
    
            $idekontenvideo->update($updateData);
    
            return response()->json([
                'status' => true,
                'message' => 'Berhasil mengupdate ide konten video',
                'data' => [
                    'ide_konten_video' => $idekontenvideo
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal mengupdate ide konten video',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function deleteIdeKontenVideo($id) {
        try {
            $idekontenvideo = IdeKontenVideo::find($id);

            if(!$idekontenvideo) {
                return response()->json([
                    'status' => false,
                    'message' => 'Data tidak ditemukan',
                ], 404);
            }
            $idekontenvideo->delete();
            return response()->json([
                'status' => true,
                'message' => 'Berhasil menghapus ide konten video',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal menghapus ide konten video',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    //Module Detail Account
    public function getDetailAccount() {
        try {
            $detailacc = DetailAccount::with('platforms')->get();

            if ($detailacc->isEmpty()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Data Akun tidak ditemukan',
                ], 404);
            }
    
            $formattedData = $detailacc->map(function ($account) {
                $platformData = [];
                foreach ($account->platforms as $platform) {
                    $platformData[$platform->dpl_platform] = [
                        'dpl_id' => $platform->dpl_id,
                        'dpl_total_konten' => $platform->dpl_total_konten,
                        'dpl_pengikut' => $platform->dpl_pengikut,
                    ];
                }
    
                return [
                    'dacc_id' => $account->dacc_id,
                    'dacc_bulan' => $account->dacc_bulan,
                    'dacc_tahun' => $account->dacc_tahun,
                    'created_at' => $account->created_at,
                    'updated_at' => $account->updated_at,
                ] + $platformData; // Menggabungkan platform data langsung ke akun
            });
    
            return response()->json([
                'status' => true,
                'message' => 'Berhasil mendapatkan data Akun',
                'data' => [
                    'detail_akun' => $formattedData
                ]
            ], 200);
    
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal mendapatkan data akun',
                'error' => $e->getMessage()
            ], 500);
        }
    }    

    public function getSortDetailAccount() {
        try {
            $detailacc = DetailAccount::with('platforms')
                ->get()
                ->sortByDesc(function ($account) {
                    return max(strtotime($account->created_at), strtotime($account->updated_at));
                });

            if ($detailacc->isEmpty()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Data Akun tidak ditemukan',
                ], 404);
            }

            // Inisialisasi total konten untuk setiap platform
            $totalKontenPerPlatform = [
                'website' => 0,
                'instagram' => 0,
                'twitter' => 0,
                'facebook' => 0,
                'youtube' => 0,
                'tiktok' => 0,
            ];

            // Array untuk melacak entri pengikut terbaru untuk setiap platform
            $latestFollowers = [
                'website' => ['date' => null, 'count' => 0, 'year' => 0, 'month' => 0],
                'instagram' => ['date' => null, 'count' => 0, 'year' => 0, 'month' => 0],
                'twitter' => ['date' => null, 'count' => 0, 'year' => 0, 'month' => 0],
                'facebook' => ['date' => null, 'count' => 0, 'year' => 0, 'month' => 0],
                'youtube' => ['date' => null, 'count' => 0, 'year' => 0, 'month' => 0],
                'tiktok' => ['date' => null, 'count' => 0, 'year' => 0, 'month' => 0],
            ];

            $formattedData = $detailacc->map(function ($account) use (&$totalKontenPerPlatform, &$latestFollowers) {
                $platformData = [];
                foreach ($account->platforms as $platform) {
                    $platformName = $platform->dpl_platform;
                    $platformData[$platformName] = [
                        'dpl_id' => $platform->dpl_id,
                        'dpl_total_konten' => $platform->dpl_total_konten,
                        'dpl_pengikut' => $platform->dpl_pengikut,
                    ];

                    // Akumulasi jumlah total konten berdasarkan platform
                    if (isset($totalKontenPerPlatform[$platformName])) {
                        $totalKontenPerPlatform[$platformName] += (int) $platform->dpl_total_konten;
                    }

                    // Update jumlah pengikut terbaru berdasarkan tahun dan bulan
                    $isNewer = false;
                    
                    // Jika belum ada data
                    if ($latestFollowers[$platformName]['date'] === null) {
                        $isNewer = true;
                    }
                    // Jika tahun lebih baru
                    elseif ($account->dacc_tahun > $latestFollowers[$platformName]['year']) {
                        $isNewer = true;
                    }
                    // Jika tahun sama tapi bulan lebih baru
                    elseif ($account->dacc_tahun == $latestFollowers[$platformName]['year'] && 
                        $account->dacc_bulan > $latestFollowers[$platformName]['month']) {
                        $isNewer = true;
                    }

                    if ($isNewer) {
                        $latestFollowers[$platformName] = [
                            'date' => max(strtotime($account->created_at), strtotime($account->updated_at)),
                            'count' => (int) $platform->dpl_pengikut,
                            'year' => $account->dacc_tahun,
                            'month' => $account->dacc_bulan
                        ];
                    }
                }

                return [
                    'dacc_id' => $account->dacc_id,
                    'dacc_bulan' => $account->dacc_bulan,
                    'dacc_tahun' => $account->dacc_tahun,
                    'created_at' => $account->created_at,
                    'updated_at' => $account->updated_at,
                ] + $platformData; // Menggabungkan platform data langsung ke akun
            });

            // Extract pengikut terbaru untuk response
            $latestFollowersCount = array_map(function($platform) {
                return $platform['count'];
            }, $latestFollowers);

            return response()->json([
                'status' => true,
                'message' => 'Berhasil mendapatkan data Akun',
                'data' => [
                    'detail_akun' => $formattedData,
                    'total_konten_per_platform' => $totalKontenPerPlatform,
                    'latest_followers' => $latestFollowersCount, // Menambahkan jumlah pengikut terbaru
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal mendapatkan data akun',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getByMonthYear(Request $request){
        try {
            $month = $request->query('month');
            $year = $request->query('year');

            $detailacc = DetailAccount::with('platforms')
                ->where('dacc_bulan', $month)
                ->where('dacc_tahun', $year)
                ->get();

            if ($detailacc->isEmpty()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Data Akun tidak ditemukan untuk bulan dan tahun yang dipilih',
                ], 404);
            }
    
            $formattedData = $detailacc->map(function ($account) {
                $platformData = [];
                foreach ($account->platforms as $platform) {
                    $platformData[$platform->dpl_platform] = [
                        'dpl_id' => $platform->dpl_id,
                        'dpl_total_konten' => $platform->dpl_total_konten,
                        'dpl_pengikut' => $platform->dpl_pengikut,
                    ];
                }
    
                return [
                    'dacc_id' => $account->dacc_id,
                    'dacc_bulan' => $account->dacc_bulan,
                    'dacc_tahun' => $account->dacc_tahun,
                    'created_at' => $account->created_at,
                    'updated_at' => $account->updated_at,
                ] + $platformData;
            });
    
            return response()->json([
                'status' => true,
                'message' => 'Berhasil mendapatkan data Akun',
                'data' => [
                    'detail_akun' => $formattedData
                ]
            ], 200);
    
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal mendapatkan data akun',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getByDacc(Request $request){
        try {
            $dacc_id = $request->query('dacc_id');

            $detailacc = DetailAccount::with('platforms')
                ->where('dacc_id', $dacc_id)
                ->first();

            if (!$detailacc) {
                return response()->json([
                    'status' => false,
                    'message' => 'Data Akun tidak ditemukan untuk ID yang diberikan',
                ], 404);
            }
    
            $platformData = [];
            foreach ($detailacc->platforms as $platform) {
                $platformData[$platform->dpl_platform] = [
                    'dpl_id' => $platform->dpl_id,
                    'dpl_total_konten' => $platform->dpl_total_konten,
                    'dpl_pengikut' => $platform->dpl_pengikut,
                ];
            }
    
            $formattedData = [
                'dacc_id' => $detailacc->dacc_id,
                'dacc_bulan' => $detailacc->dacc_bulan,
                'dacc_tahun' => $detailacc->dacc_tahun,
                'created_at' => $detailacc->created_at,
                'updated_at' => $detailacc->updated_at,
            ] + $platformData;
    
            return response()->json([
                'status' => true,
                'message' => 'Berhasil mendapatkan data Akun',
                'data' => [
                    'detail_akun' => $formattedData
                ]
            ], 200);
    
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal mendapatkan data akun',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function createDetailAccount(Request $request) {
        $validator = Validator::make($request->all(), [
            'dacc_bulan' => 'numeric|required',
            'dacc_tahun' => 'numeric|required|min:2000',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Data tidak valid',
                'error' => $validator->errors()
            ], 400);
        }
    
        try {
            $existingAccount = DetailAccount::where('dacc_bulan', $request->dacc_bulan)
                ->where('dacc_tahun', $request->dacc_tahun)
                ->first();
    
            if ($existingAccount) {
                return response()->json([
                    'status' => false,
                    'message' => 'Detail tanggal sudah ada',
                ], 400);
            }
    
            // Jika belum ada, buat akun baru
            $detailacc = DetailAccount::create([
                'dacc_bulan' => $request->dacc_bulan,
                'dacc_tahun' => $request->dacc_tahun,
            ]);
    
            return response()->json([
                'status' => true,
                'message' => 'Berhasil membuat detail tanggal',
                'data' => $detailacc
            ], 201);
    
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal membuat detail tanggal',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function createPlatform(Request $request) {
        $validator = Validator::make($request->all(), [
            'dacc_id' => 'required|exists:detail_account,dacc_id',
            'dpl_platform' => 'string|required|in:website,instagram,twitter,facebook,youtube,tiktok',
            'dpl_total_konten' => 'numeric|required',
            'dpl_pengikut' => 'numeric|nullable',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Data tidak valid',
                'error' => $validator->errors()
            ], 400);
        }
    
        try {
            // Cek apakah platform sudah ada untuk akun ini
            $exists = DetailPlatform::where('dacc_id', $request->dacc_id)
                ->where('dpl_platform', $request->dpl_platform)
                ->exists();
    
            if ($exists) {
                return response()->json([
                    'status' => false,
                    'message' => 'Data sudah ada dalam tanggal ini',
                ], 400);
            }
    
            // Simpan data platform
            $platform = DetailPlatform::create([
                'dacc_id' => $request->dacc_id,
                'dpl_platform' => $request->dpl_platform,
                'dpl_total_konten' => $request->dpl_total_konten,
                'dpl_pengikut' => $request->dpl_pengikut ?? null,
            ]);
    
            return response()->json([
                'status' => true,
                'message' => 'Berhasil menambahkan data',
                'data' => $platform
            ], 201);
    
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal menambahkan data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function updateDetailPlatform(Request $request, $id) {
        $detailPlatform = DetailPlatform::findOrFail($id);
    
        try {
            $updateData = [
                'dpl_total_konten' => $request->dpl_total_konten,
                'dpl_pengikut' => $request->dpl_pengikut,
            ];
    
            $detailPlatform->update($updateData);
    
            return response()->json([
                'status' => true,
                'message' => 'Berhasil mengupdate Detail Platform',
                'data' => [
                    'detail_platform' => $detailPlatform
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal mengupdate detail platform',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function deleteDetailPlatform($id) {
        try {
            $detailPlatform = DetailPlatform::findOrFail($id);
            $detailPlatform->delete();
    
            return response()->json([
                'status' => true,
                'message' => 'Berhasil menghapus Detail Platform'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal menghapus detail platform',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    //End Module

    //Module Content Planner
    public function getOnlineContentPlanner() {
        try {
            $onlinePlanner = OnlinePlanner::with('linkUploadPlanner')->get();
    
            if ($onlinePlanner->isEmpty()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Data Planner tidak ditemukan',
                ], 404);
            }
    
            $formattedData = $onlinePlanner->map(function ($planner) {
                $platformData = [];
                $hasLinks = false;
                
                // Check if linkUploadPlanner relation exists
                if ($planner->linkUploadPlanner) {
                    $platforms = [
                        'instagram' => $planner->linkUploadPlanner->lup_instagram,
                        'facebook' => $planner->linkUploadPlanner->lup_facebook,
                        'twitter' => $planner->linkUploadPlanner->lup_twitter,
                        'youtube' => $planner->linkUploadPlanner->lup_youtube,
                        'website' => $planner->linkUploadPlanner->lup_website,
                        'tiktok' => $planner->linkUploadPlanner->lup_tiktok,
                    ];
                    
                    foreach ($platforms as $platform => $link) {
                        if ($link) {
                            $platformData[$platform] = [
                                'link' => $link
                            ];
                            $hasLinks = true;
                        }
                    }
                }
    
                return [
                    'onp_id' => $planner->onp_id,
                    'onp_tanggal' => $planner->onp_tanggal,
                    'onp_hari' => $planner->onp_hari,
                    'onp_topik_konten' => $planner->onp_topik_konten,
                    'onp_admin' => $planner->onp_admin,
                    'onp_platform' => $planner->onp_platform,
                    'onp_checkpoint' => $planner->onp_checkpoint,
                    'created_at' => $planner->created_at,
                    'updated_at' => $planner->updated_at,
                    'platforms' => $platformData,
                    'has_links' => $hasLinks  // Tambahkan flag untuk sorting
                ];
            });
    
            // Sort berdasarkan has_links
            $sortedData = $formattedData->sortBy('has_links')->values();
            
            // Hapus flag has_links dari hasil akhir (opsional)
            $finalData = $sortedData->map(function($item) {
                unset($item['has_links']);
                return $item;
            });
    
            return response()->json([
                'status' => true,
                'message' => 'Berhasil mendapatkan data Online Planner',
                'data' => [
                    'online_planners' => $finalData
                ]
            ], 200);
    
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal mendapatkan data Online Planner',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getPlannersWithoutLinks() {
        try {
            $onlinePlanners = OnlinePlanner::whereDoesntHave('linkUploadPlanner')->get();
            
            if ($onlinePlanners->isEmpty()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Data Planner tanpa link tidak ditemukan',
                ], 404);
            }
            
            $formattedData = $onlinePlanners->map(function ($planner) {
                return [
                    'onp_id' => $planner->onp_id,
                    'onp_tanggal' => $planner->onp_tanggal,
                    'onp_hari' => $planner->onp_hari,
                    'onp_topik_konten' => $planner->onp_topik_konten,
                    'onp_admin' => $planner->onp_admin,
                    'onp_platform' => $planner->onp_platform,
                    'onp_checkpoint' => $planner->onp_checkpoint,
                    'created_at' => $planner->created_at,
                    'updated_at' => $planner->updated_at,
                    'platforms' => [] // Empty platforms array
                ];
            });
            
            return response()->json([
                'status' => true,
                'message' => 'Berhasil mendapatkan data Online Planner tanpa link',
                'data' => [
                    'online_planners' => $formattedData
                ]
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal mendapatkan data Online Planner tanpa link',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function createLinkOnlinePlanner(Request $request) {
        $validator = Validator::make($request->all(), [
            'onp_id' => 'required|exists:online_planners,onp_id',
            'lup_instagram' => 'url',
            'lup_facebook' => 'url',
            'lup_twitter' => 'url',
            'lup_youtube' => 'url',
            'lup_website' => 'url',
            'lup_tiktok' => 'url',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Data tidak valid',
                'error' => $validator->errors()
            ], 400);
        }

        try {
            $linkupload = LinkUploadPlanner::create([
                'onp_id' => $request->onp_id,
                'lup_instagram' => $request->lup_instagram,
                'lup_facebook' => $request->lup_facebook,
                'lup_twitter' => $request->lup_twitter,
                'lup_youtube' => $request->lup_youtube,
                'lup_website' => $request->lup_website,
                'lup_tiktok' => $request->lup_tiktok,
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Berhasil membuat link',
                'data' => [
                    'link_uplaod' => $linkupload
                ]
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal membuat link',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function createOnlineContentPlanner(Request $request){
        $validator = Validator::make($request->all(), [
            'onp_tanggal' => 'date|required',
            'onp_hari' => 'string|required',
            'onp_topik_konten' => 'string|required|unique:online_planners',
            'onp_admin' => 'required|string',
            'onp_platform' => 'string|required', // Remove the in:website,... constraint
            'onp_checkpoint' => 'string|required|in:jayaridho,gilang,chris,winny',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Data tidak valid',
                'error' => $validator->errors()
            ], 400);
        }
    
        // Validate platform values manually
        $allowedPlatforms = ['website', 'instagram', 'twitter', 'facebook', 'youtube', 'tiktok'];
        $platforms = explode(',', strtolower($request->onp_platform));
        
        foreach ($platforms as $platform) {
            if (!in_array($platform, $allowedPlatforms)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Platform tidak valid',
                    'error' => 'Platform harus salah satu dari: ' . implode(', ', $allowedPlatforms)
                ], 400);
            }
        }
    
        try {
            $onlineplanner = OnlinePlanner::create([
                'onp_tanggal' => $request->onp_tanggal,
                'onp_hari' => $request->onp_hari,
                'onp_topik_konten' => $request->onp_topik_konten,
                'onp_admin' => $request->onp_admin,
                'onp_platform' => strtolower($request->onp_platform),
                'onp_checkpoint' => strtolower($request->onp_checkpoint),
            ]);
    
            return response()->json([
                'status' => true,
                'message' => 'Berhasil membuat Online Content Planner',
                'data' => $onlineplanner
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal membuat Online Content Planner',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function updateLinkOnlinePlanner(Request $request, $id) {
        $linkplanneronline = LinkUploadPlanner::where('lup_id', $id)->first();

        if(!$linkplanneronline) {
            return response()->json([
                'status' => false,
                'message' => 'Data link upload planner tidak ditemukan',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'onp_id' => 'required|exists:online_planners,onp_id',
            'lup_instagram' => 'url',
            'lup_facebook' => 'url',
            'lup_twitter' => 'url',
            'lup_youtube' => 'url',
            'lup_website' => 'url',
            'lup_tiktok' => 'url',
        ]);

        if($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validator error',
                'error' => $validator->errors()
            ], 400);
        }

        try {
            $linkplanneronline->update([
                'lup_instagram' => $request->lup_instagram,
                'lup_facebook' => $request->lup_facebook,
                'lup_twitter' => $request->lup_twitter,
                'lup_youtube' => $request->lup_youtube,
                'lup_website' => $request->lup_website,
                'lup_tiktok' => $request->lup_tiktok,
            ]);
            return response()->json([
                'status' => true,
                'message' => 'Berhasil mengupdate data link upload planner',
                'data' => [
                    'link_upload' => $linkplanneronline
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal mengupdate data link upload planner',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
