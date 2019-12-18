<?php

namespace App\Http\Controllers;

use App\Helpers\Helpme;
use Illuminate\Http\Request;
use App\progress;
use App\report;

use DateTime;
use DateInterval;
use DatePeriod;
use Validator;

class ReportController extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index($proj_id)
	{
		$proj_id=			intval($proj_id);
		$reportDats=	report::where('proj_id',$proj_id)->get();

		// set header table
		$timeFrameProg= progress::selectRaw("DISTINCT YEAR(created_at) AS Year, MONTH(created_at) AS Month")->where('proj_id', $proj_id)->orderBy('created_at')->get();
	
		// process report dats into month, year, and plan score (array)
		$yearRep= 	array();
		$monthRep=	array();
		$plan=			array();
		$prevYearRep=	0;
		$flag=0;
		foreach($reportDats as $atime){
			$flag=1;
			if($prevYearRep != intval($atime->report_year)){
				$yearRep[]=		$atime->report_year;
				$prevYearRep=	intval($atime->report_year);
			}
			$month=	date('M', mktime(0, 0, 0, $atime->report_month, 10));
			$monthRep[$prevYearRep][]= $month;
			$plan[$prevYearRep][$month]= $atime->report_plan;
		}

		// process progress time frame dats into month, year (array)
		$yearProg= array();
		$monthProg= array();
		$prevYearProg=	0;
		foreach($timeFrameProg as $atime){
			if($prevYearProg != intval($atime->Year)){
				$yearProg[]=		$atime->Year;
				$prevYearProg=	intval($atime->Year);
			}
			$monthProg[$prevYearProg][]= date('M', mktime(0, 0, 0, $atime->Month, 10));
		}

		// view variable
		$monthstr='';
		$keystr='';
		$planstr='';
		$planCumstr='';
		$counter=0;
		$prevPlanVal=0;
		$labels='';
		$planDats='';

		// merger arr
		$year= array_unique(array_merge($yearRep, $yearProg));
		$year= array_combine(range(0, count($year)-1), $year);
		$month=array();
		for($i=0; $i < count($year); $i++){
			// idx= index
			$yearIdx=	$year[$i];
			$arr1= isset($monthRep[$yearIdx]) ? $monthRep[$yearIdx] : array();
			$arr2= isset($monthProg[$yearIdx]) ? $monthProg[$yearIdx] : array();
			$month[$yearIdx]= array_unique(array_merge($arr1, $arr2));
			if(count($month[$yearIdx]) > 0) $month[$yearIdx]= array_combine(range(0, count($month[$yearIdx])-1), $month[$year[$i]]);

			// set view variable
			$MonthFormat=<<<mfr
			<td class="text-center">%s</td>
			mfr;
			for($j=0; $j< count($month[$yearIdx]); $j++){
				$monthIdx=	$month[$year[$i]][$j];
				$counter++;
				$monthstr.= 	sprintf($MonthFormat, trim($monthIdx));
				$keystr.=			sprintf($MonthFormat, $counter);
				$planVal=			isset($plan[$year[$i]][$monthIdx]) ? $plan[$year[$i]][$monthIdx] : 0;
				$planstr.=		sprintf($MonthFormat, trim($planVal));
				$planCumstr.=	sprintf($MonthFormat, trim($planVal + $prevPlanVal));
				$labels.="['".$monthIdx."','".$yearIdx."'],";
				$prevPlanVal+= $planVal;
				$planDats.= "'$prevPlanVal',";

				#scoring PP
				$itemPP=  	progress::join('m_process','tb_progress.proc_id','=','m_process.proc_id')
									->join('tb_report','tb_progress.proj_id','=','tb_report.proj_id')
									->select('tb_progress.*','m_process.proc_score','m_process.proc_parid')
									->where([['tb_progress.proj_id',$proj_id],['proc_parid',2],['mat_id','>',0]])
									->whereYear('tb_progress.created_at',$yearIdx)
									->whereMonth('tb_progress.created_at',date('m', strtotime($monthIdx)))
									->orderBy('tb_progress.created_at','ASC')
									->get();
				$countItemPP[]= $itemPP->where('proc_id',3)->unique('mat_id')->count();
				$sumItemPP1[]= $itemPP->where('proc_id',3)->unique('mat_id')
									->pluck('proc_score')->sum();
				if(array_sum($sumItemPP1) == 0 || array_sum($countItemPP) == 0){
					$scorePP1[] = 0;
				}else{
					$scorePP1[]= array_sum($sumItemPP1) / array_sum($countItemPP);
				}
				$sumItemPP2[]= $itemPP->where('proc_id',4)->unique('mat_id')
									->pluck('proc_score')->sum();
				if(array_sum($sumItemPP2) == 0 || array_sum($countItemPP) == 0){
					$scorePP2[]= 0;
				}else{
					$scorePP2[]= array_sum($sumItemPP2) / array_sum($countItemPP);
				}
				$sumItemPP3[]= $itemPP->where('proc_id',5)->unique('mat_id')
									->pluck('proc_score')->sum();
				if(array_sum($sumItemPP3) == 0 || array_sum($countItemPP) == 0){
					$scorePP3[]= 0;
				}else{
					$scorePP3[]= array_sum($sumItemPP3) / array_sum($countItemPP);
				}

				#scoring PR
				$itemPR=  	progress::join('m_process','tb_progress.proc_id','=','m_process.proc_id')
									->join('tb_report','tb_progress.proj_id','=','tb_report.proj_id')
									->select('tb_progress.*','m_process.proc_score','m_process.proc_parid')
									->where([['tb_progress.proj_id',$proj_id],['proc_parid',6],['mat_id','>',0]])
									->whereYear('tb_progress.created_at',$yearIdx)
									->whereMonth('tb_progress.created_at',date('m', strtotime($monthIdx)))
									->orderBy('tb_progress.created_at','ASC')
									->get();
				$countItemPR[]= $itemPP->where('proc_id',5)->unique('mat_id')->count();
				$sumItemPR1[]= $itemPR->where('proc_id',7)->unique('mat_id')
									->pluck('proc_score')->sum();
				if(array_sum($sumItemPR1) == 0 || array_sum($countItemPR) == 0){
					$scorePR1[] = 0;
				}else{
					$scorePR1[]= array_sum($sumItemPR1) / array_sum($countItemPR);
				}									
				$sumItemPR2[]= $itemPR->where('proc_id',8)->unique('mat_id')
									->pluck('proc_score')->sum();
				if(array_sum($sumItemPR2) == 0 || array_sum($countItemPR) == 0){
					$scorePR2[] = 0;
				}else{
					$scorePR2[]= array_sum($sumItemPR2) / array_sum($countItemPR);
				}			
				$sumItemPR3[]= $itemPR->where('proc_id',9)->unique('mat_id')
									->pluck('proc_score')->sum();
				if(array_sum($sumItemPR3) == 0 || array_sum($countItemPR) == 0){
					$scorePR3[]= 0;
				}else{
					$scorePR3[]= array_sum($sumItemPR3) / array_sum($countItemPR);
				}

				#scoring FA
				$itemFA=  	progress::join('m_process','tb_progress.proc_id','=','m_process.proc_id')
									->join('tb_report','tb_progress.proj_id','=','tb_report.proj_id')
									->select('tb_progress.*','m_process.proc_score','m_process.proc_parid')
									->where([['tb_progress.proj_id',$proj_id],['proc_parid',10],['pp_id','>',0]])
									->whereYear('tb_progress.created_at',$yearIdx)
									->whereMonth('tb_progress.created_at',date('m', strtotime($monthIdx)))
									->orderBy('tb_progress.created_at','ASC')
									->get();
				$countItemFA1[]= $itemFA->where('proc_id',11)->unique('pp_id')->count();									
				$sumItemFA1[]= $itemFA->where('proc_id',11)->unique('pp_id')
									->pluck('proc_score')->sum();
				if(array_sum($sumItemFA1) == 0 || array_sum($countItemFA1) == 0){
					$scoreFA1[] = 0;
				}else{
					$scoreFA1[]= array_sum($sumItemFA1) / array_sum($countItemFA1);
				}									
				$countItemFA2[]= $itemFA->where('proc_id',12)->unique('pp_id')->count();									
				$sumItemFA2[]= $itemFA->where('proc_id',12)->unique('pp_id')
									->pluck('proc_score')->sum();
				if(array_sum($sumItemFA2) == 0 || array_sum($countItemFA2) == 0){
					$scoreFA2[] = 0;
				}else{
					$scoreFA2[]= array_sum($sumItemFA2) / array_sum($countItemFA2);
				}
				$countItemFA3[]= $itemFA->where('proc_id',13)->unique('pp_id')->count();			
				$sumItemFA3[]= $itemFA->where('proc_id',13)->unique('pp_id')
									->pluck('proc_score')->sum();
				if(array_sum($sumItemFA3) == 0 || array_sum($countItemFA3) == 0){
					$scoreFA3[]= 0;
				}else{
					$scoreFA3[]= array_sum($sumItemFA3) / array_sum($countItemFA3);
				}

				#scoring SA
				$itemSA=  	progress::join('m_process','tb_progress.proc_id','=','m_process.proc_id')
									->join('tb_report','tb_progress.proj_id','=','tb_report.proj_id')
									->select('tb_progress.*','m_process.proc_score','m_process.proc_parid')
									->where([['tb_progress.proj_id',$proj_id],['proc_parid',14],['pan_id','>',0]])
									->whereYear('tb_progress.created_at',$yearIdx)
									->whereMonth('tb_progress.created_at',date('m', strtotime($monthIdx)))
									->orderBy('tb_progress.created_at','ASC')
									->get();
				$countItemSA[]= $itemSA->where('proc_id',15)->unique('pan_id')->count();
				$sumItemSA1[]= $itemSA->where('proc_id',15)->unique('pan_id')
									->pluck('proc_score')->sum();
				if(array_sum($sumItemSA1) == 0 || array_sum($countItemSA) == 0){
					$scoreSA1[] = 0;
				}else{
					$scoreSA1[]= array_sum($sumItemSA1) / array_sum($countItemSA);
				}									
				$sumItemSA2[]= $itemSA->where('proc_id',16)->unique('pan_id')
									->pluck('proc_score')->sum();
				if(array_sum($sumItemSA2) == 0 || array_sum($countItemSA) == 0){
					$scoreSA2[] = 0;
				}else{
					$scoreSA2[]= array_sum($sumItemSA2) / array_sum($countItemSA);
				}
				$sumItemSA3[]= $itemSA->where('proc_id',17)->unique('pan_id')
									->pluck('proc_score')->sum();
				if(array_sum($sumItemSA3) == 0 || array_sum($countItemSA) == 0){
					$scoreSA3[]= 0;
				}else{
					$scoreSA3[]= array_sum($sumItemSA3) / array_sum($countItemSA);
				}
				
				#scoring AS
				$itemAS=  	progress::join('m_process','tb_progress.proc_id','=','m_process.proc_id')
									->join('tb_report','tb_progress.proj_id','=','tb_report.proj_id')
									->select('tb_progress.*','m_process.proc_score','m_process.proc_parid')
									->where([['tb_progress.proj_id',$proj_id],['proc_parid',18],['block_id','>',0]])
									->whereYear('tb_progress.created_at',$yearIdx)
									->whereMonth('tb_progress.created_at',date('m', strtotime($monthIdx)))
									->orderBy('tb_progress.created_at','ASC')
									->get();
				$countItemAS[]= $itemAS->where('proc_id',19)->unique('block_id')->count();
				$sumItemAS1[]= $itemAS->where('proc_id',19)->unique('block_id')
									->pluck('proc_score')->sum();
				if(array_sum($sumItemAS1) == 0 || array_sum($countItemAS) == 0){
					$scoreAS1[] = 0;
				}else{
					$scoreAS1[]= array_sum($sumItemAS1) / array_sum($countItemAS);
				}									
				$sumItemAS2[]= $itemAS->where('proc_id',20)->unique('block_id')
									->pluck('proc_score')->sum();
				if(array_sum($sumItemAS2) == 0 || array_sum($countItemAS) == 0){
					$scoreAS2[] = 0;
				}else{
					$scoreAS2[]= array_sum($sumItemAS2) / array_sum($countItemAS);
				}
				$sumItemAS3[]= $itemAS->where('proc_id',21)->unique('block_id')
									->pluck('proc_score')->sum();
				if(array_sum($sumItemAS3) == 0 || array_sum($countItemAS) == 0){
					$scoreAS3[]= 0;
				}else{
					$scoreAS3[]= array_sum($sumItemAS3) / array_sum($countItemAS);
				}

				#scoring ER
				$itemER=  	progress::join('m_process','tb_progress.proc_id','=','m_process.proc_id')
									->join('tb_report','tb_progress.proj_id','=','tb_report.proj_id')
									->select('tb_progress.*','m_process.proc_score','m_process.proc_parid')
									->where([['tb_progress.proj_id',$proj_id],['proc_parid',22],['ship_id','>',0]])
									->whereYear('tb_progress.created_at',$yearIdx)
									->whereMonth('tb_progress.created_at',date('m', strtotime($monthIdx)))
									->orderBy('tb_progress.created_at','ASC')
									->get();
				$countItemER[]= $itemER->where('proc_id',23)->unique('ship_id')->count();
				$sumItemER1[]= $itemER->where('proc_id',23)->unique('ship_id')
									->pluck('proc_score')->sum();
				if(array_sum($sumItemER1) == 0 || array_sum($countItemER) == 0){
					$scoreER1[] = 0;
				}else{
					$scoreER1[]= array_sum($sumItemER1) / array_sum($countItemER);
				}									
				$sumItemER2[]= $itemER->where('proc_id',24)->unique('ship_id')
									->pluck('proc_score')->sum();
				if(array_sum($sumItemER2) == 0 || array_sum($countItemER) == 0){
					$scoreER2[] = 0;
				}else{
					$scoreER2[]= array_sum($sumItemER2) / array_sum($countItemER);
				}
				$sumItemER3[]= $itemER->where('proc_id',25)->unique('ship_id')
									->pluck('proc_score')->sum();
				if(array_sum($sumItemER3) == 0 || array_sum($countItemER) == 0){
					$scoreER3[] = 0;
				}else{
					$scoreER3[]= array_sum($sumItemER3) / array_sum($countItemER);
				}
				$sumItemER4[]= $itemER->where('proc_id',26)->unique('ship_id')
									->pluck('proc_score')->sum();
				if(array_sum($sumItemER4) == 0 || array_sum($countItemER) == 0){
					$scoreER4[] = 0;
				}else{
					$scoreER4[]= array_sum($sumItemER4) / array_sum($countItemER);
				}
				$sumItemER5[]= $itemER->where('proc_id',27)->unique('ship_id')
									->pluck('proc_score')->sum();
				if(array_sum($sumItemER5) == 0 || array_sum($countItemER) == 0){
					$scoreER5[] = 0;
				}else{
					$scoreER5[]= array_sum($sumItemER5) / array_sum($countItemER);
				}
			}
		}
		for($i=0; $i < count($scorePP1); $i++){
			$realCumScore[$i]= $scorePP1[$i]+$scorePP2[$i]+$scorePP3[$i]+$scorePR1[$i]+$scorePR2[$i]+$scoreFA1[$i]+$scorePR3[$i]+$scoreFA2[$i]+$scoreFA3[$i]+$scoreSA1[$i]+$scoreSA2[$i]+$scoreSA3[$i]+$scoreAS1[$i]+$scoreAS2[$i]+$scoreAS3[$i]+$scoreER1[$i]+$scoreER2[$i]+$scoreER3[$i]+$scoreER4[$i]+$scoreER5[$i];
			$realCumScore[$i]= sprintf('%0.3f', $realCumScore[$i]) + 0;
			if($i-1 == -1){
				$realMonScore[]= $realCumScore[$i];
			}else{
				$realMonScore[]= $realCumScore[$i]-$realCumScore[$i-1];
			}
		}
		// helpme::print_rdie([$realCumScore,$realMonScore,$sumItemFA1,$countItemFA1,$scoreFA1]);

		return view('report.index', compact('proj_id','planDats','labels','planCumstr','planstr','keystr', 'monthstr', 'month', 'year','flag','realMonScore','realCumScore'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create($proj_id)
	{
		//return 'coming soon!';
		$prog_dats=		progress::where('proj_id', $proj_id)->orderBy('created_at', 'ASC')->get('created_at');
		$date_start=	trim($prog_dats->first()->created_at);
		$date_end=		trim($prog_dats->last()->created_at);

		$month=	array();
		$year=	array();
		if(strtotime($date_end) >= strtotime($date_start)){
			$start    = (new DateTime($date_start))->modify('first day of this month');
			$end      = (new DateTime($date_end))->modify('first day of next month');
			$interval = DateInterval::createFromDateString('1 month');
			$period   = new DatePeriod($start, $interval, $end);

			foreach ($period as $dt) {
				$month[]= $dt->format("m");
				$year[]= $dt->format("Y");
			}

		}

		$tot_date= count($month);
		$last_month=	$month[$tot_date - 1];
		$last_year=		$year[$tot_date - 1];
		return view('report.add', compact('date_start', 'date_end', 'month', 'year', 'tot_date', 'proj_id', 'last_month', 'last_year'));
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request)
	{
		$counter=			intval($request->plan_count);
		$total_score=	intval($request->total_score);

		$rules= array(
			'total_score'	=> 'required|integer|min:100|max:100'
		);

		$error = Validator::make($request->all(), $rules);

		if ($error->fails()) {
			return response()->json(['errors' => $error->errors()->all()]);
		}

		$proj_id=	intval($request->proj_id);
		$remark=	trim($request->remark);

		for($i=0;$i<= $counter;$i++){
			$form_data = array(
				'proj_id'			=>  $proj_id,
				'report_plan'	=>  floatval($request->{'report_plan' . $i}),
				'report_month'=>  intval($request->{'report_month' . $i}),
				'report_year'	=>  intval($request->{'report_year' . $i}),
				'remark'			=>  $remark,
			);
			report::create($form_data);
		}
		return response()->json(['success' => 'Data Added successfully.']);
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
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function edit($proj_id)
	{
			$dats=		report::where('proj_id', $proj_id)->get();
			//Helpme::print_rdie($dats);
			$tot=					count($dats) - 1;
			$last_month=	$dats[$tot]->report_month;
			$last_year=		$dats[$tot]->report_year;
			return view('report.edit', compact('proj_id', 'tot', 'dats', 'last_month', 'last_year'));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request)
	{
		$counter=			intval($request->plan_count);
		$total_score=	intval($request->total_score);

		$rules= array(
			'total_score'	=> 'required|integer|min:100|max:100'
		);

		$error = Validator::make($request->all(), $rules);

		if ($error->fails()) {
			return response()->json(['errors' => $error->errors()->all()]);
		}

		$proj_id=	intval($request->proj_id);
		$remark=	trim($request->remark);

		// delete all
		report::where('proj_id', $proj_id)->delete();

		// add all

		for($i=0;$i<= $counter;$i++){
			$form_data = array(
				'proj_id'			=>  $proj_id,
				'report_plan'	=>  floatval($request->{'report_plan' . $i}),
				'report_month'=>  intval($request->{'report_month' . $i}),
				'report_year'	=>  intval($request->{'report_year' . $i}),
				'remark'			=>  $remark,
			);
			report::create($form_data);
		}
		return response()->json(['success' => 'Data Added successfully.']);
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
	}
}
