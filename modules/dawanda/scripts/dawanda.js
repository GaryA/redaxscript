/**
 * @tableofcontents
 *
 * 1. dawanda
 *    1.1 get url
 *    1.2 get data
 *    1.3 generate key
 *    1.4 create shortcut
 *    1.5 register shortcut
 *    1.6 init
 * 2. startup
 *
 * @since 2.0.2
 *
 * @package Redaxscript
 * @author Henry Ruhs
 */

(function ($)
{
	'use strict';

	/* @section 1. dawanda */

	$.fn.dawanda = function (options)
	{
		/* extend options */

		if (r.modules.dawanda.options !== options)
		{
			options = $.extend({}, r.modules.dawanda.options, options || {});
		}

		var dawanda = {};

		/* @section 1.1 get url */

		dawanda.getURL = function (call, id)
		{
			var route = r.modules.dawanda.routes[call],
				output = '';

			/* if route present */

			if (route)
			{
				/* replace placeholder */

				if (id)
				{
					route = route.replace('{id}', id);
				}
				output = options.url + '/' + route;
			}
			return output;
		};

		/* @section 1.2 get data */

		dawanda.getData = function (call, id, data, callback)
		{
			var key = dawanda.generateKey(id, data),
				keyStorage = 'dawandaData' + dawanda.generateKey(id, data);

			data.api_key = options.key;

			/* fetch from storage */

			if (r.support.webStorage && r.support.nativeJSON)
			{
				r.modules.dawanda.storage[key] = window.sessionStorage.getItem(keyStorage) || false;

				/* restore data from storage */

				if (typeof r.modules.dawanda.storage[key] === 'string')
				{
					r.modules.dawanda.data[key] = window.JSON.parse(r.modules.dawanda.storage[key]);
				}
			}

			/* fetch from data */

			if (typeof r.modules.dawanda.data[key] === 'object' && r.modules.dawanda.data[key]['calls'][call])
			{
				/* direct callback */

				if (typeof callback === 'function')
				{
					callback.call(this);
				}
			}

			/* else request data */

			else
			{
				$.ajax(
				{
					url: dawanda.getURL(call, id),
					dataType: 'jsonp',
					data: data,
					success: function (data)
					{
						/* handle data */

						if (typeof data.response === 'object' && typeof data.response.result === 'object')
						{
							r.modules.dawanda.data[key] = $.extend({}, r.modules.dawanda.data[key], data.response.result || {});

							/* register calls */

							r.modules.dawanda.data[key]['calls'] = r.modules.dawanda.data[key]['calls'] || {};
							r.modules.dawanda.data[key]['calls'][call] = true;

							/* set related storage */

							if (r.support.webStorage && r.support.nativeJSON)
							{
								window.sessionStorage.setItem(keyStorage, window.JSON.stringify(r.modules.dawanda.data[key]));
							}

							/* delayed callback */

							if (typeof callback === 'function')
							{
								callback.call(this);
							}
						}
					}
				});
			}
		};

		/* @section 1.3 generate key */

		dawanda.generateKey = r.modules.dawanda.generateKey = function (id, data)
		{
			var output = id;

			/* stringify data object */

			if (r.support.nativeJSON)
			{
				output += JSON.stringify(data).replace(/[^a-z0-9]/g, '');
			}
			return output;
		};

		/* @section 1.4 create shortcut */

		dawanda.createShortcut = function (call)
		{
			r.modules.dawanda[call] = function (id, data, callback)
			{
				dawanda.getData(call, id, data, callback);
			};
		};

		/* @section 1.5 register shortcut */

		dawanda.registerShortcut = function ()
		{
			for (var i in r.modules.dawanda.routes)
			{
				if (r.modules.dawanda.routes.hasOwnProperty(i))
				{
					dawanda.createShortcut(i);
				}
			}
		};

		/* @section 1.6 init */

		dawanda.init = function ()
		{
			/* data and storage object */

			r.modules.dawanda.data = {};
			r.modules.dawanda.storage = {};

			/* register shortcut */

			dawanda.registerShortcut();
		};

		/* init */

		dawanda.init();
	};

	/* @section 2. startup */

	$(function ()
	{
		if (r.modules.dawanda.startup)
		{
			$.fn.dawanda(r.modules.dawanda.options);
		}
	});
})(window.jQuery || window.Zepto);