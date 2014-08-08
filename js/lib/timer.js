var timer = {
	callbacks: new Array(),

	// 생성
	create: function(elementId, residual, callback, t)
	{
		var type = t || 0 ;
		if(elementId.indexOf("#") == -1)
		{
			elementId = "#" + elementId;
		}

		if($(elementId).is(":visible")||$(elementId).is(":hidden"))
		{
			var element = $(elementId);
			if(typeof residual == "number")
			{
				if(residual > 0)
				{
					$(elementId).attr("timer", residual);
				}
			}
			else
			{
				return false;
			}

			element.text(this.format(element.attr("timer")));
			this.callbacks[elementId] = callback;
			interval.add(elementId, 1, function(id)
			{
				if($(id).is(":visible")||$(id).is(":hidden"))
				{
					this.element = $(id);
					var number = Number(this.element.attr("timer"));
					if(typeof number == "number")
					{
						this.residual = number - 1;
						this.element.attr("timer", this.residual);
						this.element.text(timer.format(this.residual, type));

						if(this.residual <= 0)
						{
							if(typeof timer.callbacks[elementId] === "function")
							{
								callback = timer.callbacks[elementId];
							}

							timer.remove(elementId);

							if(typeof callback === "function")
							{
								callback(elementId);
							}
						}
					}
					else
					{
						timer.remove(elementId);
					}
				}
				else
				{
					timer.remove(elementId);
				}
			});
		}
	},

	// 삭제
	remove: function(elementId)
	{
		if(elementId.indexOf("#") == -1)
		{
			elementId = "#" + elementId;
		}

		interval.remove(elementId);

		delete this.callbacks[elementId];
	},

	// 타이머 포맷
	format: function(residual, t)
	{
		var type = t || 0;
		
		var hh = 0;
		var mm = 0;
		var ss = 0;
		var tmp = 0;

		if(residual >= 60)
		{
			ss = residual % 60;
			tmp = Math.floor(residual / 60);
		}
		else
		{
			ss = residual;
			tmp = 0;
		}

		if(tmp >= 60)
		{
			mm = tmp % 60;
			hh = Math.floor(tmp / 60);
		}
		else
		{
			mm = tmp;
			hh = 0;
		}

		// 시간이 0일때 "00:" 도 표시하는 타입 추가 by NiceCue
		var strZero = type == 0 ? "" : "00:";

		return (hh > 0 ? this.sprintf(hh) + ":" : strZero) +
		(mm > 0 ? this.sprintf(mm) + ":" : "00:") +
		this.sprintf(ss);
	},
	
	// 10보다 작은 수 변경
	sprintf: function(str)
	{
		if(typeof Number(str) == "number")
		{
			str = String(str);
			if(str.length < 2)
			{
				str = "0" + str;
			}
		}
		else
		{
			str = "00";
		}

		return str;
	},

    strtotime: function(unixtime)
    {
        var timestamp = typeof unixtime == "undefined" ? new Date() : new Date(unixtime*1000);
        var date = $.datepicker.formatDate('yy-mm-dd', timestamp);
        var str = timestamp.toString().split(' '); 
        var Time = str[4].substr(0,5);
        return date + ' ' + Time;
    }
};
