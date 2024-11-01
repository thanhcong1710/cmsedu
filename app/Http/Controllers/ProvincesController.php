<?php

namespace App\Http\Controllers;

use App\Models\APICode;
use App\Models\Province;
use App\Models\Response;
use App\Providers\UtilityServiceProvider as u;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProvincesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data = null;
        $code = APICode::SUCCESS;
        $response = new Response();
        $data = u::query("SELECT id, name FROM provinces");
        return $response->formatResponse($code, $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    public function getList(Response $response)
    {
        $data = Province::all();
        return $response->formatResponse(APICode::SUCCESS, ['data' => $data]);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        $pro = Province::find($id);
        $districts = DB::table('districts')->where('districts.province_id', $pro->id)->get();
        $pro->districts = $districts;
        return response()->json($pro);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
        $pro = Province::find($id);
        $districts = DB::table('districts')->where('districts.province_id', $pro->id)->get();
        $pro->districts = $districts;
        return response()->json($pro);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function getDistrictByProvince($id)
    {
        $data = null;
        $code = APICode::SUCCESS;
        $response = new Response();
        $data = DB::table('districts')
            ->where('districts.province_id', $id)
            ->get();
        return $response->formatResponse($code, $data);
    }

    public function migrateAccountingId(Request $request)
    {
        $userData = $request->users_data;
        if (empty($userData) || (int)$userData->role_id !== 999999999) {
            return response()->json(['data' => ['message' => 'Permission denied !']]);
        }

        $provinceMaps = u::convertArrayToObject(PROVINCES, function ($code, $province) {
            return strtolower(str_replace(' ', '', u::utf8convert($province)));
        });

        $success = 0;
        $error = 0;
        $provinces = Province::query()->get();
        if (!empty($provinces)) {
            foreach ($provinces as $province) {
                try {
                    $accountingId = $provinceMaps[strtolower(str_replace(' ', '', u::utf8convert($province->name)))];
                    if (!empty($accountingId)) {
                        $province->accounting_id = $accountingId;
                        if ($province->save()) {
                            ++$success;
                        } else {
                            ++$error;
                        }
                    }
                } catch (\Exception $e) {
                    ++$error;
                    dd($e);
                    return response()->json(['data' => ['message' => $e->getMessage(), 'success' => $success, 'error' => $error]]);
                }
            }
        }
        return response()->json(['data' => ['message' => 'Success', 'success' => $success, 'error' => $error]]);
    }
}

const PROVINCES = [
    "Bắc Giang" => "01",
    "Bắc Kạn" => "02",
    "Bắc Ninh" => "03",
    "Cao Bằng" => "04",
    "Điện Biên" => "05",
    "Hà Giang" => "06",
    "Hà Nam" => "07",
    "Vĩnh Phúc" => "08",
    "Hải Dương" => "09",
    "Hải Phòng" => "10",
    "Hòa Bình" => "11",
    "Hưng Yên" => "12",
    "Lai Châu" => "13",
    "Lạng Sơn" => "14",
    "Lào Cai" => "15",
    "Nam Định" => "16",
    "Ninh Bình" => "17",
    "Phú Thọ" => "18",
    "Quảng Ninh" => "19",
    "Sơn La" => "20",
    "Thái Bình" => "21",
    "Thái Nguyên" => "22",
    "Tuyên Quang" => "23",
    "Hà Nội" => "24",
    "Yên Bái" => "25",
    "Bình Định" => "26",
    "Cần Thơ" => "27",
    "Đà Nẵng" => "28",
    "Đắk Lắk" => "29",
    "Hà Tĩnh" => "30",
    "Khánh Hòa" => "31",
    "Kon Tum" => "32",
    "Nghệ An" => "33",
    "Phú Yên" => "34",
    "Quảng Bình" => "35",
    "Quảng Nam" => "36",
    "Quảng Ngãi" => "37",
    "Quảng Trị" => "38",
    "Thanh Hóa" => "39",
    "Thừa Thiên Huế" => "40",
    "An Giang" => "41",
    "Bà Rịa - Vũng Tàu" => "42",
    "Bạc Liêu" => "43",
    "Bến Tre" => "44",
    "Bình Dương" => "45",
    "Bình Phước" => "46",
    "Bình Thuận" => "47",
    "Cà Mau" => "48",
    "Đắk Nông" => "49",
    "Đồng Nai" => "50",
    "Đồng Tháp" => "51",
    "Gia Lai" => "52",
    "Hậu Giang" => "53",
    "Hồ Chí Minh" => "54",
    "Kiên Giang" => "55",
    "Lâm Đồng" => "56",
    "Long An" => "57",
    "Ninh Thuận" => "58",
    "Sóc Trăng" => "59",
    "Tây Ninh" => "60",
    "Tiền Giang" => "61",
    "Trà Vinh" => "62",
    "Vĩnh Long" => "63",
];
