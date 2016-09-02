var _paq = _paq || [];
var piwikInfo = document.getElementById('piwikInfo');
var referrerPid=piwikInfo.dataset.referrerPid;
var targetPid=piwikInfo.dataset.targetPid;
_paq.push(['setCustomVariable', 1, 'referrerPid', referrerPid, 'page']);
_paq.push(['setCustomVariable', 2, 'targetPid', targetPid, 'page']);
_paq.push(['trackPageView']);
_paq.push(['enableLinkTracking']);
(function() {
	var u='//'+window.piwikInfo.dataset.domain+'/';
	_paq.push(['setTrackerUrl', u+'piwik.php']);
	_paq.push(['setSiteId', 1]);
	var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0];
	g.type='text/javascript'; g.async=true; g.defer=true; g.src=u+'piwik.js'; s.parentNode.insertBefore(g,s);
})();