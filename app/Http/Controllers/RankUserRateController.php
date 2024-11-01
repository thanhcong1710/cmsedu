<?php

namespace App\Http\Controllers;

use App\Models\APICode;
use App\Models\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as x;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Exception;
use App\Models\ProcessExcel;
use App\Models\RankUserRate;

class RankUserRateController extends Controller
{

    public function lists(Request $request)
    {
      $query = $this->queryList();
      $results = DB::select(DB::raw($query));
      return $results;
    }

    public function show($id) 
    {
        $result = RankUserRate::find($id);
        return response()->success($result);
    }

    public function update(Request $request, $id)
    {
      $dataUpdate = [
        'branch_id' => $request->branch_id,
        'score_id' => $request->score_id,
        'rank_id' => $request->rank_id,
        'note' => $request->note,
        'date' => now(),
        'updated_at' => now()
      ];
      try {
        DB::table('rank_user_rate')->where('id',$id)->update($dataUpdate);
        return response()->success('OK');
      } catch(\Mockey\Exception $e) {
        return response()->error('Fail!');
      }
      
    }

    public function exportExcelExample(Request $request)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        ProcessExcel::styleCells($spreadsheet, "A1:D1", "ffc107", "black", 12, 0, 3, "center", "center", true);
        ProcessExcel::styleCells($spreadsheet, "E1:E10", "FFC000", "black", 11, 0, 3, "center", "center", false);
        ProcessExcel::styleCells($spreadsheet, "F1:F10", "FFC000", "black", 11, 0, 3, "center", "center", false);

        $sheet->getRowDimension('1')->setRowHeight(30);

        $sheet->setCellValue('A1', 'MÃ NV');
        $sheet->setCellValue('B1', 'TÊN NV');
        $sheet->setCellValue('C1', 'HẠNG NV');
        $sheet->setCellValue('D1', 'GHI CHÚ');

        $sheet->getColumnDimension("A")->setWidth(10);
        $sheet->getColumnDimension("B")->setWidth(30);
        $sheet->getColumnDimension("C")->setWidth(20);
        $sheet->getColumnDimension("D")->setWidth(30);
        $sheet->getColumnDimension("E")->setWidth(30);
        $sheet->getColumnDimension("F")->setWidth(30);


        $sheet->setCellValue("A2", 'G0000');
        $sheet->setCellValue("B2", 'Nguyen Van A');
        $sheet->setCellValue("C2", 'CM1');
        $sheet->setCellValue("D2", '');

        $sheet->setCellValue("A3", 'G0001');
        $sheet->setCellValue("B3", 'Nguyen Van B');
        $sheet->setCellValue("C3", 'CM2');
        $sheet->setCellValue("D3", '');


        ProcessExcel::styleCells($spreadsheet, "C1:C4", "FFC000", "black", 11, 0, 3, "center", "center", false);

        $sheet->setCellValue('E2', 'CM có 5 bậc nhân viên:');
        $sheet->setCellValue('E3', 'CM1');
        $sheet->setCellValue('E4', 'CM2');
        $sheet->setCellValue('E5', 'CM3');
        $sheet->setCellValue('E6', 'CM4');
        $sheet->setCellValue('E7', 'CM5');

        $sheet->setCellValue('F2', 'OMs có 3 bậc quản lý:');
        $sheet->setCellValue('F3', 'JOM');
        $sheet->setCellValue('F4', 'OM');
        $sheet->setCellValue('F5', 'SOM');


