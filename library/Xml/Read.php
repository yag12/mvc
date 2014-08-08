<?php
class XmlRead
{
	/**
	* @Param XMLReader $xml
	* @Return array or string
	*/
	static function xmlToAssoc($xml = null)
	{
		$assoc = null;
		$tmpKey = null;
		while($xml->read())
		{
			switch ($xml->nodeType)
			{
				case XMLReader::END_ELEMENT: return $assoc;
				case XMLReader::ELEMENT:
					if(!empty($assoc[$xml->name]))
					{
						if($tmpKey !== $xml->name)
						{
							$tmpKey = $xml->name;
							$tmp = $assoc[$xml->name];
							unset($assoc[$xml->name]);
							$assoc[$xml->name][] = $tmp;
						}

						$assoc[$xml->name][] = $xml->isEmptyElement ? '' : XmlRead::xmlToAssoc($xml);
					}
					else
					{
						$assoc[$xml->name] = $xml->isEmptyElement ? '' : XmlRead::xmlToAssoc($xml);
					}

					if($xml->hasAttributes)
					{
					    $el =& $assoc[$xml->name][count($assoc[$xml->name]) - 1];
					    while($xml->moveToNextAttribute()) $el['attributes'][$xml->name] = $xml->value;
					}
					break;
				case XMLReader::TEXT:
				case XMLReader::CDATA: $assoc .= $xml->value;
			}
		}

		return $assoc;
	}

	/**
	* @Param xml $xmlData
	* @Return array
	*/
	static public function toArray($xmlData = null)
	{
		$xml = new XMLReader;
		$xml->open($xmlData);
		$data = XmlRead::xmlToAssoc($xml);
		$xml->close();

		return $data;
	}

	/**
	* @Param void
	* @Return xml
	*/
	static public function uploadXml()
	{
		$xmlData = file_get_contents("php://input");
		$xmlData = gzuncompress($xmlData);

		return $xmlData;
	}
}
