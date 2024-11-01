<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SchedulesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $schedules = $request->pageSize == null?5: $request->pageSize;
        $schedules = Product::paginate($pageSize);
        return response()->json($schedules);
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
        //
        $this->validate($request, [
            'cjrn_classdate' => 'required'
        ]);

        $schedule = new Schedule();
        $schedule->id = $request->id;
        $schedule->cjrn_classdate = $request->cjrn_classdate;

        $schedule->save();
        return response()->json($schedule);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        $schedule = Schedule::find($id);
        if ($schedule) return response()->json($schedule);
        return response()->json();
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
         $schedule = Schedule::find($id);
        if ($schedule) return response()->json($schedule);
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
        //
        $this->validate($request, [
            'id' => 'required',
            'cjrn_classdate' => 'required'
        ]);

        $schedule = Schedule::find($id);
        $schedule->id = $request->id;
        $schedule->cjrn_classdate = $request->cjrn_classdate;

        $schedule->save();
        return response()->json($schedule);
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
        $schedule = Schedule::find($id);
        if ($schedule->delete()) {
            DB::table('contracts')->where('start_cjrn_id',$id)->delete();
            return response()->json("delete success");
        }
        return response()->json("delete Error");
    }

    public function search(Request $request){
        $field = explode("," , $request->field);
        $keyword = explode(",", $request->keyword);
        $pageSize = $request->pageSize;
        // dd($field);
        $column = DB::getSchemaBuilder()->getColumnListing("schedules");

        $p = DB::table('schedules');

        if (in_array($field[0], $column)) $p->where($field[0], 'like', '%'.$keyword[0].'%');
        for ($i=1; $i<count($field); $i++){
            if (in_array($field[$i], $column)){
                $p->Where($field[$i], 'like', '%'.$keyword[$i].'%');
            }
        }
        return $p->paginate($pageSize);
    }

    public function getSchedulesByClassId($classId)
    {
        $schedules = Schedule::getSchedules($classId);
        return response()->json(['success' => true, 'code' => 200, 'message' => 'success!', 'data' => $schedules]);
    }
}
