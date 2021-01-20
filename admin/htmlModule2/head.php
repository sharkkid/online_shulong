<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="description" content="">
<meta name="author" content="Wholetech">
		
<title><?php echo CN_NAME;?></title>

<link rel="stylesheet" href="./../css/com.css">
		
<script type="text/javascript" src="<?php echo WT_SERVER;?>/lib/jquery-1.11.3.min.js"></script>
<script type="text/javascript" src="<?php echo WT_SERVER;?>/lib/jquery.tablesorter.js"></script>
		
<!-- bootstrap -->
<link rel="stylesheet" href="<?php echo WT_SERVER;?>/lib/bootstrap/3.3.5/css/bootstrap.min.css">
<link rel="stylesheet" href="<?php echo WT_SERVER;?>/lib/bootstrap/3.3.5/css/bootstrap-theme.min.css">
<script src="<?php echo WT_SERVER;?>/lib/bootstrap/3.3.5/js/bootstrap.min.js"></script>
<!--[if lt IE 9]>
	<script src="<?php echo WT_SERVER;?>/lib/html5shiv/3.7.0/html5.js" type="text/javascript"></script>
	<script src="<?php echo WT_SERVER;?>/lib/respond/1.4.2/respond.min.js" type="text/javascript"></script>
<![endif]-->

<!-- jasny-bootstrap -->
<link rel="stylesheet" href="<?php echo WT_SERVER;?>/lib/jasny-bootstrap/3.1.3/css/jasny-bootstrap.min.css">
<script src="<?php echo WT_SERVER;?>/lib/jasny-bootstrap/3.1.3/js/jasny-bootstrap.min.js"></script>

<!-- bootbox -->
<script src="<?php echo WT_SERVER;?>/lib/bootbox/4.4.0/bootbox.min.js"></script>

<!-- bootstrap-validator -->
<script src="<?php echo WT_SERVER;?>/lib/bootstrap-validator/0.9.0/validator.min.js"></script>

