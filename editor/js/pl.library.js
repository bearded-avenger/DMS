

function plVerticalCenter() {
	jQuery('.pl-center-inside').each(function(){
		var colHeight = jQuery(this).height()
		,	centeredElement = jQuery(this).find('.pl-center-me')
		,	infoHeight = centeredElement.height()
		
		//30px away from being centered so we can transition to center point on hover
		centeredElement.css('margin-top', ((colHeight / 2) - (infoHeight / 2 )) )
	});	
}

function plIsset(variable){
	if(typeof(variable) != "undefined" && variable !== null)
		return true
	else
		return false
}

/* Data cleanup and handling
 * ============================================= */
function pl_html_input( text ) {
	return jQuery.trim( pl_htmlEntities( pl_stripSlashes( text ) ) );
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



