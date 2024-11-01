<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Room;

class RoomsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        $pageSize = $request->pageSize == null? 5: $request->pageSize ;
        $rooms = Room::paginate($pageSize);
        foreach ($rooms as $room) {
            # code...
            $branch = DB::table('branches')->where('branches.id', $room->branch_id)->get();
            $room->branch_name = $branch[0]->name;
        }
        return response()->json($rooms);
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

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $room = new Room();
        $room->room_name = $request->room_name;
        $room->branch_id = $request->branch_id;
        $room->type = $request->type;
        $room->created_at = now();
        $room->updated_at = now();
        $room->status = $request->status;

        $room->save();

        return response()->success('Thêm mới thành công!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function list($page,Request $request){
        $zones = [];
        $response = [
            'done' => false,
            'message' => 'Invalid Token'
        ];

        $query_information = self::query($request);
        $get_code_query = $query_information['base_query'];
        // return $get_code_query
        $rooms =  DB::select(DB::raw($get_code_query));
        if ($zones){
        }
        $cpage = $query_information['page'];
        $limit = $query_information['limit'];
        $total = $query_information['total'];
        $lpage = $total <= $limit ? 1 : (int)round(ceil($total/$limit));
        $ppage = $cpage > 0 ?  $cpage-1 : 0;
        $npage = $cpage < $lpage ? $cpage +1 : $lpage;
        $response['done'] = true;
        $response['pagination'] = [
            'spage' => 1,
            'ppage' => $ppage,
            'npage' => $npage,
            'lpage' => $lpage,
            'cpage' => $cpage,
            'limit' => $limit,
            'total' => $total
        ];
        $response['rooms'] = $rooms;
        $response['message'] = 'successful';
        return response()->json($response);

    }

    private function query($request, $page = 1, $limit = 20, $filter = NULL){
        $selected_page = $request->page ?  (int) $request->page: 0;
        $page = $selected_page > 0 ? $selected_page : $page;
        $offset = $page == 1 ? 0 : $limit * ($page-1);
        $limitation =  $limit > 0 ? " LIMIT $offset, $limit": "";
        $filter = $request->filter ? (int)$request->filter : $filter;

        $where = '';

        $branch = $request->branches;
        if($branch and $branch != '')
            $where .= " AND branches.id in ($branch)";
        $roomName = $request->room_name;
        if($roomName != '' )
            $where .= " AND rooms.room_name like '$roomName%'";
        $status = $request->status;
        if($status != '' )
            $where .= " AND rooms.status = $status";

        $query = "SELECT rooms.*, branches.name as branch_name from rooms 
                    Left JOIN branches on branches.id = rooms.branch_id 
                    WHERE 1 = 1 
                    $where ORDER BY rooms.id DESC $limitation ";
        $count_query = "SELECT COUNT(rooms.id) as total FROM rooms 
                        Left JOIN branches on branches.id = rooms.branch_id
                        WHERE 1 = 1 $where";
        $total = DB::select(DB::raw($count_query));
        $total = $total[0]->total;
        return [
            'base_query' => $query,
            'total' => $total,
            'limit' => $limit,
            'page' => $page
        ];
    }

    public function show($id)
    {
        //
       $room = DB::select(DB::raw("SELECT r.*,br.name as branch_name from rooms as r LEFT JOIN branches as br on br.id = r.branch_id where r.id = $id"));
        return response()->json($room[0]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
       $room = Room::find($id);
        if ($room){
            return response()->json($room);
        }
        return response()->json();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $room = Room::find($id);
        $room->room_id = $request->room_id;
        $room->room_name = $request->room_name;
        $room->branch_id = $request->branch_id;
        $room->updated_at = now();
        $room->status = $request->status;
        $room->save();

        return response()->success('Cập nhật thành công!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
         $room = Room::find($id);
        if ($room->delete()){
            DB::table('sessions')->where('room_id', $id)->delete();
            return response()->json("Delete Success!");
        }
        return response()->json("Delete Error");
    }

    public function search(Request $request){
        $field = explode("," , $request->field);
        $keyword = explode(",", $request->keyword);
        $pageSize = $request->pageSize;
        // dd($field);
        $column = DB::getSchemaBuilder()->getColumnListing("rooms");

        $p = DB::table('rooms');

        if (in_array($field[0], $column)) $p->where($field[0], 'like', '%'.$keyword[0].'%');
        for ($i=1; $i<count($field); $i++){
            if (in_array($field[$i], $column)){
                $p->Where($field[$i], 'like', '%'.$keyword[$i].'%');
            }
        }
        return $p->paginate($pageSize);
    }
    public function getRoomsByBranch(Request $request,$id)
    {
        if($request->status!=NULL){
            $listRooms = Room::where('branch_id',$id)->where('status',$request->status)->get();
        }else{
            $listRooms = Room::where('branch_id',$id)->get();
        }
        return response()->json($listRooms);
    }
}
