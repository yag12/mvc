<?php
class Func
{
	/**
	* @Desc 페이징
	* @Param int $total 총 레코드 수
	* @Param int $limit 한페이지당 출력 레코드 수
	* @Param int $pgnum 현재 페이지번호
	* @Param int $count 한번에 출력할 페이징수
	* @Return array
	*/
	static public function getPaginator($total = 0, $limit = 10, $pgnum = 1, $count = 10)
	{
		$paginator = array();
		$totalpgs = ceil($total / $limit);
		$pg_count = ceil($pgnum / $count);
		$start_pg = $pgnum - 2;
		if($pgnum < 3) $start_pg = 1;
		$end_pg = $start_pg + $count - 1;
		if($end_pg > $totalpgs)	$end_pg = $totalpgs;
		$start_pg = ($end_pg - $count >= 1) ? $end_pg - $count + 1 : 1;
		for($i=$start_pg; $i<=$end_pg; $i++){
			$paginator[] = $i;
		}

		return $paginator;
	}

	/**
	* @Desc 글자 자르기
	* @Param string $str
	* @Param int $len
	* @Param mixed $suffix
	* @Return array
	*/
	static public function cutString($str = null, $len = 1, $suffix = '..')
	{
        return mb_strimwidth($str, 0, $len, $suffix);
	}
}
