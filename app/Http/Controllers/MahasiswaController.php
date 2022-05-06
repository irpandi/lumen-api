<?php

namespace App\Http\Controllers;

use App\Models\TblMahasiswa;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class MahasiswaController extends Controller
{
    // * Function untuk get all data mahasiswa
    public function index(Request $req)
    {
        $status  = 200;
        $message = 'success';

        $search = '';
        $sort   = 'tbl_mahasiswa.id';
        $order  = 'desc';
        $limit  = 10;

        $reqSearch = isset($req->search) ? $req->search : '';
        $reqSort   = isset($req->sort) ? $req->sort : '';
        $reqOrder  = isset($req->order) ? $req->order : '';
        $reqLimit  = isset($req->limit) ? $req->limit : '';

        if ($reqSearch != '') {
            $search = $reqSearch;
        }

        if ($reqSort != '') {
            $sort = $reqSort;
        }

        if ($reqOrder != '') {
            $order = $reqOrder;
        }

        if ($reqLimit != '') {
            $limit = $reqLimit;
        }

        $data = TblMahasiswa::select(
            'tbl_mahasiswa.id',
            'tbl_mahasiswa.user_id',
            'tbl_mahasiswa.nim',
            'tbl_mahasiswa.nik',
            'user.name'
        )
            ->leftJoin('user', 'user.id', '=', 'tbl_mahasiswa.user_id')
            ->where('tbl_mahasiswa.nim', 'like', '%' . $search . '%')
            ->orWhere('user.name', 'like', '%' . $search . '%')
            ->orderBy($sort, $order)
            ->paginate($limit);

        $response = array(
            'status'  => $status,
            'message' => $message,
            'data'    => $data,
        );

        return response()->json($response, $status);
    }

    // * Function untuk show detail mahasiswa
    public function show($id)
    {
        $status  = 200;
        $message = 'success';

        $data = TblMahasiswa::where('id', $id)
            ->with('user')
            ->first();

        $response = array(
            'status'  => $status,
            'message' => $message,
            'data'    => $data,
        );

        return response()->json($response, $status);
    }

    // * Function untuk store data mahasiswa
    public function store(Request $req)
    {
        $this->validate($req, [
            'nim'   => 'required|unique:tbl_mahasiswa',
            'nik'   => 'required|unique:tbl_mahasiswa',
            'name'  => 'required',
            'email' => 'required|unique:user|email',
        ]);

        $status  = 500;
        $message = 'failed';
        $data    = null;

        $hashPassword = Hash::make('secret');

        $storeUser = User::create($req->only(['name', 'email']));

        if ($storeUser) {
            User::where('id', $storeUser->id)
                ->update([
                    'password' => $hashPassword,
                ]);

            $storeMahasiswa = TblMahasiswa::create($req->only(['nim', 'nik', 'date_of_birth', 'place_of_birth', 'gender', 'phone_number', 'address']));
            if ($storeMahasiswa) {
                TblMahasiswa::where('id', $storeMahasiswa->id)
                    ->update([
                        'user_id' => $storeUser->id,
                    ]);

                $status  = 200;
                $message = 'success';
                $data    = $storeMahasiswa;
            }
        }

        $response = array(
            'status'  => $status,
            'message' => $message,
            'data'    => $data,
        );

        return response()->json($response, $status);
    }

    // * Function untuk update data mahasiswa
    public function update(Request $req, $id)
    {
        $message = 'success';
        $status  = 200;
        $data    = 1;
        $rules   = [
            'name'  => 'required',
            'email' => 'required|email',
        ];

        $mahasiswa = TblMahasiswa::where('id', $id)
            ->with('user')
            ->first();

        if ($mahasiswa && $mahasiswa->user) {
            if ($mahasiswa->user->email != $req->email) {
                $rules['email'] = 'required|unique:user|email';
            }
        }

        $this->validate($req, $rules);

        User::where('id', $mahasiswa->user->id)
            ->update([
                'name'  => $req->name,
                'email' => $req->email,
            ]);

        TblMahasiswa::where('id', $id)
            ->update([
                'date_of_birth'  => $req->date_of_birth,
                'place_of_birth' => $req->place_of_birth,
                'gender'         => $req->gender,
                'phone_number'   => $req->phone_number,
                'address'        => $req->address,
            ]);

        $response = array(
            'status'  => $status,
            'message' => $message,
            'data'    => $data,
        );

        return response()->json($response, $status);
    }

    // * Function untuk delete data mahasiswa
    public function destroy($id)
    {
        $status  = 500;
        $message = 'failed';
        $data    = null;

        $mahasiswa = TblMahasiswa::where('id', $id)
            ->with('user')
            ->first();

        if ($mahasiswa) {
            $mahasiswa->delete();

            if ($mahasiswa->user) {
                $user = User::find($mahasiswa->user->id);
                $user->delete();
            }

            $status  = 200;
            $message = 'success';
            $data    = 1;
        }

        $response = array(
            'status'  => $status,
            'message' => $message,
            'data'    => $data,
        );

        return response()->json($response, $status);
    }
}
