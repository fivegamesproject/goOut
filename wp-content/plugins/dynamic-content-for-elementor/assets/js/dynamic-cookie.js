"use strict";

jQuery( window ).on( 'elementor/frontend/init', () => {
	class DynamicCookie extends elementorModules.frontend.handlers.Base {
		onInit() {
			super.onInit();
			const elementSettings = this.getElementSettings();
			const mode = ( elementSettings.setcookie === 'yes' ) ? 'set' : 'del';
			const cookieName = elementSettings.cookie_name;
			const whenAlreadySet = elementSettings.cookie_if_exists; // append_comma, overwrite
			let cookieValue = elementSettings.cookie_value;
			const cookieExpirationTime = elementSettings.cookie_expires;
			const cookieExpirationTimeUnit = elementSettings.cookie_expires_value; // minutes or days
			if ( cookieName === '' ) {
				return;
			}
			if (mode === 'set') {
				var expires = "";
				if (cookieExpirationTime) {
					var date = new Date();
					if (cookieExpirationTimeUnit === 'minutes') {
						date.setTime(date.getTime() + (cookieExpirationTime*60*1000));
					} else if (cookieExpirationTimeUnit === 'days') {
						date.setTime(date.getTime() + (cookieExpirationTime*24*60*60*1000));
					}
					expires = "; expires=" + date.toUTCString();
				}
				// the following extract the value of our cookie from document.cookie
				var currentValue = document.cookie.replace(new RegExp("(?:(?:^|.*;)\\s*" + encodeURIComponent(cookieName).replace(/[\-\.\+\*]/g, "\\$&") + "\\s*\\=\\s*([^;]*).*$)|^.*$"), "$1") || null;
				if(currentValue && whenAlreadySet === 'append_comma'){
					cookieValue = currentValue+','+cookieValue;
				}
				document.cookie = cookieName + "=" + (cookieValue || "")  + expires + "; path=/";
			} else if (mode === 'unset') {
				document.cookie = cookieName + '=; expires=Thu, 01 Jan 1970 00:00:01 GMT;';
			}
		}
	}

	const addHandler = ( $element ) => {
		elementorFrontend.elementsHandler.addHandler( DynamicCookie, { $element, } );
	};
	elementorFrontend.hooks.addAction( 'frontend/element_ready/dce-dynamiccookie.default', addHandler );
});
