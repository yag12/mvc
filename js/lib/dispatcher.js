var dispatcher = {
	// 스타트업
	startup: function(url, request, callback)
	{
		url = global.defaultUrl + "/" + url + ".json";
		$.ajax({
			url: url,
			data: request,
			type: "POST",
			dataType: "json",
			success: function(data){
				if(typeof callback == "function")
				{
					callback(data);
				}
			}
		});

		return true;
	},

	fileupload: function(url, frm, callback)
	{
		url = global.defaultUrl + "/" + url + ".json";
		$.ajax({
			url: url,
			data: frm.serialize(),
			type: "POST",
			contentType:attr( "enctype", "multipart/form-data" ),
			success: function(data){
				if(typeof callback == "function")
				{
					callback(data);
				}
			}
		});

		return true;
	}
};
