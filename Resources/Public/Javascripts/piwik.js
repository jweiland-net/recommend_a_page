var _paq = _paq || [];
_paq.push(['trackPageView']);
_paq.push(['enableLinkTracking']);
(function() {
	var piwikInfo = document.getElementById('pageid');

	var pid=piwikInfo.dataset.pid;
	var u='//'+piwikInfo.dataset.piwikUrl+'/';

	_paq.push(['setCustomVariable','1','typo3pid',pid]);
	_paq.push(['setTrackerUrl', u+'piwik.php']);
	_paq.push(['setSiteId', 1]);
	var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0];
	g.type='text/javascript'; g.async=true; g.defer=true; g.src=u+'piwik.js'; s.parentNode.insertBefore(g,s);
})();