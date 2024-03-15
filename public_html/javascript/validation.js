function CreateError(elem,msg,currentValidation,tag = 'input'){
	if(!$(tag+"[name="+elem+"]").parent().hasClass("state-error")){
		if($(tag+"[name="+elem+"]").parent().hasClass("state-success")){
			$(tag+"[name="+elem+"]").parent().removeClass("state-success");
		}
		$(tag+"[name="+elem+"]").parent().addClass("state-error");
		$("<em for="+elem+" class='invalid'>"+msg+"</em>").insertAfter($(tag+"[name="+elem+"]").parent());
	}else{
		if($(tag+"[name="+elem+"]").parent().hasClass("state-success")){
			$(tag+"[name="+elem+"]").parent().removeClass("state-success");
		}
	}
	return false;
}
function NameValidation(name){
	var Test = false;
	var TestFor = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZÃ Ã¡Ã¢Ã¤Ã£Ã¥acceÃ¨Ã©ÃªÃ«eiÃ¬Ã­Ã®Ã¯lnÃ²Ã³Ã´Ã¶ÃµÃ¸Ã¹ÃºÃ»Ã¼uuÃ¿Ã½zzÃ±Ã§cÅ¡Å¾Ã€ÃÃ‚Ã„ÃƒÃ…ACCEEÃˆÃ‰ÃŠÃ‹ÃŒÃÃŽÃILNÃ’Ã“Ã”Ã–Ã•Ã˜Ã™ÃšÃ›ÃœUUÅ¸ÃZZÃ‘ÃŸÃ‡Å’Ã†CÅ Å½?Ã° -";
	for (var x = 0; x <= name.length-1; x++) {
		Test = false;
		for (var i = 0; i <= TestFor.length-1; i++) {
			if (name.substr(x,1) == TestFor.substr(i,1)){
				Test = true;
				break;
			};
		};
		if(Test == false){
			return false;
		};
	};
	return true;
};

// BillerNameValidation Explanation: ^(?!\s)(?!.*\s$)(?=.*[a-zA-Z0-9])[a-zA-Z0-9 '~?!]{2,}$
//    ^ start of the string
//    (?!\s) lookahead assertion for don't start with space
//    (?!.*\s$) lookahead assertion for don't end with space
//    (?=.*[a-zA-Z0-9]) lookahead assertion for atleast one alpha or numeric character
//    [a-zA-Z0-9 '~?!] characters we want to match (customize as required)
//    {2,} match minimum 2 and maximum any number of characters from the previously defined class
//    $ end of the string

function BillerNameValidation(name){
    const regex = /^(?!\s)(?!.*\s$)(?=.*[a-zA-Z0-9])[a-zA-Z0-9 '~?!]{2,}$/;
    return regex.test(name);
};
