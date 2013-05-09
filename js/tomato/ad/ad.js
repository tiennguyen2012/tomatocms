/**
 * TomatoCMS
 * 
 * LICENSE
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE Version 2 
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-2.0.txt
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@tomatocms.com so we can send you a copy immediately.
 * 
 * @copyright	Copyright (c) 2009-2010 TIG Corporation (http://www.tig.vn)
 * @license		http://www.gnu.org/licenses/gpl-2.0.txt GNU GENERAL PUBLIC LICENSE Version 2
 * @version 	$Id: ad.js 1640 2010-03-16 11:05:54Z huuphuoc $
 */

/* ========== Registry namespace ============================================ */
'Tomato.Ad.Banner'.namespace();
'Tomato.Ad.Zone'.namespace();
'Tomato.Ad.Loader'.namespace();

/* ========== Tomato.Ad.Banner ============================================== */

/**
 * Represent a banner.
 * If the banner have type of flash, the script require SwfObject to render the 
 * flash.
 */
Tomato.Ad.Banner = function(id, zone, options, baseUrl) {
	this._trackUrl = baseUrl + '/ad/track/';
	
	this._id = id;
	this._zone = zone;
	this._options = {
		/** 
		 * Format of banner. It can take one of values: image, flash, html
		 * TODO: Support frame
		 */
		'format': 'image',
		
		/** 
		 * The text of banner
		 */
		'text': '',
		'code': '',
		'clickUrl': '',
		'target': 'new_tab',
		'imageUrl': '',
		'pageUrl': window.location,
		'pageName': '',
		'mode': 'unique',
		
		/**
		 * The time (in seconds) each banner will be displayed in sharing mode.
		 * Note that, this timeout can not work with flash or animated image.
		 */
		'timeout': 15
	};
	
	this._init(options);
};

/**
 * Banner's mode
 * Do NOT change these values
 */
Tomato.Ad.Banner.UNIQUE_MODE 	= 'unique';
Tomato.Ad.Banner.SHARE_MODE 	= 'share';
Tomato.Ad.Banner.ALTERNATE_MODE = 'alternate';

Tomato.Ad.Banner.prototype.getId = function() { return this._id; };
Tomato.Ad.Banner.prototype.getZone = function() { return this._zone; };
Tomato.Ad.Banner.prototype.setZone = function(zone) { this._zone = zone; };

Tomato.Ad.Banner.prototype.getOption = function(name) {
	return this._options[name];
};

Tomato.Ad.Banner.prototype.render = function() {
	switch (this._options['format']) {
		case 'image':
			var ret = this._buildTrackUrl();
			// If you want to show all banners in zone at the sametime
			// don't set the width and height of image
			if (Tomato.Ad.Banner.ALTERNATE_MODE == this._options['mode']) {
				ret += '<img src="' + this._options['imageUrl'] + '" /></a>';
			} else {
				ret += '<img src="' + this._options['imageUrl'] + '" width="' + this._zone.getWidth() + '" height="' + this._zone.getHeight() + '" /></a>';
			}
			return ret;
			break;
		case 'flash':
			/**
			 * Require SWFObject to render flash file
			 */
			swfobject.embedSWF(this._options['imageUrl'], this._zone.getContainerId(), 
						this._zone.getWidth(), this._zone.getHeight(), "9.0.0", 
						"", {}, { allowscriptaccess: "always" }, {});
			break;
		case 'html':
			return this._options['code'];
			break;
	}
};

Tomato.Ad.Banner.prototype._init = function(options) {
	for (var name in options) {
		if (options[name] != null || options[name] != undefined) {
			this._options[name] = options[name];
		}
	}
};

Tomato.Ad.Banner.prototype._buildTrackUrl = function() {
	// Build target window
	var target = "_blank";
	switch (this._options['target']) {
		case 'new_tab':
			target = "_blank";
			break;
		case 'new_window':
			target = "_blank";
			break;
		case 'same_window':
			target = '';
			break;
	}
	
	switch (this._options['format']) {
		case 'image':
			return '<a target="' + target +'" alt="' + escape(this._options['text']) + '" title="' + escape(this._options['text']) 
				+ '" href="' + this._trackUrl + '?bannerId=' + this._id + '&zoneId=' + this._zone.getId() + '&pageName=' + this._options['pageName']
				+ '&clickUrl=' + escape(this._options['clickUrl']) + '">';
//			return '<a target="_blank" href="' + this._options['clickUrl'] + '">';
			break;
		default:
			break;
	}
};

/* ========== Tomato.Ad.Zone ================================================ */

Tomato.Ad.Zone = function(id, name, width, height) {
	this._id = id;
	this._name = name;
	this._width = width;
	this._height = height;
	this._banners = new Array();
	this._bannerIds = new Array();
	this._mode = Tomato.Ad.Banner.UNIQUE_MODE;
	this._currentBannerIndex = 0;
	this._containerId = this._id;
};

Tomato.Ad.Zone.prototype.getId = function() {
	return this._id;
};

/**
 * Getters/Setters
 */
