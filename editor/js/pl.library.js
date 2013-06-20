function plUniqueID( length ) {
	var length = length || 6
	
  // Math.random should be unique because of its seeding algorithm.
  // Convert it to base 36 (numbers + letters), and grab the first 9 characters
  // after the decimal.
  return Math.random().toString(36).substr(2, length);
};

function plIsset(variable){
	if(typeof(variable) != "undefined" && variable !== null)
		return true
	else
		return false
}

function plPrint(variable){
	if(jQuery.pl.config.devMode == 1	)
		console.log( variable )
}

/* Data cleanup and handling
 * ============================================= */
function pl_html_input( text ) {
	
	if( typeof text != 'string')
		return text
	else 	
		return jQuery.trim( pl_htmlEntities( pl_stripSlashes( text ) ) )
}	

function pl_stripSlashes (str) {

  return (str + '').replace(/\\(.?)/g, function (s, n1) {
    switch (n1) {
    case '\\':
      return '\\';
    case '0':
      return '\u0000';
    case '':
      return '';
    default:
      return n1;
    }
  });
}

function pl_htmlEntities(str) {
    return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
}


function basename (path, suffix) {
  // http://kevin.vanzonneveld.net
  // +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  // +   improved by: Ash Searle (http://hexmen.com/blog/)
  // +   improved by: Lincoln Ramsay
  // +   improved by: djmix
  // *     example 1: basename('/www/site/home.htm', '.htm');
  // *     returns 1: 'home'
  // *     example 2: basename('ecra.php?p=1');
  // *     returns 2: 'ecra.php?p=1'
  var b = path.replace(/^.*[\/\\]/g, '');

  if (typeof(suffix) == 'string' && b.substr(b.length - suffix.length) == suffix) {
    b = b.substr(0, b.length - suffix.length);
  }

  return b;
}

/* Simple Shortcode System
 * ============================================= */
function pl_do_shortcode(opt) {
	
	var match = opt.match( /\[([^\]]*)/ ) || false
	var shortcode = (match) ? match[1] : false
	
	if(!shortcode)
		return opt
		
	switch(shortcode) {
		case 'pl_child_url':
			opt = opt.replace(/\[pl_child_url\]/g, jQuery.pl.config.urls.StyleSheetURL)
	}
	return opt
}