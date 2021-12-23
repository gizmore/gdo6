/**
 * gizmore string utility.
 * Some code here is taken from http://phpjs.orgode.
 * @version 6.10.4
 **/

String.prototype.ltrim = function(chars) { chars = chars || "\\s"; return this.replace(new RegExp("^[" + chars + "]+", "g"), ""); };
String.prototype.rtrim = function(chars) { chars = chars || "\\s"; return this.replace(new RegExp("[" + chars + "]+$", "g"), ""); };
String.prototype.trim = function(chars) { return this.rtrim(chars).ltrim(chars); };
String.prototype.startsWith = function(s) { return this.match(new RegExp('^'+s, 'i')) !== null; };
String.prototype.endsWith = function(s) { return this.match(new RegExp(s+'$', 'i')) !== null; };
String.prototype.contains = function(s) { return this.match(new RegExp(s, 'i')) !== null; };
String.prototype.substrFrom = function(s, d) { var i = this.indexOf(s); return i === -1 ? d : this.substr(i+s.length); };
String.prototype.rsubstrFrom = function(s, d) { var i = this.lastIndexOf(s); return i === -1 ? d : this.substr(i+s.length); };
String.prototype.substrTo = function(s, d) { var i = this.indexOf(s); return i === -1 ? d : this.substring(0, i); };
String.prototype.rsubstrTo = function(s, d) { var i = this.lastIndexOf(s); return i === -1 ? d : this.substring(0, i); };
String.prototype.nibbleTo = function(s) { var r = this.substrTo(s); this.replace(this.substrFrom(s)); return r; };

String.prototype.html = function(s) {
	return s.replace(/[&<"']/g, function(m) {
		switch (m) {
		case '&':
			return '&amp;';
		case '<':
			return '&lt;';
		case '"':
			return '&quot;';
		default:
			return '&#039;';
		}
	});
};

// -----------------------------------------------------------------------------

function clamp(num, min, max) {
	if ((min !== undefined) && (num < min)) return min;
	if ((max !== undefined) && (num > max)) return max;
	return num;
}

function urlParam(name, url) {
	if (!url) url = window.location.href;
	name = name.replace(/[\[\]]/g, "\\$&");
	var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
		results = regex.exec(url);
	if (!results) return null;
	if (!results[2]) return '';
	return decodeURIComponent(results[2].replace(/\+/g, " "));
}

/** PHP.JS BELOW HERE **/

/*
 * Code here is taken from http://phpjs.org
 * See: http://phpjs.org/pages/license (MIT or GPLv2)
 */
