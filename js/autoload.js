var global = {
	defaultUrl: typeof url != "undefined" ? url : "",
	jsUrl: (typeof url != "undefined" ? url : "") + "/js",
	cssUrl: (typeof url != "undefined" ? url : "") + "/css",
	imgUrl: (typeof url != "undefined" ? url : "") + "/image",

	defaultJs: [
		"jquery@jquery-1.9.1",
		"jquery@jquery-ui-1.9.2.custom.min",
		"jquery@jquery.alerts",
		//"jquery@jquery.jplayer.min",
		//"jquery@jquery.tmpl",
		"jquery@jquery.mousewheel.min",
		"jquery@jquery.mCustomScrollbar.min",
		//"jquery@jquery.getScript",
		//"jquery@jquery.cookie",
		//"lib@timer",
		//"lib@move",
		"lib@dispatcher"
	],

	defaultCss: [
		"common",
		"jquery-ui-1.9.2.custom",
		"mCustomScrollbar",
		"jquery.alerts"
	],

	init: function()
	{
		for(var i in this.defaultJs)
		{
			if(this.defaultJs[i].length > 0)
			{
				document.write("<script src='" + this.jsUrl + "/" + this.defaultJs[i].replace(/@/gi, "/").replace("[language]", this.language) + ".js' ");
				document.write("type='text/javascript' language='javascript'></script>");
			}
		}
		
		for(var j in this.defaultCss)
		{
			if(this.defaultCss[j].length > 0)
			{
				document.write("<link rel='stylesheet' ");
				document.write("href='" + this.cssUrl + "/" + this.defaultCss[j].replace(/@/gi, "/") + ".css' type='text/css' />");
			}
		}
		
		delete this.defaultJs;
		delete this.defaultCss;
		delete this.init;
	}
};

global.init();
