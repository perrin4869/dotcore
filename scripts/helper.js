// JavaScript Document

// Taken from http://blog.outofhanwell.com/2006/03/10/object-oriented-callbacks/
function createObjectCallback(obj, fn, argumentsOverride) {
/// <summary>Creates a callback object with the correct "this" pointer</summary>
/// <param name="obj">The object that refers to the "this" pointer inside fn function</param>
/// <param name="fn" type="function">The function to callback</param>
/// <returns type="void">The callback function</returns>

    return function() {

        var finalArgs;
        if(argumentsOverride != null)
        {
            finalArgs = new Array();
            var i;
            for(i = 0; i < arguments.length;i++)
            {
                finalArgs.push(arguments[i]);
            }
            for(i = 0; i < argumentsOverride.length;i++)
            {
                finalArgs.push(argumentsOverride[i]);
            }
        }
        else
        {
            finalArgs = arguments;
        }

        fn.apply(obj, finalArgs);
    };
}


function $get(id)
{
    return document.getElementById(id);
}

/*
	Developed by Robert Nyman, http://www.robertnyman.com
	Code/licensing: http://code.google.com/p/getelementsbyclassname/
*/
var getElementsByClassName = function (className, tag, elm){
	if (document.getElementsByClassName && elm != null && elm.getElementsByClassName) {
		getElementsByClassName = function (className, tag, elm) {
			elm = elm || document;
			var elements = elm.getElementsByClassName(className),
				nodeName = (tag)? new RegExp("\\b" + tag + "\\b", "i") : null,
				returnElements = [],
				current;
			for(var i=0, il=elements.length; i<il; i+=1){
				current = elements[i];
				if(!nodeName || nodeName.test(current.nodeName)) {
					returnElements.push(current);
				}
			}
			return returnElements;
		};
	}
	else if (document.evaluate) {
		getElementsByClassName = function (className, tag, elm) {
			tag = tag || "*";
			elm = elm || document;
			var classes = className.split(" "),
				classesToCheck = "",
				xhtmlNamespace = "http://www.w3.org/1999/xhtml",
				namespaceResolver = (document.documentElement.namespaceURI === xhtmlNamespace)? xhtmlNamespace : null,
				returnElements = [],
				elements,
				node;
			for(var j=0, jl=classes.length; j<jl; j+=1){
				classesToCheck += "[contains(concat(' ', @class, ' '), ' " + classes[j] + " ')]";
			}
			try	{
				elements = document.evaluate(".//" + tag + classesToCheck, elm, namespaceResolver, 0, null);
			}
			catch (e) {
				elements = document.evaluate(".//" + tag + classesToCheck, elm, null, 0, null);
			}
			while ((node = elements.iterateNext())) {
				returnElements.push(node);
			}
			return returnElements;
		};
	}
	else {
		getElementsByClassName = function (className, tag, elm) {
			tag = tag || "*";
			elm = elm || document;
			var classes = className.split(" "),
				classesToCheck = [],
				elements = (tag === "*" && elm.all)? elm.all : elm.getElementsByTagName(tag),
				current,
				returnElements = [],
				match;
			for(var k=0, kl=classes.length; k<kl; k+=1){
				classesToCheck.push(new RegExp("(^|\\s)" + classes[k] + "(\\s|$)"));
			}
			for(var l=0, ll=elements.length; l<ll; l+=1){
				current = elements[l];
				match = false;
				for(var m=0, ml=classesToCheck.length; m<ml; m+=1){
					match = classesToCheck[m].test(current.className);
					if (!match) {
						break;
					}
				}
				if (match) {
					returnElements.push(current);
				}
			}
			return returnElements;
		};
	}
	return getElementsByClassName(className, tag, elm);
};

function getFileSync(url) {
  if (window.XMLHttpRequest) {
    AJAX=new XMLHttpRequest();
  } else {
    AJAX=new ActiveXObject("Microsoft.XMLHTTP");
  }
  if (AJAX) {
     AJAX.open("GET", url, false);
     AJAX.send(null);
     return AJAX.responseText;
  } else {
     return false;
  }
}

function swapNode (srcNode, node) {
    var nextSibling = srcNode.nextSibling;
    var parentNode = srcNode.parentNode;
    node.parentNode.replaceChild(srcNode, node);
    if(node == nextSibling){
        parentNode.insertBefore(node, srcNode);
    }
    else{
        // If we were swapping this for its next element, we only need to insert the element we removed before this element
        parentNode.insertBefore(node, nextSibling);
    }
}

function swapClass (srcNode, node) {
  var cssClass = srcNode.className;
  srcNode.className = node.className;
  node.className = cssClass;
}

function setOpacity(e, value) {
    e.style.opacity = value/10;
    e.style.filter = 'alpha(opacity=' + value*10 + ')';
}

// Function from:
// http://robertnyman.com/2006/04/24/get-the-rendered-style-of-an-element/
function getStyle(oElm, strCssRule){
    var strValue = "";
    if(document.defaultView && document.defaultView.getComputedStyle){
            strValue = document.defaultView.getComputedStyle(oElm, "").getPropertyValue(strCssRule);
    }
    else if(oElm.currentStyle){
            strCssRule = strCssRule.replace(/\-(\w)/g, function (strMatch, p1){
                    return p1.toUpperCase();
            });
            strValue = oElm.currentStyle[strCssRule];
    }
    return strValue;
}

function getInnerText(elem)
{
    return elem.innerHTML
        .replace(/<\s*(\w[\w\d]*)\b[^>]*>([\s\S]*?)<\s*\/\s*\1\s*>/gi, "$2")
        .replace(/<[^>]+?\/>/, '');
}

function getAttributeNS(elem, ns, attr) {
    return elem.getAttribute(ns+":"+attr);
}

function setAttributeNS(elem, ns, attr, val) {
    elem.setAttribute(ns+":"+attr, val);
}

function createCookie(c_name,value,expiredays)
{
var exdate=new Date();
exdate.setDate(exdate.getDate()+expiredays);
document.cookie=c_name+ "=" +escape(value)+
((expiredays==null) ? "" : ";expires="+exdate.toGMTString());
}

function readCookie(name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(';');
    for(var i=0;i < ca.length;i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1,c.length);
        if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
    }
    return null;
}

function eraseCookie(name) {
	createCookie(name,"",-1);
}

function getOffset (element) {
    var offset = {top: 0, left: 0};

    if (element.offsetTop)
    {
        offset.top += element.offsetTop;
        offset.left += element.offsetLeft;
    }

    var parent = element.offsetParent;
    while (parent) {
        if (parent.offsetTop)
            offset.top += parent.offsetTop;
        if(parent.offsetLeft)
            offset.left += parent.offsetLeft;

        parent = parent.offsetParent;
    }

    return offset;
}
