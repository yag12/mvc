<?php
class FileUpload {

	protected $path = '.';
	protected $nm = array();
	protected $file = array();
	protected $file_name = array();
	protected $file_type = array();
	protected $width = array();
	protected $height = array();
	protected $str = array();
	protected $size = array();
	protected $ftype = array();

	/**
	* @Desc Construct
	* @Param mixed $path
	* @Param array $nm
	* @Return void
	*/
	public function __construct($path = null, $nm = array())
	{
		if(!empty($path))
		{
			$this->path = $path;
		}

		if(!empty($nm))
		{
			$this->nm = $nm;
		}

		if(!empty($_FILES))
		{
			foreach($_FILES as $file)
			{
				if(!empty($file))
				{
					if(is_array($file['tmp_name']))
					{
						$this->file = array_merge($this->file, $file['tmp_name']);
						$this->file_name = array_merge($this->file_name, $file['name']);
						$this->file_type = array_merge($this->file_type, $file['type']);
					}
					else
					{
						$this->file[] = $file['tmp_name'];
						$this->file_name[] = $file['name'];
						$this->file_type[] = $file['type'];
					}
				}
			}
		}
	}
	
	/**
	* @Desc File Name Check
	* @Param void
	* @Retrun void
	*/
	protected function chkFilename($file_name = null)
	{
		// 파일이름을 배열로 저장
		$val = explode(".",$file_name);
		$num = sizeof($val) - 1;

		// 파일 확장자
		$img_nm = $val[$num];

		// 대문자를 소문자로 변환
		$img_nm = strtolower($img_nm);

		if(!empty($this->nm))
		{
			if(is_array($this->nm))
			{
				foreach($this->nm as $nm)
				{
					// 확장자가 jpg,jpeg,gif 인지 검색
					if($img_nm == $nm) return true;
				}
			}else{
				if($img_nm == $nm) return true;
			}
		
			return false;
		}

		return true;
	}
	
	/**
	* @Desc File Upload
	* @Param void
	* @Retrun void
	*/
	public function upload()
	{
		for($i=0;$i<sizeof($this->file);$i++){
			if($this->file_name[$i]){
			
				// 파일 확장가 확인
				if(!$this->chkFilename($this->file_name[$i]))
				{
					throw new Exception("업로드가 가능한 파일형식이 아닙니다.", 401);
				}

				$img_analyze = NULL;
				$width = NULL;
				$height = NULL;

				$this->file_name[$i] = stripslashes($this->file_name[$i]);
				$this->file_name[$i] = ereg_replace("[[:space:]]","_",$this->file_name[$i]);
				$this->file_name[$i] = ereg_replace("#","",$this->file_name[$i]);
				$this->file_name[$i] = ereg_replace("%","",$this->file_name[$i]);
				// 이미지 파일 사이즈구하기
				$img_analyze = @getimagesize($this->file[$i]);
				$this->width[$i] = $img_analyze[0];
				$this->height[$i] = $img_analyze[1];
				$this->str[$i] = $img_analyze[3];
				$this->size[$i] = filesize($this->file[$i]);
				$this->ftype[$i] = $this->file_type[$i];

				$count = 0;
				$echo_count = "";
				$original_name = $this->file_name[$i];

				$file_name[$i] = $this->file_name[$i];
				// 같은 이름의 파일이 있는 경우 다른 이름으로 저장
				while(true){
					$file_name[$i] = $echo_count.$original_name;
					if(!file_exists($this->path."/".$file_name[$i])) break;
					if($count) $count++;	else $count=1;
					$echo_count = $count."_";
				}
				$this->file_name[$i] = $file_name[$i];

				// 디렉토리 없는 경우 생성
				if(!@is_dir($this->path)){
					// 디렉토리 생성
					if(!@mkdir($this->path, 0777))	
						throw new Exception("디렉토리 생성 에러", 401);
				}else{
					// 디렉토리 퍼미션 수정
					/*
					if(!@chmod($this->path, 0777))		
						functions::hisback("퍼미션 수정 에러");
					*/
				}

				// 업로드 파일을 디렉토리로 이동
				if(!@move_uploaded_file($this->file[$i],$this->path."/".$file_name[$i]))	
					throw new Exception("첨부 파일 업로드 에러", 401);	

			}
		}
	}

	/**
	* @Desc get File
	* @Param void
	* @Return array
	*/
	public function getFiles()
	{
		$filename = array();
		for($i=0;$i<sizeof($this->file_name);$i++){
			$filename[$i]['name']	= !empty($this->file_name[$i]) ? $this->file_name[$i] : '';
			$filename[$i]['width']	= !empty($this->width[$i]) ? $this->width[$i] : 0;
			$filename[$i]['height']	= !empty($this->height[$i]) ? $this->height[$i] : 0;
			$filename[$i]['str']	= !empty($this->str[$i]) ? $this->str[$i] : '';
			$filename[$i]['size']	= !empty($this->size[$i]) ? $this->size[$i] : 0;
			$filename[$i]['type']	= !empty($this->ftype[$i]) ? $this->ftype[$i] : '';
		}

		return $filename;
	}
}