Tomato.Ad.Zone.prototype.getContainerId = function() { return this._containerId; };
Tomato.Ad.Zone.prototype.setContainerId = function(id) { this._containerId = id; };
Tomato.Ad.Zone.prototype.getName = function() { return this._name; };
Tomato.Ad.Zone.prototype.getWidth = function() { return this._width; };
Tomato.Ad.Zone.prototype.getHeight = function() { return this._height; };

Tomato.Ad.Zone.prototype.addBanner = function(banner) {
	banner.setZone(this); 
	this._banners[this._banners.length] = banner;
	this._bannerIds[this._bannerIds.length] = banner.getId();
	this._mode = banner.getOption('mode');
};

Tomato.Ad.Zone.prototype.render = function() {
	if (this._banners.length == 0) {
		return;
	}
	$('#' + this._containerId).addClass('t_ad_zone');
	switch (this._mode) {
		case Tomato.Ad.Banner.UNIQUE_MODE:
			var html = this._banners[0].render();
			$('#' + this._containerId).html(html);
			break;
		case Tomato.Ad.Banner.SHARE_MODE:
			this._renderShareImageBanner();
			break;
		case Tomato.Ad.Banner.ALTERNATE_MODE:
			var html = '';
			// TODO: Add padding for image
			for (var i = 0; i < this._banners.length; i++) {
				html += this._banners[i].render();
			}
			$('#' + this._containerId).html(html);
			break;
	}
};

Tomato.Ad.Zone.prototype._renderShareImageBanner = function() {
	var self = this;
	var html = this._banners[this._currentBannerIndex].render();
	$('#' + this._containerId).html(html);
	
	setTimeout(function() { 
			self._renderShareImageBanner(); 
		}, this._banners[this._currentBannerIndex].getOption('timeout') * 1000
	);
	this._currentBannerIndex++;
	if (this._currentBannerIndex >= this._banners.length) {
		this._currentBannerIndex = 0;
	}
};

Tomato.Ad.Zone.prototype.contain = function(banner) {
	return ($.inArray(banner.getId(), this._bannerIds) > -1);
};

/* ========== Tomato.Ad.Loader ============================================== */

Tomato.Ad.Loader.load = function(zoneId, containerId, url) {
	if (url == null) {
		url = parseUri(window.location).relative;
	}
	url = Tomato.Ad.Loader._normalizeUrl(url);
	
	var gZone = G_AD_ZONES[zoneId + ''];
	if (gZone == null) {
		return;
	}
	// Convert from G_AD_ZONES to Zone instance
	var zone = new Tomato.Ad.Zone(gZone.id, gZone.name, gZone.width, gZone.height);
	zone.setContainerId(containerId);
	
	var gBanner = null, match = false, pageUrl = null;
	for (var i = 0; i < G_AD_BANNERS.length; i++) {
		gBanner = G_AD_BANNERS[i];
		var banner = new Tomato.Ad.Banner(gBanner.id, new Tomato.Ad.Zone(gBanner.zone.id), gBanner.options, gBanner.baseUrl);
		
		pageUrl = banner.getOption('pageUrl');
		pageUrl = Tomato.Ad.Loader._normalizeUrl(pageUrl);
		var regex = new RegExp(pageUrl, "g");
		
		match = (url == '' && pageUrl == '')
				|| (pageUrl != '' && regex.exec(url) != null);
		if (match && banner.getZone().getId() == zone.getId() && !zone.contain(banner)) {
			zone.addBanner(banner);
		}
	}
	zone.render();
};

Tomato.Ad.Loader._normalizeUrl = function(url) {
	// Remove all "/" at the begining
	url = url.replace(/^(\/+)/, '');
	
	// Remove all "/" at the end
	url = url.replace(/(\/+)$/, '');
	return url;
};

/* ========== Libs ========================================================== */

/**
 * parseUri 1.2.1
 * (c) 2007 Steven Levithan <stevenlevithan.com> 
 * MIT License
 */
function parseUri(str) {
	var	o   = parseUri.options,
		m   = o.parser[o.strictMode ? "strict" : "loose"].exec(str),
		uri = {},
		i   = 14;

	while (i--) uri[o.key[i]] = m[i] || "";

	uri[o.q.name] = {};
	uri[o.key[12]].replace(o.q.parser, function ($0, $1, $2) {
		if ($1) uri[o.q.name][$1] = $2;
	});
	return uri;
};

parseUri.options = {
	strictMode: false,
	key: ["source","protocol","authority","userInfo","user","password","host","port","relative","path","directory","file","query","anchor"],
	q:   {
		name:   "queryKey",
		parser: /(?:^|&)([^&=]*)=?([^&]*)/g
	},
	parser: {
		strict: /^(?:([^:\/?#]+):)?(?:\/\/((?:(([^:@]*):?([^:@]*))?@)?([^:\/?#]*)(?::(\d*))?))?((((?:[^?#\/]*\/)*)([^?#]*))(?:\?([^#]*))?(?:#(.*))?)/,
		loose:  /^(?:(?![^:@]+:[^:@\/]*@)([^:\/?#.]+):)?(?:\/\/)?((?:(([^:@]*):?([^:@]*))?@)?([^:\/?#]*)(?::(\d*))?)(((\/(?:[^?#](?![^?#\/]*\.[^?#\/.]+(?:[?#]|$)))*\/?)?([^?#\/]*))(?:\?([^#]*))?(?:#(.*))?)/
	}
};
