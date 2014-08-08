<?php
require_once dirname(__FILE__) . '/Excel/PHPExcel.php';
class Excel extends PHPExcel
{
	/**
	* @Desc 파일명
	* @Var mixed
	*/
	protected $fileName = 'noName';

	/**
	* @Desc 파일명
	* @Param mixed $name
	* @Return boolean
	*/
	public function setFileName($name = null)
	{
		$this->fileName = $name;

		return true;
	}

	/**
	* @Desc 시트제목
	* @Param mixed $title
	* @Return boolean
	*/
	public function setSheetTitle($title = null)
	{
		$this->getActiveSheet()->setTitle($title);

		return true;
	}

	/**
	* @Desc 내용 입력
	* @Param array $data
	* @Return void
	*/
	public function setData($data = array())
	{
		if(!empty($data))
		{
			$str = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
			$length = strlen($str);
			$defaultSheet = $this->setActiveSheetIndex(0);
			$number = 0;
			foreach($data as $row)
			{
				if(is_array($row))
				{
					$number++;
					$subnum = 0;
					foreach($row as $value)
					{
						$key = null;
						$num = ceil($subnum / $length);
						if($num > 1)
						{
							$key = substr($str, $num-1, 1);
							$key = $key . substr($str, $subnum-(($num-1)*$length), 1);
						}
						else
						{
							$key = substr($str, $subnum, 1);
						}

						$defaultSheet->setCellValue($key . $number, $value);
						$subnum++;
					}
				}
			}
		}
	}

	/**
	* @Desc 헤더 추가
	* @Param void
	* @Return void
	*/
	private function setHreader()
	{
		header("Content-Type:Application/vnd.ms-excel");
		header("Content-Disposition: attachment;filename='" . $this->fileName . ".xls'");
		header("Cache-Control:max-age=0");
	}

	/**
	* @Desc 엑셀파일 다운로드
	* @Param void
	* @Return void
	*/
	public function output()
	{
		$this->setHreader();
		
		$xlsIOFactory = PHPExcel_IOFactory::createWriter($this, "Excel5");
		$xlsIOFactory->save('php://output');
	}
}