function sprintf ()
{
// http://kevin.vanzonneveld.net
// +   original by: Ash Searle (http://hexmen.com/blog/)
// + namespaced by: Michael White (http://getsprink.com)
// +	tweaked by: Jack
// +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
// +	  input by: Paulo Freitas
// +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
// +	  input by: Brett Zamir (http://brett-zamir.me)
// +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
// *	 example 1: sprintf("%01.2f", 123.1);
// *	 returns 1: 123.10
// *	 example 2: sprintf("[%10s]", 'monkey');
// *	 returns 2: '[	monkey]'
// *	 example 3: sprintf("[%'#10s]", 'monkey');
// *	 returns 3: '[####monkey]'
	var regex = /%%|%(\d+\$)?([-+\'#0 ]*)(\*\d+\$|\*|\d+)?(\.(\*\d+\$|\*|\d+))?([scboxXuidfegEG])/g;
	var a = arguments,
		i = 0,
		format = a[i++];

	// pad()
	var pad = function (str, len, chr, leftJustify) {
		if (!chr) {
			chr = ' ';
		}
		var padding = (str.length >= len) ? '' : Array(1 + len - str.length >>> 0).join(chr);
		return leftJustify ? str + padding : padding + str;
	};

	// justify()
	var justify = function (value, prefix, leftJustify, minWidth, zeroPad, customPadChar) {
		var diff = minWidth - value.length;
		if (diff > 0) {
			if (leftJustify || !zeroPad) {
				value = pad(value, minWidth, customPadChar, leftJustify);
			} else {
				value = value.slice(0, prefix.length) + pad('', diff, '0', true) + value.slice(prefix.length);
			}
		}
		return value;
	};

	// formatBaseX()
	var formatBaseX = function (value, base, prefix, leftJustify, minWidth, precision, zeroPad) {
		// Note: casts negative numbers to positive ones
		var number = value >>> 0;
		prefix = prefix && number && {
			'2': '0b',
			'8': '0',
			'16': '0x'
		}[base] || '';
		value = prefix + pad(number.toString(base), precision || 0, '0', false);
		return justify(value, prefix, leftJustify, minWidth, zeroPad);
	};

	// formatString()
	var formatString = function (value, leftJustify, minWidth, precision, zeroPad, customPadChar) {
		if (precision != null) {
			value = value.slice(0, precision);
		}
		return justify(value, '', leftJustify, minWidth, zeroPad, customPadChar);
	};

	// doFormat()
	var doFormat = function(substring, valueIndex, flags, minWidth, _, precision, type) {
		var number;
		var prefix;
		var method;
		var textTransform;
		var value;

		if (substring == '%%') {
			return '%';
		}

		// parse flags
		var leftJustify = false,
			positivePrefix = '',
			zeroPad = false,
			prefixBaseX = false,
			customPadChar = ' ';
		var flagsl = flags.length;
		for (var j = 0; flags && j < flagsl; j++) {
			switch (flags.charAt(j)) {
			case ' ':
				positivePrefix = ' ';
				break;
			case '+':
				positivePrefix = '+';
				break;
			case '-':
				leftJustify = true;
				break;
			case "'":
				customPadChar = flags.charAt(j + 1);
				break;
			case '0':
				zeroPad = true;
				break;
			case '#':
				prefixBaseX = true;
				break;
			}
		}

		// parameters may be null, undefined, empty-string or real valued
		// we want to ignore null, undefined and empty-string values
		if (!minWidth) {
			minWidth = 0;
		} else if (minWidth == '*') {
			minWidth = +a[i++];
		} else if (minWidth.charAt(0) == '*') {
			minWidth = +a[minWidth.slice(1, -1)];
		} else {
			minWidth = +minWidth;
		}

		// Note: undocumented perl feature:
		if (minWidth < 0) {
			minWidth = -minWidth;
			leftJustify = true;
		}

		if (!isFinite(minWidth)) {
			throw new Error('sprintf: (minimum-)width must be finite');
		}

		if (!precision) {
			precision = 'fFeE'.indexOf(type) > -1 ? 6 : (type == 'd') ? 0 : undefined;
		} else if (precision == '*') {
			precision = +a[i++];
		} else if (precision.charAt(0) == '*') {
			precision = +a[precision.slice(1, -1)];
		} else {
			precision = +precision;
		}

		// grab value using valueIndex if required?
		value = valueIndex ? a[valueIndex.slice(0, -1)] : a[i++];

		switch (type) {
		case 's':
			return formatString(String(value), leftJustify, minWidth, precision, zeroPad, customPadChar);
		case 'c':
			return formatString(String.fromCharCode(+value), leftJustify, minWidth, precision, zeroPad);
		case 'b':
			return formatBaseX(value, 2, prefixBaseX, leftJustify, minWidth, precision, zeroPad);
		case 'o':
			return formatBaseX(value, 8, prefixBaseX, leftJustify, minWidth, precision, zeroPad);
		case 'x':
			return formatBaseX(value, 16, prefixBaseX, leftJustify, minWidth, precision, zeroPad);
		case 'X':
			return formatBaseX(value, 16, prefixBaseX, leftJustify, minWidth, precision, zeroPad).toUpperCase();
		case 'u':
			return formatBaseX(value, 10, prefixBaseX, leftJustify, minWidth, precision, zeroPad);
		case 'i':
		case 'd':
			number = (+value) | 0;
			prefix = number < 0 ? '-' : positivePrefix;
			value = prefix + pad(String(Math.abs(number)), precision, '0', false);
			return justify(value, prefix, leftJustify, minWidth, zeroPad);
		case 'e':
		case 'E':
		case 'f':
		case 'F':
		case 'g':
		case 'G':
			number = +value;
			prefix = number < 0 ? '-' : positivePrefix;
			method = ['toExponential', 'toFixed', 'toPrecision']['efg'.indexOf(type.toLowerCase())];
			textTransform = ['toString', 'toUpperCase']['eEfFgG'.indexOf(type) % 2];
			value = prefix + Math.abs(number)[method](precision);
			return justify(value, prefix, leftJustify, minWidth, zeroPad)[textTransform]();
		default:
			return substring;
		}
	};

	return format.replace(regex, doFormat);
}


function vsprintf(format, args) {
	return sprintf.apply(this, [format].concat(args));
}


function in_array(needle, haystack, argStrict) {
	if (!!argStrict) {
		for (var key in haystack) {
			if (haystack[key] === needle) {
				return true;
			}
		}
	} else for (var key in haystack) {
		if (haystack[key] == needle) {
			return true;
		}
	}
	return false;
}

function array_values(input) {

	if (input && typeof input === 'object' && input.change_key_case) {
		return input.values();
	}
	
	var tmp_arr = [];
	for (var key in input) {
		tmp_arr[tmp_arr.length] = input[key];
	}
	return tmp_arr;
}

/**
 * discuss at: http://locutus.io/php/explode/
 * original by: Kevin van Zonneveld (http://kvz.io)
 * @param delimiter
 * @param string
 * @param limit
 * @returns
 */
function explode(delimiter, string, limit) {
	if (arguments.length < 2 || typeof delimiter === 'undefined' || typeof string === 'undefined') {
		return null;
	}
	if (delimiter === '' || delimiter === false || delimiter === null) {
		return false;
	}
	if (typeof delimiter === 'function' || typeof delimiter === 'object' || typeof string === 'function' || typeof string === 'object') {
		return { 0: '' };
	}
	if (delimiter === true) {
		delimiter = '1'
	}

	// Here we go...
	delimiter += '';
	string += '';

	var s = string.split(delimiter);

	if (typeof limit === 'undefined') return s;

	if (limit === 0) {
		limit = 1;
	}

	// Positive limit
	if (limit > 0) {
		if (limit >= s.length) {
			return s;
		}
		return s.slice(0, limit - 1).concat([s.slice(limit - 1).join(delimiter)]);
	}

	// Negative limit
	if (-limit >= s.length) {
		return [];
	}
	
	s.splice(s.length + limit)

	return s;
}

/**
 * https://locutus.io/php/strings/str_replace/
 */
function str_replace (search, replace, subject, countObj) { // eslint-disable-line camelcase
  //  discuss at: https://locutus.io/php/str_replace/
  // original by: Kevin van Zonneveld (https://kvz.io)
  // improved by: Gabriel Paderni
  // improved by: Philip Peterson
  // improved by: Simon Willison (https://simonwillison.net)
  // improved by: Kevin van Zonneveld (https://kvz.io)
  // improved by: Onno Marsman (https://twitter.com/onnomarsman)
  // improved by: Brett Zamir (https://brett-zamir.me)
  //  revised by: Jonas Raoni Soares Silva (https://www.jsfromhell.com)
  // bugfixed by: Anton Ongson
  // bugfixed by: Kevin van Zonneveld (https://kvz.io)
  // bugfixed by: Oleg Eremeev
  // bugfixed by: Glen Arason (https://CanadianDomainRegistry.ca)
  // bugfixed by: Glen Arason (https://CanadianDomainRegistry.ca)
  // bugfixed by: Mahmoud Saeed
  //    input by: Onno Marsman (https://twitter.com/onnomarsman)
  //    input by: Brett Zamir (https://brett-zamir.me)
  //    input by: Oleg Eremeev
  //      note 1: The countObj parameter (optional) if used must be passed in as a
  //      note 1: object. The count will then be written by reference into it's `value` property
  //   example 1: str_replace(' ', '.', 'Kevin van Zonneveld')
  //   returns 1: 'Kevin.van.Zonneveld'
  //   example 2: str_replace(['{name}', 'l'], ['hello', 'm'], '{name}, lars')
  //   returns 2: 'hemmo, mars'
  //   example 3: str_replace(Array('S','F'),'x','ASDFASDF')
  //   returns 3: 'AxDxAxDx'
  //   example 4: var countObj = {}
  //   example 4: str_replace(['A','D'], ['x','y'] , 'ASDFASDF' , countObj)
  //   example 4: var $result = countObj.value
  //   returns 4: 4
  //   example 5: str_replace('', '.', 'aaa')
  //   returns 5: 'aaa'
  let i = 0
  let j = 0
  let temp = ''
  let repl = ''
  let sl = 0
  let fl = 0
  const f = [].concat(search)
  let r = [].concat(replace)
  let s = subject
  let ra = Object.prototype.toString.call(r) === '[object Array]'
  const sa = Object.prototype.toString.call(s) === '[object Array]'
  s = [].concat(s)
  const $global = (typeof window !== 'undefined' ? window : global)
  $global.$locutus = $global.$locutus || {}
  const $locutus = $global.$locutus
  $locutus.php = $locutus.php || {}
  if (typeof (search) === 'object' && typeof (replace) === 'string') {
    temp = replace
    replace = []
    for (i = 0; i < search.length; i += 1) {
      replace[i] = temp
    }
    temp = ''
    r = [].concat(replace)
    ra = Object.prototype.toString.call(r) === '[object Array]'
  }
  if (typeof countObj !== 'undefined') {
    countObj.value = 0
  }
  for (i = 0, sl = s.length; i < sl; i++) {
    if (s[i] === '') {
      continue
    }
    for (j = 0, fl = f.length; j < fl; j++) {
      if (f[j] === '') {
        continue
      }
      temp = s[i] + ''
      repl = ra ? (r[j] !== undefined ? r[j] : '') : r[0]
      s[i] = (temp).split(f[j]).join(repl)
      if (typeof countObj !== 'undefined') {
        countObj.value += ((temp.split(f[j])).length - 1)
      }
    }
  }
  return sa ? s : s[0]
}

function strtr (str, trFrom, trTo) {
  //  discuss at: https://locutus.io/php/strtr/
  // original by: Brett Zamir (https://brett-zamir.me)
  //    input by: uestla
  //    input by: Alan C
  //    input by: Taras Bogach
  //    input by: jpfle
  // bugfixed by: Kevin van Zonneveld (https://kvz.io)
  // bugfixed by: Kevin van Zonneveld (https://kvz.io)
  // bugfixed by: Brett Zamir (https://brett-zamir.me)
  // bugfixed by: Brett Zamir (https://brett-zamir.me)
  //   example 1: var $trans = {'hello' : 'hi', 'hi' : 'hello'}
  //   example 1: strtr('hi all, I said hello', $trans)
  //   returns 1: 'hello all, I said hi'
  //   example 2: strtr('äaabaåccasdeöoo', 'äåö','aao')
  //   returns 2: 'aaabaaccasdeooo'
  //   example 3: strtr('ääääääää', 'ä', 'a')
  //   returns 3: 'aaaaaaaa'
  //   example 4: strtr('http', 'pthxyz','xyzpth')
  //   returns 4: 'zyyx'
  //   example 5: strtr('zyyx', 'pthxyz','xyzpth')
  //   returns 5: 'http'
  //   example 6: strtr('aa', {'a':1,'aa':2})
  //   returns 6: '2'
//   const krsort = require('../array/krsort')
//   const iniSet = require('../info/ini_set')
  let fr = ''
  let i = 0
  let j = 0
  let lenStr = 0
  let lenFrom = 0
  let sortByReference = false
  let fromTypeStr = ''
  let toTypeStr = ''
  let istr = ''
  const tmpFrom = []
  const tmpTo = []
  let ret = ''
  let match = false
  // Received replace_pairs?
  // Convert to normal trFrom->trTo chars
  if (typeof trFrom === 'object') {
    // Not thread-safe; temporarily set to true
    // @todo: Don't rely on ini here, use internal krsort instead
//    sortByReference = iniSet('locutus.sortByReference', false)
//    trFrom = krsort(trFrom)
//   iniSet('locutus.sortByReference', sortByReference)
    for (fr in trFrom) {
      if (trFrom.hasOwnProperty(fr)) {
        tmpFrom.push(fr)
        tmpTo.push(trFrom[fr])
      }
    }
    trFrom = tmpFrom
    trTo = tmpTo
  }
  // Walk through subject and replace chars when needed
  lenStr = str.length
  lenFrom = trFrom.length
  fromTypeStr = typeof trFrom === 'string'
  toTypeStr = typeof trTo === 'string'
  for (i = 0; i < lenStr; i++) {
    match = false
    if (fromTypeStr) {
      istr = str.charAt(i)
      for (j = 0; j < lenFrom; j++) {
        if (istr === trFrom.charAt(j)) {
          match = true
          break
        }
      }
    } else {
      for (j = 0; j < lenFrom; j++) {
        if (str.substr(i, trFrom[j].length) === trFrom[j]) {
          match = true
          // Fast forward
          i = (i + trFrom[j].length) - 1
          break
        }
      }
    }
    if (match) {
      ret += toTypeStr ? trTo.charAt(j) : trTo[j]
    } else {
      ret += str.charAt(i)
    }
  }
  return ret
}
