<?php
class ThumbImage
{
	/**
	 * @Desc 원본 이미지 실경로
	 * @var string
	 */
	static $real_path		= './';
	
	/**
	 * @Desc 썸네일 이미지 생성경로
	 * @var string
	 */
	static $target_path		= './';
	
	/**
	 * @Desc 썸네일 이미지 생성이름
	 * @var string
	 */
	static $add_name		= 'thumb_';
	
	/**
	 * @Desc 썸네일 이미지 품질
	 * @var int
	 */
	static $image_quality	= 75;
	
	/**
	 * @Desc 썸네일 생성 가능한 확장자
	 * @var array
	 */
	static $image_ext		= array(1 => 'jpg', 'gif', 'png');
	
	/**
	 * @Desc 썸네일 생성 파일 종류
	 * @var string
	 */
	static $ext;
	
	/**
	 * @Desc 원본 이미지 링크 리소스
	 * @var resource
	 */
	static $src;
	
	/**
	 * @Desc 썸네일 이미지 생성
	 * @var ImageCreateTrueColor
	 */
	static $thumb;
	
	/**
	 * @Desc 썸네일 이미지 가로크기
	 * @var int
	 */
	static $thumb_width;
	
	/**
	 * @Desc 썸네일 이미지 세로크기
	 * @var int
	 */
	static $thumb_height;

	/**
	 * @Desc 썸네일 이미지 만들기
	 * @param string $real_image
	 * @param int $width
	 * @param int $height
	 * @param string $target_ext
	 * @return boolen
	 */
	static public function imageResize($real_image, $width, $height, $real_path = './', $target_path = './', $add_name = 'thumb_', $target_ext = null){
		self::$add_name		= $add_name;
		self::$real_path	= $real_path;
		self::$target_path	= $target_path;
		
		if($target_ext === NULL){
			$ext		= split('\.', $real_image);
			$ext_name	= strtolower($ext[sizeof($ext)-1]);
		}else{
			$ext_name	= $target_ext;
		}
		self::$ext	= strtolower($ext_name);

		if(is_file(self::$real_path . '/' . $real_image)){
			if(array_search(self::$ext, self::$image_ext) != NULL){
				self::getImageSrc($real_image);
				self::getTureColor($real_image, $width, $height);
				ImageCopyResampled(self::$thumb, self::$src, 0, 0, 0, 0, self::$thumb_width, self::$thumb_height, ImageSX(self::$src), ImageSY(self::$src));    
				self::createImage($real_image);

				ImageDestroy(self::$src);
				ImageDestroy(self::$thumb);
				
				return TRUE;
			}else{
				return FALSE;
			}
		}else{
			return FALSE;
		}
	}
	
	/**
	 * @Desc 원본 이미지 리소스
	 * @param string $real_image
	 */
	static function getImageSrc($real_image){
		$file_name	= self::$real_path . '/' . $real_image;
		try{
			switch(self::$ext){
				case 'jpg':
					self::$src = ImageCreateFromJPEG($file_name);
					break;
				case 'gif':
					self::$src = ImageCreateFromGIF($file_name);
					break;
				case 'png':
					self::$src = ImageCreateFromPNG($file_name);
					break;
			}
		}catch(Exception $e){
			echo $e->getMessage();
		}
	}
	
	/**
	 * @Desc 썸네일 이미지 생성
	 * @param string $real_image
	 * @param int $width
	 * @param int $height
	 */
	static function getTureColor($real_image, $width, $height){
		list($tmp_width, $tmp_height)	= getimagesize(self::$real_path . '/' . $real_image);
		$ratio = 1;
		if ($tmp_width > $width || $tmp_height > $height) {
			if ($tmp_width / $width > $tmp_height / $height) {
				$ratio	= $width / $tmp_width;
			}else{
				$ratio	= $height / $tmp_height;
			}
			
			self::$thumb_width	= intval($tmp_width * $ratio);
			self::$thumb_height	= intval($tmp_height * $ratio);
		}else if ($tmp_width < $width && $tmp_height < $height) {
			self::$thumb_width	= intval($tmp_width);
			self::$thumb_height	= intval($tmp_height);
		}else{
			self::$thumb_width	= $width;
			self::$thumb_height	= $height;
		}
		
		self::$thumb = ImageCreateTrueColor(self::$thumb_width, self::$thumb_height);
	}
	
	/**
	 * @Desc 이미지 Output
	 * @param string $real_image
	 */
	static function createImage($real_image){
		$file_name	= self::$target_path . '/' . self::$add_name . $real_image;
		
		try{
			switch(self::$ext){
				case 'jpg':
					ImageJPEG(self::$thumb, $file_name, self::$image_quality);
					break;
				case 'gif':
					ImageGIF(self::$thumb, $file_name, self::$image_quality);
					break;
				case 'png':
					ImagePNG(self::$thumb, $file_name, self::$image_quality);
					break;
			}
		}catch(Exception $e){
			echo $e->getMessage();
		}
	}
}
