<?php

namespace Autoborna\CoreBundle\EventListener;

use Autoborna\CoreBundle\CoreEvents;
use Autoborna\CoreBundle\Event\BuildJsEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class BuildJsSubscriber implements EventSubscriberInterface
{
    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            CoreEvents::BUILD_MAUTIC_JS => ['onBuildJs', 1000],
        ];
    }

    /**
     * Adds the AutobornaJS definition and core
     * JS functions for use in Bundles. This
     * must retain top priority of 1000.
     */
    public function onBuildJs(BuildJsEvent $event)
    {
        $js = <<<'JS'
// Polyfill for CustomEvent to support IE 9+
(function () {
    if ( typeof window.CustomEvent === "function" ) return false;
    function CustomEvent ( event, params ) {
        params = params || { bubbles: false, cancelable: false, detail: undefined };
        var evt = document.createEvent( 'CustomEvent' );
        evt.initCustomEvent( event, params.bubbles, params.cancelable, params.detail );
        return evt;
    }
    CustomEvent.prototype = window.Event.prototype;
    window.CustomEvent = CustomEvent;
})();

var AutobornaJS = AutobornaJS || {};

AutobornaJS.serialize = function(obj) {
    if ('string' == typeof obj) {
        return obj;
    }

    return Object.keys(obj).map(function(key) {
        return encodeURIComponent(key) + '=' + encodeURIComponent(obj[key]);
    }).join('&');
};

AutobornaJS.documentReady = function(f) {
    /in/.test(document.readyState) ? setTimeout(function(){AutobornaJS.documentReady(f)}, 9) : f();
};

AutobornaJS.iterateCollection = function(collection) {
    return function(f) {
        for (var i = 0; collection[i]; i++) {
            f(collection[i], i);
        }
    };
};

AutobornaJS.log = function() {
    var log = {};
    log.history = log.history || [];

    log.history.push(arguments);

    if (window.console) {
        console.log(Array.prototype.slice.call(arguments));
    }
};

AutobornaJS.setCookie = function(name, value) {
    document.cookie = name+"="+value+"; path=/; secure";
};

AutobornaJS.createCORSRequest = function(method, url) {
    var xhr = new XMLHttpRequest();
    
    method = method.toUpperCase();
    
    if ("withCredentials" in xhr) {
        xhr.open(method, url, true);
    } else if (typeof XDomainRequest != "undefined") {
        xhr = new XDomainRequest();
        xhr.open(method, url);
    }
    
    return xhr;
};
AutobornaJS.CORSRequestsAllowed = true;
AutobornaJS.makeCORSRequest = function(method, url, data, callbackSuccess, callbackError) {
    // Check for stored contact in localStorage
    data = AutobornaJS.appendTrackedContact(data);
    
    var query = AutobornaJS.serialize(data);
    if (method.toUpperCase() === 'GET') {
        url = url + '?' + query;
        var query = '';
    }
    
    var xhr = AutobornaJS.createCORSRequest(method, url);
    var response;
    
    callbackSuccess = callbackSuccess || function(response, xhr) { };
    callbackError = callbackError || function(response, xhr) { };

    if (!xhr) {
        AutobornaJS.log('AutobornaJS.debug: Could not create an XMLHttpRequest instance.');
        return false;
    }

    if (!AutobornaJS.CORSRequestsAllowed) {
        callbackError({}, xhr);
        
        return false;
    }
    
    xhr.onreadystatechange = function (e) {
        if (xhr.readyState === XMLHttpRequest.DONE) {
            response = AutobornaJS.parseTextToJSON(xhr.responseText);
            if (xhr.status === 200) {
                callbackSuccess(response, xhr);
            } else {
                callbackError(response, xhr);
               
                if (xhr.status === XMLHttpRequest.UNSENT) {
                    // Don't bother with further attempts
                    AutobornaJS.CORSRequestsAllowed = false;
                }
            }
        }
    };
   
    if (typeof xhr.setRequestHeader !== "undefined"){
        if (method.toUpperCase() === 'POST') {
            xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
        }
    
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.withCredentials = true;
    }
    xhr.send(query);
};

AutobornaJS.parseTextToJSON = function(maybeJSON) {
    var response;

    try {
        // handle JSON data being returned
        response = JSON.parse(maybeJSON);
    } catch (error) {
        response = maybeJSON;
    }

    return response;
};

AutobornaJS.insertScript = function (scriptUrl) {
    var scriptsInHead = document.getElementsByTagName('head')[0].getElementsByTagName('script');
    var lastScript    = scriptsInHead[scriptsInHead.length - 1];
    var scriptTag     = document.createElement('script');
    scriptTag.async   = 1;
    scriptTag.src     = scriptUrl;
    
    if (lastScript) {
        lastScript.parentNode.insertBefore(scriptTag, lastScript);
    } else {
        document.getElementsByTagName('head')[0].appendChild(scriptTag);
    }
};

AutobornaJS.insertStyle = function (styleUrl) {
    var linksInHead = document.getElementsByTagName('head')[0].getElementsByTagName('link');
    var lastLink    = linksInHead[linksInHead.length - 1];
    var linkTag     = document.createElement('link');
    linkTag.rel     = "stylesheet";
    linkTag.type    = "text/css";
    linkTag.href    = styleUrl;
    
    if (lastLink) {
        lastLink.parentNode.insertBefore(linkTag, lastLink.nextSibling);
    } else {
        document.getElementsByTagName('head')[0].appendChild(linkTag);
    }
};

AutobornaJS.guid = function () {
    function s4() {
        return Math.floor((1 + Math.random()) * 0x10000).toString(16).substring(1);
    }
    
    return s4() + s4() + '-' + s4() + '-' + s4() + '-' + s4() + '-' + s4() + s4() + s4();
};

AutobornaJS.dispatchEvent = function(name, detail) {
    var event = new CustomEvent(name, {detail: detail});
    document.dispatchEvent(event);
};

function s4() {
  return Math.floor((1 + Math.random()) * 0x10000)
    .toString(16)
    .substring(1);
}

AutobornaJS.mtcSet = false;
AutobornaJS.appendTrackedContact = function(data) {
    if (window.localStorage) {
        if (mtcId  = localStorage.getItem('mtc_id')) {
            data['autoborna_device_id'] = localStorage.getItem('autoborna_device_id');
        }              
    }
    
    return data;
};

AutobornaJS.getTrackedContact = function () {
    if (AutobornaJS.mtcSet) {
        // Already set
        return;
    }
    
    AutobornaJS.makeCORSRequest('GET', AutobornaJS.contactIdUrl, {}, function(response, xhr) {
        AutobornaJS.setTrackedContact(response);
    });
};

AutobornaJS.setTrackedContact = function(response) {
    if (response.id) {
        AutobornaJS.setCookie('mtc_id', response.id);
        AutobornaJS.setCookie('mtc_sid', response.sid);
        AutobornaJS.setCookie('autoborna_device_id', response.device_id);
        AutobornaJS.mtcSet = true;
            
        // Set the id in local storage in case cookies are only allowed for sites visited and Autoborna is on a different domain
        // than the current page
        try {
            localStorage.setItem('mtc_id', response.id);
            localStorage.setItem('mtc_sid', response.sid);
            localStorage.setItem('autoborna_device_id', response.device_id);
        } catch (e) {
            console.warn('Browser does not allow storing in local storage');
        }
    }
};

// Register events that should happen after the first event is delivered
AutobornaJS.postEventDeliveryQueue = [];
AutobornaJS.firstDeliveryMade      = false;
AutobornaJS.onFirstEventDelivery = function(f) {
    AutobornaJS.postEventDeliveryQueue.push(f);
};
AutobornaJS.preEventDeliveryQueue = [];
AutobornaJS.beforeFirstDeliveryMade = false;
AutobornaJS.beforeFirstEventDelivery = function(f) {
    AutobornaJS.preEventDeliveryQueue.push(f);
};
document.addEventListener('autobornaPageEventDelivered', function(e) {
    var detail   = e.detail;
    var isImage = detail.image;
    if (isImage && !AutobornaJS.mtcSet) {
        AutobornaJS.getTrackedContact();
    } else if (detail.response && detail.response.id) {
        AutobornaJS.setTrackedContact(detail.response);
    }
    
    if (!isImage && typeof detail.event[3] === 'object' && typeof detail.event[3].onload === 'function') {
       // Execute onload since this is ignored if not an image
       detail.event[3].onload(detail)       
    }
    
    if (!AutobornaJS.firstDeliveryMade) {
        AutobornaJS.firstDeliveryMade = true;
        for (var i = 0; i < AutobornaJS.postEventDeliveryQueue.length; i++) {
            if (typeof AutobornaJS.postEventDeliveryQueue[i] === 'function') {
                AutobornaJS.postEventDeliveryQueue[i](detail);
            }
            delete AutobornaJS.postEventDeliveryQueue[i];
        }
    }
});

/**
* Check if a DOM tracking pixel is present
*/
AutobornaJS.checkForTrackingPixel = function() {
    if (document.readyState !== 'complete') {
        // Periodically call self until the DOM is completely loaded
        setTimeout(function(){AutobornaJS.checkForTrackingPixel()}, 9)
    } else {
        // Only fetch once a tracking pixel has been loaded
        var maxChecks  = 3000; // Keep it from indefinitely checking in case the pixel was never embedded
        var checkPixel = setInterval(function() {
            if (maxChecks > 0 && !AutobornaJS.isPixelLoaded(true)) {
                // Try again
                maxChecks--;
                return;
            }
    
            clearInterval(checkPixel);
            
            if (maxChecks > 0) {
                // DOM image was found 
                var params = {}, hash;
                var hashes = AutobornaJS.trackingPixel.src.slice(AutobornaJS.trackingPixel.src.indexOf('?') + 1).split('&');

                for(var i = 0; i < hashes.length; i++) {
                    hash = hashes[i].split('=');
                    params[hash[0]] = hash[1];
                }

                AutobornaJS.dispatchEvent('autobornaPageEventDelivered', {'event': ['send', 'pageview', params], 'params': params, 'image': true});
            }
        }, 1);
    }
}
AutobornaJS.checkForTrackingPixel();

AutobornaJS.isPixelLoaded = function(domOnly) {
    if (typeof domOnly == 'undefined') {
        domOnly = false;
    }
    
    if (typeof AutobornaJS.trackingPixel === 'undefined') {
        // Check the DOM for the tracking pixel
        AutobornaJS.trackingPixel = null;
        var imgs = Array.prototype.slice.apply(document.getElementsByTagName('img'));
        for (var i = 0; i < imgs.length; i++) {
            if (imgs[i].src.indexOf('mtracking.gif') !== -1) {
                AutobornaJS.trackingPixel = imgs[i];
                break;
            }
        }
    } else if (domOnly) {
        return false;
    }

    if (AutobornaJS.trackingPixel && AutobornaJS.trackingPixel.complete && AutobornaJS.trackingPixel.naturalWidth !== 0) {
        // All the browsers should be covered by this - image is loaded
        return true;
    }

    return false;
};

if (typeof window[window.AutobornaTrackingObject] !== 'undefined') {
    AutobornaJS.input = window[window.AutobornaTrackingObject];
    if (typeof AutobornaJS.input.q === 'undefined') {
        // In case mt() is not executed right away
        AutobornaJS.input.q = [];
    }
    AutobornaJS.inputQueue = AutobornaJS.input.q;

    // Dispatch the queue event when an event is added to the queue
    if (!AutobornaJS.inputQueue.hasOwnProperty('push')) {
        Object.defineProperty(AutobornaJS.inputQueue, 'push', {
            configurable: false,
            enumerable: false,
            writable: false,
            value: function () {
                for (var i = 0, n = this.length, l = arguments.length; i < l; i++, n++) {
                    AutobornaJS.dispatchEvent('eventAddedToAutobornaQueue', arguments[i]);
                }
                return n;
            }
        });
    }

    AutobornaJS.getInput = function(task, type) {
        var matches = [];
        if (typeof AutobornaJS.inputQueue !== 'undefined' && AutobornaJS.inputQueue.length) {
            for (var i in AutobornaJS.inputQueue) {
                if (AutobornaJS.inputQueue[i][0] === task && AutobornaJS.inputQueue[i][1] === type) {
                    matches.push(AutobornaJS.inputQueue[i]);
                }
            }
        }
        
        return matches; 
    }
}

AutobornaJS.ensureEventContext = function(event, context0, context1) { 
    return (typeof(event.detail) !== 'undefined'
        && event.detail[0] === context0
        && event.detail[1] === context1);
};
JS;
        $event->appendJs($js, 'Autoborna Core');
    }
}
