var _paq = _paq || [];
var piwikInfo = document.getElementById('piwikInfo');
var pid=piwikInfo.dataset.pid;
_paq.push(['setCustomVariable', 1, 'typo3pid', pid, 'page']);
_paq.push(['trackPageView']);
_paq.push(['enableLinkTracking']);
(function() {
	var u='//'+window.piwikInfo.dataset.domain+'/';
	_paq.push(['setTrackerUrl', u+'piwik.php']);
	_paq.push(['setSiteId', 1]);
	var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0];
	g.type='text/javascript'; g.async=true; g.defer=true; g.src=u+'piwik.js'; s.parentNode.insertBefore(g,s);
})();