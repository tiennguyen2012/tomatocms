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
 * @version 	$Id: widget.loader.js 1288 2010-02-24 04:02:40Z huuphuoc $
 */

/* ========== Registry namespace ============================================ */
'Tomato.Core.Widget.Loader'.namespace();

/* ========== Tomato.Core.Widget.Loader ===================================== */

/**
 * Tomato.Core.Widget.Loader
 * This class required plugins:
 * - ajaxq
 * - jquery.json
 */
Tomato.Core.Widget.Loader = function() {};
Tomato.Core.Widget.Loader.baseUrl = '/';
Tomato.Core.Widget.Loader.queue = function(module, name, data, containerId) {
	Tomato.Core.Widget.Loader.queueAction(module, name, 'show', data, containerId);
};
Tomato.Core.Widget.Loader.queueAction = function(module, name, act, data, containerId) {
	$('#' + containerId).addClass('t_g_loading').html('');
	var baseUrl = Tomato.Core.Widget.Loader.baseUrl;
	baseUrl = baseUrl.replace(/\/+$/,"");
	$.ajaxq('widget', {
		url: baseUrl + '/core/widget/ajax/',
		data: { mod: module, name: name, act: act, params: data },
		success: function(response) {
			response = $.evalJSON(response);
			for (var i in response.css) {
				if ($('head').find('link[href="' + response.css[i] + '"]').length == 0) {
					$('<link rel="stylesheet" type="text/css" href="' + response.css[i] + '" />').appendTo('head');
				}
			}
			for (i in response.javascript) {
				if ($('body').find('script[src="' + response.javascript[i] + '"]').length == 0) {
					$('<script type="text/javascript" src="' + response.javascript[i] + '"></script>').prependTo('body');
				}
			}
			$('#' + containerId).removeClass('t_g_loading').html(response.content);
		}
	});
};