<script type="text/javascript">
	var alert_msg;
	$(document).ready(function() {
        $(".tablesorter").tablesorter({widgets: ['zebra']});

        alert_msg = function(msg, callback) { 
        	$('#alert-modal').find('.msg').html(msg);
			$('#alert-modal').modal({ backdrop: 'static', keyboard: false });
        }
        $('#alert-modal').on('hidden.bs.modal', function (e) {
        	location.reload();
        });
	});

	/**
	 * This was developed to allow for the formatting of dates in JavaScript and ActionScript like PHP can do.
	 * http://jacwright.com/projects/javascript/date_format/
	 * myDate.format('Y/m/d H:i:s');
	 */
	Date.prototype.format = function(format) {
	    var returnStr = '';
	    var replace = Date.replaceChars;
	    for (var i = 0; i < format.length; i++) {       var curChar = format.charAt(i);         if (i - 1 >= 0 && format.charAt(i - 1) == "\\") {
	            returnStr += curChar;
	        }
	        else if (replace[curChar]) {
	            returnStr += replace[curChar].call(this);
	        } else if (curChar != "\\"){
	            returnStr += curChar;
	        }
	    }
	    return returnStr;
	};

	Date.replaceChars = {
	    shortMonths: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
	    longMonths: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
	    shortDays: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
	    longDays: ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],

	    // Day
	    d: function() { return (this.getDate() < 10 ? '0' : '') + this.getDate(); },
	    D: function() { return Date.replaceChars.shortDays[this.getDay()]; },
	    j: function() { return this.getDate(); },
	    l: function() { return Date.replaceChars.longDays[this.getDay()]; },
	    N: function() { return this.getDay() + 1; },
	    S: function() { return (this.getDate() % 10 == 1 && this.getDate() != 11 ? 'st' : (this.getDate() % 10 == 2 && this.getDate() != 12 ? 'nd' : (this.getDate() % 10 == 3 && this.getDate() != 13 ? 'rd' : 'th'))); },
	    w: function() { return this.getDay(); },
	    z: function() { var d = new Date(this.getFullYear(),0,1); return Math.ceil((this - d) / 86400000); }, // Fixed now
	    // Week
	    W: function() { var d = new Date(this.getFullYear(), 0, 1); return Math.ceil((((this - d) / 86400000) + d.getDay() + 1) / 7); }, // Fixed now
	    // Month
	    F: function() { return Date.replaceChars.longMonths[this.getMonth()]; },
	    m: function() { return (this.getMonth() < 9 ? '0' : '') + (this.getMonth() + 1); },
	    M: function() { return Date.replaceChars.shortMonths[this.getMonth()]; },
	    n: function() { return this.getMonth() + 1; },
	    t: function() { var d = new Date(); return new Date(d.getFullYear(), d.getMonth(), 0).getDate() }, // Fixed now, gets #days of date
	    // Year
	    L: function() { var year = this.getFullYear(); return (year % 400 == 0 || (year % 100 != 0 && year % 4 == 0)); },   // Fixed now
	    o: function() { var d  = new Date(this.valueOf());  d.setDate(d.getDate() - ((this.getDay() + 6) % 7) + 3); return d.getFullYear();}, //Fixed now
	    Y: function() { return this.getFullYear(); },
	    y: function() { return ('' + this.getFullYear()).substr(2); },
	    // Time
	    a: function() { return this.getHours() < 12 ? 'am' : 'pm'; },
	    A: function() { return this.getHours() < 12 ? 'AM' : 'PM'; },
	    B: function() { return Math.floor((((this.getUTCHours() + 1) % 24) + this.getUTCMinutes() / 60 + this.getUTCSeconds() / 3600) * 1000 / 24); }, // Fixed now
	    g: function() { return this.getHours() % 12 || 12; },
	    G: function() { return this.getHours(); },
	    h: function() { return ((this.getHours() % 12 || 12) < 10 ? '0' : '') + (this.getHours() % 12 || 12); },
	    H: function() { return (this.getHours() < 10 ? '0' : '') + this.getHours(); },
	    i: function() { return (this.getMinutes() < 10 ? '0' : '') + this.getMinutes(); },
	    s: function() { return (this.getSeconds() < 10 ? '0' : '') + this.getSeconds(); },
	    u: function() { var m = this.getMilliseconds(); return (m < 10 ? '00' : (m < 100 ? '0' : '')) + m; },
	    // Timezone
	    e: function() { return "Not Yet Supported"; },
	    I: function() { return "Not Yet Supported"; },
	    O: function() { return (-this.getTimezoneOffset() < 0 ? '-' : '+') + (Math.abs(this.getTimezoneOffset() / 60) < 10 ? '0' : '') + (Math.abs(this.getTimezoneOffset() / 60)) + '00'; },
	    P: function() { return (-this.getTimezoneOffset() < 0 ? '-' : '+') + (Math.abs(this.getTimezoneOffset() / 60) < 10 ? '0' : '') + (Math.abs(this.getTimezoneOffset() / 60)) + ':00'; }, // Fixed now
	    T: function() { var m = this.getMonth(); this.setMonth(0); var result = this.toTimeString().replace(/^.+ \(?([^\)]+)\)?$/, '$1'); this.setMonth(m); return result;},
	    Z: function() { return -this.getTimezoneOffset() * 60; },
	    // Full Date/Time
	    c: function() { return this.format("Y-m-d\\TH:i:sP"); }, // Fixed now
	    r: function() { return this.toString(); },
	    U: function() { return this.getTime() / 1000; }
	};
	
	/**
	 * Parses a date string and returns the Date object
	 * @param date_time	'YYYY/MM/DD HH:MM:SS', 'DD/MM/YYYY HH:MM:SS', 'YYYY/MM/DD', 'DD/MM/YYYY' 
	 * @returns Date object
	 */
	function str2Date(date_time)
	{
		var date_types = {
				iso_date_time: /(\d{4})[-\/](\d{2})[-\/](\d{2}) (\d{2}):(\d{2}):(\d{2})/,
				ita_date_time: /(\d{2})[-\/](\d{2})[-\/](\d{4}) (\d{2}):(\d{2}):(\d{2})/,
				iso_date: /(\d{4})[-\/](\d{2})[-\/](\d{2})/,
				ita_date: /(\d{2})[-\/](\d{2})[-\/](\d{4})/
		}
		var dateArray = null, dateObject = null;
		if((dateArray = date_types.iso_date_time.exec(date_time))) 
			dateObject = new Date((+dateArray[1]), (+dateArray[2])-1, (+dateArray[3]), (+dateArray[4]), (+dateArray[5]), (+dateArray[6]));
		else if((dateArray = date_types.ita_date_time.exec(date_time))) 
			dateObject = new Date((+dateArray[3]), (+dateArray[2])-1, (+dateArray[1]), (+dateArray[4]), (+dateArray[5]), (+dateArray[6]));
		else if((dateArray = date_types.iso_date.exec(date_time))) 
			dateObject = new Date((+dateArray[1]), (+dateArray[2])-1, (+dateArray[3]), 0, 0, 0);
		else if((dateArray = date_types.ita_date.exec(date_time))) 
			dateObject = new Date((+dateArray[3]), (+dateArray[2])-1, (+dateArray[1]), 0, 0, 0);
		return dateObject;
	}
</script>