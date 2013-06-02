
/**
 * Cuts a string to a given length
 * 
 * @param string: the string which is cut
 * @param number: the maximal length of the string
 */
function cutString(string, number){
	if(string.length > number){
		var pos = string.indexOf(" ", (number-3))
		if(pos !== -1){
			string = string.substring(0, pos);
			string+=("...");
		}
		
	}
	
	return string;
	
}