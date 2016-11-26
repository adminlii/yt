jQuery.extend({
	M_encry : function(data){
		var setToken = '~!@#$%^&4512*-678';
	    var date     = new Date().getTime();
	    var token    = hex_md5(date+setToken).substr(8, 16);

	    var key =   CryptoJS.enc.Latin1.parse(token);
	    var iv =    CryptoJS.enc.Latin1.parse(token);
	    var data = encodeURIComponent(CryptoJS.AES.encrypt(data, key, { iv: iv, mode: CryptoJS.mode.CBC, padding: CryptoJS.pad.ZeroPadding }));
	    
	    return  {data:data,date:date};
	}
});