        $writer = new Xlsx($spreadsheet);
        try {
          header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
          header('Content-Disposition: attachment;filename="Biểu mẫu đánh giá nhân viên.xlsx"');
          header('Cache-Control: max-age=0');
          $writer->save("php://output");
        } catch (Exception $exception) {
          throw $exception;
        }
        exit;
    }

    public function exportExcel(Request $request)
    {
      $query = $this->queryList();
      $lists = DB::select(DB::raw($query));


      $spreadsheet = new Spreadsheet();
      $sheet = $spreadsheet->getActiveSheet();
      ProcessExcel::styleCells($spreadsheet, "A1:D1", "ffc107", "black", 12, 0, 3, "center", "center", true);
      ProcessExcel::styleCells($spreadsheet, "E1:E10", "FFC000", "black", 11, 0, 3, "center", "center", false);
      ProcessExcel::styleCells($spreadsheet, "F1:F10", "FFC000", "black", 11, 0, 3, "center", "center", false);

      $sheet->getRowDimension('1')->setRowHeight(30);

      $sheet->setCellValue('A1', 'MÃ NV');
      $sheet->setCellValue('B1', 'TÊN NV');
      $sheet->setCellValue('C1', 'HẠNG NV');
      $sheet->setCellValue('D1', 'GHI CHÚ');

      $sheet->getColumnDimension("A")->setWidth(10);
      $sheet->getColumnDimension("B")->setWidth(30);
      $sheet->getColumnDimension("C")->setWidth(20);
      $sheet->getColumnDimension("D")->setWidth(30);
      $sheet->getColumnDimension("E")->setWidth(30);
      $sheet->getColumnDimension("F")->setWidth(30);


      $x = 2;
      foreach ($lists as $row) {

        $sheet->setCellValue("A$x", $row->hrm_id);
        $sheet->setCellValue("B$x", $row->full_name);
        $sheet->setCellValue("C$x", $row->rank_name);
        $sheet->setCellValue("D$x", $row->note);

        $x++;
      }
      ProcessExcel::styleCells($spreadsheet, "C1:C$x", "FFC000", "black", 11, 0, 3, "center", "center", false);

      $sheet->setCellValue('E2', 'CM có 5 bậc nhân viên:');
      $sheet->setCellValue('E3', 'CM1');
      $sheet->setCellValue('E4', 'CM2');
      $sheet->setCellValue('E5', 'CM3');
      $sheet->setCellValue('E6', 'CM4');
      $sheet->setCellValue('E7', 'CM5');

      $sheet->setCellValue('F2', 'OMs có 3 bậc quản lý:');
      $sheet->setCellValue('F3', 'JOM');
      $sheet->setCellValue('F4', 'OM');
      $sheet->setCellValue('F5', 'SOM');


      $writer = new Xlsx($spreadsheet);
      try {
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Danh sách đánh giá nhân viên.xlsx"');
        header('Cache-Control: max-age=0');
        $writer->save("php://output");
      } catch (Exception $exception) {
        throw $exception;
      }
      exit;

    }

    public function uploadRateExcel(Request $request)
    {
      $dataRequest = $request->all();
      $userData = $request->users_data;
      $code = APICode::SUCCESS;
      $data = null;
      $response = new Response();

      $attachedFile = $dataRequest['files'];
      if(!$attachedFile) {
        $code = APICode::PERMISSION_DENIED;
        return $response->formatResponse($code, $data);
      }

      // SAVE FILES TO SERVER
      $explod = explode(',', $attachedFile);
      $decod = base64_decode($explod[1]);
      if (str_contains($explod[0], 'spreadsheetml')){
        $extend = 'xlsx';
      } else {
        $code = APICode::PERMISSION_DENIED;
        return $response->formatResponse($code, $data);
      }
      $fileAttached = md5($request->attached_file.str_random()).'.'.$extend;
      $p = FOLDER.DS.'doc\\other\\'.$fileAttached;
      file_put_contents($p, $decod);

      $reader = new x();
      $reader->setReadDataOnly(true);
      $spreadsheet = $reader->load( $p);
      $sheet = $spreadsheet->setActiveSheetIndex(0);
      $dataXslx = $sheet->toArray();

      unset($dataXslx[0]);
      //Progress
      try {
        foreach ($dataXslx as $item) {
          if( isset($item[0]) and $item[0] != null ) {
            $hrm_id = $item[0];
            $rankAlias = $item[2];
            $note = $item[3];

            $user = DB::table('users as u')
              ->join('term_user_branch as tub','tub.user_id','u.id')
              ->where('u.hrm_id',$hrm_id)->where('tub.status',1)->first();
            $rank = DB::table('ranks as r')->where('r.name',$rankAlias)->where('r.type',0)->first();
            if($user and $rank) {
              $validRate = DB::table('rank_user_rate')->where('user_id',$user->user_id)
                  ->where('branch_id',$user->branch_id)->where('rank_id',$rank->id)->first();
              $dataInsert = [
                'user_id' => $user->user_id,
                'branch_id' => $user->branch_id,
                'rank_id' => $rank->id,
                'date' => date('Y-m-d'),
                'created_at' => date('Y-m-d'),
                'updated_at' => date('Y-m-d'),
                'score_id' => $dataRequest['score'],
                'editor_id' => $userData->id,
                'note' => $note
              ];
              if( !$validRate ) {
                DB::table('rank_user_rate')->insert($dataInsert);
              } else {
                DB::table('rank_user_rate')->where('id',$validRate->id)->update($dataInsert);
              }
            }
          }
        }
        return $response->formatResponse($code, $data);
      } catch( \Exception $e ) {
        $code = 500;
        return $response->formatResponse($code, $data);
      }
    }

    public function remove( Request $request, $id )
    {
      $code = APICode::SUCCESS;
      $data = null;
      $response = new Response();
      try {
        DB::table('rank_user_rate')->where('id',$id)->delete();
        return $response->formatResponse($code, $data);
      } catch (\Exception $e) {
        $code = 500;
        return $response->formatResponse($code, $data);
      }
    }

    public function queryList($where = "")
    {
      $query = "
        SELECT
          rur.id as id,
          u.hrm_id as hrm_id,
          u.full_name as full_name,
          r.name as rank_name,
          s.name as score_name,
          b.name as branch,
          rur.note as note
        FROM rank_user_rate as rur
        LEFT JOIN users as u on u.id = rur.user_id
        LEFT JOIN ranks as r on r.id = rur.rank_id
        LEFT JOIN scores as s on s.id = rur.score_id
        LEFT JOIN branches as b on b.id = rur.branch_id
        WHERE u.status = 1
        $where
      ";
      return $query;
    }

    public function getRanksList()
    {

      $qr = "SELECT r.* FROM ranks as r WHERE r.type = 0";

      $ranks = DB::select(DB::raw($qr));

      return response()->json($ranks);
    }

    public function searchRanksList( Request $request)
    {
      dd($request->all());
    }
}
